<?php
/**
 * API で共通に使うリクエストの確認
 */

/**
 * 同じサイトのページから送られたか (CSRF 対策)
 * Origin (なければ Referer) のホストが, このサイトのホストと同じかを確かめる
 * どちらも送られてこない場合は, 同じサイトからとはみなさない
 */
function is_same_site(): bool {
  $host = $_SERVER['HTTP_HOST'] ?? '';
  $source = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
  $parts = parse_url($source);
  if ($host === '' || empty($parts['host'])) return false;
  $source_host = $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
  return strcasecmp($source_host, $host) === 0;
}
