<?php
/**
 * ContentEngine.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 *
 * qwel-tools-md-engine をベースに, はりいしゃ向けに調整したもの
 * - 読み込むディレクトリやパンくずの表記をオプションで指定できる (お知らせ・固定ページで使い分ける)
 * - 記事が見つからない場合の判定 (is_found), 前後の記事 (get_adjacent) を追加
 */

require_once __DIR__ . '/ContentLoader.php';
require_once __DIR__ . '/ContentNavigation.php';
require_once __DIR__ . '/Parsedown.php';

class ContentEngine {
  protected int $count;
  protected int $page;
  protected array $posts = [];
  protected ?array $article = null;
  protected array $navigation;

  /**
   * オプション:
   * dir: Markdown を置くディレクトリ (規定で content/)
   * count: 一覧の1ページあたりの件数 (規定で 10)
   * navigation: パンくずの表記 (ContentNavigation のオプション)
   */
  public function __construct(array $options = []) {
    $slug = $_GET['slug'] ?? null;
    $this->count = max(1, (int)($_GET['count'] ?? $options['count'] ?? 10));
    $this->page  = max(1, (int)($_GET['page'] ?? 1));
    $this->navigation = $options['navigation'] ?? [];

    $dir    = $options['dir'] ?? dirname(__DIR__) . '/content/';
    $loader = new ContentLoader($dir);

    // 全記事取得
    $this->posts = $loader->load();

    // 個別記事取得
    if ($this->is_single()) {
      $this->article = $loader->find((string)$slug);
    }
  }

  // 個別記事ページか否か
  public function is_single(): bool {
    return isset($_GET['slug']);
  }

  // 個別記事が見つかったか否か
  public function is_found(): bool {
    return $this->article !== null;
  }

  // 一覧の現在のページ番号
  public function get_page(): int {
    return $this->page;
  }

  // 全記事からページ数を切り取って取得
  public function get_posts(?int $page = null, ?int $count = null): array {
    $page  = $page  ?? $this->page;
    $count = $count ?? $this->count;
    return array_slice($this->posts, $count * ($page - 1), $count);
  }

  // 個別記事のメタ情報 (frontmatter) 取得
  public function get_meta(string $key, string $default = ''): string {
    return $this->article[$key] ?? $default;
  }

  // タイトル取得
  public function get_title(): string {
    return $this->get_meta('title');
  }

  // 日付取得
  public function get_date(string $format = 'Y.m.d'): string {
    $date = $this->get_meta('date');
    return $date ? date($format, strtotime($date)) : '';
  }

  // 記事内容取得
  public function get_content(): string {
    $parsedown = new Parsedown();
    return $parsedown->text($this->get_meta('content'));
  }

  // 前後の記事 (prev: 新しい記事, next: 古い記事) 取得
  public function get_adjacent(): array {
    $slugs = array_column($this->posts, 'slug');
    $index = array_search($this->get_meta('slug'), $slugs, true);
    if ($index === false) return ['prev' => null, 'next' => null];
    return [
      'prev' => $this->posts[$index - 1] ?? null,
      'next' => $this->posts[$index + 1] ?? null
    ];
  }

  // パンくず生成
  public function get_breadcrumb(): string {
    $nav = new ContentNavigation($this->navigation);
    return $this->is_found() ? $nav->breadcrumb($this->article) : $nav->breadcrumb();
  }

  // ページネーション生成
  public function pagination(): string {
    $currentPage = $this->page;
    $totalPages  = (int)ceil(count($this->posts) / $this->count);

    $nav = new ContentNavigation($this->navigation);

    return $nav->pagination($currentPage, $totalPages);
  }
}
