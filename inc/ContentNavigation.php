<?php
/**
 * ContentNavigation.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

class ContentNavigation {
  protected string $topLabel;
  protected string $topPath;
  protected ?string $subdirLabel;
  protected ?string $subdirPath;
  protected string $pageUrl;
  protected string $firstPageUrl;

  public function __construct(array $options = []) {
    $this->topLabel    = $options['topLabel']    ?? 'Top';
    $this->topPath     = $options['topPath']     ?? '/';
    // null を明示した場合 (固定ページ等) は一覧の階層を出さないため, ?? ではなく array_key_exists で判定する
    $this->subdirLabel = array_key_exists('subdirLabel', $options) ? $options['subdirLabel'] : 'Blog';
    $this->subdirPath  = array_key_exists('subdirPath', $options)  ? $options['subdirPath']  : '/blog/';
    // ページ送りのURL (%d にページ番号が入る). 1ページ目だけ別のURLにできる
    $this->pageUrl      = $options['pageUrl']      ?? '?page=%d';
    $this->firstPageUrl = $options['firstPageUrl'] ?? sprintf($this->pageUrl, 1);
  }

  // パンくずHTML生成
  // subdirPath が null の場合 (固定ページ等), 一覧の階層を出さない
  public function breadcrumb(?array $article = null): string {
    $crumbs = [];
    $item = fn($label, $path = null) => $path === null
      ? '<li class="breadcrumb__item is-current"><span aria-current="page">' . htmlspecialchars($label) . '</span></li>'
      : '<li class="breadcrumb__item"><a href="' . htmlspecialchars($path) . '">' . htmlspecialchars($label) . '</a></li>';

    // Top
    $crumbs[] = $item($this->topLabel, $this->topPath);

    // Subdir
    if ($this->subdirLabel) {
      $isCurrent = !($article && isset($article['title']));
      $crumbs[] = $item($this->subdirLabel, $isCurrent ? null : $this->subdirPath);
    }

    // Article title
    if ($article && isset($article['title'])) {
      $crumbs[] = $item($article['title']);
    }

    return '<nav aria-label="パンくずリスト"><ol id="breadcrumb" class="breadcrumb">'
       . implode('', $crumbs) . '</ol></nav>';
  }

  // ページネーションHTML生成
  public function pagination(int $currentPage, int $totalPages): string {
    if ($totalPages <= 1) return '';

    $html = '<nav aria-label="ページ送り"><ul class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
      if ($i === $currentPage) {
        $html .= '<li class="pagination__item is-current"><span aria-current="page">' . $i . '</span></li>';
      } else {
        $url = $i === 1 ? $this->firstPageUrl : sprintf($this->pageUrl, $i);
        $html .= '<li class="pagination__item"><a href="' . htmlspecialchars($url) . '">' . $i . '</a></li>';
      }
    }
    $html .= '</ul></nav>';
    return $html;
  }
}
