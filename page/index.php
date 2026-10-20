<?php
/**
 * 固定ページ (一覧: /page/, 個別のページ: /page/xxx/)
 * ページは content/page/*.md に置く. 一覧は件数が少ない想定なので, ページ送りをせず全件を表示する
 */
require_once dirname(__DIR__) . '/inc/templates/posts.php';

render_posts('page', ['layout' => 'page']);
