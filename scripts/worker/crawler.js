const amqp = require('amqplib/callback_api');
const puppeteer = require('puppeteer');
const app = require('./lib/app.js');
const api = require('./lib/api.js');

(() => {
  amqp.connect('amqp://localhost', (err, conn) => {

    conn.createChannel((err, ch) => {
      var q = 'crawl';
      ch.prefetch(2);
      ch.assertQueue(q, { durable: true });

      ch.consume(q, async (msg) => {
        ch.ack(msg);
        const packet = JSON.parse(msg.content);
        const results = await app.crawlSite(packet);
        console.log(results);
      }, { noAck: false });
    });
  });
})()
