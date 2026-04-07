// Don't overwrite WordPress/ACF jQuery in the block editor — they rely on it for
// repeater "Add row", link picker, and other UI. Only expose our jQuery if none exists.
import $ from 'jquery';

if (typeof window.jQuery === 'undefined') {
  window.$ = window.jQuery = $;
}

$(function () {
  // Block editor scripts (use existing jQuery so ACF handlers keep working)
});
