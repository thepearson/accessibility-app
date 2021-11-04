const puppeteer = require('puppeteer');
const { AxePuppeteer } = require('@axe-core/puppeteer');
const api = require('./api.js');

const VIEWPORT = {width: 1920, height: 1080, deviceScaleFactor: 2};
const crawledUrls = [];
const urls = [];


/**
 *
 * @param {*} page
 * @param {*} url
 * @param {*} callback
 */
const scan = async function(page, url, options, resultsCallback) {

  await page.goto(url, {
    waitUntil: 'networkidle0'
  });

  const results = await new AxePuppeteer(page).analyze();
  resultsCallback(results, options);
  return true;
}

/**
 * Callback used to process the page response.
 *
 * @param {*} url
 * @param {*} page
 */
const handleResults = async function(results, options) {
  var data = [];
  for (const violation of results.violations) {
    const violation_id = violation.id;
    for (const node of violation.nodes) {
      data.push({
        rule_id: violation_id,
        result: "violations",
        impact: node.impact,
        html: node.html,
        //target: node.target,
        message: node.failureSummary
      });
    }
  }

  await api.post(
    `${options.meta.hostname}${options.meta.results}`,
    {
      results: data
    },
    options.meta.token
  );


  // TODO: Add the other results/.
};


const accessibleScan = async function(options) {
  const startTime = Date.now();

  const browser = await puppeteer.launch({
    ignoreHTTPSErrors: true
  });

  const page = await browser.newPage();
  await page.setBypassCSP(true);

  if (VIEWPORT) {
    await page.setViewport(VIEWPORT);
  }

  try {
    await scan(page, options.url, options, handleResults);
  } catch (e) {
    // do nothing
  }

  //
  await browser.close();

  return {
      start_time: startTime,
      end_time: Date.now()
  }
}


module.exports = {
  accessibleScan
}