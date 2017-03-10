<template>
    <div class="item item__full"
         :class="{ item__active: active }">
         <component
            :is="item.component"
            :details="item.attributes.details"
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
            }
        }
}
</script>
