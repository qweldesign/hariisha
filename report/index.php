<?php
/**
 * 活動報告 (一覧: /report/, 個別記事: /report/xxx/)
 * 記事は content/report/*.md に置く
 */
require_once dirname(__DIR__) . '/inc/templates/posts.php';

render_posts([
  'type'        => 'report',
  'label'       => '活動報告',
  'lead'        => '展覧会やレジデンシー、地域の人たちとの取り組みの様子をお伝えします。',
  'description' => 'はりいしゃの活動報告。展覧会やアーティスト・イン・レジデンス、地域の学校や人たちとの取り組みの様子をお伝えします。',
]);
