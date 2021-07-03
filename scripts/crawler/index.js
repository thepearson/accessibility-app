//require('dotenv').config();
const puppeteer = require('puppeteer');
const { AxePuppeteer } = require('@axe-core/puppeteer');

const VIEWPORT = {width: 1920, height: 1080, deviceScaleFactor: 2};
const BASE_URL = process.env.BASE_URL || 'https://www.pixelite.co.nz/';

const data = [];
const crawledUrls = [];
const urls = [];


/**
 * Finds all anchors on the page, inclusive of those within shadow roots.
 * Note: Intended to be run in the context of the page.
 * @param {boolean=} sameOrigin When true, only considers links from the same origin as the app.
 * @return {!Array<string>} List of anchor hrefs.
 */
function collectAllSameOriginAnchorsDeep(sameOrigin = true) {

  const allElements = [];

  const findAllElements = function(nodes) {
    for (let i = 0, el; el = nodes[i]; ++i) {
      allElements.push(el);
      // If the element has a shadow root, dig deeper.
      if (el.shadowRoot) {
        findAllElements(el.shadowRoot.querySelectorAll('*'));
      }
    }
  };

  findAllElements(document.querySelectorAll('*'));

  const filtered = allElements
    .filter(el => el.localName === 'a' && el.href && el.href.indexOf('#') < 0) // element is an anchor with an href.
    .filter(el => el.href !== location.href) // link doesn't point to page's own URL.
    .filter(el => {
      if (sameOrigin) {
        return new URL(location).origin === new URL(el.href).origin;
      }
      return true;
    })
    .map(a => a.href);

  return Array.from(new Set(filtered));
}



/**
 *
 * @param {*} page
 * @param {*} url
 * @param {*} callback
 */
async function crawl(page, url, callback) {

  // if url is yet to be crawled, lets crawl it.
  if (crawledUrls.indexOf(url) < 0) {

    await page.goto(url, {
      waitUntil: 'networkidle0'
    });

    const results = await new AxePuppeteer(page).analyze();
    console.log(JSON.stringify(results.violations, undefined, 2));

    // Get all the damn links, and add them to the damn list.
    const anchors = await page.evaluate(collectAllSameOriginAnchorsDeep);
    for (let link of anchors) {
      if (urls.indexOf(link) < 0) {
        urls.push(link);
      }
    }

    // Process the URL
    callback(url, page);

    crawledUrls.push(url);

    return true;
  }
  return true;
}


/**
 * Callback used to process the page response.
 *
 * @param {*} url
 * @param {*} page
 */
const handleResponse = async function(url, page) {
  console.log(url);
};


(async () => {
  const browser = await puppeteer.launch({
    ignoreHTTPSErrors: true
  });

  const page = await browser.newPage();
  await page.setBypassCSP(true);

  if (VIEWPORT) {
    await page.setViewport(VIEWPORT);
  }

  try {
    // Init the first url
    urls.push(BASE_URL);

    while (urls.length > crawledUrls.length) {
      // Just keep iterating until it's done, this will skip already crawled urls.
      for (const url of urls) {
        await crawl(page, url, handleResponse);
      }
    }
  } catch (e) {
    // do nothing
  }

  //
  await browser.close();
})();