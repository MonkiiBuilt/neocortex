<template>
    <div class="items">
        <item
            v-for="(item, index) in items"
            :item="item"
            :index="index"
            :active="index == activeItem"
            :next="next">
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
                this.$http.get('item').then((response) =>
                {
                    this.$set(this, 'items', response.data.data);

                    this.items.map(function (e) {
                        e.component = 'item-' + e.attributes.type;
                    });

                }, (response) => {
                    console.log('error fetching items');
                });
            },

            // Used by child Items to trigger an advance to the next Item
            next() {
                this.activeItem = (this.items.length - 1 == this.activeItem) ? 0 : this.activeItem + 1;
            }
        },
    }
</script>
