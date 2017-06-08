<template>
    <div class="item-video"
         :class="{ component__active: active }">
        <video>
<!--            <source :src="webmUrl" type="video/webm">-->
            <source :src="mp4Url" type="video/mp4">
        </video>
    </div>
</template>

<style lang="sass">
    .item-video {
        video {
            width: 100%;
            height: 100%;
        }
    }
</style>

<script>
    export default {
        data() {
            return {
                video: null,
                duration: null,
            };
        },
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
                this.becameActive();
            });
        },

        updated() {
            this.becameActive();
        },

        methods: {
            becameActive() {
                // Start playing the video when we're ready
                this.video.play();

                this.waitForNext();
            },
            waitForNext() {
                // For a basic image, cycle after 10 seconds
                if (this.active) {
                    window.setTimeout(this.next, this.duration * 1000)
                }
            }
        }
    }
</script>
