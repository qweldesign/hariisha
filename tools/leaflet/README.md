# はりいしゃ 三つ折りパンフレット (A4 巻き三つ折り)

- `index.html` をブラウザで開くと, ガイド (仕上がり線: 赤 / 折り線: 青破線 / 安全域: 青点線) 付きで確認できる
- `index.html?print` でガイドなし表示

---

## 寸法

用紙は塗り足し込み 303 x 216mm (A4 横 297 x 210mm + 塗り足し各3mm)。

| 面 | 左 | 中央 | 右 |
| --- | --- | --- | --- |
| 外面 | 折り込み面 97mm | 裏表紙 100mm | 表紙 100mm |
| 中面 | ギャラリー 100mm (表紙の裏) | 100mm | 折り込み面 97mm (宿泊・アクセス) |

---

## 書き出し

```
npm i playwright
npx playwright install chromium
node export.js 600   # 解像度 (dpi). 省略時は 350
```

`out/outside.png`, `out/inside.png` (塗り足し込み) と `out/brochure.pdf` を出力する

---

## 制作者 | Author

[QWEL.DESIGN](https://qwel.design)  
福井を拠点に活動するフロントエンド開発者  
Front-end developer based in Fukui, Japan  
