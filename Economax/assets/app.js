/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

import 'bootstrap';

const alert = document.querySelectorAll('.alert');

if (alert) {
    Array.from(alert).forEach(element => {
        setTimeout(() => {
            element.classList.add('out');

            setTimeout(() => {
                element.remove();
            }, 1000);
        }, 5000);
    });
}