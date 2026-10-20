<?php
/**
 * 活動報告 (一覧: /report/, 2ページ目以降: /report/page/2/, 個別記事: /report/xxx/)
 * 記事は content/report/*.md に置く. 表示名や説明文は config.php の SITE_POST_TYPES
 */
require_once dirname(__DIR__) . '/inc/templates/posts.php';

render_posts('report');
