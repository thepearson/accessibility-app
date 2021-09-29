//require('dotenv').config();
const puppeteer = require('puppeteer');
const { AxePuppeteer } = require('@axe-core/puppeteer');

const VIEWPORT = {width: 1920, height: 1080, deviceScaleFactor: 2};

const data = [];
const crawledUrls = [];
const urls = [];

const axios = require('axios');


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
async function crawl(page, url, options, responseCallback, anchorsCallback = null) {

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


const handleAnchors = async function(anchors, options) {

  const newUrls = anchors.filter(a => urls.indexOf(a) < 0)
    .map(u => u.replace(options.base_url, ""));

  if (newUrls.length < 1) {
    return;
  }

  const payload = {
    urls: newUrls
  }

  const axiosOptions = {
    headers: {
      'Authorization': `Bearer ${options.meta.token}`
    }
  }
  
  //console.log(payload, axiosOptions)
  axios.post(
    `${options.meta.hostname}${options.meta.data}`, 
    payload, 
    axiosOptions)
  .then(response => {
    console.log(response);
  }).catch(e => {
    console.log(e);
  });
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

  const axiosOptions = {
    headers: {
      'Authorization': `Bearer ${options.meta.token}`
    }
  }

  axios.post(
    `${options.meta.hostname}${options.meta.status}`, 
    payload, 
    axiosOptions)
  .then(response => {
    console.log(response);
  }).catch(e => {
    console.log(e);
  });
};


(async () => {

  // test data
  const options = {
    base_url: 'https://www.education.govt.nz/',
    recursive: true,
    meta: {
      token: "dhNduqxrBYLLQ9evXRaqPYHavy4D4Y405pxpDjNKemqOm0nTFzh0YWFL6yfEjQ0u",
      hostname: "http://localhost",
      status: "/api/crawl/update",
      data: "/api/sites/1/urls"
    }
  }

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
})();