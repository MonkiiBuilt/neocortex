<template>
    <div class="item item__full"
         :class="{ item__active: active }">
         <component
            :is="item.component"
            :details="item.details"
            :index="index"
            :active="active"
            :next="next">
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
            // This can be used by an item to trigger a transition to the
            // next Item in the ItemCollection
            next: {
                type: Function,
                required: true
            }
        }
}
</script>

<style lang="sass">

.item {
    transform: translateX(-100%);
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

.item__active {
    transform: translateX(0);
}

.item-clickable {
    cursor: pointer;
}

.user-interactive .item__full {
    cursor: default;
}

</style>