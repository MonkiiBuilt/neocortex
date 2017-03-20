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
            this.waitForNext();
        },

        updated() {
            this.waitForNext();
        },

        methods: {
            waitForNext() {
                // For vimeo embed, cycle after video duration (+5 seconds)
                if (this.active) {
                    if (this.details) {
                        var src = $("#vid-"+this.index).attr("data-src");
                        $("#vid-"+this.index).attr("src", src);

                        // Extra time for loading process
                        window.setTimeout(this.unload, this.details.duration + 5000);
                    }
                    else {
                        window.setTimeout(this.next, 1000);
                    }
                }
            },
            unload() {
                $("#vid-"+this.index).attr("src", "");
                this.next();
            }
        }
    }
</script>
