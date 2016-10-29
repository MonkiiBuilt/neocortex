
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: '#app'
});

(function($) {
    $(function() {

        // Non-vue code here
        var $items = $('.item'),
            $activeItem = $('.item.item--active');

        // Nothing to do
        if ($items.length == 0) {
            return;
        }

        // If none is marked active, start with the first
        if ($activeItem.length == 0) {
            $activeItem = $('.item:first').addClass('item--active')
        }


    });
})(jQuery);

