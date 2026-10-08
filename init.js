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

/**
 * Auto Copyright
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */
class AutoCopyright {
  constructor(startYear, companyName, elem) {
    elem ||= document.querySelector('.footer__copyright');
    if (elem) elem.innerHTML = this.generate(startYear, companyName);
  }

  generate(startYear, companyName) {
    const currentYear = new Date().getFullYear();
    // 開始年と同じ年は範囲表記にしない
    const years = startYear < currentYear ? `${startYear} - ${currentYear}` : `${currentYear}`;
    return `&copy; ${years} ${companyName}`;
  }
}

// type="module" のスクリプトは HTML の解析後に実行されるため, DOM を待たずに初期化できる
new ActionCore.Preset();
new Fader();
new Slider();
Dots.init();
new ContactForm({ formUrl: './#reserve' });
new AutoCopyright(2020, '福井市越前海岸盛り上げ隊');
