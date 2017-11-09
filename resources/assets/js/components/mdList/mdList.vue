<template>
  <ul class="md-list" :class="[themeClass]">
    <slot></slot>
  </ul>
</template>
<script>
  import theme from '../../core/components/mdTheme/mixin';
  export default {
    name: 'md-list',
    mixins: [theme]
  };
</script>