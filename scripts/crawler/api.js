const axios = require('axios');

const post = async function(url, data, token) {
    return await axios.post(url, data, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    }).catch((e) => {
        console.log(e);
    });
}

module.exports = {
    post
}
  