// Action Core
import ActionCore from './js/action-core.js';

// Fader
import Fader from './js/fader.js';

// Slider
import Slider from './js/slider.js';

// Dots
import Dots from './js/dots.js';

// Contact Form (トップページの入力画面と /contact/confirm/ で動く. 画面の URL は config.php の SITE_CONTACT)
import ContactForm from './js/contact-form.js';

// type="module" のスクリプトは HTML の解析後に実行されるため, DOM を待たずに初期化できる
new ActionCore.Preset();
new Fader();
new Slider();
Dots.init();
new ContactForm();
