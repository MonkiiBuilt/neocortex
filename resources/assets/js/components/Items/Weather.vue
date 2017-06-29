<template>
    <div class="item-weather"
        :class="{ component__active: active }">
        <div class="item-inner">
            <div class="city-name">Melbourne</div>

            <div class="forecast forecast__now">
                <div class="forecast-day">Now</div>
                <div class="forecast-icon"><img :src="weatherIconSrc(details.forecasts[0].icon)" /></div>
                <div class="forecast-temp"><span class="temp">{{ airTemp }}°</span> (feels like <span class="temp">{{ apparentTemp }}°</span>)</div>
            </div>

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
        background: #333;
        color: #333;
        height: 100%;

        display: flex;
        align-items: center;
        justify-content: center;

        &.component__active {
            background: #fff;
            transition: background-color 1s ease;
        }

        .item-inner {
            min-width: 60%;
            max-width: 60%;
        }

        .city-name {
            font-size: 5rem;
        }
        .temp {
            font-size: 1.5;
        }

        .forecast {
            display: flex;
            align-items: center;
            padding: 0.8rem 2rem;
            margin: 0.8rem 0;

            background: #fff;
            border-radius: 1rem;
            font-size: 4rem;

            .forecast-day, .forecast-icon {
                padding: 0 2rem;
            }
            .forecast-temp {
                flex-grow: 1;
            }
        }
        .forecast-day {
            width: 10rem;
        }
        .forecast__now {
            font-size: 1.2;
            font-weight: bold;
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
            airTemp() {
                return this.details.readings.air_temperature.value;
            },
            apparentTemp() {
                return this.details.readings.apparent_temp.value;
            },
            tempSymbol() {
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
            this.startIfActive();
        },

        updated() {
            this.startIfActive();
        },

        methods: {
            startIfActive() {
                console.log('isActive?', this.active, this.details);
                if (!this.active) {
                    return;
                }

                this.waitForNext();
            },
            weatherIconSrc(icon) {
                return "/images/weather/" + icon + ".png";
            },
            temperatureRange(min, max) {
                if (min && max)
                    return min + "° - " + max + "°";
                return "";
            },
            waitForNext() {
                if (this.nextTimeout) {
                    window.clearTimeout(this.nextTimeout);
                }

                // For weather, cycle after 8 seconds
                this.nextTimeout = window.setTimeout(this.next, 8000)
            }
        }
    }
</script>
