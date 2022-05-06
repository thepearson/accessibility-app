const puppeteer = require('puppeteer');
const lighthouse = require('lighthouse');
const { URL } = require('url');

(async() => {
  const url = 'https://education.govt.nz';
  
  // Use Puppeteer to launch headful Chrome and don't use its default 800x600 viewport.
  const browser = await puppeteer.launch({
    headless: true,
    defaultViewport: {
      width: 1920,
      height: 1080,
      isLandscape: true
    },
  });

  // Lighthouse will open the URL.
  // Puppeteer will observe `targetchanged` and inject our stylesheet.
  const {lhr} = await lighthouse(url, {
    port: (new URL(browser.wsEndpoint())).port,
    output: 'json',
  });

  console.log(`Lighthouse scores: ${Object.values(lhr.categories).map(c => `${c.id}: ${c.score}`).join(', ')}`);
  
  await browser.close();
})();
