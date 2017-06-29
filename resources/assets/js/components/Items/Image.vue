<template>
    <div class="item-image"
        :class="{ component__active: active }"
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
                if (this.details) {
                    return this.details.url;
                }

                return '';
            },
        },

        mounted() {
            this.startIfActive();
        },

        updated() {
            this.startIfActive();
        },

        methods: {
            startIfActive() {
                if (!this.active) {
                    return;
                }

                this.waitForNext();
            },
            waitForNext() {
                if (this.nextTimeout) {
                    window.clearTimeout(this.nextTimeout);
                }

                // For a basic image, cycle after 10 seconds
                this.nextTimeout = window.setTimeout(this.next, 10000)
            }
        }
    }
</script>

<style lang="sass">

.item-image {
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}

</style>