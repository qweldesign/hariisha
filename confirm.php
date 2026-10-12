<?php
/**
 * お問い合わせの確認画面
 * 入力内容は js/contact-form.js が SessionStorage から読み込んで表示し, 送信する (api/send.php)
 */
require_once __DIR__ . '/inc/partials/site.php';

render_header([
  'title'   => '内容確認',
  'path'    => 'confirm.php',
  'noindex' => true,
]);
?>
    <main id="main" class="main">
      <header class="pageHeader">
        <div class="pageHeader__container">
          <h1 class="pageHeader__title">内容確認</h1>
          <p class="pageHeader__lead">以下の内容で送信します。よろしければ「送信する」を押してください。</p>
        </div>
      </header>
      <section class="contactPage">
        <div class="contactPage__container">
          <!-- 入力内容は js/contact-form.js が表示する -->
          <table class="contactPage__table" data-contact-confirm></table>
          <div class="contactPage__actions">
            <button class="button is-primary is-md" type="button" data-contact-send>送信する</button>
            <button class="button is-tertiary is-md" type="button" data-contact-back>入力画面に戻る</button>
          </div>
        </div>
      </section>
    </main>
<?php render_footer(); ?>
