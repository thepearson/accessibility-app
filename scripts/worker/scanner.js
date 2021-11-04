const amqp = require('amqplib/callback_api');
const puppeteer = require('puppeteer');
const app = require('./lib/app.js');
const api = require('./lib/api.js');

(() => {
  amqp.connect('amqp://localhost', (err, conn) => {

    conn.createChannel((err, ch) => {
      var q = 'scan';
      ch.prefetch(2);
      ch.assertQueue(q, { durable: true });

      ch.consume(q, async (msg) => {
        try {
          const packet = JSON.parse(msg.content);
          const results = await app.accessibleScan(packet);
          console.log(results);
          ch.ack(msg);
        } catch(e) {
          console.log(e);
          ch.ack(msg);
        }

      }, { noAck: false });
    });
  });
})()
