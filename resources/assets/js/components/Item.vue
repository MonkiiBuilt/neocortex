<template>
    <div class="item item__full item-image"
         :class="{ item__active: active }"
         :style="{ backgroundImage: 'url(' + imageUrl + ')' }">

    </div>
</template>

<script>
    export default {
        props: [
            'item',
            'index',
            'active',
        ],

        computed: {
            imageUrl() {
                if (this.item.attributes.details) {
                    console.log(this.item.attributes.details.url);
                    return this.item.attributes.details.url;
                }
                return '';
            },
        },
    }
</script>
