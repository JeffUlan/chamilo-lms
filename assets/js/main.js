var moment = require('moment');
require("moment/min/locales.min");
global.moment = moment;

const $ = require('jquery');
// create global $ and jQuery variables
window.jQuery = window.$ = global.$ = global.jQuery = $;

// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap-sass');
require('readmore-js');
require('chosen-js');
require('webpack-jquery-ui');
require('webpack-jquery-ui/css');
require('font-awesome-webpack');
require('ckeditor');
require('mediaelement');
require('js-cookie');
require('fullcalendar');
require('qtip2');
require('image-map-resizer')




/*

require('jquery-ui-timepicker-addon');
require('chosen-js');*/
//require('bootstrap-daterangepicker');
