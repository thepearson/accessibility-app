const amqp = require('amqplib/callback_api');
const app = require('./app.js');
const api = require('./api.js');

(() => {

  const results = app.startScan({
    url: "https://education.govt.nz/our-work/our-role-and-our-people"
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


{
  "results": [
      {
          rule_id: 'aria-dialog-name',
          result: 'violations',
          impact: 'serious',
          html: '<div class="header__mega" id="header__mega_66" aria-expanded="false" role="dialog">',
          message: 'Fix any of the following:\n' +
              '  aria-label attribute does not exist or is empty\n' +
              '  aria-labelledby attribute does not exist, references elements that do not exist or references elements that are empty\n' +
              '  Element has no title attribute'
      },
      {
          rule_id: 'aria-dialog-name',
          result: 'violations',
          impact: 'serious',
          html: '<div class="header__mega" id="header__mega_67" aria-expanded="false" role="dialog">',
          message: 'Fix any of the following:\n' +
              '  aria-label attribute does not exist or is empty\n' +
              '  aria-labelledby attribute does not exist, references elements that do not exist or references elements that are empty\n' +
              '  Element has no title attribute'
      },
      {
          rule_id: 'aria-dialog-name',
          result: 'violations',
          impact: 'serious',
          html: '<div class="header__mega" id="header__mega_68" aria-expanded="false" role="dialog">',
          message: 'Fix any of the following:\n' +
              '  aria-label attribute does not exist or is empty\n' +
              '  aria-labelledby attribute does not exist, references elements that do not exist or references elements that are empty\n' +
              '  Element has no title attribute'
      },
      {
          rule_id: 'aria-dialog-name',
          result: 'violations',
          impact: 'serious',
          html: '<div class="header__mega" id="header__mega_138" aria-expanded="false" role="dialog">',
          message: 'Fix any of the following:\n' +
              '  aria-label attribute does not exist or is empty\n' +
              '  aria-labelledby attribute does not exist, references elements that do not exist or references elements that are empty\n' +
              '  Element has no title attribute'
      },
      {
          rule_id: 'heading-order',
          result: 'violations',
          impact: 'moderate',
          html: '<h3>Tā mātou kaupapa – Our purpose</h3>',
          message: 'Fix any of the following:\n  Heading order invalid'
      },
      {
          rule_id: 'landmark-unique',
          result: 'violations',
          impact: 'moderate',
          html: '<nav class="header__menu" id="menu">',
          message: 'Fix any of the following:\n' +
              '  The landmark must have a unique aria-label, aria-labelledby, or title to make landmarks distinguishable'
      },
      {
          rule_id: 'region',
          result: 'violations',
          impact: 'moderate',
          html: '<div class="header__externals">',
          message: 'Fix any of the following:\n' +
              '  Some page content is not contained by landmarks'
      },
      {
          rule_id: 'region',
          result: 'violations',
          impact: 'moderate',
          html: '<span class="breadcrumbs__seperate">/</span>',
          message: 'Fix any of the following:\n' +
              '  Some page content is not contained by landmarks'
      },
      {
          rule_id: 'region',
          result: 'violations',
          impact: 'moderate',
          html: '<span id="breadcrumb-link-text-1" class="breadcrumb__link-text">Our work</span>',
          message: 'Fix any of the following:\n' +
              '  Some page content is not contained by landmarks'
      },
      {
          rule_id: 'region',
          result: 'violations',
          impact: 'moderate',
          html: '<a href="/our-work/our-role-and-our-people/#"><span>Share</span></a>',
          message: 'Fix any of the following:\n' +
              '  Some page content is not contained by landmarks'
      },
      {
          rule_id: 'region',
          result: 'violations',
          impact: 'moderate',
          html: '<li class="print"><a href="/our-work/our-role-and-our-people/#"><span>Print</span></a></li>',
          message: 'Fix any of the following:\n' +
              '  Some page content is not contained by landmarks'
      }

  ]
}
