<template>
  <div class="md-app layout-fill layout-column">
    <md-app-menu ref="menu" :md-token="mdToken" :md-title="mdTitle"></md-app-menu>
    <md-app-toolbar ref="toolbar" @toggle="toggle" :md-token="mdToken" :md-title="mdTitle"></md-app-toolbar>
    <md-app-content :md-token="mdToken" :md-title="mdTitle"></md-app-content>
    <md-app-footer :md-token="mdToken" :md-title="mdTitle"></md-app-footer>
  </div>
</template>
<script>
  export default {
    props: {
      mdToken: String,
      mdTitle: String
    },
    computed: {
    },
    methods: {
      toggle() {
        this.$refs.menu.toggle();
      }
    }
  };
</script>
