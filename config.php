<?php
/**
 * サイトの設定
 * サイト名や URL, 投稿タイプなど, サイトごとに変わる値はここにまとめる
 * (inc/ 以下のエンジン・テンプレートには, サイト固有の値を書かない)
 */

// 日付の判定・表示のタイムゾーン
date_default_timezone_set('Asia/Tokyo');

// サイト名 (<title> の「記事タイトル | サイト名」と og:site_name に使う)
const SITE_NAME = '海辺の古民家はりいしゃ';
// トップページの <title>
const SITE_HOME_TITLE = '海辺の古民家はりいしゃ | 福井市越前海岸のゲストハウス&ギャラリー';
// サイトの URL (末尾のスラッシュなし. OGP, canonical の絶対URLに使う)
const SITE_URL = 'https://hariisha.jp';
// サイトのルートのパス (ドメイン直下なら '/'. 例: /tools/simple-cms/ に置く場合)
const SITE_ROOT = '/';
// サイトの説明 (description の既定値)
const SITE_DESCRIPTION = '福井市越前海岸の旧街道に佇む古民家ゲストハウス「はりいしゃ」。海まで徒歩5分。版画家のコレクションを展示するギャラリーと、アーティスト・イン・レジデンスを併設しています。';
// OGP 画像の既定値 (サイトのルートからのパス. 1200 × 630. 個別に画像を指定したページ・記事はそちらを使う)
const SITE_OGP_IMAGE = 'assets/ogp.jpg';
// OGP 画像の既定値の代替テキスト (トップページに使う)
const SITE_OGP_IMAGE_ALT = '紺地に「海辺の古民家 はりいしゃ」の文字と、三角屋根のギャラリーの外観と越前海岸の夕日の写真を丸く切り抜いて配したイメージ';
// 記事一覧のサムネイルの既定値 (記事にアイキャッチがない場合)
const SITE_THUMBNAIL_IMAGE = 'assets/photos/tatami-room.jpg';
// Google Fonts などの読み込み (不要なら空文字)
const SITE_FONTS_URL = 'https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@500;700&family=Zen+Kaku+Gothic+New:wght@400;500;700&family=Klee+One:wght@600&display=swap';
// 言語
const SITE_LANG = 'ja';
// Google アナリティクスの測定 ID (不要なら空文字)
const SITE_GA_ID = 'G-05PSB10CL7';

// ヘッダーのロゴ (文字. リンク先はトップページ)
const SITE_LOGO_TEXT = '海辺の古民家はりいしゃ';

// ヘッダーのナビ (トップページのセクションの id => 表示名. トップでは #xxx, ほかのページでは /#xxx にする)
const SITE_NAV = [
  'info'      => 'お知らせ',
  'about'     => '泊まる',
  'guide'     => '料金',
  'gallery'   => 'ギャラリー',
  'residency' => 'レジデンシー',
  'access'    => 'アクセス',
  'reserve'   => 'ご予約',
];

// パンくずの先頭の階層 (path はサイトのルートからのパス, または http から始まる URL)
const SITE_BREADCRUMB = [
  ['label' => 'トップ', 'path' => ''],
];

// フッターの名前と所在地
const SITE_FOOTER_NAME = '海辺の古民家 はりいしゃ';
const SITE_ADDRESS = '〒910-3553 福井県福井市蒲生町1-42';

// フッターのリンク (サイトのルートからのパス => 表示名)
const SITE_FOOTER_LINKS = [
  'page/story/'       => 'はりいしゃのはなし',
  'page/stay-guide/'  => '館内のご利用案内',
  'page/nearby/'      => '周辺のご案内',
  'page/artist-stay/' => '滞在制作・ギャラリー利用について',
  'info/'             => 'お知らせ',
  'report/'           => '活動報告',
  'page/privacy/'     => 'プライバシーポリシー',
];

// フッターの SNS など (url は http から始まる URL, またはサイトのルートからのパス. icon は icons.svg の symbol の id)
const SITE_SNS = [
  ['label' => 'Instagram', 'url' => 'https://www.instagram.com/art.hariisha/', 'icon' => 'si-instagram'],
  ['label' => 'LINE公式アカウント', 'url' => 'https://lin.ee/TfaDFmh', 'icon' => 'si-line'],
];

// 著作権表示 (開始年 - 今年 運営者)
const SITE_OWNER = '福井市越前海岸盛り上げ隊';
const SITE_START_YEAR = 2020;

/**
 * 投稿タイプ
 * キー: 投稿タイプの名前
 * label: 表示名 (一覧の見出し, パンくず, 「新しい○○」などの表記)
 * path: 一覧の URL (サイトのルートからのパス. 同じ名前のディレクトリに index.php と .htaccess を置く)
 * dir: Markdown を置くディレクトリ (サイトのルートからのパス)
 * list: 一覧ページをサイトマップに載せるか (規定で true)
 * count: 一覧の1ページあたりの件数 (null で全件)
 * show_date: 日付を表示するか
 * lead: 一覧の見出しの下の文
 * description: 一覧の description (規定で lead)
 */
const SITE_POST_TYPES = [
  'info' => [
    'label'       => 'お知らせ',
    'path'        => 'info/',
    'dir'         => 'content/info',
    'count'       => 10,
    'show_date'   => true,
    'lead'        => '展覧会やイベント、宿泊についての最新情報をお届けします。',
    'description' => 'はりいしゃからのお知らせ。展覧会やイベント、レジデンシー、宿泊についての最新情報をお届けします。',
  ],
  'report' => [
    'label'       => '活動報告',
    'path'        => 'report/',
    'dir'         => 'content/report',
    'count'       => 10,
    'show_date'   => true,
    'lead'        => '展覧会やレジデンシー、地域の人たちとの取り組みの様子をお伝えします。',
    'description' => 'はりいしゃの活動報告。展覧会やアーティスト・イン・レジデンス、地域の学校や人たちとの取り組みの様子をお伝えします。',
  ],
  // 固定ページ (日付を出さず, 一覧は全件を1ページに出す)
  'page' => [
    'label'       => 'ページ一覧',
    'path'        => 'page/',
    'dir'         => 'content/page',
    'count'       => null,
    'show_date'   => false,
    'lead'        => '',
    'description' => '海辺の古民家はりいしゃのページの一覧です。',
  ],
];

// URL の形式 (false: ?slug=xxx, ?page=2 / true: xxx/, page/2/ ※ .htaccess のリライトが必要)
const SITE_PRETTY_URLS = true;

// 投稿タイプ以外のページ (サイトのルートからのパス. サイトマップに載せる)
const SITE_PAGES = [];

/**
 * お問い合わせフォーム (入力: トップページの #reserve, 確認・完了: /contact/confirm/, /contact/thanks/)
 * SMTP のパスワードなど秘密の値は inc/.env に書く (inc/.env.example を参照)
 *
 * path: フォームの URL (サイトのルートからのパス. 同じ名前のディレクトリに index.php, confirm/, thanks/ を置く. 送信は api/send.php)
 * form_url: 入力画面を別の場所 (トップページのセクションなど) に置く場合の URL (サイトのルートからのパス)
 * title: ページのタイトル
 * lead: フォームの上の説明文 (HTML 可)
 * submit_label: 入力画面の送信ボタンの文字
 * note: 送信ボタンの下の注記 (HTML 可)
 * interval: 同じ人 (セッション) が続けて送信できるまでの秒数 (0 で制限しない)
 * reply_footer: 自動返信メールの末尾に付ける文
 * env: .env の場所 (公開ディレクトリの外に置く場合は, このパスを書き換える. 空き状況カレンダーのパスワードもここから読む)
 */
const SITE_CONTACT = [
  'path'         => 'contact/',
  'form_url'     => '#reserve',
  'title'        => 'お問い合わせ',
  'lead'         => '',
  'submit_label' => '送信内容を確認',
  'note'         => 'ご入力内容は、<a href="/page/privacy/">プライバシーポリシー</a>に沿って取り扱います。',
  'interval'     => 60,
  'reply_footer' => "
内容を確認のうえ、折り返しご連絡いたします。今しばらくお待ちください。

────────────────────────────────────────────────
海辺の古民家 はりいしゃ
〒910-3553 福井県福井市蒲生町1-42
https://hariisha.jp

運営：福井市越前海岸盛り上げ隊
────────────────────────────────────────────────
",
  'env'          => __DIR__ . '/inc/.env',
];

/**
 * お問い合わせフォームの項目 (キーが name 属性. 並び順に表示する)
 * label: 項目名 (確認画面とメール本文にも使う)
 * type: text, email, tel, url, date, textarea, select, radio, checkbox
 *       (email の最初の項目を, 自動返信の宛先と管理者宛メールの返信先にする)
 * required: 必須か
 * options: select, radio, checkbox の選択肢 (キー => 表示名 の形にすると, query でキーを指定して選べる)
 * query: select の初期値を URL のクエリで選べるようにする (例: 'subject' なら /?subject=artist-stay#reserve)
 * range_end: date の項目を期間の開始日にし, 指定した項目 (終了日) と同じ行に並べる
 *            (今日より前の日付と, 開始日より前の終了日を選べないようにする)
 * row_label: 期間の行の見出し (規定で label)
 * placeholder, autocomplete, rows: 入力欄の属性 (任意)
 */
const SITE_CONTACT_FIELDS = [
  'name' => [
    'label'        => 'お名前',
    'type'         => 'text',
    'required'     => true,
    'autocomplete' => 'name',
  ],
  'email' => [
    'label'        => 'メールアドレス',
    'type'         => 'email',
    'required'     => true,
    'autocomplete' => 'email',
  ],
  'subject' => [
    'label'    => '件名',
    'type'     => 'select',
    'required' => true,
    'query'    => 'subject',
    'options'  => [
      'stay'        => 'ゲストハウスの空き状況・ご予約',
      'gallery'     => 'ギャラリー来館のご予約',
      'printmaking' => '版画体験のご予約',
      'artist-stay' => '滞在制作・展示会のご希望',
      'other'       => 'ご質問・ご意見・その他',
    ],
  ],
  'schedule_start' => [
    'label'     => 'ご希望の日程 (開始)',
    'row_label' => 'ご希望の日程',
    'type'      => 'date',
    'range_end' => 'schedule_end',
  ],
  'schedule_end' => [
    'label' => 'ご希望の日程 (終了)',
    'type'  => 'date',
  ],
  'guests' => [
    'label'   => '人数',
    'type'    => 'select',
    'options' => ['1名', '2名', '3名', '4名'],
  ],
  'message' => [
    'label' => 'ご質問・ご要望',
    'type'  => 'textarea',
    'rows'  => 5,
  ],
];
