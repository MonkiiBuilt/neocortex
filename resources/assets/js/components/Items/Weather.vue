<template>
    <div class="item-weather"
        :class="{ component__active: active }">
        <div class="item-inner">
            <div class="city-name">Melbourne</div>
            <div class="current-icon"><img :src="weatherIconSrc(details.forecasts[0].icon)" />°</div>
            <div class="current-temp">{{ temperature }}°</div>

            <div v-for="(forecast, index) in details.forecasts"
                 v-if="forecast.air_temperature_minimum"
                 class="forecast">
                <div class="forecast-day">{{ forecast.day }}</div>
                <div class="forecast-icon"><img :src="weatherIconSrc(forecast.icon)" /></div>
                <div class="forecast-temp">{{ temperatureRange(forecast.air_temperature_minimum, forecast.air_temperature_maximum) }}</div>
            </div>
        </div>
    </div>
</template>

<style lang="sass">
    .item-weather {
        background: #fff;
        color: #333;
        height: 100%;

        display: flex;
        align-items: center;
        justify-content: center;

        .item-inner {
            min-width: 60%;
            max-width: 60%;
        }

        .current-icon, .current-temp {
            display: inline-block;
        }
        .city-name {
            font-size: 48px;
        }
        .current-temp {
            font-size: 36px;
        }
        .forecast {
            font-size: 30px;
            div {
                display: inline-block;
            }
        }
    }
</style>



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
            temperature() {
                return this.details.readings.apparent_temp.value;
            },
            tempSymbol() {
                console.log(this.details);
                console.log(this.details.readings.apparent_temp.units);
                switch(this.details.readings.apparent_temp.units) {
                    case "Celsius":
                        return "℃";
                    case "Fahrenheit":
                        return "℉";
                }
                return "°";
            },
        },

        mounted() {
            this.waitForNext();
        },

        updated() {
            this.waitForNext();
        },

        methods: {
            weatherIconSrc(icon) {
                return "/images/weather/" + icon + ".png";
            },
            temperatureRange(min, max) {
                if (min && max)
                    return min + "° - " + max + "°";
                return "";
            },
            waitForNext() {
                // For a basic image, cycle after 10 seconds
                if (this.active) {
                    window.setTimeout(this.next, 4000)
                }
            }
        }
    }
</script>
