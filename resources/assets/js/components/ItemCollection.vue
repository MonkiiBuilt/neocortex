<template>
    <div class="items">
        <item
            v-for="(item, index) in items"
            :item="item"
            :index="index"
            :active="activeItem == index">
        </item>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                activeItem: 0,
                items: [],
            }
        },

        mounted() {
            console.log('Component ready.')
            this.fetchItems();
        },

        methods: {
            fetchItems() {
                console.log('fetching items');
                this.$http.get('/api/v1/item').then((response) =>
                {
                    this.$set(this, 'items', response.data.data);
                    window.setTimeout(this.advanceActiveItem, 10000);
                }, (response) => {
                    console.log('error fetching items');
                });
            },

            advanceActiveItem() {
                this.activeItem = (this.items.length - 1 == this.activeItem) ? 0 : this.activeItem + 1;
                window.setTimeout(this.advanceActiveItem, 10000);
            }
        },

        http: {
            root: 'item',
        }
    }
</script>
