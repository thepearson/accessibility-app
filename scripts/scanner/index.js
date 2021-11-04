const amqp = require('amqplib/callback_api');
const puppeteer = require('puppeteer');
const app = require('./app.js');
const api = require('./api.js');

(() => {
  amqp.connect('amqp://localhost', (err, conn) => {

    conn.createChannel((err, ch) => {
      var q = 'scan';
      ch.prefetch(2);
      ch.assertQueue(q, {durable: true});

      ch.consume(q, async (msg) => {

        const packet = JSON.parse(msg.content);

        // Update
        await api.post(
            `${packet.meta.hostname}${packet.meta.update}`,
            {
                status: 'processing',
            },
            packet.meta.token
        );
        
        const results = await app.startScan(packet);

        // Update
        await api.post(
            `${packet.meta.hostname}${packet.meta.update}`,
            {
                status: 'success',
            },
            packet.meta.token
        );

        ch.ack(msg);

      }, {noAck: false});
    });
  });
})()
