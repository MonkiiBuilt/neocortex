<template>
    <div id="main"
        :class="{ 'user-interactive': userInteractive }"
        v-on:click="onUserInteraction"
        v-on:mousemove="onUserInteraction"
    >
        <item-collection ref="itemCollection"></item-collection>
    </div>
</template>

<script>

export default {
  data: function() {
    return {
      user: [],
      items: [],
      userInteractive: false,
    };
  },

  mounted () {
    // window.itemCollection = this.$vm.itemCollection;
    console.log(this.$refs.itemCollection);
    // debug this ya gronk
    window.itemCollection = this.$refs.itemCollection;
  },

  methods: {
    onUserInteraction (event) {
      // Detect user interaction so we know when we're in kiosk mode or have
      // a PEBCAC
      this.userInteractive = true;
    }
  }
}

</script>

<style lang="sass">

</style>