const puppeteer = require('puppeteer');
const app = require('./lib/app.js');
const api = require('./lib/api.js');

(async () => {
    const packet = {
        url: "https://applications.education.govt.nz/"
    }

    const results = await app.crawlSite(packet);
})()
