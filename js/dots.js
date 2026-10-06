/**
 * Dots
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

/**
 * 水玉テクスチャ (パンフレットの手描きの水玉 = 木漏れ日のような光だまり) を canvas に描く
 * 点の配置と形は読み込みのたびにランダムに決まり, 動かさずに静止した絵として表示する
 *
 * 使い方:
 * _dots.scss をバンドルした css を読み込み,
 * 水玉を表示したい要素 (position: relative) に [data-dots] 属性を付与して Dots.init() を呼ぶ
 * 描画に成功すると要素に .is-dots-ready が付与される (JSが動かない環境では水玉は表示されない)
 * 色は CSS変数 --dots-color, 全体の透明度は $dots-opacity (_dots.scss) で指定する
 *
 * レイアウト (layout):
 * cloud: 要素の一部 (region) の枠に, 左下→右上の対角線に沿って帯状に散らす (規定)
 * band: 天の川のように, 要素全体に帯状に散らす
 * cluster: SVG (src) の <circle> の配置をそのまま使い, 形だけ木漏れ日の水玉にする
 *
 * パラメータ:
 * すべて Dots.defaults に既定値があり, 次の順に上書きされる
 *   Dots.defaults < new Dots(elem, options) の options < data属性
 * data属性名は, パラメータ名をケバブケースにして data-dots- を付けたもの
 *   例: sizeMax → data-dots-size-max="8"
 * 長さ (px) はルート要素の文字サイズ 16px 基準の値で, モバイルでは rem に合わせて縮む
 */

const TAU = Math.PI * 2;
const DEGREE = Math.PI / 180;

// 標準正規分布の乱数 (Box-Muller法)
function gaussian() {
  const u = 1 - Math.random();
  const v = Math.random();
  return Math.sqrt(-2 * Math.log(u)) * Math.cos(TAU * v);
}

// min〜max の一様乱数
function between(min, max) {
  return min + Math.random() * (max - min);
}

// camelCase → kebab-case
function toKebab(key) {
  return key.replace(/[A-Z]/g, (char) => `-${char.toLowerCase()}`);
}

// 水玉の輪郭を作る (面積が半径1の円とほぼ等しい Path2D. 長軸は x 軸方向)
// パンフレットの手描きの水玉 (木漏れ日のような楕円の光だまり) に近づけるため,
// 楕円をベースに, 卵形の片寄り・ときどき雫のような尖り・筆のかすかなゆらぎを重ねる
function createBlobPath(params) {
  const { wobble, aspectMin, aspectMax, egg, tipChance, roughness } = params;
  // wobble で全体の歪み具合をまとめて強弱できる (0 で正円)
  const aspect = 1 + (between(aspectMin, aspectMax) - 1) * wobble;
  const eggness = between(0, egg) * wobble * (Math.random() < 0.5 ? -1 : 1);
  // 雫の尖りは長軸の端 (0 か π) 付近に出す
  const tip = Math.random() < tipChance ? between(0.12, 0.25) * wobble : 0;
  const tipAngle = (Math.random() < 0.5 ? 0 : Math.PI) + between(-0.4, 0.4);
  const ripples = [3, 4, 5, 6].map((n) => ({
    n,
    amp: between(0, roughness) * wobble,
    phase: Math.random() * TAU
  }));

  const steps = 32;
  const points = Array.from({ length: steps }, (_, i) => {
    const theta = (i / steps) * TAU;
    // 楕円 (x²/a² + y² = 1) の極座標の半径
    let radius = 1 / Math.sqrt((Math.cos(theta) / aspect) ** 2 + Math.sin(theta) ** 2);
    // 卵形: 長軸の片側を太らせる
    radius *= 1 + eggness * Math.cos(theta);
    // 雫: 一点だけ緩やかに尖らせる
    const diff = Math.atan2(Math.sin(theta - tipAngle), Math.cos(theta - tipAngle));
    radius *= 1 + tip * Math.exp(-(diff ** 2) / (2 * 0.2 ** 2));
    // 筆のゆらぎ
    radius *= 1 + ripples.reduce((sum, h) => sum + h.amp * Math.sin(h.n * theta + h.phase), 0);
    // 楕円にしても面積が変わらないよう補正
    radius /= Math.sqrt(aspect);
    return [Math.cos(theta) * radius, Math.sin(theta) * radius];
  });

  // 各点の中点を通る二次ベジェ曲線で, 角のない滑らかな輪郭にする
  const path = new Path2D();
  const mid = (a, b) => [(a[0] + b[0]) / 2, (a[1] + b[1]) / 2];
  const start = mid(points[steps - 1], points[0]);
  path.moveTo(start[0], start[1]);
  points.forEach((point, i) => {
    const end = mid(point, points[(i + 1) % steps]);
    path.quadraticCurveTo(point[0], point[1], end[0], end[1]);
  });
  path.closePath();
  return path;
}

/**
 * レイアウト
 * 点の生成 (createDots), サイズ変更時の計算 (resize), 各点の描画位置 (place) を受け持つ
 * 点の座標は要素や枠に対する割合で持ち, 画面サイズが変わっても同じ配置を保つ
 * 新しいレイアウトは, 同じメソッドを持つクラスを LAYOUTS に追加すれば使える
 */

// 枠 (幅・高さとも -0.5〜0.5 の正規化座標) の中に, 左下→右上の対角線に沿って帯状に点を生成する
// refWidth, refHeight: 点どうしの間隔を測るための基準の枠の大きさ (px)
function createDiagonalBand(dots, refWidth, refHeight) {
  const { spread, thickness, spacing } = dots.params;
  const placed = [];

  return dots.createParticles((r) => {
    for (let attempt = 0; attempt < 30; attempt++) {
      const along = gaussian() * spread;
      const across = gaussian() * thickness;
      // 対角線 (左下→右上) は正規化座標で (1, -1) / √2 の向き
      const x = (along + across) / Math.SQRT2;
      const y = (across - along) / Math.SQRT2;
      // 枠からはみ出す点は引き直す
      if (Math.abs(x) > 0.5 || Math.abs(y) > 0.5) continue;
      // パンフレットのように点どうしが重ならないよう, 近すぎる点は引き直す
      const tooClose = placed.some((other) => {
        const gap = (r + other.r) * spacing;
        return Math.hypot((x - other.x) * refWidth, (y - other.y) * refHeight) < gap;
      });
      if (tooClose) continue;
      placed.push({ x, y, r });
      return { x, y };
    }
    return null; // 置ける場所が見つからない点は使わない
  });
}

// cloud: 要素の一部 (region) の枠に帯状に散らす
class CloudLayout {
  constructor(dots) {
    this.dots = dots;
  }

  createDots() {
    const { region } = this.dots.params;
    return createDiagonalBand(this.dots, 1440 * region, 900 * region);
  }

  resize() {
    const { width, height, params } = this.dots;
    this.boxWidth = width * params.region;
    this.boxHeight = height * params.region;
    // 枠の中心 (x, y は 0〜100 で, 枠が要素の左端・上端〜右端・下端に接する位置)
    this.centerX = this.boxWidth / 2 + (width - this.boxWidth) * (params.x / 100);
    this.centerY = this.boxHeight / 2 + (height - this.boxHeight) * (params.y / 100);
    this.count = this.dots.getCount(width * height);
  }

  place(dot) {
    return {
      x: this.centerX + dot.x * this.boxWidth,
      y: this.centerY + dot.y * this.boxHeight,
      r: dot.r * this.dots.unit
    };
  }
}

// band: 天の川のように, 要素全体に帯状に散らす
class BandLayout {
  constructor(dots) {
    this.dots = dots;
  }

  createDots() {
    return createDiagonalBand(this.dots, 1440, 900);
  }

  resize() {
    const { width, height } = this.dots;
    this.count = this.dots.getCount(width * height);
  }

  place(dot) {
    const { width, height, unit } = this.dots;
    return {
      x: width / 2 + dot.x * width,
      y: height / 2 + dot.y * height,
      r: dot.r * unit
    };
  }
}

// cluster: SVG の <circle> の配置をそのまま使う
class ClusterLayout {
  constructor(dots) {
    this.dots = dots;
  }

  async createDots() {
    const { params } = this.dots;
    const response = await fetch(params.src);
    if (!response.ok) throw new Error(response.status);
    const svg = new DOMParser().parseFromString(await response.text(), 'image/svg+xml').documentElement;

    const viewBox = (svg.getAttribute('viewBox') || '0 0 100 100').split(/[\s,]+/).map(Number);
    this.viewWidth = viewBox[2];
    this.viewHeight = viewBox[3];

    return Array.from(svg.querySelectorAll('circle')).map((circle) => ({
      x: Number(circle.getAttribute('cx')),
      y: Number(circle.getAttribute('cy')),
      r: Number(circle.getAttribute('r')),
      opacity: 1
    }));
  }

  resize() {
    const { width, height, rem, params } = this.dots;
    this.scale = (params.size * rem) / this.viewWidth;
    this.offsetX = (width - this.viewWidth * this.scale) * (params.x / 100);
    this.offsetY = (height - this.viewHeight * this.scale) * (params.y / 100);
    this.count = this.dots.particles.length;
  }

  place(dot) {
    return {
      x: this.offsetX + dot.x * this.scale,
      y: this.offsetY + dot.y * this.scale,
      r: dot.r * this.scale
    };
  }
}

const LAYOUTS = {
  cloud: CloudLayout,
  band: BandLayout,
  cluster: ClusterLayout
};

export default class Dots {
  /**
   * パラメータの既定値
   * デザイン調整はここ (または data属性) の値の変更で行う
   */
  static defaults = {
    // 配置
    layout: 'cloud', // 'cloud' | 'band' | 'cluster'
    x: 0, // 枠 (cloud) や塊 (cluster) の位置 (横) 0〜100 で, 要素の左端〜右端に接する
    y: 100, // 同じく (縦) 0〜100 で, 要素の上端〜下端に接する

    // 点の数 (cloud, band)
    count: 80, // 要素が 1440×900px のときの点の数. 要素の面積に比例して増減する
    countMin: 30, // 小さい画面での最少の点の数

    // 点の大きさと濃さ (cloud, band)
    sizeMin: 4.5, // 最小の半径 (px)
    sizeMax: 9, // 最大の半径 (px)
    sizeBias: 1.5, // 大きさの偏り. 1 で均等, 大きいほど小さい点が多くなる
    opacityMin: 0.75, // 最小の点の不透明度 (最大の点は opacityMax)
    opacityMax: 1,
    spacing: 1.4, // 点どうしの間隔. 2点の半径の和の何倍まで近づけるか. 0 で重なりを許す

    // 帯の形 (cloud, band)
    thickness: 0.11, // 帯の太さ (枠に対する割合. 正規分布の標準偏差)
    spread: 0.3, // 帯の長さ方向の広がり (枠に対する割合. 正規分布の標準偏差)
    region: 0.5, // 枠の大きさ (cloud のみ. 要素の幅・高さに対する割合)

    // 形 (パンフレットの手描きの水玉 = 木漏れ日のような楕円の光だまり)
    wobble: 1, // 歪み具合の全体の強さ 0〜1 (0 で正円). 以下の形の値すべてに掛かる
    aspectMin: 1.08, // 楕円の縦横比 (長径 / 短径)
    aspectMax: 1.35,
    egg: 0.08, // 卵形の片寄り (長軸の片側の太り具合)
    tipChance: 0.15, // 雫のような尖りが出る点の割合 0〜1
    roughness: 0.015, // 筆のかすかなゆらぎ
    tilt: 35, // 楕円の長軸の傾き (度, 右上がりが正). 木漏れ日のように向きを揃える
    tiltJitter: 30, // 傾きのばらつき (度). 各点 tilt ± tiltJitter/2
    shapeCount: 24, // 形のバリエーション数

    // SVG の塊 (cluster)
    src: './assets/dots.svg', // 水玉の座標を読む SVG
    size: 22 // 塊の幅 (rem)
  };

  constructor(elem, options = {}) {
    this.elem = elem;
    if (!this.elem) return;

    this.params = this.readParams(options);
    const Layout = LAYOUTS[this.params.layout];
    if (!Layout) {
      console.warn(`Dots: unknown layout "${this.params.layout}"`);
      return;
    }
    this.layout = new Layout(this);
    this.particles = [];

    // bind
    this.resize = this.resize.bind(this);

    this.init();
  }

  // 既定値 < options < data属性 の順に上書きし, 既定値の型に合わせて変換する
  readParams(options) {
    const params = { ...Dots.defaults, ...options };
    for (const key of Object.keys(Dots.defaults)) {
      const value = this.elem.getAttribute(`data-dots-${toKebab(key)}`);
      if (value === null) continue;
      if (typeof Dots.defaults[key] === 'string') {
        params[key] = value;
        continue;
      }
      const number = Number(value);
      if (value === '' || Number.isNaN(number)) {
        console.warn(`Dots: data-dots-${toKebab(key)}="${value}" is not a number`);
        continue;
      }
      params[key] = number;
    }
    return params;
  }

  async init() {
    try {
      this.particles = (await this.layout.createDots()).map((dot) => ({ ...dot, ...this.createLook() }));
    } catch (error) {
      // 読み込みに失敗した場合は, 水玉を描かない
      console.warn('Dots: failed to create particles', error);
      return;
    }
    if (!this.particles.length) return;

    this.createShapes();
    this.createCanvas();
    this.resize();

    // サイズ変更時は, 同じ配置のまま描き直す
    this.resizeObserver = new ResizeObserver(this.resize);
    this.resizeObserver.observe(this.elem);

    this.elem.classList.add('is-dots-ready');
  }

  // cloud, band 用: 位置を返す関数から, 大きさと濃さを付けた点を生成する
  // 画面が大きい場合にも足りるよう多めに用意し, 描画時に数を絞る
  createParticles(createPosition) {
    const { count, countMin, sizeMin, sizeMax, sizeBias, opacityMin, opacityMax } = this.params;
    const pool = Math.max(countMin, Math.ceil(count * 3));
    const particles = [];
    for (let i = 0; i < pool; i++) {
      const size = Math.random() ** sizeBias; // 0〜1 (大きさの順位)
      const r = sizeMin + size * (sizeMax - sizeMin);
      const position = createPosition(r);
      if (!position) continue; // 置ける場所がなかった点は使わない
      particles.push({
        ...position,
        r,
        opacity: opacityMin + size * (opacityMax - opacityMin)
      });
    }
    return particles;
  }

  // 面積 (px²) に応じた点の数
  getCount(area) {
    const { count, countMin } = this.params;
    const scaled = Math.round((count * area) / (1440 * 900));
    return Math.min(this.particles.length, Math.max(countMin, scaled));
  }

  // 形と向きを1点ずつ決める
  createLook() {
    const { shapeCount, tilt, tiltJitter } = this.params;
    return {
      shape: Math.floor(Math.random() * shapeCount),
      // 画面座標は y が下向きなので, 右上がりは負の角度. 形の向き (0 か π) も混ぜる
      rotation: -(tilt + between(-tiltJitter / 2, tiltJitter / 2)) * DEGREE + (Math.random() < 0.5 ? 0 : Math.PI)
    };
  }

  // 水玉の形を用意する (wobble が 0 なら正円で描くため不要)
  createShapes() {
    const { wobble, shapeCount } = this.params;
    this.shapes = wobble > 0 ? Array.from({ length: shapeCount }, () => createBlobPath(this.params)) : [];
  }

  createCanvas() {
    this.canvas = document.createElement('canvas');
    this.canvas.classList.add('dots');
    this.canvas.setAttribute('aria-hidden', 'true');
    this.elem.prepend(this.canvas);
    this.ctx = this.canvas.getContext('2d');
  }

  resize() {
    const dpr = window.devicePixelRatio || 1;
    this.width = this.elem.clientWidth;
    this.height = this.elem.clientHeight;
    this.canvas.width = Math.round(this.width * dpr);
    this.canvas.height = Math.round(this.height * dpr);
    this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    this.color = getComputedStyle(this.elem).getPropertyValue('--dots-color').trim() || '#6f7888';

    // 長さの単位: rem はルート要素の文字サイズ (モバイルでは流動する), unit は 16px 基準からの倍率
    this.rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
    this.unit = this.rem / 16;

    this.layout.resize();
    this.draw();
  }

  draw() {
    const { ctx } = this;
    ctx.clearRect(0, 0, this.width, this.height);
    ctx.fillStyle = this.color;

    for (let i = 0; i < this.layout.count; i++) {
      const dot = this.particles[i];
      const { x, y, r } = this.layout.place(dot);
      this.fillDot(x, y, r, dot);
    }
    ctx.globalAlpha = 1;
  }

  fillDot(x, y, r, dot) {
    const { ctx } = this;
    ctx.globalAlpha = dot.opacity;

    if (!this.shapes.length) {
      // 正円
      ctx.beginPath();
      ctx.arc(x, y, r, 0, TAU);
      ctx.fill();
      return;
    }

    // 水玉の形 (半径1) を, 位置・向き・大きさを変えて描く
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(dot.rotation);
    ctx.scale(r, r);
    ctx.fill(this.shapes[dot.shape]);
    ctx.restore();
  }

  destroy() {
    this.resizeObserver?.disconnect();
    this.canvas?.remove();
    this.elem?.classList.remove('is-dots-ready');
  }

  // [data-dots] を持つ要素をまとめて初期化する
  static init(selector = '[data-dots]', options = {}) {
    return Array.from(document.querySelectorAll(selector)).map((elem) => new Dots(elem, options));
  }
}
