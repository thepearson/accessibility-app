const axios = require('axios');

const post = async function(url, data, token) {
    return await axios.post(url, data, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });
}

module.exports = {
    post
}
  