<?php
/**
 * お問い合わせの完了画面
 */
require_once __DIR__ . '/inc/partials/site.php';

render_header([
  'title'   => '送信完了',
  'path'    => 'thanks.php',
  'noindex' => true,
]);
?>
    <main id="main" class="main">
      <header class="pageHeader">
        <div class="pageHeader__container">
          <h1 class="pageHeader__title">送信完了</h1>
        </div>
      </header>
      <section class="contactPage">
        <div class="contactPage__container">
          <p>お問い合わせありがとうございました。</p>
          <p>ご入力いただいたメールアドレスに、受付確認のメールをお送りしました。内容を確認のうえ、折り返しご連絡いたします。</p>
          <p class="contactPage__note">確認メールが届かない場合は、迷惑メールフォルダをご確認いただくか、お手数ですが再度お問い合わせください。</p>
          <div class="contactPage__actions">
            <a class="button is-primary is-md" href="<?= path() ?>">トップへ戻る</a>
          </div>
        </div>
      </section>
    </main>
<?php render_footer(); ?>
