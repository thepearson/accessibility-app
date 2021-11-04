const puppeteer = require('puppeteer');
const { AxePuppeteer } = require('@axe-core/puppeteer');
const api = require('./api.js');
const UrlParser = require('url');

const VIEWPORT = { width: 1920, height: 1080, deviceScaleFactor: 2 };
const LOGGING = process.env.LOGGING || 0;

const log = (msg) => {
    if (LOGGING > 0) console.log(msg)
};


function formatRequests(requests, includeRaw = false) {
    const data = {};
    data.totalRequests = requests.length;
    data.requestUrls = requests;
    return data;
  }
  
function formatResponses(responses, includeRaw = false) {
    const data = [];
    for (let response of responses) {

        if (!response.headers.hasOwnProperty('content-type')) {
            continue;
        }

        const packet = {
            uri: (response.url.indexOf('data') === 0) ? 'datauri' : response.url,
            status: response.status,
            size: 0,
        };

        const mime = response.headers['content-type'].split(';')[0];
        packet.mime = mime;
        if (response.headers.hasOwnProperty('content-length')) {
            packet.size += parseInt(response.headers['content-length']);
        }

        data.push(packet);
    }

    return data;
}
  

/**
 *
 * @param {*} page
 * @param {*} url
 * @param {*} callback
 */
const scan = async function (page, url, options, resultsCallback) {
    const requests = [];
    const responses = [];

    page.on("request", (request) => {
        requests.push(request.url());
    });

    page.on("response", (response) => {
        const resp = {
            headers: response.headers(),
            url: response.url(),
            status: response.status(),
        }
        responses.push(resp);
    });

    await page.goto(url, {
        waitUntil: 'networkidle0'
    });
    
    const results = await new AxePuppeteer(page).analyze();
    const metrics = await page.metrics();
    return resultsCallback(url, results, { 
        stats: metrics,
        requests: formatRequests(requests, true),
        responses: formatResponses(responses, true)
    }, options);
}



/**
 * Callback used to process the page response.
 *
 * @param {*} url
 * @param {*} page
 */
const handleScanResults = async function (url, results, metrics, options) {
    var data = [];
    for (const violation of results.violations) {
        const violation_id = violation.id;
        for (const node of violation.nodes) {
            data.push({
                rule_id: violation_id,
                result: "violations",
                impact: node.impact,
                html: node.html,
                message: node.failureSummary
            });
        }
    }

    const data_to_send = {
        accessibility: data,
        metrics: metrics,
    };

    //console.log(JSON.stringify(data_to_send));

    //return;

    log('Scanned ' + url + ', found ' + data.length + ' violations');

    if (options.meta.cli !== true) {
        await api.post(
            `${options.meta.hostname}${options.meta.results}`,
            data_to_send,
            options.meta.token
        );
    }

    return true;
};


/**
 * Finds all anchors on the page, inclusive of those within shadow roots.
 * Note: Intended to be run in the context of the page.
 * @param {boolean=} sameOrigin When true, only considers links from the same origin as the app.
 * @return {!Array<string>} List of anchor hrefs.
 */
const collectAllSameOriginAnchorsDeep = function (sameOrigin = true) {

    const allElements = [];

    const findAllElements = function (nodes) {
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
const crawl = async function (page, url, options, urls, crawledUrls, responseCallback, anchorsCallback = null) {

    // if url is yet to be crawled, lets crawl it.
    if (crawledUrls.indexOf(url) < 0) {

        await page.goto(url, {
            waitUntil: 'networkidle0'
        });

        // Get all the damn links, and add them to the damn list.
        const anchors = await page.evaluate(collectAllSameOriginAnchorsDeep);
        anchorsCallback(anchors, options, urls, crawledUrls);

        for (let link of anchors) {
            if (urls.indexOf(link) < 0) {
                urls.push(link);
            }
        }

        crawledUrls.push(url);

        // Process the URL
        responseCallback(url, page, options, urls, crawledUrls);
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
const handleNewAnchors = async function (anchors, options, urls, crawledUrls) {
    const newUrls = anchors.filter(a => urls.indexOf(a) < 0)
        .map(u => UrlParser.parse(u).path);

    if (newUrls.length < 1) return;

    log('Discovered an additional ' + newUrls.length + ' url/s');
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
const handlePageCrawl = async function (url, page, options, urls, crawledUrls) {
    log('Crawled: ' + url);
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











/**
 * Perform an accessibility scan of a website.
 * 
 * @param {*} options 
 * @returns 
 */
const accessibleScan = async function (options) {
    const startTime = Date.now();

    // Update

    if (options.meta.cli !== true) {
        await api.post(
            `${options.meta.hostname}${options.meta.update}`,
            {
                status: 'processing',
            },
            options.meta.token
        );
    }


    const browser = await puppeteer.launch({
        ignoreHTTPSErrors: true
    });

    const page = await browser.newPage();
    await page.setBypassCSP(true);

    if (VIEWPORT) {
        await page.setViewport(VIEWPORT);
    }

    var data;

    try {
        data = await scan(page, options.url, options, handleScanResults);
    } catch (e) {
        // do nothing
        console.log(e);
    }

    //
    await browser.close();

    // Update
    if (options.meta.cli !== true) {
        await api.post(
            `${options.meta.hostname}${options.meta.update}`,
            {
                status: 'success',
            },
            options.meta.token
        );
    }

    return {
        start_time: startTime,
        end_time: Date.now(),
        data: data
    }
}

/**
 * Performa a crawl of a website for all urls and links
 * 
 * @param {*} options 
 * @returns 
 */
const crawlSite = async function (options) {

    const startTime = Date.now();
    const crawledUrls = [];
    const urls = [];

    // Update
    await api.post(
        `${options.meta.hostname}${options.meta.status}`,
        {
            status: 'processing',
            total: 0,
            complete: 0,
        },
        options.meta.token
    );

    if (options.base_url.charAt(options.base_url.length - 1) == "/") {
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
                await crawl(page, url, options, urls, crawledUrls, handlePageCrawl, handleNewAnchors);
            }
        }
    } catch (e) {
        await browser.close();

        // TODO: Something here
    }

    //
    await browser.close();

    // Update
    await api.post(
        `${options.meta.hostname}${options.meta.status}`,
        {
            status: 'success',
            total: urls.length,
            complete: crawledUrls.length,
        },
        options.meta.token
    );

    return {
        found_urls: urls.length,
        time_taken: (Date.now() - startTime) / 1000
    }
}

module.exports = {
    accessibleScan,
    crawlSite
}