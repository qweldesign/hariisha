<?php
/**
 * 記事一覧 (info, report, page で共通)
 * .postList.is-list で, アイキャッチ画像・日付・タイトル・概要を並べる
 * アイキャッチ (img) がない記事は, SITE_THUMBNAIL_IMAGE (inc/partials/site.php) を代わりに表示する
 * 記事全体をクリックできるよう, タイトルのリンク (.postItem__link) を記事の大きさまで広げている (scss/_postList.scss)
 */
require_once dirname(__DIR__) . '/partials/site.php';

/**
 * $posts: ContentEngine::get_posts() の戻り値
 * $type: 投稿タイプ (info, report, page. 記事の URL に使う)
 * $show_date: 日付を表示するか (固定ページの一覧では表示しない)
 */
function render_post_list(array $posts, string $type, bool $show_date = true): void {
?>
          <ul class="postList is-list">
<?php foreach ($posts as $post) { ?>
            <li class="postList__item">
              <article class="postItem">
                <figure class="postItem__image">
                  <img src="<?= e(path(($post['img'] ?? '') ?: SITE_THUMBNAIL_IMAGE)) ?>" alt="" loading="lazy">
                </figure>
                <div class="postItem__content">
<?php if ($show_date && !empty($post['date'])) { ?>
                  <p class="postItem__info"><?= date_tag($post['date'], 'postItem__date') ?></p>
<?php } ?>
                  <h2 class="postItem__heading"><a class="postItem__link" href="<?= e(post_url($type, $post)) ?>"><?= e($post['title'] ?? '') ?></a></h2>
<?php if (!empty($post['summary'])) { ?>
                  <p class="postItem__excerpt"><?= e($post['summary']) ?></p>
<?php } ?>
                </div>
              </article>
            </li>
<?php } ?>
          </ul>
<?php
}
