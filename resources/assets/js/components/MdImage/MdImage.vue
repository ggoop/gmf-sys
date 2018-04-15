<template>
  <div class="md-image" :class="[$mdActiveTheme]">
    <slot>
      <img :src="mdSrc">
    </slot>
  </div>
</template>

<style lang="scss">
  .md-image {
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>

<script lang="babel">
  import MdComponent from 'gmf/core/MdComponent'

  export default new MdComponent({
    name: 'MdImage',
    props: {
      mdSrc: String
    }
  })
</script>
