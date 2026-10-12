<?php
/**
 * 一覧と個別記事を持つ投稿タイプ (info: お知らせ, report: 活動報告) の共通テンプレート
 * 一覧: /{type}/, 2ページ目以降: /{type}/page/2/, 個別記事: /{type}/xxx/
 * 記事は content/{type}/*.md に置き, URL は {type}/.htaccess のリライトで振り分ける
 */
require_once dirname(__DIR__) . '/partials/site.php';
require_once __DIR__ . '/postList.php';

/**
 * $options:
 * type: 投稿タイプ (URL とディレクトリ名に使う. 例: info)
 * label: 表示名 (例: お知らせ)
 * lead: 一覧ページの見出しの下の文
 * description: 一覧ページの description (規定で lead と同じ)
 * count: 一覧の1ページあたりの件数 (規定で 10)
 */
function render_posts(array $options): void {
  $type        = $options['type'];
  $label       = $options['label'];
  $lead        = $options['lead'] ?? '';
  $description = $options['description'] ?? $lead;

$cms = new ContentEngine([
    'dir'   => dirname(__DIR__, 2) . '/content/' . $type . '/',
    'count' => $options['count'] ?? 10,
    'navigation' => [
      'topLabel'     => 'トップ',
      'topPath'      => path(),
      'subdirLabel'  => $label,
      'subdirPath'   => path($type . '/'),
      'pageUrl'      => path($type . '/page/%d/'),
      'firstPageUrl' => path($type . '/')
    ]
  ]);

  // 記事が見つからない場合と, 一覧のページ番号が範囲外 (例: /info/page/99/) の場合は 404
  $not_found = $cms->is_single()
    ? !$cms->is_found()
    : $cms->get_page() > 1 && !$cms->get_posts();
  if ($not_found) {
    http_response_code(404);
  }

  if ($cms->is_found()) {
    $page = [
      'title'       => $cms->get_title(),
      'description' => $cms->get_meta('summary'),
      'path'        => $type . '/' . rawurlencode($cms->get_meta('slug')) . '/',
      'image'       => $cms->get_meta('img'),
    ];
  } else {
    // 一覧の2ページ目以降は, それぞれのURLを canonical にする
    $page_number = $cms->get_page();
    $page = [
      'title'       => $not_found ? 'ページが見つかりません' : ($page_number > 1 ? "{$label} ({$page_number}ページ目)" : $label),
      'description' => $description,
      'path'        => $page_number > 1 ? "{$type}/page/{$page_number}/" : $type . '/',
      'noindex'     => $not_found,
    ];
  }

  render_header($page);
?>
    <main id="main" class="main">
<?php if ($cms->is_found()) { ?>
      <!-- 個別記事 -->
      <article>
        <header class="pageHeader">
          <div class="pageHeader__container">
            <?= $cms->get_breadcrumb() ?>
            <p class="pageHeader__label"><?= e($label) ?></p>
            <h1 class="pageHeader__title is-article"><?= e($cms->get_title()) ?></h1>
            <p class="pageHeader__meta"><?= date_tag($cms->get_meta('date')) ?></p>
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
<?php $adjacent = $cms->get_adjacent(); ?>
            <nav class="postNav" aria-label="前後の<?= e($label) ?>">
<?php if ($adjacent['prev']) { ?>
              <a class="postNav__link is-prev" href="<?= e(post_url($type, $adjacent['prev'])) ?>">
                <span class="postNav__label">新しい<?= e($label) ?></span>
                <span class="postNav__title"><?= e($adjacent['prev']['title']) ?></span>
              </a>
<?php } ?>
<?php if ($adjacent['next']) { ?>
              <a class="postNav__link is-next" href="<?= e(post_url($type, $adjacent['next'])) ?>">
                <span class="postNav__label">前の<?= e($label) ?></span>
                <span class="postNav__title"><?= e($adjacent['next']['title']) ?></span>
              </a>
<?php } ?>
            </nav>
            <div class="subPage__actions">
              <a class="button is-primary is-md" href="<?= path($type . '/') ?>"><?= e($label) ?>一覧へ</a>
            </div>
          </div>
        </div>
      </article>
<?php } elseif ($not_found) { ?>
      <!-- 記事が見つからない -->
      <header class="pageHeader">
        <div class="pageHeader__container">
          <?= $cms->get_breadcrumb() ?>
          <h1 class="pageHeader__title">ページが見つかりません</h1>
        </div>
      </header>
      <div class="subPage">
        <div class="subPage__container">
          <p>お探しの<?= e($label) ?>は、削除されたか、URLが変更された可能性があります。</p>
          <div class="subPage__actions">
            <a class="button is-primary is-md" href="<?= path($type . '/') ?>"><?= e($label) ?>一覧へ</a>
          </div>
        </div>
      </div>
<?php } else { ?>
      <!-- 一覧 -->
      <header class="pageHeader">
        <div class="pageHeader__container">
          <?= $cms->get_breadcrumb() ?>
          <h1 class="pageHeader__title"><?= e($label) ?></h1>
          <p class="pageHeader__lead"><?= e($lead) ?></p>
        </div>
      </header>
      <div class="subPage">
        <div class="subPage__container is-wide">
<?php $posts = $cms->get_posts(); ?>
<?php if ($posts) { ?>
<?php render_post_list($posts, $type); ?>
          <?= $cms->pagination() ?>
<?php } else { ?>
          <p class="subPage__empty">現在<?= e($label) ?>はありません。</p>
<?php } ?>
        </div>
      </div>
<?php } ?>
    </main>
<?php
  render_footer();
}
