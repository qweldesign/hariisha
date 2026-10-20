<?php
/**
 * site.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 *
 * 全ページ共通のヘッダー・フッターを出力する
 * 中身 (サイト名, ナビ, SNS など) は config.php の設定から作る
 */

require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__) . '/helpers.php';

/**
 * <head> ~ ヘッダーまで出力
 * $page:
 *   title: ページのタイトル (<title> は「title | サイト名」. 省略するとサイト名だけ)
 *   full_title: <title> をそのまま指定する (トップページなど)
 *   description: description (規定で SITE_DESCRIPTION)
 *   path: このページのサイトのルートからのパス (canonical, og:url に使う)
 *   image: OGP 画像 (規定で SITE_OGP_IMAGE)
 *   image_alt, image_width, image_height: OGP 画像の代替テキストと大きさ
 *   type: og:type (規定で article)
 *   noindex: 検索結果に出さない (404 など)
 *   home: トップページか (ナビをページ内リンクにして, ScrollSpy で現在地を示す)
 */
function render_header(array $page = []): void {
  $title = $page['full_title'] ?? (!empty($page['title']) ? $page['title'] . ' | ' . SITE_NAME : SITE_NAME);
  $description = ($page['description'] ?? '') ?: SITE_DESCRIPTION;
  $url = absolute_url($page['path'] ?? '');
  $image = absolute_url(($page['image'] ?? '') ?: SITE_OGP_IMAGE);
  $home = !empty($page['home']);
  ?>
<!DOCTYPE html>
<html lang="<?= e(SITE_LANG) ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--
      Built with QWEL Markdown Engine (Simple CMS)
      A lightweight, file-based CMS that renders Markdown as HTML.
      Created by QWEL.DESIGN - https://qwel.design
      Source: https://github.com/qweldesign/qwel-tools-md-engine (MIT License)
    -->
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
<?php if (SITE_FONTS_URL) { ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= e(SITE_FONTS_URL) ?>">
<?php } ?>
    <link rel="stylesheet" href="<?= path('style.css') ?>">
    <link rel="icon" href="<?= path('favicon.ico') ?>">
<?php if (SITE_GA_ID) { ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(SITE_GA_ID) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '<?= e(SITE_GA_ID) ?>');
    </script>
<?php } ?>
  </head>
  <body>
    <div data-scroll-sentinel></div>
    <header id="header" class="header" data-active-header>
      <div class="header__container">
        <nav id="gNav" class="gNav" aria-label="グローバルナビゲーション">
          <p class="gNav__siteBrand">
            <a href="<?= path() ?>"><?= e(SITE_LOGO_TEXT) ?></a>
          </p>
<?php if (SITE_NAV) { ?>
          <ul class="gNav__primaryMenu visible-md" data-primary-menu>
<?php foreach (SITE_NAV as $id => $label) { ?>
<?php if ($home) { ?>
            <li class="gNav__menuItem" data-spy-nav><a href="#<?= e($id) ?>"><?= e($label) ?></a></li>
<?php } else { ?>
            <li class="gNav__menuItem"><a href="<?= path() ?>#<?= e($id) ?>"><?= e($label) ?></a></li>
<?php } ?>
<?php } ?>
          </ul>
<?php } ?>
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
          <p class="footer__name"><?= e(SITE_FOOTER_NAME) ?></p>
          <address class="footer__address"><?= e(SITE_ADDRESS) ?></address>
          <p class="footer__contact"><a href="<?= e(contact_form_url()) ?>">ご予約・お問い合わせ</a></p>
<?php if (SITE_SNS) { ?>
          <ul class="footer__sns">
<?php foreach (SITE_SNS as $sns) { ?>
<?php $external = (bool)preg_match('#^https?://#', $sns['url']); ?>
            <li>
              <a href="<?= e(path($sns['url'])) ?>"<?= $external ? ' target="_blank" rel="noopener"' : '' ?>>
<?php if (!empty($sns['icon'])) { ?>
                <svg class="icon" aria-hidden="true"><use href="<?= path('assets/icons.svg') ?>#<?= e($sns['icon']) ?>"></use></svg>
<?php } ?>
                <span><?= e($sns['label']) ?></span>
              </a>
            </li>
<?php } ?>
          </ul>
<?php } ?>
        </div>
<?php if (SITE_FOOTER_LINKS) { ?>
        <nav class="footer__nav" aria-label="サイト内のページ">
          <ul>
<?php foreach (SITE_FOOTER_LINKS as $linkPath => $label) { ?>
            <li><a href="<?= e(path($linkPath)) ?>"><?= e($label) ?></a></li>
<?php } ?>
          </ul>
        </nav>
<?php } ?>
      </div>
      <small class="footer__copyright">&copy; <?= e(copyright_years()) ?> <?= e(SITE_OWNER) ?></small>
    </footer>
    <script src="<?= path('init.js') ?>" type="module"></script>
  </body>
</html>
<?php
}

// お問い合わせフォーム (入力画面) の URL
function contact_form_url(): string {
  return path(SITE_CONTACT['form_url'] ?? SITE_CONTACT['path']);
}
