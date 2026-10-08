## QWEL STARTER TEMPLATE

[QWEL.DESIGN](https://qwel.design) のweb開発のためのスターターキット

---

## 投稿 (お知らせ・活動報告・固定ページ)

[qwel-tools-md-engine](https://github.com/qweldesign/qwel-tools-md-engine) をベースにした Markdown のコンテンツエンジン (`inc/`)。PHP が動くサーバーで, Markdown を置くだけでページが増える。

| 投稿タイプ | 原稿の置き場所 | URL |
| --- | --- | --- |
| info (お知らせ) | `content/info/*.md` | 一覧 `/info/`, `/info/page/2/` / 個別 `/info/xxx/` |
| report (活動報告) | `content/report/*.md` | 一覧 `/report/`, `/report/page/2/` / 個別 `/report/xxx/` |
| page (固定ページ) | `content/page/*.md` | `/page/xxx/` |

info と report は同じテンプレート (`inc/templates/posts.php`) を使い, `info/index.php` と `report/index.php` では表示名や説明文だけを指定している。投稿タイプを増やす場合は, 同じ形の `index.php` と `.htaccess` を持つディレクトリを作り, `content/` に同名のディレクトリを用意する。

URL は各ディレクトリの `.htaccess` のリライト (Apache の mod_rewrite) で `index.php?slug=xxx` に振り分けている。旧形式の `?slug=xxx` で来た場合は新しい URL へ 301 で転送する。サイトをドメイン直下以外に置く場合は, 各 `.htaccess` の `RewriteBase` と転送先, `inc/partials/site.php` の `SITE_ROOT` を書き換える。info と report の slug に `page` は使えない (ページ送りの URL と重なるため)。

原稿の冒頭に frontmatter を書く。

```
---
title: "記事のタイトル"
slug: "story"
img: "/assets/photos/main-house.jpg"
date: "2026-10-08"
summary: "一覧や description に使う要約"
---
```

- `title`, `slug` (URL に使う英数字), `date` (並び順) は必須
- `img`: アイキャッチ (任意, OGP 画像にもなる)
- `summary`: 一覧・見出しのリード文・description に使う要約 (任意)
- `draft: "true"`: 下書き (公開しない)
- 値の後ろにコメント (`# ...`) は書けない (値の一部として読み込まれる)

原稿用の写真は `content/images/` に置く (記事ごとにディレクトリを分けると管理しやすい. 例: `content/images/yamadayasutaka-2025/01.jpg`)。`content/` は直接のアクセスを禁止しているが, `content/images/` だけは画像の拡張子 (jpg, png, gif, webp, avif, svg) のファイルを公開している。本文中の画像やリンクは `/content/images/...` `/#reserve` のようにサイトのルートからのパスで書く (URL の階層が深いため, `../` のような相対パスは使わない)。ヘッダー・フッター・OGP は `inc/partials/site.php` で共通化している。`content/` と `inc/` は `.htaccess` で直接のアクセスを禁止している (Apache の場合)。

---

## ライセンス | License

MIT License

詳しくは LICENSE ファイルをご覧ください。  
See the LICENSE file for details.  

---

## 制作者 | Author

[QWEL.DESIGN](https://qwel.design)  
福井を拠点に活動するフロントエンド開発者  
Front-end developer based in Fukui, Japan  
