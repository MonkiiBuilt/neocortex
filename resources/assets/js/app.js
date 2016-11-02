
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

Vue.http.options.root = '/api/v1';

Vue.component('item', require('./components/Item.vue'));
Vue.component('item-collection', require('./components/ItemCollection.vue'));

const app = new Vue({
    el: '#main',

    data: {
        user: [],
        items: []
    },

    template: '<div id="main"><item-collection></item-collection></div>',


    ready () {
    },

    methods: {
    }
});




// (function($) {
//     $(function() {
//
//         // Non-vue code here
//         var $items = $('.item'),
//             $activeItem = $('.item.item__active');
//
//         // Nothing to do
//         if ($items.length == 0) {
//             return;
//         }
//
//         // If none is marked active, start with the first
//         if ($activeItem.length == 0) {
//             $activeItem = $('.item:first').addClass('item__active')
//         }
//         cycleActiveItem();
//     });
// })(jQuery);
//
// function cycleActiveItem() {
//     var $activeItem = $('.item.item__active');
//     $activeItem.removeClass('item__active');
//     if ($activeItem.next().length > 0) {
//         console.log($activeItem.next());
//         $activeItem.next().addClass('item__active');
//     } else {
//         $activeItem.prevAll().last().addClass('item__active');
//     }
//
//     setTimeout(cycleActiveItem, 5000);
// }