<template>
    <div class="item item__full item-image"
         :class="{ item__active: active }"
         :style="{ backgroundImage: 'url(' + imageUrl + ')' }">
    </div>
</template>

<script>
    export default {
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
            // This can be used by an item to trigger a transition to the
            // next Item in the ItemCollection
            next: {
                type: Function,
                required: true
            }
        },

        computed: {
            imageUrl() {
                if (this.item.attributes.details) {
                    return this.item.attributes.details.url;
                }
                return '';
            },
        },

        mounted() {
            this.waitForNext();
        },

        updated() {
            this.waitForNext();
        },

        methods: {
            waitForNext() {
                // For a basic image, cycle after 10 seconds
                if (this.active) {
                    window.setTimeout(this.next, 10000)
                }
            }
        }
}
</script>
