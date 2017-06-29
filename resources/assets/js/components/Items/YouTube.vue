<template>
    <div class="item-iframe"
        :class="{ component__active: active }">
        <iframe :id="youtubeID" :data-src="youtubeUrl" src="" frameborder="0" allowfullscreen>
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
            youtubeID() {
                if (this.details) {
                    var id = "vid-" + this.index;
                    return id;
                }

                return '';
            },
            youtubeUrl() {
                if (this.details) {
                    var src = "http://www.youtube.com/embed/" + this.details.vid_id + "?rel=0&hd=1&autoplay=1";
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

                // For youtue embed, cycle after video duration (+5 seconds)
                this.nextTimeout = window.setTimeout(this.unload, this.details.duration + 5000);
            },
            unload() {
                $("#vid-"+this.index).attr("src", "");
                this.next();
            }
        }
    }
</script>
