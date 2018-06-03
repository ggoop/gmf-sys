<template>
  <span class="md-dialog-title md-title">
    <slot />
  </span>
</template>

<script>
export default {
  name: 'MdDialogTitle'
}
</script>

<style lang="scss">
  @import "~gmf/components/MdAnimation/variables";

  .md-dialog-title {
    margin-bottom: 20px;
    padding: 24px 24px 0;
  }
</style>
