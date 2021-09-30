const puppeteer = require('puppeteer');
const { AxePuppeteer } = require('@axe-core/puppeteer');
const api = require('./api.js');

const VIEWPORT = {width: 1920, height: 1080, deviceScaleFactor: 2};
const crawledUrls = [];
const urls = [];


/**
 * Finds all anchors on the page, inclusive of those within shadow roots.
 * Note: Intended to be run in the context of the page.
 * @param {boolean=} sameOrigin When true, only considers links from the same origin as the app.
 * @return {!Array<string>} List of anchor hrefs.
 */
const collectAllSameOriginAnchorsDeep = function(sameOrigin = true) {

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
const crawl = async function(page, url, options, responseCallback, anchorsCallback = null) {

  // if url is yet to be crawled, lets crawl it.
  if (crawledUrls.indexOf(url) < 0) {

    await page.goto(url, {
      waitUntil: 'networkidle0'
    });

    // Get all the damn links, and add them to the damn list.
    const anchors = await page.evaluate(collectAllSameOriginAnchorsDeep);
    anchorsCallback(anchors, options);
    
    for (let link of anchors) {
      if (urls.indexOf(link) < 0) {
        urls.push(link);
      }
    }

    crawledUrls.push(url);

    // Process the URL
    responseCallback(url, page, options);
    return true;
  }
  return true;
}


/**
 * Callback function for when we find anchors on a page.
 * 
 * @param {*} anchors 
 * @param {*} options 
 * @returns 
 */
const handleAnchors = async function(anchors, options) {
  const newUrls = anchors.filter(a => urls.indexOf(a) < 0)
    .map(u => u.replace(options.base_url, ""));

  if (newUrls.length < 1) return;
  
  const payload = { urls: newUrls }
  await api.post(
    `${options.meta.hostname}${options.meta.data}`,
    payload,
    options.meta.token
  );
}

/**
 * Callback used to process the page response.
 *
 * @param {*} url
 * @param {*} page
 */
const handleResponse = async function(url, page, options) {
  const payload = {
    status: 'processing',
    total: urls.length,
    complete: crawledUrls.length,
  }

  await api.post(
    `${options.meta.hostname}${options.meta.status}`,
    payload,
    options.meta.token
  );
};

const startCrawl = async function(options) {

  const startTime = Date.now();

  if(options.base_url.charAt( options.base_url.length-1 ) == "/") {
    options.base_url = options.base_url.slice(0, -1)
  }

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
    urls.push(options.base_url);

    while (urls.length > crawledUrls.length) {
      // Just keep iterating until it's done, this will skip already crawled urls.
      for (const url of urls) {
        await crawl(page, url, options, handleResponse, handleAnchors);
      }
    }
  } catch (e) {
    // do nothing
  }

  //
  await browser.close();

  return {
      start_time: startTime,
      total: urls.length,
      complete: crawledUrls.length,
      end_time: Date.now()
  }
}


module.exports = {
  startCrawl
}