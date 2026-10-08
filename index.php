<?php
/**
 * トップページ
 * お知らせ (content/info/) の最新記事を差し込むため PHP にしている
 * ヘッダー・フッターはサブページと共通 (inc/partials/site.php)
 */
require_once __DIR__ . '/inc/ContentEngine.php';
require_once __DIR__ . '/inc/partials/site.php';

// お知らせ (最新3件)
$info = new ContentEngine(['dir' => __DIR__ . '/content/info/']);
$latest_info = $info->get_posts(1, 3);

render_header([
  'full_title'   => '海辺の古民家 はりいしゃ | 福井市越前海岸のゲストハウス＆ギャラリー',
  'title'        => '',
  'description'  => SITE_DESCRIPTION,
  'path'         => '',
  'type'         => 'website',
  'image'        => 'assets/ogp.jpg',
  'image_width'  => 1200,
  'image_height' => 630,
  'image_alt'    => '紺地に「海辺の古民家 はりいしゃ」の文字と、三角屋根のギャラリーの外観と越前海岸の夕日の写真を丸く切り抜いて配したイメージ',
  'is_top'       => true,
]);
?>
    <main id="main" class="main">
      <!-- ファーストビュー -->
      <header id="hero" class="hero" data-dots>
        <div class="hero__container">
          <div class="hero__content">
            <p class="hero__lead">Guest house & Gallery</p>
            <h1 class="hero__title">
              <span>海辺の古民家</span>
              <span>はりいしゃ</span>
            </h1>
            <p class="hero__copy">アーティストも泊まる海辺の古民家での<br>滞在を楽しみませんか。</p>
            <div class="hero__actions">
              <a class="button is-secondary is-md" href="#reserve">空き状況を問い合わせる</a>
              <a class="button is-primary is-sm" href="#guide">料金を見る</a>
            </div>
          </div>
          <div class="hero__visual">
            <p class="hero__caption visible-md">海まで徒歩５分</p>
            <div class="fader hero__fader" data-gallery="fader">
              <ul class="fader__inner">
                <li class="fader__item" data-gallery-item>
                  <img src="./assets/photos/gallery-exterior.jpg" alt="元鍼灸院の洋館を改装したギャラリーの外観" loading="lazy">
                </li>
                <li class="fader__item" data-gallery-item>
                  <img src="./assets/photos/tatami-room.jpg" alt="座卓と座布団が並ぶ畳の座敷" loading="lazy">
                </li>
                <li class="fader__item" data-gallery-item>
                  <img src="./assets/photos/main-house.jpg" alt="縁側と庭のある母屋の外観" loading="lazy">
                </li>
                <li class="fader__item" data-gallery-item>
                  <img src="./assets/photos/sea-rocks.jpg" alt="はりいしゃ近くの海に沈む夕日と岩礁">
                </li>
              </ul>
            </div>
          </div>
        </div>
      </header>

<?php if ($latest_info) { ?>
      <!-- お知らせ (最新3件) -->
      <section class="topInfo" aria-labelledby="topInfoHeading">
        <div class="topInfo__container">
          <h2 id="topInfoHeading" class="topInfo__heading">お知らせ</h2>
          <ul class="topInfo__list">
<?php foreach ($latest_info as $post) { ?>
            <li class="topInfo__item">
              <a class="topInfo__link" href="<?= e(path('info/' . rawurlencode($post['slug']) . '/')) ?>">
                <time class="topInfo__date" datetime="<?= e(date('Y-m-d', strtotime($post['date']))) ?>"><?= e(date('Y.m.d', strtotime($post['date']))) ?></time>
                <span class="topInfo__title"><?= e($post['title']) ?></span>
              </a>
            </li>
<?php } ?>
          </ul>
          <a class="topInfo__more" href="<?= path('info/') ?>">お知らせ一覧</a>
        </div>
      </section>
<?php } ?>

      <!-- 古民家に泊まる -->
      <section id="about" class="section is-white" data-spy-section>
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>古民家に泊まる</h2>
            <div class="section__body" data-readable>
              <div class="mediaText">
                <div class="mediaText__text">
                  <p class="mediaText__lead">旧街道に佇む、<br>ひとと文化が行き交う家。</p>
                  <p>福井市越前海岸の旧街道沿いにある古民家「はりいしゃ」。
                    <br>日本建築の母屋に、西洋風の元鍼灸院が寄り添うように建っています。名前の由来は、この「はりいしゃ（鍼医者）」から。</p>
                  <p>板張りの床、引き戸のガラス窓、意匠を凝らした柱や建具。
                    <br>地域の仲間たちがクラウドファンディングで資金を集め、自らの手で改修してきました。</p>
                  <p>作家が滞在して制作し、地域の人たちが集う、この海辺の文化の拠点に、旅の方にも泊まっていただけるようにしました。
                    <br>一度きりの観光ではなく、何度も訪れたくなる居場所として、越前海岸との縁を結んでいただけたら嬉しいです。</p>
                  <p class="mediaText__action">
                    <a class="button is-tertiary is-md" href="<?= path('page/story/') ?>">はりいしゃのはなし</a>
                  </p>
                </div>
                <figure class="mediaText__media">
                  <img src="./assets/photos/main-house.jpg" alt="縁側と庭のある母屋の外観" loading="lazy">
                </figure>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 館内のようす -->
      <!-- 水玉 (昼): 丸に近い光だまりを, 向きをばらつかせて右上に -->
      <section id="space" class="section is-navy houseView" data-spy-section data-dots data-dots-x="100" data-dots-y="0" data-dots-aspect-min="1.02" data-dots-aspect-max="1.12" data-dots-tilt="0" data-dots-tilt-jitter="180">
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>館内の様子</h2>
            <div class="section__body" data-readable>
              <p class="houseView__lead">昭和の手仕事が、<br>いまも息づく家。</p>
              <p>畳の続き間、丸窓の向こうのダイニング、タイル張りのキッチン、職人の手による組子の欄間。
                <br>日本建築の母屋と元鍼灸院の洋館、それぞれの趣をお楽しみください。</p>
            </div>
          </div>
          <!-- スライダーは画面幅いっぱいに表示する -->
          <div class="houseView__slider" data-readable>
            <div class="slider" data-gallery="slider" data-flickable>
              <ul class="slider__inner" data-gallery-main>
                <li class="slider__item" data-gallery-item>
                  <figure>
                    <img src="./assets/photos/tatami-room.jpg" alt="座卓と座布団が並ぶ畳の座敷" width="1440" height="960">
                    <figcaption class="slider__caption">母屋の座敷。続き間を広々と使えます。</figcaption>
                  </figure>
                </li>
                <li class="slider__item" data-gallery-item>
                  <figure>
                    <img src="./assets/photos/round-window.jpg" alt="丸窓越しに見えるダイニングとキッチン" width="750" height="900">
                    <figcaption class="slider__caption">丸窓の向こうにダイニングとキッチン。</figcaption>
                  </figure>
                </li>
                <li class="slider__item" data-gallery-item>
                  <figure>
                    <img src="./assets/photos/kitchen.jpg" alt="タイル張りのアイランドキッチン" width="1200" height="900">
                    <figcaption class="slider__caption">タイル張りのアイランドキッチン。</figcaption>
                  </figure>
                </li>
                <li class="slider__item" data-gallery-item>
                  <figure>
                    <img src="./assets/photos/inner-rooms.jpg" alt="障子を開け放った奥の間をのぞく子ども" width="1080" height="1350">
                    <figcaption class="slider__caption">障子を開けると奥まで見通せます。</figcaption>
                  </figure>
                </li>
                <li class="slider__item" data-gallery-item>
                  <figure>
                    <img src="./assets/photos/narcissus.jpg" alt="窓辺のガラスの花器に生けた水仙" width="1440" height="960">
                    <figcaption class="slider__caption">窓辺の水仙は越前海岸の冬の花。</figcaption>
                  </figure>
                </li>
                <li class="slider__item" data-gallery-item>
                  <figure>
                    <img src="./assets/photos/kumiko.jpg" alt="麻の葉模様の組子欄間" width="1350" height="1080">
                    <figcaption class="slider__caption">職人の手による麻の葉の組子。</figcaption>
                  </figure>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- ご宿泊について -->
      <section id="guide" class="section is-white" data-spy-section>
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>ご宿泊について</h2>
            <div class="section__body stayGuide" data-readable>
              <div class="stayGuide__overview">
                <div>
                  <h3 class="stayGuide__heading">料金（1泊）</h3>
                  <table class="priceTable">
                    <tbody>
                      <tr>
                        <th scope="row">基本料金（1〜2名）</th>
                        <td>25,000円</td>
                      </tr>
                      <tr>
                        <th scope="row">夏休期・休祝日前日（1〜2名）</th>
                        <td>30,000円</td>
                      </tr>
                      <tr>
                        <th scope="row">3名以降 1名追加ごと（時期問わず）</th>
                        <td>+5,000円</td>
                      </tr>
                      <tr>
                        <th scope="row">小学生</th>
                        <td>3,000円 / 名</td>
                      </tr>
                      <tr>
                        <th scope="row">未就学児（添い寝）</th>
                        <td>無料</td>
                      </tr>
                    </tbody>
                  </table>
                  <p class="stayGuide__note">※ 中学生以上は大人料金、表記はすべて税抜価格です。</p>
                </div>
                <dl class="infoList">
                  <dt>定員</dt>
                  <dd>4名</dd>
                  <dt>チェックイン</dt>
                  <dd>14:00〜17:00</dd>
                  <dt>チェックアウト</dt>
                  <dd>10:00</dd>
                  <dt>駐車場</dt>
                  <dd>敷地内2台まで無料</dd>
                </dl>
              </div>
              <div>
                <h3 class="stayGuide__heading">キャンセルについて</h3>
                <p>ご宿泊の7日前からキャンセル料が発生します。あらかじめご了承ください。</p>
                <ol class="cancelSteps">
                  <li class="cancelSteps__item">
                    <span class="cancelSteps__term">宿泊日の8日前まで</span>
                    <span class="cancelSteps__fee">無料</span>
                  </li>
                  <li class="cancelSteps__item">
                    <span class="cancelSteps__term">7日前〜2日前</span>
                    <span class="cancelSteps__fee">宿泊料金の50%</span>
                  </li>
                  <li class="cancelSteps__item">
                    <span class="cancelSteps__term">前日・当日・不泊</span>
                    <span class="cancelSteps__fee">宿泊料金の100%</span>
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 滞在のたのしみ -->
      <!-- 水玉 (午後): 少し伸びた光だまりを, 右下がりに左下へ -->
      <section id="stay" class="section is-sand" data-spy-section data-dots data-dots-x="0" data-dots-y="100" data-dots-aspect-min="1.15" data-dots-aspect-max="1.45" data-dots-tilt="-30">
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>滞在の愉しみ</h2>
            <div class="section__body" data-readable>
              <ul class="postList is-grid">
                <li class="postList__item">
                  <article class="postItem">
                    <figure class="postItem__image">
                      <img src="./assets/photos/woodblock-print.jpg" alt="刷り上がった木版画を手に取る様子" loading="lazy">
                    </figure>
                    <div class="postItem__content">
                      <h3 class="postItem__heading">木版画の体験</h3>
                      <p>滞在中に木版画の体験もできます。
                        <br>※有料・要予約（お問い合わせください）
                      </p>
                    </div>
                  </article>
                </li>
                <li class="postList__item">
                  <article class="postItem">
                    <figure class="postItem__image">
                      <img src="./assets/photos/sea-rocks.jpg" alt="岩礁の向こうに沈む夕日" loading="lazy">
                    </figure>
                    <div class="postItem__content">
                      <h3 class="postItem__heading">海まで徒歩5分</h3>
                      <p>日本海に沈む夕日、岩場の散歩、夏は越廼海水浴場へ。季節ごとに違う表情の海が、すぐそばにあります。</p>
                    </div>
                  </article>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- ギャラリー -->
      <section id="gallery" class="section is-white" data-spy-section>
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>ギャラリー</h2>
            <div class="section__body" data-readable>
              <div class="mediaText is-reverse">
                <div class="mediaText__text">
                  <p class="mediaText__label">海辺の小さな展示室</p>
                  <p class="mediaText__lead brandLogo">gallery はりいしゃ</p>
                  <p>元鍼灸院の洋館を改装した展示室で、版画家のコレクションを常設展示しています。</p>
                  <p class="mediaText__note">開廊: 第1/第3 日/月 11:00～16:00 (入館料 200円)
                    <br>宿泊のお客様を除き、上記日程外でのご来場は、予約制です。フォームからお申込みください。</p>
                </div>
                <div class="mediaText__media is-pair">
                  <img src="./assets/photos/gallery-exterior.jpg" alt="元鍼灸院の洋館を改装したギャラリーの外観" loading="lazy">
                  <img src="./assets/photos/gallery-window.jpg" alt="木枠の窓越しに見えるギャラリーの展示室" loading="lazy">
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- レジデンシー -->
      <section id="residency" class="section is-navy" data-spy-section>
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>レジデンシー</h2>
            <div class="section__body" data-readable>
              <div class="mediaText">
                <p class="mediaText__lead">越前の風土に触れ、<br>新しい文化の風を起こす。</p>
                <div class="mediaText__text">
                  <p>「はりいしゃレジデンシー」は、越前の風土に触れ、地域の特色や魅力と向き合いながら、招聘作家に滞在制作・展示会を行っていただくプログラムです。発表する作品のジャンルは美術・工芸・音楽など、文化活動の域を問いません。滞在制作・展示会のご希望は当サイトのフォームから承ります。</p>
                </div>
              </div>
              <h3>これまでの展示</h3>
              <ul class="archiveList">
                <li class="archiveList__item">
                  <a class="archiveList__link" href="https://discoverechizen.com/blog-and-news/naminokori-2026/">
                    <span class="archiveList__date">2026.06</span>
                    <span>
                      <span class="archiveList__title">波残り（なみのこり）</span>
                      <span class="archiveList__artist">木版画家 杉本 奈奈重</span>
                    </span>
                  </a>
                </li>
                <li class="archiveList__item">
                  <a class="archiveList__link" href="https://discoverechizen.com/blog-and-news/yoko-kawabata-report/">
                    <span class="archiveList__date">2025.12</span>
                    <span>
                      <span class="archiveList__title">虹色のしずく</span>
                      <span class="archiveList__artist">虹織り作家 YOKO KAWABATA</span>
                    </span>
                  </a>
                </li>
                <li class="archiveList__item">
                  <a class="archiveList__link" href="https://discoverechizen.com/blog-and-news/izumiharuomi-2025/">
                    <span class="archiveList__date">2025.12</span>
                    <span>
                      <span class="archiveList__title">日本画展 feel</span>
                      <span class="archiveList__artist">日本画家 泉 東臣</span>
                    </span>
                  </a>
                </li>
                <li class="archiveList__item">
                  <a class="archiveList__link" href="https://discoverechizen.com/blog-and-news/yamadayasutaka-2025-report/">
                    <span class="archiveList__date">2025.11</span>
                    <span>
                      <span class="archiveList__title">十一月の山の風のなかに</span>
                      <span class="archiveList__artist">彫刻家 山田 康貴</span>
                    </span>
                  </a>
                </li>
                <li class="archiveList__item">
                  <a class="archiveList__link" href="https://discoverechizen.com/blog-and-news/kazutoarizuka-2025/">
                    <span class="archiveList__date">2025.10</span>
                    <span>
                      <span class="archiveList__title">LIMINAL DIVER</span>
                      <span class="archiveList__artist">彫刻家 蟻塚 知都</span>
                    </span>
                  </a>
                </li>
                <li class="archiveList__item">
                  <a class="archiveList__link" href="https://discoverechizen.com/blog-and-news/otoshibumi-2025/">
                    <span class="archiveList__date">2025.10</span>
                    <span>
                      <span class="archiveList__title">海辺のかれら</span>
                      <span class="archiveList__artist">アニメーション作家 おとしぶみ</span>
                    </span>
                  </a>
                </li>
              </ul>
              <p><a href="https://discoverechizen.com/category/hariisha-residency/">過去の展示をもっと見る</a></p>
            </div>
          </div>
        </div>
      </section>

      <!-- 運営 -->
      <!-- 水玉 (夕方): 細長く伸びた影を, 少なめに右下へ -->
      <section id="team" class="section is-cream" data-spy-section data-dots data-dots-x="100" data-dots-y="100" data-dots-aspect-min="1.4" data-dots-aspect-max="1.7" data-dots-tilt="-15" data-dots-count="70">
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>運営チーム</h2>
            <div class="section__body" data-readable>
              <p class="section__text mb-large">海辺の古民家はりいしゃは「福井市越前海岸盛り上げ隊」が運営しています。「福井市越前海岸盛り上げ隊」は、ガラス作家、版画家、漁師、きこり、デザイナー、プログラマーなど、越前海岸エリアで事業を展開する異業種の仲間の集いです。</p>
              <ul class="memberList">
                <li class="memberList__item">
                  <img class="memberList__photo" src="./assets/photos/prt00.jpg">
                  <div>
                    <span class="memberList__role">隊長／<a href="https://watariglass.com" target="_blank">WATARIGLASS studio</a></span>
                    <span class="memberList__name">長谷川 渡</span>
                    <p class="memberList__note">ガラス作家。越前海岸の地域振興を牽引。</p>
                  </div>
                </li>
                <li class="memberList__item">
                  <img class="memberList__photo" src="./assets/photos/prt01.jpg">
                  <div>
                    <span class="memberList__role">企画・運営担当／<a href="https://hangakobo.com" target="_blank">版画ゆうびん舎</a></span>
                    <span class="memberList__name">おさの なおこ</span>
                    <p class="memberList__note">版画家。民宿・レジデンシー事業を企画・運営。</p>
                  </div>
                </li>
                <li class="memberList__item">
                  <img class="memberList__photo" src="./assets/photos/prt02.jpg">
                  <div>
                    <span class="memberList__role">デザイン担当／<a href="https://mogurimasu.com" target="_blank">mogurimasu</a></span>
                    <span class="memberList__name">鈴木 淳子</span>
                    <p class="memberList__note">デザイナー。レジデンシー事業の企画補助。</p>
                  </div>
                </li>
                <li class="memberList__item">
                  <img class="memberList__photo" src="./assets/photos/prt03.jpg">
                  <div>
                    <span class="memberList__role">システム担当／<a href="https://qwel.design" target="_blank">QWEL.DESIGN</a></span>
                    <span class="memberList__name">伊藤 大悟</span>
                    <p class="memberList__note">webエンジニア。当サイトを運営。</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- アクセス -->
      <section id="access" class="section is-white" data-spy-section>
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading" data-readable>アクセス</h2>
            <div class="section__body access" data-readable>
              <div class="embed" data-safe-embed>
                 <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5544.794580500314!2d136.01037282317606!3d36.03719540213307!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x5ff8c9e5af5a570d%3A0x1985eca1de905ca7!2z44Gv44KK44GE44GX44KDIOi2iuWJjea1t-WyuOebm-OCiuS4iuOBkumaiuS6pOa1geaWveiorQ!5e0!3m2!1sja!2sjp!4v1791271954655!5m2!1sja!2sjp" loading="lazy" title="海辺の古民家はりいしゃへのアクセス"></iframe>
              </div>
              <div>
                <p class="access__address">〒910-3553 福井県福井市蒲生町1-42</p>
                <dl class="access__routes">
                  <dt>バス</dt>
                  <dd>福井駅から越前海岸ブルーラインで1時間20分</dd>
                  <dt>お車</dt>
                  <dd>敦賀IC、または鯖江ICから50〜60分</dd>
                  <dt>駐車場</dt>
                  <dd>敷地内に2台。海水浴場横のトイレ前にも駐車できます。</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ご予約・お問い合わせ -->
      <section id="reserve" class="section is-navy" data-spy-section>
        <div class="section__inner">
          <div class="section__container">
            <h2 class="section__heading is-reserve" data-readable>ご予約・お問い合わせ</h2>
            <div class="section__body reserve" data-readable>
              <div class="reserve__contact">
                <p>ゲストハウスとしての空き状況の確認、ギャラリー来館・版画体験のご予約、滞在制作・展示会のご希望、当サイトへの質問・意見など、フォームからお気軽にお問い合わせください。</p>
              </div>
              <!-- 送信は js/contact-form.js (確認画面 confirm.html → api/send.php) -->
              <form class="form" data-contact-form>
                <table>
                  <tr>
                    <th><label for="guestName">お名前<span class="is-required">*必須</span></label></th>
                    <td><input id="guestName" type="text" name="お名前" autocomplete="name" required></td>
                  </tr>
                  <tr>
                    <th><label for="guestEmail">メールアドレス<span class="is-required">*必須</span></label></th>
                    <td><input id="guestEmail" type="email" name="メールアドレス" autocomplete="email" required></td>
                  </tr>
                  <tr>
                    <th><label for="stayDate">ご希望の宿泊日</label></th>
                    <td><input id="stayDate" type="date" name="宿泊希望日"></td>
                  </tr>
                  <tr>
                    <th><label for="guestCount">人数</label></th>
                    <td>
                      <select id="guestCount" name="人数">
                        <option value="1名">1名</option>
                        <option value="2名">2名</option>
                        <option value="3名">3名</option>
                        <option value="4名">4名</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th><label for="message">ご質問・ご要望</label></th>
                    <td><textarea id="message" name="ご質問・ご要望" rows="5"></textarea></td>
                  </tr>
                </table>
                <input class="button is-primary is-lg" type="submit" value="送信内容を確認">
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php render_footer(); ?>
