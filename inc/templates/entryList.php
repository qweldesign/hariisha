<?php
/**
 * 記事一覧 (info, report, page で共通)
 * 1カラムで, アイキャッチ画像の下に日付・タイトル・概要を並べる
 */
require_once dirname(__DIR__) . '/ContentEngine.php';
require_once dirname(__DIR__) . '/partials/site.php';

/**
 * $posts: ContentEngine::get_posts() の戻り値
 * $post_url: 記事の配列を受け取り, URL を返す関数
 * $show_date: 日付を表示するか (固定ページの一覧では表示しない)
 */
function render_entry_list(array $posts, callable $post_url, bool $show_date = true): void {
?>
          <ul class="entryList">
<?php foreach ($posts as $post) { ?>
            <li class="entryList__item">
              <a class="entryList__link" href="<?= e($post_url($post)) ?>">
<?php if (!empty($post['img'])) { ?>
                <figure class="entryList__image">
                  <img src="<?= e(path($post['img'])) ?>" alt="" loading="lazy">
                </figure>
<?php } ?>
                <div class="entryList__content">
<?php if ($show_date && !empty($post['date'])) { ?>
                  <time class="entryList__date" datetime="<?= e(ContentEngine::format_date($post['date'], true)) ?>"><?= e(ContentEngine::format_date($post['date'])) ?></time>
<?php } ?>
                  <h2 class="entryList__title"><?= e($post['title'] ?? '') ?></h2>
<?php if (!empty($post['summary'])) { ?>
                  <p class="entryList__summary"><?= e($post['summary']) ?></p>
<?php } ?>
                </div>
              </a>
            </li>
<?php } ?>
          </ul>
<?php
}
