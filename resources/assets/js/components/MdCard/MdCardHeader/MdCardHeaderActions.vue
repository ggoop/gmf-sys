<template>
  <div class="md-card-header-actions">
    <slot />
  </div>
</template>

<script>
  export default {
    name: 'MdCardHeaderActions',
    props: {
      
    }
  }
</script>

<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
  .md-card-header-actions {
    padding:0px;
    display: flex;
    align-items: center;
    .md-button {
      margin: 0;

      &:first-child {
        margin-left: 0;
      }

      &:last-child {
        margin-right: 0;
      }

      + .md-button {
        margin-left: 4px;
      }
    }
  }
</style>
