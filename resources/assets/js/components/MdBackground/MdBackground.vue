<template>
  <div class="md-background" :style="styles"></div>
</template>
<script>
  import MdComponent from 'core/MdComponent';
  export default new MdComponent({
    name: 'MdBackground',
    data: () => ({
      styles:{}
    }),
  });
</script>

<style lang="scss">
  @import "~components/MdAnimation/variables";
  .md-background {
    height: 200%;
    left: 0;
    position: absolute;
    right: 0;
    top:-100%;
    z-index: -10;
  }
  
</style>
