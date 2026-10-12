// 書き出し: node export.js [dpi]
// out/outside.png, out/inside.png (塗り足し込み 303x216mm) と out/brochure.pdf を出力する
const path = require('path');
const fs = require('fs');
const { chromium } = require('playwright');
(async () => {
  const dpi = parseInt(process.argv[2] || '350', 10);
  const scale = dpi / 96;
  const out = path.join(__dirname, 'out');
  fs.mkdirSync(out, { recursive: true });
  const browser = await chromium.launch(process.env.CHROMIUM ? { executablePath: process.env.CHROMIUM } : {});
  const page = await browser.newPage({ viewport: { width: 1200, height: 900 }, deviceScaleFactor: scale });
  await page.goto('file://' + path.join(__dirname, 'index.html') + '?print');
  await page.evaluate(() => document.fonts.ready);
  // 水玉の配置 (フォント読み込み後に行う) を待つ
  await page.waitForFunction(() => document.documentElement.dataset.dotsReady === 'true');
  await page.waitForTimeout(300);
  // 1面ずつ表示して書き出す (隣の面が端に写り込まないように)
  const names = ['outside', 'inside'];
  const count = await page.$$eval('.sheet', (els) => els.length);
  for (let i = 0; i < count; i++) {
    await page.$$eval('.sheet', (els, i) => els.forEach((el, j) => { el.style.display = i === j ? '' : 'none'; }), i);
    const sheet = (await page.$$('.sheet'))[i];
    const box = await sheet.boundingBox();
    // 用紙の外 (端数の1px) が写らないよう, 端数を切り捨てて切り抜く
    const clip = {
      x: Math.ceil(box.x * scale) / scale,
      y: Math.ceil(box.y * scale) / scale,
      width: Math.floor(box.width * scale) / scale,
      height: Math.floor(box.height * scale) / scale,
    };
    await page.screenshot({ path: path.join(out, names[i] + '.png'), clip });
  }
  await page.$$eval('.sheet', (els) => els.forEach((el) => { el.style.display = ''; }));
  await page.pdf({ path: path.join(out, 'brochure.pdf'), width: '303mm', height: '216mm', printBackground: true });
  await browser.close();
  console.log('done', dpi + 'dpi');
})();
