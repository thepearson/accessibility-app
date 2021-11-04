const amqp = require('amqplib/callback_api');
const puppeteer = require('puppeteer');
const app = require('./app.js');
const api = require('./api.js');

(() => {
  amqp.connect('amqp://localhost', (err, conn) => {

    conn.createChannel((err, ch) => {
      var q = 'crawl';
      ch.prefetch(2);
      ch.assertQueue(q, {durable: true});

      ch.consume(q, async (msg) => {

        const packet = JSON.parse(msg.content);

        // Update
        await api.post(
            `${packet.meta.hostname}${packet.meta.status}`,
            {
                status: 'processing',
                total: 0,
                complete: 0,
            },
            packet.meta.token
        );

        const results = await app.startCrawl(packet);
      
        // Update
        await api.post(
            `${packet.meta.hostname}${packet.meta.status}`,
            {
                status: 'success',
                total: results.total,
                complete: results.complete,
            },
            packet.meta.token
        );

        ch.ack(msg);

      }, {noAck: false});
    });
  });
})()
