<?php
/**
 * ContentLoader.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

class ContentLoader {
  private string $dir;

  public function __construct(string $dir) {
    $this->dir = rtrim($dir, '/');
  }

  // 全記事を日付の新しい順に取得 (draft: true の記事は除く)
  public function load(): array {
    $articles = [];
    foreach (glob("$this->dir/*.md") as $file) {
      $parsed = $this->parse($file);
      if ($parsed && !$this->isDraft($parsed)) $articles[] = $parsed;
    }
    usort($articles, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
    return $articles;
  }

  // slug が一致する記事を取得
  public function find(string $slug): ?array {
    foreach (glob("$this->dir/*.md") as $file) {
      $parsed = $this->parse($file);
      if ($parsed && !$this->isDraft($parsed) && ($parsed['slug'] ?? null) === $slug) return $parsed;
    }
    return null;
  }

  protected function isDraft(array $article): bool {
    return ($article['draft'] ?? 'false') === 'true';
  }

  protected function parse(string $file): ?array {
    $content = file_get_contents($file);
    if (!$content) return null;

    if (preg_match('/^---\s*(.*?)\s*---\s*(.*)$/s', $content, $matches)) {
      $front = $matches[1];
      $body  = $matches[2];

      $meta = [];
      foreach (explode("\n", trim($front)) as $line) {
        if (preg_match('/^(\w+):\s*"?(.+?)"?$/', trim($line), $m)) {
          $meta[$m[1]] = $m[2];
        }
      }

      $meta['content'] = $body;
      return $meta;
    }

    return null;
  }
}
