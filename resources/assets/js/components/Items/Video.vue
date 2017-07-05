<template>
    <div class="item-video"
         :class="{ component__active: active }">
        <video loop>
<!--            <source :src="webmUrl" type="video/webm">-->
            <source :src="mp4Url" type="video/mp4">
        </video>
    </div>
</template>

<style lang="sass">
    .item-video {
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        height: 100%;

        video {
            width: 100%;
            height: 100%;
        }
    }
</style>

<script>
    import BaseItem from './BaseItem.vue';

    export default {
        // Inherit props and basic functionality from BaseItem.vue
        extends: BaseItem,

        data() {
            return {
                video: null,
                duration: null,
            };
        },

        computed: {
            webmUrl() {
                if (this.details) {
                    return this.details.url.replace('gifv','webm');
                }

                return '';
            },
            mp4Url() {
                if (this.details) {
                    return this.details.url.replace('gifv','mp4');
                }

                return '';
            },
        },

        mounted() {
            // Cache a reference to the video element
            this.video = this.$el.querySelector('video');

            this.video.addEventListener('loadedmetadata', () => {
                // Once the video is loaded, we'll know its duration
                this.duration = this.video.duration;

                // Wait until we know the duration to start the timer for
                // proceeding to the next item
                if (this.active) {
                    this.becameActive();
                }
            });
        },

        methods: {
            becameActive() {
                console.log('Video becameActive');
                // Start playing the video when we're ready
                this.video.play();

                // If the item has a set amount of time it should display, or
                // can set a timeout to transition to the next item, it can do
                // that here
                if (this.itemNextTimeout) {
                    window.clearTimeout(this.itemNextTimeout);
                }
                // Play the video through twice
                this.itemNextTimeout = window.setTimeout(this.unload, this.duration * 1000 * 2);
                console.log('Video timeout', this.itemNextTimeout, this.duration * 1000 * 2, Date.now());
            },
            unload() {
                // Stop the video before proceeding to the next Item
                this.video.pause();
                this.video.currentTime = 0;

                // Let the parent know this Item is done displaying
                this.done();
            },
        }
    }
</script>
