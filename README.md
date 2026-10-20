# 海辺の古民家 はりいしゃ

海辺の古民家はりいしゃ | 福井市越前海岸のゲストハウス&ギャラリー  

[https://hariisha.jp](https://hariisha.jp)  

※ このリポジトリは、[QWEL Markdown Engine (Simple CMS)](https://github.com/qweldesign/qwel-tools-md-engine) を使用して制作しています。  

---

## 投稿 (お知らせ・活動報告・固定ページ)

[qwel-tools-md-engine](https://github.com/qweldesign/qwel-tools-md-engine) をベースにした Markdown のコンテンツエンジン (`inc/`)。PHP が動くサーバーで, Markdown を置くだけでページが増える。

| 投稿タイプ | 原稿の置き場所 | URL |
| --- | --- | --- |
| info (お知らせ) | `content/info/*.md` | 一覧 `/info/`, `/info/page/2/` / 個別 `/info/xxx/` |
| report (活動報告) | `content/report/*.md` | 一覧 `/report/`, `/report/page/2/` / 個別 `/report/xxx/` |
| page (固定ページ) | `content/page/*.md` | `/page/xxx/` |

サイト名・ナビ・投稿タイプ (表示名や説明文)・お問い合わせフォームの項目などは `config.php` にまとめている。info, report, page は同じテンプレート (`inc/templates/posts.php`) を使う。投稿タイプを増やす場合は, `config.php` の `SITE_POST_TYPES` に追加し, 同じ形の `index.php` と `.htaccess` を持つディレクトリを作り, `content/` に同名のディレクトリを用意する。サイトマップ (`/sitemap.xml`) は `sitemap.php` が `content/` から自動で作る。

URL は各ディレクトリの `.htaccess` のリライト (Apache の mod_rewrite) で `index.php?slug=xxx` に振り分けている。旧形式の `?slug=xxx` で来た場合は新しい URL へ 301 で転送する。サイトをドメイン直下以外に置く場合は, `config.php` の `SITE_ROOT` を書き換える。info と report の slug に `page` は使えない (ページ送りの URL と重なるため)。

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

原稿用の写真は `content/images/` に置く (記事ごとにディレクトリを分けると管理しやすい. 例: `content/images/yamadayasutaka-2025/01.jpg`)。`content/` は直接のアクセスを禁止しているが, `content/images/` だけは画像の拡張子 (jpg, png, gif, webp, avif, svg) のファイルを公開している。本文中の画像やリンクは `/content/images/...` `/#reserve` のようにサイトのルートからのパスで書く (URL の階層が深いため, `../` のような相対パスは使わない)。ヘッダー・フッター・OGP は `inc/partials/site.php` で共通化している。`content/`, `inc/`, `config.php` は `.htaccess` で直接のアクセスを禁止している (Apache の場合)。

---

## お問い合わせフォーム

トップページのフォーム (`#reserve`) → 確認画面 `/contact/confirm/` → 送信 `api/send.php` (PHPMailer) → 完了画面 `/contact/thanks/`。入力内容は SessionStorage で確認画面へ受け渡す (`js/contact-form.js`)。

- `inc/` で `composer install` を実行して PHPMailer を入れる
- `inc/.env.example` を `inc/.env` にコピーし, SMTP などの設定を書く (`.env` は Git に含めない)
- 項目・自動返信の署名などは `config.php` の `SITE_CONTACT`, `SITE_CONTACT_FIELDS` で設定する (入力欄・確認画面・メール本文・サーバー側の検証をまとめて作る)
- 迷惑メール対策: Origin / Referer のチェック, 見えない入力欄 (ハニーポット), 同じセッションからの送信間隔の制限
- トップページの URL に `?subject=キー#reserve` を付けると, 件名を選んだ状態でフォームを開ける (キーは `config.php` の件名の `options`)

## 空き状況カレンダー

トップページのカレンダー (`js/business-calendar.js`) は `api/calendar.php` から空き状況を読み込む。データは `api/data/calendar.sqlite` (初回に自動で作成. Git に含めない)。状態は 予約可 / 予約不可 の2通りで, 登録のない日は予約可になる。

編集は `tools/calendar/` で行う。パスワード (`inc/.env` の `CALENDAR_PASSWORD`) を入力すると, 日付をクリックして状態を切り替えられる (その場で保存される)。パスワードはタブを閉じるまで SessionStorage に保持する。`CALENDAR_PASSWORD` を設定しないと, 誰も編集できない。

## 制作用ツール (`tools/`)

| ツール | 内容 |
| --- | --- |
| `tools/calendar/` | 空き状況の編集 |
| `tools/leaflet/` | 三つ折りリーフレット (詳しくは `tools/leaflet/README.md`) |
| `tools/ogp/` | OGP 画像 (`assets/ogp.jpg`) の制作 |

---

---

## ライセンス | License

### コード部分 | Code

MIT License

詳しくは LICENSE ファイルをご覧ください。  
See the LICENSE file for details.

### 写真・文章・その他コンテンツ | Contents

写真・文章・その他コンテンツの著作権は全て、福井市越前海岸盛り上げ隊に帰属します。  
複製・再利用・再配布は、明示的な許可なしには認められていません。

All copyrights in the photos, texts, and other content belong to Discover Echizen Coast.  
Reproduction, reuse, or redistribution without explicit permission is prohibited.

---

## 制作者 | Author

[QWEL.DESIGN](https://qwel.design)  
福井を拠点に活動するフロントエンド開発者  
Front-end developer based in Fukui, Japan  
