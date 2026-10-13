<?php
/**
 * 全ページ (トップ, 投稿, 固定ページ, お問い合わせの確認・完了画面) 共通の設定と部品
 * ヘッダー・フッターの出力と, URL・日付の表記をまとめている
 */
require_once dirname(__DIR__) . '/ContentEngine.php';

// 日付の判定・表示は日本時間で行う (サーバーの設定が UTC でもずれないように)
date_default_timezone_set('Asia/Tokyo');

// サイト共通の設定
const SITE_NAME = '海辺の古民家はりいしゃ';
const SITE_URL  = 'https://hariisha.jp';
// サイトのルートのパス (ドメイン直下に置く前提. 例えば /hariisha/ に置く場合はここと .htaccess を書き換える)
const SITE_ROOT = '/';
// 運営者 (フッターの著作権表示) と, 著作権表示の開始年
const SITE_OWNER = '福井市越前海岸盛り上げ隊';
const SITE_START_YEAR = 2020;
// OGP 画像の既定値 (記事にアイキャッチがない場合など)
const SITE_OGP_IMAGE = 'assets/ogp.jpg';
// 投稿一覧のサムネイルの既定値 (記事にアイキャッチがない場合)
const SITE_THUMBNAIL_IMAGE = 'assets/photos/tatami-room.jpg';
const SITE_DESCRIPTION = '福井市越前海岸の旧街道に佇む古民家ゲストハウス「はりいしゃ」。海まで徒歩5分。版画家のコレクションを展示するギャラリーと、アーティスト・イン・レジデンスを併設しています。';

// グローバルナビ (トップページのセクションへのリンク)
const SITE_NAV = [
  'info'      => 'お知らせ',
  'about'     => '泊まる',
  'guide'     => '料金',
  'gallery'   => 'ギャラリー',
  'residency' => 'レジデンシー',
  'access'    => 'アクセス',
  'reserve'   => 'ご予約'
];

// 所在地と SNS (フッターに表示する)
const SITE_ADDRESS = '〒910-3553 福井県福井市蒲生町1-42';
const SITE_SNS = [
  'instagram' => ['label' => 'Instagram', 'url' => 'https://www.instagram.com/art.hariisha/'],
  'line'      => ['label' => 'LINE公式アカウント', 'url' => 'https://lin.ee/TfaDFmh'],
];

// フッターのリンク (サイトのルートからのパス => 表示名)
const SITE_FOOTER_LINKS = [
  'page/story/'       => 'はりいしゃのはなし',
  'page/stay-guide/'  => '館内のご利用案内',
  'page/nearby/'      => '周辺のご案内',
  'page/artist-stay/' => '滞在制作・ギャラリー利用について',
  'info/'             => 'お知らせ',
  'report/'           => '活動報告',
  'page/privacy/'     => 'プライバシーポリシー'
];

// HTMLエスケープ
function e(?string $value): string {
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// サイトのルートからのパス (例: assets/ogp.jpg) を, ルートからの絶対パスにする
// リライトで /info/xxx/ のように階層が深くなっても, 相対パスのずれが起きない
function path(string $path = ''): string {
  return preg_match('#^https?://#', $path) ? $path : SITE_ROOT . ltrim($path, '/');
}

// サイトのルートからのパスを絶対URLにする (OGP, canonical 用)
function absolute_url(string $path): string {
  return preg_match('#^https?://#', $path) ? $path : SITE_URL . path($path);
}

// 記事の URL (例: /info/website-open/). $type は投稿タイプ (info, report, page)
function post_url(string $type, array $post): string {
  return path($type . '/' . rawurlencode($post['slug'] ?? '') . '/');
}

// 記事の日付の time 要素 (年月のみの日付にも対応する. 日付がなければ空文字)
function date_tag(?string $date, string $class = ''): string {
  if (!$date) return '';
  $class_attr = $class !== '' ? ' class="' . e($class) . '"' : '';
  return '<time' . $class_attr . ' datetime="' . e(ContentEngine::format_date($date, true)) . '">'
    . e(ContentEngine::format_date($date)) . '</time>';
}

// 著作権表示の年 (例: 2020 - 2026. 開始年と同じ年は範囲表記にしない)
function copyright_years(): string {
  $current = (int)date('Y');
  return SITE_START_YEAR < $current ? SITE_START_YEAR . ' - ' . $current : (string)$current;
}

/**
 * <head> ~ ヘッダーまで出力
 * $page: title, description, path (サイトのルートからのパス), image, type, noindex
 *   full_title: <title> をそのまま指定する (トップページ用. 指定がなければ「title | サイト名」)
 *   image_alt, image_width, image_height: OGP 画像の代替テキストと大きさ
 *   is_top: トップページ (ナビをページ内リンクにして, ScrollSpy で現在地を示す)
 */
function render_header(array $page): void {
  $title = $page['full_title'] ?? (!empty($page['title']) ? $page['title'] . ' | ' . SITE_NAME : SITE_NAME);
  $is_top = !empty($page['is_top']);
  $description = ($page['description'] ?? '') ?: SITE_DESCRIPTION;
  $url = absolute_url($page['path'] ?? '/');
  $image = absolute_url(($page['image'] ?? '') ?: SITE_OGP_IMAGE);
  ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
<?php if (!empty($page['noindex'])) { ?>
    <meta name="robots" content="noindex">
<?php } else { ?>
    <link rel="canonical" href="<?= e($url) ?>">
<?php } ?>
    <meta property="og:type" content="<?= e($page['type'] ?? 'article') ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:url" content="<?= e($url) ?>">
    <meta property="og:image" content="<?= e($image) ?>">
<?php if (!empty($page['image_width'])) { ?>
    <meta property="og:image:width" content="<?= e((string)$page['image_width']) ?>">
    <meta property="og:image:height" content="<?= e((string)$page['image_height']) ?>">
<?php } ?>
<?php if (!empty($page['image_alt'])) { ?>
    <meta property="og:image:alt" content="<?= e($page['image_alt']) ?>">
<?php } ?>
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@500;700&family=Zen+Kaku+Gothic+New:wght@400;500;700&family=Klee+One:wght@600&display=swap">
    <link rel="stylesheet" href="<?= path('style.css') ?>">
    <link rel="icon" href="<?= path('favicon.ico') ?>">
  </head>
  <body>
    <div data-scroll-sentinel></div>
    <header id="header" class="header" data-active-header>
      <div class="header__container">
        <nav id="gNav" class="gNav" aria-label="グローバルナビゲーション">
          <p class="gNav__siteBrand">
            <a href="<?= path() ?>">海辺の古民家はりいしゃ</a>
          </p>
          <ul class="gNav__primaryMenu visible-md" data-primary-menu>
<?php foreach (SITE_NAV as $id => $label) { ?>
<?php if ($is_top) { ?>
            <li class="gNav__menuItem" data-spy-nav><a href="#<?= $id ?>"><?= e($label) ?></a></li>
<?php } else { ?>
            <li class="gNav__menuItem"><a href="<?= path() ?>#<?= $id ?>"><?= e($label) ?></a></li>
<?php } ?>
<?php } ?>
          </ul>
        </nav>
      </div>
    </header>
<?php
}

// フッター ~ </html> まで出力
function render_footer(): void {
  ?>
    <footer id="footer" class="footer">
      <div class="footer__container">
        <div class="footer__info">
          <p class="footer__name">海辺の古民家 はりいしゃ</p>
          <address class="footer__address"><?= e(SITE_ADDRESS) ?></address>
          <p class="footer__contact"><a href="<?= path() ?>#reserve">ご予約・お問い合わせ</a></p>
          <ul class="footer__sns">
<?php foreach (SITE_SNS as $key => $sns) { ?>
            <li>
              <a href="<?= e($sns['url']) ?>" target="_blank" rel="noopener">
                <svg class="icon" aria-hidden="true"><use href="<?= path('assets/icons.svg') ?>#si-<?= e($key) ?>"></use></svg>
                <span><?= e($sns['label']) ?></span>
              </a>
            </li>
<?php } ?>
          </ul>
        </div>
        <nav class="footer__nav" aria-label="サイト内のページ">
          <ul>
<?php foreach (SITE_FOOTER_LINKS as $link_path => $label) { ?>
            <li><a href="<?= path($link_path) ?>"><?= e($label) ?></a></li>
<?php } ?>
          </ul>
        </nav>
      </div>
      <small class="footer__copyright">&copy; <?= e(copyright_years()) ?> <?= e(SITE_OWNER) ?></small>
    </footer>
    <script src="<?= path('init.js') ?>" type="module"></script>
  </body>
</html>
<?php
}
