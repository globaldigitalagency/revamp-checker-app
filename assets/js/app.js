import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import '../scss/app.scss';

import hideUnless from './utils/_hideUnless.js';

window.addEventListener('load', function () {
    hideUnless();
});
