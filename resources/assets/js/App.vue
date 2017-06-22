<template>
    <div id="main"
        :class="{ 'user-interactive': userInteractive }"
        v-on:click="onUserInteraction"
        v-on:mousemove="onUserInteraction"
    >
        <item-collection></item-collection>
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

  ready () {
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