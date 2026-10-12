<?php
/**
 * 空き状況カレンダーの API
 * qwel-tools-business-calendar (https://github.com/qweldesign/qwel-tools-business-calendar) をもとに, はりいしゃ向けに調整
 *
 * 取得: GET  api/calendar.php?method=fetch&year=2026&month=10
 *       → [{"date":"2026-10-12","state":0}, ...] (登録のある日だけ. 登録のない日は JS 側の既定値)
 * 更新: POST api/calendar.php?method=update  (date=2026-10-12, state=0|1, password=編集用のパスワード)
 * 確認: POST api/calendar.php?method=auth    (password=編集用のパスワード) → 正しければ 200, 違えば 401
 *
 * 状態 (state): 0 = 予約不可, 1 = 予約可
 * データは api/data/calendar.sqlite (初回に自動で作成. data/.htaccess で直接のアクセスを禁止)
 *
 * 更新には, api/.env の CALENDAR_PASSWORD と同じパスワードが要る (編集画面 tools/calendar/ で入力する)
 * あわせて, 同じサイトのページからの送信かも確かめる
 */

require_once __DIR__ . '/lib/request.php';
require_once __DIR__ . '/lib/env.php';

// 過ぎた日の判定を日本時間で行う
date_default_timezone_set('Asia/Tokyo');

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
  $pdo->exec('CREATE TABLE IF NOT EXISTS t_status (date TEXT PRIMARY KEY, state INTEGER NOT NULL CHECK (state IN (0, 1)))');
  return $pdo;
}

// 編集用のパスワードが正しいか (api/.env の CALENDAR_PASSWORD と比べる)
// 設定がない場合は, 誰も編集できないようにする
function is_valid_password(string $password): bool {
  try {
    $expected = env_required(load_env(__DIR__ . '/.env'), 'CALENDAR_PASSWORD');
  } catch (RuntimeException $e) {
    error_log('空き状況カレンダーの設定エラー: ' . $e->getMessage());
    return false;
  }
  return hash_equals($expected, $password);
}

// 更新・確認の共通のチェック (POST, 同じサイトから, パスワード)
function require_editor(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['error' => 'POST only']);
  if (!is_same_site()) respond(403, ['error' => 'forbidden']);
  if (!is_valid_password((string)($_POST['password'] ?? ''))) {
    // 総当たりで試されにくいよう, 間違えたときは少し待たせる
    sleep(1);
    respond(401, ['error' => 'invalid password']);
  }
}

$method = $_GET['method'] ?? '';

try {
  if ($method === 'fetch') {
    $year = (int) ($_GET['year'] ?? date('Y'));
    $month = (int) ($_GET['month'] ?? date('n'));
    if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) respond(400, ['error' => 'invalid month']);

    // 日付は YYYY-MM-DD の文字列なので, 月の初日〜末日で範囲検索できる (主キーの索引が効く)
    $stmt = db()->prepare('SELECT date, state FROM t_status WHERE date BETWEEN :first AND :last ORDER BY date');
    $stmt->execute([':first' => sprintf('%04d-%02d-01', $year, $month), ':last' => sprintf('%04d-%02d-31', $year, $month)]);
    $rows = array_map(fn($row) => ['date' => $row['date'], 'state' => (int) $row['state']], $stmt->fetchAll());
    respond(200, $rows);
  }

  if ($method === 'auth') {
    require_editor();
    respond(200, ['ok' => true]);
  }

  if ($method === 'update') {
    require_editor();

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
