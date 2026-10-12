<?php
/**
 * 記事一覧 (info, report, page で共通)
 * .postList.is-list で, アイキャッチ画像・日付・タイトル・概要を並べる
 * 記事全体をクリックできるよう, タイトルのリンク (.postItem__link) を記事の大きさまで広げている (scss/_postList.scss)
 */
require_once dirname(__DIR__) . '/ContentEngine.php';
require_once dirname(__DIR__) . '/partials/site.php';

/**
 * $posts: ContentEngine::get_posts() の戻り値
 * $post_url: 記事の配列を受け取り, URL を返す関数
 * $show_date: 日付を表示するか (固定ページの一覧では表示しない)
 */
function render_post_list(array $posts, callable $post_url, bool $show_date = true): void {
?>
          <ul class="postList is-list">
<?php foreach ($posts as $post) { ?>
            <li class="postList__item">
              <article class="postItem">
<?php if (!empty($post['img'])) { ?>
                <figure class="postItem__image">
                  <img src="<?= e(path($post['img'])) ?>" alt="" loading="lazy">
                </figure>
<?php } ?>
                <div class="postItem__content">
<?php if ($show_date && !empty($post['date'])) { ?>
                  <p class="postItem__info"><time class="postItem__date" datetime="<?= e(ContentEngine::format_date($post['date'], true)) ?>"><?= e(ContentEngine::format_date($post['date'])) ?></time></p>
<?php } ?>
                  <h2 class="postItem__heading"><a class="postItem__link" href="<?= e($post_url($post)) ?>"><?= e($post['title'] ?? '') ?></a></h2>
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
