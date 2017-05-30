<template>
    <div class="items">
        <item
            v-for="(item, index) in items"
            :item="item"
            :index="index"
            :active="index == activeItem"
            :next="next"
            :key="item.id">
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
            console.log('ItemCollection mounted')
            this.fetchItems();
        },

        methods: {
            fetchItems() {
                console.log('ItemCollection fetching items');
                this.$http.get('queue').then((response) =>
                {
                    console.log(this.items);
                    // API delivers "queue" items
                    // We extract all the "item" objects
                    for (let fetchedEntry in response.data.data) {
                        let fetchedItem = response.data.data[fetchedEntry].attributes.item;
console.log(fetchedItem);
                        // Mark each Item as "seen" because it was seen in the
                        // API response. Afterwards we will remove all Items
                        // that were not "seen" as we know they've been removed
                        fetchedItem.seen = true;

                        // Check if the fetched item is already in the active
                        // display queue
                        let itemFound = false;
                        for (let queueEntry in this.items) {
                            let queueItem = this.items[queueEntry];
                            if (queueItem.id === fetchedItem.id) {
                                queueItem.seen = true;
                                itemFound = true;
                                break;
                            }
                        }

                        // If the item was found in the current list, no need
                        // to add it
                        if (itemFound) {
                            console.log("Item " + fetchedItem.id + " found");
                            continue;
                        }

                        // Add the item fetched from the server to the display
                        // queue
                        this.items.push(fetchedItem);
                    }

                    // Examine each item in the queue, removing items that were
                    // not seen in the last update from the server, and reset
                    // seen flag and component while we're iterating through
                    let j = 0, squeezing = false;
                    this.items.forEach((item, index) => {
                        if (item.seen) {
                            if (squeezing) this.items[j] = item;
                            j++;
                        } else squeezing = true;
                        item.component = 'item-' + item.type;
                        delete(item.seen);
                    });
                    this.items.length = j;

                }, (response) => {
                    console.log('error fetching items');
                });
            },

            // Used by child Items to trigger an advance to the next Item
            next() {
                let nextActiveItem = ((this.items.length - 1) === this.activeItem) ? 0 : (this.activeItem + 1);
                console.log('nextActiveItem',nextActiveItem);

                // Periodically refresh the queue from the server
                if (nextActiveItem === 0) {
                    // Only attempt to fetch when we go back to the start, as
                    // item indexes may change
                    this.fetchItems();
                }

                // Only update this.activeItem once sync is complete or the
                // current activeItem could be removed
                if (this.activeItem !== nextActiveItem) {
                    this.activeItem = nextActiveItem;
                } else {
                    // There is only one item in the list, we do this to force
                    // an update so that the 'next' method keeps getting called
                    this.activeItem = -1;
                    this.$nextTick(function() {
                        this.activeItem = 0;
                    });
                }
            }
        },
    }
</script>
