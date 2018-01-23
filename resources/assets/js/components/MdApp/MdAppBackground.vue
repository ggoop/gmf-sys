<template>
  <div class="md-app-background">
    <slot />
  </div>
</template>
<script>
export default {
  name: 'MdAppBackground',
  inject: ['MdApp']
}

</script>
<style lang="scss">
.md-app-background {
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-position: center;
  -webkit-background-size: 100%;
  background-size: 100%;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

</style>
