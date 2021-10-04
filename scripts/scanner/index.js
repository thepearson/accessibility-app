const amqp = require('amqplib/callback_api');
const app = require('./app.js');
const api = require('./api.js');

(() => {

  const results = app.startScan({
    url: "https://education.govt.nz"
  });

  // amqp.connect('amqp://localhost', (err, conn) => {

  //   conn.createChannel((err, ch) => {
  //     var q = 'scan';
  //     ch.prefetch(2);
  //     ch.assertQueue(q, {durable: true});

  //     ch.consume(q, async (msg) => {

  //       const packet = JSON.parse(msg.content);

  //       console.log(packet);

  //       // {
  //       //   url: 'https://designsystem.education.govt.nz/rss.xml',
  //       //   meta: {
  //       //     token: '13amEwxhyGE2T20TRtznk6P5uml7pg68ePrkEBhZROefa2IrrmOfAxuGK1vft4fL',
  //       //     hostname: 'http://localhost',
  //       //     update: '/api/url_scan/update'
  //       //   }
  //       // }
        
  //       const results = await app.startScan(packet);
      
  //       ch.ack(msg);

  //       console.log(results);

  //     }, {noAck: false});
  //   });
  // });
})()
