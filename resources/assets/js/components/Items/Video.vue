<template>
    <div class="item-video">
        <video autoplay loop>
            <source :src="webmUrl" type="video/webm">
            <source :src="mp4Url" type="video/mp4">
        </video>
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
