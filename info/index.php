<?php
/**
 * お知らせ (一覧: /info/, 個別記事: /info/xxx/)
 * 記事は content/info/*.md に置く
 */
require_once dirname(__DIR__) . '/inc/templates/posts.php';

render_posts([
  'type'        => 'info',
  'label'       => 'お知らせ',
  'lead'        => '展覧会やイベント、宿泊についての最新情報をお届けします。',
  'description' => 'はりいしゃからのお知らせ。展覧会やイベント、レジデンシー、宿泊についての最新情報をお届けします。',
]);
