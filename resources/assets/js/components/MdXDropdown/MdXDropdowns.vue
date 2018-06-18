<template>
  <div :class="b()" class="md-1px-b">
    <slot />
  </div>
</template>

<script>
  import create from 'gmf/core/MdComponent';
  export default create({
    name: 'MdXDropdowns',
    props: {},
    data() {
      return {};
    },
    methods: {}
  });
</script>
<style lang="scss">
  @import '~gmf/style/variables';
  .md-x-dropdowns {
    position: relative;
    user-select: none;
    background-color: #fff;
    display: flex;
  }
</style>