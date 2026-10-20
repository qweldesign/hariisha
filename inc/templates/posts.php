<?php
/**
 * posts.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 *
 * 投稿タイプの一覧と個別記事を出力する共通テンプレート
 * 一覧: config.php の path, 2ページ目以降: page/2/, 個別記事: xxx/ (リライトは各ディレクトリの .htaccess)
 *
 * 使い方 (一覧を置くディレクトリの index.php):
 *   require_once dirname(__DIR__) . '/inc/templates/posts.php';
 *   render_posts('info');
 */

require_once __DIR__ . '/postList.php';

/**
 * $type: 投稿タイプ (config.php の SITE_POST_TYPES のキー)
 * $options:
 *   layout: 'post' (規定. 投稿タイプの名前・日付・前後の記事・一覧へのボタンを出す)
 *           'page' (固定ページ. 見出しの下に summary を出し, 前後の記事は出さない)
 */
function render_posts(string $type, array $options = []): void {
  $config = post_type($type);
  $label  = $config['label'];
  $isPage = ($options['layout'] ?? 'post') === 'page';
  $cms = content_engine($type);

  // 記事が見つからない場合と, 一覧のページ番号が範囲外 (例: /info/page/99/) の場合は 404
  $notFound = $cms->is_not_found();
  if ($notFound) {
    http_response_code(404);
  }

  if ($cms->is_found()) {
    $page = [
      'title'       => $cms->get_title(),
      'description' => $cms->get_meta('summary'),
      'path'        => post_path($type, ['slug' => $cms->get_meta('slug')]),
      'image'       => $cms->get_meta('img'),
    ];
  } else {
    // 一覧の2ページ目以降は, それぞれのURLを canonical にする
    $pageNumber = $cms->get_page();
    $page = [
      'title'       => $notFound ? 'ページが見つかりません' : ($pageNumber > 1 ? "{$label} ({$pageNumber}ページ目)" : $label),
      'description' => $notFound && $isPage ? '' : $config['description'],
      'path'        => page_path($type, $pageNumber),
      'noindex'     => $notFound,
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
<?php if ($isPage) { ?>
            <h1 class="pageHeader__title"><?= e($cms->get_title()) ?></h1>
<?php if ($cms->get_meta('summary')) { ?>
            <p class="pageHeader__lead"><?= e($cms->get_meta('summary')) ?></p>
<?php } ?>
<?php } else { ?>
            <p class="pageHeader__label"><?= e($label) ?></p>
            <h1 class="pageHeader__title is-article"><?= e($cms->get_title()) ?></h1>
<?php if ($config['show_date']) { ?>
            <p class="pageHeader__meta"><?= date_tag($cms->get_meta('date')) ?></p>
<?php } ?>
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
<?php if (!$isPage) { ?>
<?php $adjacent = $cms->get_adjacent(); ?>
            <nav class="postNav" aria-label="前後の<?= e($label) ?>">
<?php if ($adjacent['prev']) { ?>
              <a class="postNav__link is-prev" href="<?= e(post_url($type, $adjacent['prev'])) ?>">
                <span class="postNav__label">新しい<?= e($label) ?></span>
                <span class="postNav__title"><?= e($adjacent['prev']['title'] ?? '') ?></span>
              </a>
<?php } ?>
<?php if ($adjacent['next']) { ?>
              <a class="postNav__link is-next" href="<?= e(post_url($type, $adjacent['next'])) ?>">
                <span class="postNav__label">前の<?= e($label) ?></span>
                <span class="postNav__title"><?= e($adjacent['next']['title'] ?? '') ?></span>
              </a>
<?php } ?>
            </nav>
            <div class="subPage__actions">
              <a class="button is-primary is-md" href="<?= e(type_url($type)) ?>"><?= e($label) ?>一覧へ</a>
            </div>
<?php } ?>
          </div>
        </div>
      </article>
<?php } elseif ($notFound) { ?>
      <!-- 記事が見つからない -->
      <header class="pageHeader">
        <div class="pageHeader__container">
          <?= $cms->get_breadcrumb() ?>
          <h1 class="pageHeader__title">ページが見つかりません</h1>
        </div>
      </header>
      <div class="subPage">
        <div class="subPage__container">
          <p>お探しの<?= e($isPage ? 'ページ' : $label) ?>は、削除されたか、URLが変更された可能性があります。</p>
          <div class="subPage__actions">
            <a class="button is-primary is-md" href="<?= e(type_url($type)) ?>"><?= e($label) ?><?= $isPage ? '' : '一覧' ?>へ</a>
          </div>
        </div>
      </div>
<?php } else { ?>
      <!-- 一覧 -->
      <header class="pageHeader">
        <div class="pageHeader__container">
          <?= $cms->get_breadcrumb() ?>
          <h1 class="pageHeader__title"><?= e($label) ?></h1>
<?php if ($config['lead']) { ?>
          <p class="pageHeader__lead"><?= e($config['lead']) ?></p>
<?php } ?>
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
