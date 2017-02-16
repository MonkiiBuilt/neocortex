<template>
    <div class="item-image"
         :style="{ backgroundImage: 'url(' + imageUrl + ')' }">
    </div>
</template>

<script>
    export default {
        props: {
            details: {
                type: Object,
                required: true
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
                if (this.details) {
                    return this.details.url;
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
