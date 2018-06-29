<template>
  <div class="md-app-bottom-bar">
    <slot />
  </div>
</template>
<script>
export default {
  name: 'MdAppBottomBar',
  inject: ['MdApp']
}

</script>
<style lang="scss">
.md-app-bottom-bar {

}

</style>
