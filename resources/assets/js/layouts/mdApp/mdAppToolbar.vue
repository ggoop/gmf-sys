<template>
  <md-toolbar class="md-accent">
    <md-button class="md-icon-button" @click.native="toggle()">
      <md-icon>menu</md-icon>
    </md-button>
    <h2 class="md-title">
      <img src="/img/logo.png"/>
    </h2>
    <md-input-container class="md-flex md-header-search">
      <md-input class="md-header-search-input" placeholder="search"></md-input>
    </md-input-container>
    <md-button class="md-icon-button">
      <md-icon>favorite</md-icon>
    </md-button>
  </md-toolbar>
</template>
<script>
  export default {
    props: {
      mdToken: String,
      mdTitle: String
    },
    methods: {
      toggle() {
        this.$emit('toggle');
      }
    }
  };
</script>
