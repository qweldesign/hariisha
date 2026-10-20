<?php
/**
 * contact.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 *
 * お問い合わせフォームの3画面 (入力 → 確認 → 完了)
 * 入力内容は sessionStorage で確認画面へ渡し, 確認画面から api/send.php へ送る (js/contact-form.js)
 *
 * 使い方 (config.php の SITE_CONTACT の path と同じ名前のディレクトリ):
 *   index.php:         render_contact('form');
 *   confirm/index.php: render_contact('confirm');
 *   thanks/index.php:  render_contact('thanks');
 * 入力画面をトップページのセクションなどに置く場合は, SITE_CONTACT の form_url を設定し,
 * その場所で render_contact_form() を呼ぶ (index.php は form_url へ転送する)
 */

require_once dirname(__DIR__) . '/partials/site.php';
require_once dirname(__DIR__) . '/ContactForm.php';

// お問い合わせフォームの各画面の URL ($step: '' (入力), 'confirm/', 'thanks/')
// 入力画面は, form_url があればその URL
function contact_url(string $step = ''): string {
  if ($step === '' && !empty(SITE_CONTACT['form_url'])) return path(SITE_CONTACT['form_url']);
  return path(SITE_CONTACT['path'] . $step);
}

/**
 * $step: form (入力), confirm (確認), thanks (完了)
 */
function render_contact(string $step): void {
  // 入力画面を別の場所に置いている場合は, そこへ転送する
  if ($step === 'form' && !empty(SITE_CONTACT['form_url'])) {
    header('Location: ' . contact_url(), true, 301);
    exit;
  }

  $title = SITE_CONTACT['title'];
  $stepTitles = ['confirm' => '内容確認', 'thanks' => '送信完了'];
  $stepTitle = $stepTitles[$step] ?? null;
  $leads = [
    'form'    => SITE_CONTACT['lead'] ?? '',
    'confirm' => '以下の内容で送信します。よろしければ「送信する」を押してください。',
  ];
  $lead = $leads[$step] ?? '';

  render_header([
    'title'       => $stepTitle ?? $title,
    'description' => strip_tags(SITE_CONTACT['lead'] ?? ''),
    'path'        => SITE_CONTACT['path'] . ($stepTitle ? $step . '/' : ''),
    'noindex'     => $stepTitle !== null,
  ]);

  // パンくず (トップ > お問い合わせ > 確認・完了)
  $breadcrumb = new ContentNavigation([
    'parents'     => array_map(fn($crumb) => ['label' => $crumb['label'], 'path' => path($crumb['path'])], SITE_BREADCRUMB),
    'subdirLabel' => $title,
    'subdirPath'  => contact_url(),
  ]);
?>
    <main id="main" class="main">
      <header class="pageHeader">
        <div class="pageHeader__container">
          <?= $stepTitle ? $breadcrumb->breadcrumb(['title' => $stepTitle]) : $breadcrumb->breadcrumb() ?>
          <h1 class="pageHeader__title"><?= e($stepTitle ?? $title) ?></h1>
<?php if ($lead) { ?>
          <p class="pageHeader__lead"><?= $lead ?></p>
<?php } ?>
        </div>
      </header>
      <section class="contactPage">
        <div class="contactPage__container">
<?php
  match ($step) {
    'confirm' => render_contact_confirm(),
    'thanks'  => render_contact_thanks(),
    default   => render_contact_form(),
  };
?>
        </div>
      </section>
    </main>
<?php
  render_footer();
}

// 入力画面 (トップページのセクションなどからも呼べる)
function render_contact_form(): void {
?>
              <!-- 送信は js/contact-form.js (確認画面 <?= e(contact_url('confirm/')) ?> → api/send.php) -->
              <form class="form" data-contact-form data-confirm-url="<?= e(contact_url('confirm/')) ?>">
                <table>
<?php foreach (SITE_CONTACT_FIELDS as $name => $field) { ?>
<?php render_contact_field($name, $field); ?>
<?php } ?>
                </table>
                <div class="form__honeypot" aria-hidden="true">
                  <label for="contact-<?= ContactForm::HONEYPOT ?>">この欄は空のままにしてください</label>
                  <input id="contact-<?= ContactForm::HONEYPOT ?>" type="text" name="<?= ContactForm::HONEYPOT ?>" tabindex="-1" autocomplete="off">
                </div>
                <input class="button is-primary is-lg" type="submit" value="<?= e(SITE_CONTACT['submit_label'] ?? '確認') ?>">
<?php if (!empty(SITE_CONTACT['note'])) { ?>
                <p class="form__note"><?= SITE_CONTACT['note'] ?></p>
<?php } ?>
              </form>
<?php
}

// 入力画面の1項目 (表の1行). 期間の終了日 (ほかの項目の range_end) は, 開始日の行にまとめて出す
function render_contact_field(string $name, array $field): void {
  if (in_array($name, array_column(SITE_CONTACT_FIELDS, 'range_end'), true)) return;

  $type     = $field['type'] ?? 'text';
  $id       = 'contact-' . $name;
  $label    = $field['label'] ?? $name;
  $required = !empty($field['required']);
  $mark     = $required ? '<span class="is-required">*必須</span>' : '';
?>
                  <tr>
<?php if ($type === 'radio' || $type === 'checkbox') { ?>
                    <th><span id="<?= e($id) ?>-label"><?= e($label) ?><?= $mark ?></span></th>
                    <td>
                      <ul class="form__choices" role="group" aria-labelledby="<?= e($id) ?>-label">
<?php foreach (array_values($field['options'] ?? []) as $i => $option) { ?>
                        <li><label><input type="<?= e($type) ?>" name="<?= e($name) ?>" value="<?= e($option) ?>"<?= $type === 'radio' && $required ? ' required' : '' ?><?= $type === 'radio' && $i === 0 && $required ? ' checked' : '' ?>> <?= e($option) ?></label></li>
<?php } ?>
                      </ul>
                    </td>
<?php } elseif (!empty($field['range_end'])) { ?>
<?php $endName = $field['range_end']; $end = SITE_CONTACT_FIELDS[$endName] ?? []; ?>
                    <th><label for="<?= e($id) ?>"><?= e($field['row_label'] ?? $label) ?><?= $mark ?></label></th>
                    <td>
                      <!-- 開始日を選ぶと, 終了日はそれより前を選べなくなる (js/contact-form.js) -->
                      <div class="form__range" data-date-range>
                        <input id="<?= e($id) ?>" type="date"<?= contact_field_attrs($name, $field) ?> aria-label="<?= e($label) ?>" data-date-start>
                        <span class="form__rangeEnd">
                          <span class="form__rangeSeparator" aria-hidden="true">〜</span>
                          <input id="contact-<?= e($endName) ?>" type="date"<?= contact_field_attrs($endName, $end) ?> aria-label="<?= e($end['label'] ?? $endName) ?>" data-date-end>
                        </span>
                      </div>
                    </td>
<?php } else { ?>
                    <th><label for="<?= e($id) ?>"><?= e($label) ?><?= $mark ?></label></th>
                    <td>
<?php if ($type === 'textarea') { ?>
                      <textarea id="<?= e($id) ?>"<?= contact_field_attrs($name, $field) ?> rows="<?= (int)($field['rows'] ?? 8) ?>"></textarea>
<?php } elseif ($type === 'select') { ?>
<?php $selected = contact_selected_option($field); ?>
                      <select id="<?= e($id) ?>"<?= contact_field_attrs($name, $field) ?>>
<?php foreach ($field['options'] ?? [] as $option) { ?>
                        <option value="<?= e($option) ?>"<?= $option === $selected ? ' selected' : '' ?>><?= e($option) ?></option>
<?php } ?>
                      </select>
<?php } else { ?>
                      <input id="<?= e($id) ?>" type="<?= e($type) ?>"<?= contact_field_attrs($name, $field) ?>>
<?php } ?>
                    </td>
<?php } ?>
                  </tr>
<?php
}

// 入力欄に付ける属性 (name, required, autocomplete, placeholder)
function contact_field_attrs(string $name, array $field): string {
  $attrs = ' name="' . e($name) . '"';
  if (!empty($field['required'])) $attrs .= ' required';
  if (!empty($field['autocomplete'])) $attrs .= ' autocomplete="' . e($field['autocomplete']) . '"';
  if (!empty($field['placeholder'])) $attrs .= ' placeholder="' . e($field['placeholder']) . '"';
  return $attrs;
}

// select の初期値 (query に書いたクエリで, 選択肢のキーを指定できる. 例: /?subject=artist-stay#reserve)
function contact_selected_option(array $field): ?string {
  $options = $field['options'] ?? [];
  $key = !empty($field['query']) ? ($_GET[$field['query']] ?? null) : null;
  return is_string($key) && isset($options[$key]) ? $options[$key] : null;
}

// 確認画面 (値は js/contact-form.js が sessionStorage から入れる)
function render_contact_confirm(): void {
?>
          <div data-contact-confirm data-form-url="<?= e(contact_url()) ?>" data-thanks-url="<?= e(contact_url('thanks/')) ?>" data-send-url="<?= e(path('api/send.php')) ?>">
            <div data-contact-filled hidden>
              <table class="contactPage__table">
<?php foreach (SITE_CONTACT_FIELDS as $name => $field) { ?>
                <tr>
                  <th><?= e($field['label'] ?? $name) ?></th>
                  <td data-contact-value="<?= e($name) ?>"></td>
                </tr>
<?php } ?>
              </table>
              <p class="contactPage__message" data-contact-message role="alert" hidden></p>
              <div class="contactPage__actions">
                <button class="button is-primary is-md" type="button" data-contact-send>送信する</button>
                <button class="button is-tertiary is-md" type="button" data-contact-back>入力画面に戻る</button>
              </div>
            </div>
            <div data-contact-empty hidden>
              <p>入力内容が見つかりませんでした。お手数ですが、もう一度ご入力ください。</p>
              <div class="contactPage__actions">
                <a class="button is-primary is-md" href="<?= e(contact_url()) ?>">入力画面へ</a>
              </div>
            </div>
            <noscript><p>お問い合わせフォームのご利用には JavaScript を有効にしてください。</p></noscript>
          </div>
<?php
}

// 完了画面
function render_contact_thanks(): void {
?>
          <p>お問い合わせありがとうございました。</p>
          <p>ご入力いただいたメールアドレスに、受付確認のメールをお送りしました。内容を確認のうえ、折り返しご連絡いたします。</p>
          <p class="contactPage__note">確認メールが届かない場合は、迷惑メールフォルダをご確認いただくか、お手数ですが再度お問い合わせください。</p>
          <div class="contactPage__actions">
            <a class="button is-primary is-md" href="<?= e(path()) ?>">トップへ戻る</a>
          </div>
<?php
}
