<?php
/**
 * サブページ (お知らせ・固定ページ) 共通の設定と部品
 * トップページ (index.html) と同じヘッダー・フッターを出力する
 */

// サイト共通の設定
const SITE_NAME = '海辺の古民家 はりいしゃ';
const SITE_URL  = 'https://hariisha.jp';
// サイトのルートのパス (ドメイン直下に置く前提. 例えば /hariisha/ に置く場合はここと .htaccess を書き換える)
const SITE_ROOT = '/';
const SITE_DESCRIPTION = '福井市越前海岸の旧街道に佇む古民家ゲストハウス「はりいしゃ」。海まで徒歩5分。版画家のコレクションを展示するギャラリーと、アーティスト・イン・レジデンスを併設しています。';

// グローバルナビ (トップページのセクションへのリンク)
const SITE_NAV = [
  'about'     => '概要',
  'space'     => '館内',
  'guide'     => '料金',
  'stay'      => '滞在',
  'gallery'   => 'ギャラリー',
  'residency' => 'レジデンシー',
  'team'      => '運営',
  'access'    => 'アクセス',
  'reserve'   => 'ご予約',
];

// フッターのリンク (サブページ)
const FOOTER_NAV = [
  'info/'       => 'お知らせ',
  'page/story/' => 'はりいしゃのはなし',
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

/**
 * <head> ~ ヘッダーまで出力
 * $page: title, description, path (サイトのルートからのパス), image, type, noindex
 *   full_title: <title> をそのまま指定する (トップページ用. 指定がなければ「title | サイト名」)
 *   image_alt, image_width, image_height: OGP 画像の代替テキストと大きさ
 *   is_top: トップページ (ナビをページ内リンクにして, ScrollSpy で現在地を示す)
 */
function render_header(array $page): void {
  $title = $page['full_title'] ?? ($page['title'] ? $page['title'] . ' | ' . SITE_NAME : SITE_NAME);
  $is_top = !empty($page['is_top']);
  $description = $page['description'] ?: SITE_DESCRIPTION;
  $url = absolute_url($page['path'] ?? '/');
  $image = absolute_url($page['image'] ?? '/assets/ogp.jpg');
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
            <a class="brandLogo" href="<?= path() ?>">海辺の古民家はりいしゃ</a>
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
      <p class="brandLogo">海辺の古民家はりいしゃ</p>
      <nav aria-label="フッターナビゲーション">
        <ul class="footer__nav">
<?php foreach (FOOTER_NAV as $href => $label) { ?>
          <li class="footer__navItem"><a href="<?= path($href) ?>"><?= e($label) ?></a></li>
<?php } ?>
        </ul>
      </nav>
      <small class="footer__copyright"></small>
    </footer>
    <script src="<?= path('init.js') ?>" type="module"></script>
  </body>
</html>
<?php
}
