// (1) Global UI libraries — MUST be first
import $ from "jquery";
window.$ = window.jQuery = $;

import toastr from "toastr";
window.toastr = toastr;

import flatpickr from "flatpickr";
window.flatpickr = flatpickr;

// CSS libraries
import "toastr/build/toastr.min.css";
import "flatpickr/dist/flatpickr.min.css";
import "flatpickr/dist/themes/dark.css";
import "@fortawesome/fontawesome-free/css/all.min.css";

// (2) Initialize Laravel/Livewire/Alpine
import './bootstrap';

// (3) PowerGrid — depends on bootstrap
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';
import './../../vendor/power-components/livewire-powergrid/dist/tailwind.css';

// (4) Your own scripts — depends on everything above
import './pdf-extractor';
