<template>
    <div class="item-iframe"
        :class="{ component__active: active }">
        <iframe :src="youtubeUrl" frameborder="0" allowfullscreen></iframe>
    </div>
</template>

<script>
    import BaseItem from './BaseItem.vue';

    export default {
        // Inherit props and basic functionality from BaseItem.vue
        extends: BaseItem,

        computed: {
            youtubeUrl() {
                // Making this contingent on this.active means the iframe
                // src will be set to '' when this slide isn't active,
                // stopping the video
                if (this.details && this.active) {
                    return "http://www.youtube.com/embed/" + this.details.vid_id + "?rel=0&hd=1&autoplay=1";
                }
                return '';
            }
        },

        methods: {
            becameActive() {
                if (this.itemNextTimeout) {
                    window.clearTimeout(this.itemNextTimeout);
                }
                // For YouTube embed, cycle after video duration (+5 seconds)
                this.itemNextTimeout = window.setTimeout(this.done, this.details.duration + 5000);
            },
        }
    }
</script>
