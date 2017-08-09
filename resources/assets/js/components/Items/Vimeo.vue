<template>
    <div class="item-iframe"
        :class="{ component__active: active }">
        <iframe :src="vimeoUrl" frameborder="0" allowfullscreen></iframe>
    </div>
</template>

<script>
    import BaseItem from './BaseItem.vue';

    export default {
        // Inherit props and basic functionality from BaseItem.vue
        extends: BaseItem,

        computed: {
            vimeoUrl() {
                // Making this contingent on this.active means the iframe
                // src will be set to '' when this slide isn't active,
                // stopping the video
                if (this.details && this.active) {
                    var src = "https://player.vimeo.com/video/" + this.details.vid_id + "?autoplay=1&badge=0"
                    return src;
                }

                return '';
            }
        },

        methods: {
            becameActive() {
                if (this.itemNextTimeout) {
                    window.clearTimeout(this.itemNextTimeout);
                }
                // For vimeo embed, cycle after video duration (+5 seconds)
                this.itemNextTimeout = window.setTimeout(this.done, this.details.duration + 5000);
            },
        }
    }
</script>
