<template>
    <div class="item-weather"
        :class="{ component__active: active }">
        <h1>Melbourne</h1>
        <h2>{{ details.apparent_temp.value }}°</h2>
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
            tempSymbol() {
                console.log(this.details.apparent_temp.units);
                switch(this.details.apparent_temp.units) {
                    case "Celsius":
                        return "℃";
                    case "Fahrenheit":
                        return "℉";
                }
                return "°";
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
                // For a basic image, cycle after 10 seconds
                if (this.active) {
                    window.setTimeout(this.next, 4000)
                }
            }
        }
    }
</script>
