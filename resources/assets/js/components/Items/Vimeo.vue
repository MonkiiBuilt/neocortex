<template>
    <div class="item-iframe"
        :class="{ component__active: active }">
        <iframe :id="vimeoID" :data-src="vimeoUrl" src="" frameborder="0" allowfullscreen>
        </iframe>
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
            vimeoID() {
                if (this.details) {
                    var id = "vid-" + this.index;
                    return id;
                }

                return '';
            },
            vimeoUrl() {
                if (this.details) {
                    var src = "https://player.vimeo.com/video/" + this.details.vid_id + "?autoplay=1&badge=0"
                    return src;
                }

                return '';
            }
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

                // Set the src of the iframe element to start playing
                if (this.details) {
                    var src = $("#vid-"+this.index).attr("data-src");
                    $("#vid-"+this.index).attr("src", src);

                    this.waitForNext();
                }
            },
            waitForNext() {
                if (this.nextTimeout) {
                    window.clearTimeout(this.nextTimeout);
                }

                // For vimeo embed, cycle after video duration (+5 seconds)
                this.nextTimeout = window.setTimeout(this.unload, this.details.duration + 5000);
            },
            unload() {
                $("#vid-"+this.index).attr("src", "");
                this.next();
            }
        }
    }
</script>
