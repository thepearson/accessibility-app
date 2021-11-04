const amqp = require('amqplib/callback_api');
const puppeteer = require('puppeteer');
const app = require('./lib/app.js');
const api = require('./lib/api.js');

(async () => {
    const packet = {
        url: "https://applications.education.govt.nz/education-sector-logon-esl",
        meta: {
            cli: true
        }
    }
    const results = await app.accessibleScan(packet);
    //console.log(results);
})()
