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
                this.$http.get('queue').then((response) =>
                {
                    // API now delivers "queue" items
                    // We extract all the "item" objects
                    var items = [];
                    for (var queueEntry in response.data.data) {
                        items.push(response.data.data[queueEntry].attributes.item)
                    }
                    this.$set(this, 'items', items);

                    // Map each item type to a Vue component
                    this.items.map(function (e) {
                        e.component = 'item-' + e.type;
                    });

                }, (response) => {
                    console.log('error fetching items');
                });
            },

            // Used by child Items to trigger an advance to the next Item
            next() {
                this.activeItem = ((this.items.length - 1) == this.activeItem) ? 0 : (this.activeItem + 1);
            }
        },
    }
</script>
