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
            this.waitForNext();
        },

        updated() {
            this.waitForNext();
        },

        methods: {
            waitForNext() {
                // For youtube embed, cycle after video duration (+5 seconds)
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
