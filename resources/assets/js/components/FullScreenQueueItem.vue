<template>
    <div class="item item__full"
         :class="{ item__active: active }">
         <component
            :is="item.component"
            :item="item"
            :index="index"
            :active="active"
            v-on:done="propagateDone"
         >
         </component>
    </div>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true
            },
            index: Number,
            active: {
                  type: Boolean,
                  default: false
            },
        },
        methods: {
            // Pretty fookin clever m8
            // ...args means take all arguments passed to the function
            propagateDone (...args) {
                // This is probably not a great practice, but Vue doesn't have
                // event propagation down the component chain
                // @see https://vuejs.org/v2/guide/components.html#Non-Parent-Child-Communication
                this.$emit('done', ...args); // Pass on all args
            },
        },
    }
</script>

<style lang="sass">

    .item {
        transform: translateX(-100%);

        &.item__active {
            transform: translateX(0);
        }
    }

    .item__full {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
        overflow: auto;
        background: #000;
        cursor: none;
    }

    .item-clickable {
        cursor: pointer;
    }

    .user-interactive .item__full {
        cursor: default;
    }

</style>