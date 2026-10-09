<?php
/**
 * 固定ページ (/page/xxx/) と, その一覧 (/page/)
 * ページは content/page/*.md に置く
 * URL は page/.htaccess のリライトで index.php?slug=xxx に振り分ける
 * 一覧は件数が少ない想定なので, ページ送りをせず全件を表示する
 */
require_once dirname(__DIR__) . '/inc/ContentEngine.php';
require_once dirname(__DIR__) . '/inc/partials/site.php';
require_once dirname(__DIR__) . '/inc/templates/entryList.php';

const PAGE_LIST_LABEL = 'ページ一覧';

$cms = new ContentEngine([
  'dir'   => dirname(__DIR__) . '/content/page/',
  'count' => 100,
  'navigation' => [
    'topLabel'    => 'トップ',
    'topPath'     => path(),
    'subdirLabel' => PAGE_LIST_LABEL,
    'subdirPath'  => path('page/')
  ]
]);

// ページの URL
$page_url = fn(array $post): string => path('page/' . rawurlencode($post['slug']) . '/');

if (!$cms->is_single()) {
  // 一覧
  $page = [
    'title'       => PAGE_LIST_LABEL,
    'description' => SITE_NAME . 'のページの一覧です。',
    'path'        => 'page/',
  ];
} elseif (!$cms->is_found()) {
  // ページが見つからない場合は 404
  http_response_code(404);
  $page = ['title' => 'ページが見つかりません', 'path' => 'page/', 'noindex' => true];
} else {
  $page = [
    'title'       => $cms->get_title(),
    'description' => $cms->get_meta('summary'),
    'path'        => 'page/' . rawurlencode($cms->get_meta('slug')) . '/',
    'image'       => $cms->get_meta('img', '/assets/ogp.jpg'),
  ];
}

render_header($page);
?>
    <main id="main" class="main">
<?php if ($cms->is_found()) { ?>
      <article>
        <header class="pageHeader">
          <div class="pageHeader__container">
            <?= $cms->get_breadcrumb() ?>
            <h1 class="pageHeader__title"><?= e($cms->get_title()) ?></h1>
<?php if ($cms->get_meta('summary')) { ?>
            <p class="pageHeader__lead"><?= e($cms->get_meta('summary')) ?></p>
<?php } ?>
          </div>
        </header>
        <div class="subPage">
          <div class="subPage__container">
<?php if ($cms->get_meta('img')) { ?>
            <figure class="subPage__image">
              <img src="<?= e(path($cms->get_meta('img'))) ?>" alt="">
            </figure>
<?php } ?>
            <div class="entry">
<?= $cms->get_content() ?>
            </div>
          </div>
        </div>
      </article>
<?php } elseif (!$cms->is_single()) { ?>
      <!-- 一覧 -->
      <header class="pageHeader">
        <div class="pageHeader__container">
          <?= $cms->get_breadcrumb() ?>
          <h1 class="pageHeader__title"><?= e(PAGE_LIST_LABEL) ?></h1>
        </div>
      </header>
      <div class="subPage">
        <div class="subPage__container is-wide">
<?php render_entry_list($cms->get_posts(), $page_url, false); ?>
        </div>
      </div>
<?php } else { ?>
      <!-- ページが見つからない -->
      <header class="pageHeader">
        <div class="pageHeader__container">
          <?= $cms->get_breadcrumb() ?>
          <h1 class="pageHeader__title">ページが見つかりません</h1>
        </div>
      </header>
      <div class="subPage">
        <div class="subPage__container">
          <p>お探しのページは、削除されたか、URLが変更された可能性があります。</p>
          <div class="subPage__actions">
            <a class="button is-primary is-md" href="<?= path('page/') ?>"><?= e(PAGE_LIST_LABEL) ?>へ</a>
          </div>
        </div>
      </div>
<?php } ?>
    </main>
<?php render_footer(); ?>
