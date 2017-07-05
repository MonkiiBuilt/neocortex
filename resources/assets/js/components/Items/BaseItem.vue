<template>
    <div class="item-unknown"
        :class="{ component__active: active }">
    </div>
</template>

<script>
    export default {
        data () {
            return {
                // Slide transition time (in ms)
                itemActiveTime: 10000,
                // Keep track of a single timeout for rotating the display
                itemNextTimeout: null,
            };
        },
        // These are the props that all Item components receive
        props: {
            item: {
                type: Object,
                required: true
            },
            index: Number,
            active: {
                type: Boolean,
                default: false
            },
        },

        computed: {
            // Proxy for item.details to make templates cleaner
            details: function () {
                return this.item.details;
            },
        },

        // Watch for changes to this Item's active state, if it becomes active
        // then "start" the Item
        watch: {
            active: function (isActive) {
                if (isActive) {
                    this.becameActive();
                }
            },
        },

        methods: {
            becameActive () {
                console.log('BaseItem becameActive');
                // When an item needs to "start" when it becomes active, like
                // starting a video, this is where to do it

                // If the item has a set amount of time it should display, or
                // can set a timeout to transition to the next item, it can do
                // that here
                if (this.itemNextTimeout) {
                    window.clearTimeout(this.itemNextTimeout);
                }
                this.itemNextTimeout = window.setTimeout(this.done, this.itemActiveTime);
            },
            done () {
                // If a leftover timeout is active, clear it
                window.clearTimeout(this.itemNextTimeout);

                // Let the parent component know this Item is "done" displaying
                this.$emit('done', this.item.id, this.details);
            }
        }
    }
</script>

<style lang="sass">


</style>