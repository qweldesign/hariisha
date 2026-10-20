<?php
/**
 * postList.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 *
 * 記事一覧 (info, report, page で共通)
 * .postList.is-list で, アイキャッチ画像・日付・タイトル・概要を並べる
 * アイキャッチ (img) がない記事は, SITE_THUMBNAIL_IMAGE (config.php) を代わりに表示する
 * 記事全体をクリックできるよう, タイトルのリンク (.postItem__link) を記事の大きさまで広げている (scss/_postList.scss)
 */
require_once dirname(__DIR__) . '/partials/site.php';

/**
 * $posts: ContentEngine::get_posts() の戻り値
 * $type: 投稿タイプ (info, report, page. 記事の URL に使う)
 * $showDate: 日付を表示するか (規定で投稿タイプの show_date)
 */
function render_post_list(array $posts, string $type, ?bool $showDate = null): void {
  $showDate ??= post_type($type)['show_date'];
?>
          <ul class="postList is-list">
<?php foreach ($posts as $post) { ?>
            <li class="postList__item">
              <article class="postItem">
                <figure class="postItem__image">
                  <img src="<?= e(image_url($post['img'] ?? null)) ?>" alt="" loading="lazy">
                </figure>
                <div class="postItem__content">
<?php if ($showDate && !empty($post['date'])) { ?>
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
