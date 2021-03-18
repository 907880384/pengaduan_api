window._ = require('lodash');
require('dotenv').config();

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.SOCKET_HOST = process.env.MIX_CLIENT_SOCKET_HOST;
window.SOCKET_PORT = process.env.MIX_CLIENT_SOCKET_PORT;

window.moment = require('moment');
window.Swal = require('sweetalert2');

//window.Webcam = require('webcam-easy');