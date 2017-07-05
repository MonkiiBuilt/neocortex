<template>
    <div class="item-image"
        :class="{ component__active: active }"
        :style="{ backgroundImage: 'url(' + imageUrl + ')' }">
    </div>
</template>

<script>
    import BaseItem from './BaseItem.vue';

    export default {
        // Inherit props and basic functionality from BaseItem.vue
        extends: BaseItem,

        computed: {
            imageUrl() {
                if (this.details) {
                    return this.details.url;
                }

                return '';
            },
        },
    }
</script>

<style lang="sass">

.item-image {
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}

</style>