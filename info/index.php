<?php
/**
 * お知らせ (一覧: /info/, 2ページ目以降: /info/page/2/, 個別記事: /info/xxx/)
 * 記事は content/info/*.md に置く. 表示名や説明文は config.php の SITE_POST_TYPES
 */
require_once dirname(__DIR__) . '/inc/templates/posts.php';

render_posts('info');
