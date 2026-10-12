// Action Core
import ActionCore from './js/action-core.js';

// Fader
import Fader from './js/fader.js';

// Slider
import Slider from './js/slider.js';

// Dots
import Dots from './js/dots.js';

// Contact Form
import ContactForm from './js/contact-form.js';

// Business Calendar (空き状況)
import BusinessCalendar from './js/business-calendar.js';

// type="module" のスクリプトは HTML の解析後に実行されるため, DOM を待たずに初期化できる
new ActionCore.Preset();
new Fader();
new Slider();
Dots.init();
new ContactForm({ formUrl: './#reserve' });
new BusinessCalendar();
