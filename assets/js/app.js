import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.min.css';

import '../scss/app.scss';

import hideUnless from './utils/_hideUnless.js';

window.addEventListener('load', function (event) {
    hideUnless();
});
