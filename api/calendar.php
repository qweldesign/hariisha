<?php
/**
 * 空き状況カレンダーの API
 * qwel-tools-business-calendar (https://github.com/qweldesign/qwel-tools-business-calendar) をもとに, はりいしゃ向けに調整
 *
 * 取得: GET  api/calendar.php?method=fetch&year=2026&month=10
 *       → [{"date":"2026-10-12","state":0}, ...] (登録のある日だけ. 登録のない日は JS 側の既定値)
 * 更新: POST api/calendar.php?method=update  (date=2026-10-12, state=0|1)
 *
 * 状態 (state): 0 = 予約不可, 1 = 予約可
 * データは api/data/calendar.sqlite (初回に自動で作成. data/.htaccess で直接のアクセスを禁止)
 *
 * TODO: 更新には認証を付ける (今は同じサイトからの送信かどうかしか確かめていない)
 */

const CALENDAR_DB = __DIR__ . '/data/calendar.sqlite';
const CALENDAR_STATES = [0, 1];

header('Content-Type: application/json; charset=UTF-8');

function respond(int $status, $body = null): void {
  http_response_code($status);
  if ($body !== null) echo json_encode($body, JSON_UNESCAPED_UNICODE);
  exit;
}

function db(): PDO {
  $pdo = new PDO('sqlite:' . CALENDAR_DB);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  // 1日につき1件だけ持つ (更新は上書き)
  $pdo->exec('CREATE TABLE IF NOT EXISTS t_status (date TEXT PRIMARY KEY, state INTEGER NOT NULL)');
  return $pdo;
}

// 同じサイトのページから送られたか (Origin / Referer のホストがこのサイトと同じか)
function is_same_site(): bool {
  $host = $_SERVER['HTTP_HOST'] ?? '';
  $source = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
  $parts = parse_url($source);
  if ($host === '' || empty($parts['host'])) return false;
  $source_host = $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
  return strcasecmp($source_host, $host) === 0;
}

$method = $_GET['method'] ?? '';

try {
  if ($method === 'fetch') {
    $year = (int) ($_GET['year'] ?? date('Y'));
    $month = (int) ($_GET['month'] ?? date('n'));
    if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) respond(400, ['error' => 'invalid month']);

    $stmt = db()->prepare('SELECT date, state FROM t_status WHERE substr(date, 1, 7) = :ym ORDER BY date');
    $stmt->execute([':ym' => sprintf('%04d-%02d', $year, $month)]);
    $rows = array_map(fn($row) => ['date' => $row['date'], 'state' => (int) $row['state']], $stmt->fetchAll());
    respond(200, $rows);
  }

  if ($method === 'update') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['error' => 'POST only']);
    if (!is_same_site()) respond(403, ['error' => 'forbidden']);

    $date = $_POST['date'] ?? '';
    $state = (int) ($_POST['state'] ?? -1);
    $parsed = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$parsed || $parsed->format('Y-m-d') !== $date) respond(400, ['error' => 'invalid date']);
    if (!in_array($state, CALENDAR_STATES, true)) respond(400, ['error' => 'invalid state']);

    $pdo = db();
    $stmt = $pdo->prepare('INSERT OR REPLACE INTO t_status (date, state) VALUES (:date, :state)');
    $stmt->execute([':date' => $date, ':state' => $state]);
    // 過ぎた日のデータは要らないので, 更新のついでに消す
    $pdo->prepare('DELETE FROM t_status WHERE date < :today')->execute([':today' => date('Y-m-d')]);
    respond(200, ['date' => $date, 'state' => $state]);
  }

  respond(400, ['error' => 'unknown method']);
} catch (PDOException $e) {
  error_log('空き状況カレンダーのエラー: ' . $e->getMessage());
  respond(500, ['error' => 'database error']);
}
