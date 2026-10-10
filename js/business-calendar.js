/**
 * Business Calendar
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

/**
 * 空き状況カレンダー
 * qwel-tools-business-calendar をもとに, はりいしゃ向けに調整したもの
 * 状態は 0: 予約不可 / 1: 予約可 の2通り. データは API (api/calendar.php) から取得・更新する
 *
 * 使い方:
 * [data-business-calendar] を付けた要素の中に, 次の要素を置く
 *   .calendar__prev / .calendar__next (前月・翌月のボタン), .calendar__current (表示中の年月)
 *   table の thead.calendar__head と tbody.calendar__body
 * 編集する画面では [data-business-calendar="edit"] とする. 日付をクリックすると状態が切り替わり, API に保存される
 *
 * オプション:
 * endpoint: API の URL (規定で ./api/calendar.php)
 * startOnMon: 月曜始まりにする (規定で false)
 * delay: 今日から何日後を予約受付の初日にするか (規定で 0. それより前の日は予約不可)
 * defaultState: 登録のない日の状態 (規定で 1: 予約可)
 * holidaysUrl: 祝日データの URL (規定で holidays-jp. 取得できなければ祝日の色付けを省く)
 */

const STATE_LABELS = ['予約不可', '予約可'];

export default class BusinessCalendar {
  constructor(options = {}) {
    this.elem = options.elem || document.querySelector('[data-business-calendar]');
    if (!this.elem) return;

    this.endpoint = options.endpoint || './api/calendar.php';
    this.startOnMon = !!options.startOnMon;
    this.delay = options.delay || 0;
    this.defaultState = options.defaultState ?? 1;
    this.holidaysUrl = options.holidaysUrl || 'https://holidays-jp.github.io/api/v1/date.json';
    this.editable = this.elem.dataset.businessCalendar === 'edit';

    this.prev = this.elem.querySelector('.calendar__prev');
    this.next = this.elem.querySelector('.calendar__next');
    this.current = this.elem.querySelector('.calendar__current');
    this.head = this.elem.querySelector('.calendar__head');
    this.body = this.elem.querySelector('.calendar__body');
    if (!this.head || !this.body) return;

    this.weeks = this.startOnMon
      ? ['月', '火', '水', '木', '金', '土', '日']
      : ['日', '月', '火', '水', '木', '金', '土'];
    if (this.startOnMon) this.elem.classList.add('is-startOnMon');
    if (this.editable) this.elem.classList.add('is-editMode');

    // 今月から表示する. 今月より前には戻れないようにする
    const today = new Date();
    this.year = this.minYear = today.getFullYear();
    this.month = this.minMonth = today.getMonth();

    this.holidays = this.fetchHolidays();
    this.makeHead();
    this.render();
    this.handleEvents();
  }

  // 'YYYY-MM-DD' (端末の時刻で). 文字列のまま大小比較できる
  static format(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  // 予約受付の初日
  get startDate() {
    const date = new Date();
    date.setDate(date.getDate() + this.delay);
    return BusinessCalendar.format(date);
  }

  async fetchHolidays() {
    try {
      const res = await fetch(this.holidaysUrl);
      return res.ok ? await res.json() : {};
    } catch (e) {
      return {};
    }
  }

  async fetchStatus(year, month) {
    try {
      const res = await fetch(`${this.endpoint}?method=fetch&year=${year}&month=${month + 1}`);
      if (!res.ok) throw new Error(res.status);
      const rows = await res.json();
      return Object.fromEntries(rows.map((row) => [row.date, Number(row.state)]));
    } catch (e) {
      console.error('空き状況を取得できませんでした', e);
      return null;
    }
  }

  handleEvents() {
    this.prev?.addEventListener('click', (event) => {
      event.preventDefault();
      if (this.year === this.minYear && this.month === this.minMonth) return;
      this.month--;
      if (this.month < 0) { this.month = 11; this.year--; }
      this.render();
    });

    this.next?.addEventListener('click', (event) => {
      event.preventDefault();
      this.month++;
      if (this.month > 11) { this.month = 0; this.year++; }
      this.render();
    });

    if (this.editable) {
      this.body.addEventListener('click', (event) => {
        const cell = event.target.closest('[data-date]');
        if (cell) this.toggle(cell);
      });
      this.body.addEventListener('keydown', (event) => {
        const cell = event.target.closest('[data-date]');
        if (cell && (event.key === 'Enter' || event.key === ' ')) {
          event.preventDefault();
          this.toggle(cell);
        }
      });
    }
  }

  makeHead() {
    const tr = document.createElement('tr');
    this.weeks.forEach((week) => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = week;
      tr.appendChild(th);
    });
    this.head.replaceChildren(tr);
  }

  async render() {
    const year = this.year, month = this.month;
    if (this.current) this.current.textContent = `${year}年${month + 1}月`;
    if (this.prev) this.prev.toggleAttribute('aria-disabled', year === this.minYear && month === this.minMonth);

    this.elem.classList.add('is-loading');
    const [holidays, status] = await Promise.all([this.holidays, this.fetchStatus(year, month)]);
    // 読み込み中に月を送った場合は, 古い結果を描かない
    if (year !== this.year || month !== this.month) return;
    this.elem.classList.remove('is-loading');
    this.elem.classList.toggle('is-error', status === null);

    this.makeBody(year, month, holidays, status || {});
  }

  makeBody(year, month, holidays, status) {
    const first = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0).getDate();
    const offset = (first.getDay() + (this.startOnMon ? 6 : 0)) % 7;
    const startDate = this.startDate;
    const rows = [];
    let day = 1 - offset;

    while (day <= lastDay) {
      const tr = document.createElement('tr');
      for (let i = 0; i < 7; i++, day++) {
        const td = document.createElement('td');
        if (day < 1 || day > lastDay) {
          td.className = 'is-empty';
          tr.appendChild(td);
          continue;
        }
        const date = BusinessCalendar.format(new Date(year, month, day));
        // 受付初日より前は予約不可. それ以降は登録があればその状態, なければ既定値
        const isPast = date < startDate;
        const state = isPast ? 0 : (status[date] ?? this.defaultState);

        td.dataset.date = date;
        td.dataset.state = state;
        td.dataset.week = (i + (this.startOnMon ? 1 : 0)) % 7; // 0: 日曜 〜 6: 土曜
        if (isPast) td.classList.add('is-past');
        if (holidays[date]) {
          td.classList.add('is-holiday');
          td.title = holidays[date];
        }
        if (this.editable && !isPast) td.tabIndex = 0;
        td.innerHTML = `<span class="calendar__day">${day}</span><span class="calendar__mark" aria-hidden="true"></span>`;
        this.label(td);
        tr.appendChild(td);
      }
      rows.push(tr);
    }
    this.body.replaceChildren(...rows);
  }

  // 読み上げ用のラベル (例: 10月12日 日曜日 予約可)
  label(td) {
    const [, m, d] = td.dataset.date.split('-').map(Number);
    const week = ['日', '月', '火', '水', '木', '金', '土'][td.dataset.week];
    td.setAttribute('aria-label', `${m}月${d}日 ${week}曜日 ${STATE_LABELS[td.dataset.state]}`);
  }

  async toggle(cell) {
    if (cell.classList.contains('is-past') || cell.classList.contains('is-saving')) return;
    const prevState = Number(cell.dataset.state);
    const state = prevState === 1 ? 0 : 1;

    cell.dataset.state = state;
    cell.classList.add('is-saving');
    this.label(cell);

    try {
      const body = new FormData();
      body.set('date', cell.dataset.date);
      body.set('state', state);
      const res = await fetch(`${this.endpoint}?method=update`, { method: 'POST', body });
      if (!res.ok) throw new Error(res.status);
    } catch (e) {
      // 保存できなければ元に戻す
      console.error('空き状況を保存できませんでした', e);
      cell.dataset.state = prevState;
      this.label(cell);
      cell.classList.add('is-failed');
      setTimeout(() => cell.classList.remove('is-failed'), 1500);
    } finally {
      cell.classList.remove('is-saving');
    }
  }
}
