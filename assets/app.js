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
const $=require('jquery');
require('bootstrap');

//////////////////// DATATABLE ////////////////////
import 'datatables.net';

//////////////////// NAVBAR ////////////////////
$('.expandHome').mouseover(function() {
    $('.sub-home').css({
        'display': 'block'
    });
});
$('.subnavbtn').mouseover(function() {
    $('.sub-home').css({
        'display': 'none'
    });
});
$('#trapezoid').mouseleave(function() {
    $('#trapezoid').css({
        'margin-top': '-53px'
    });
    $('.sub-home').css({
        'display': 'none'
    });
}).mouseenter(function() {
    $('#trapezoid').css({
        'margin-top': '0px'
    });
});

