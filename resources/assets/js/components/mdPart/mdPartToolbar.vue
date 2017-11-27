<template>
  <md-toolbar class="md-part-toolbar md-dense" md-elevation="1">
    <slot></slot>
  </md-toolbar>
</template>
<script>
export default {
  name: 'MdPartToolbar',
  inject: ['MdPart'],
  mounted() {
    this.MdPart.toolbar.element = this.$el
  }
};

</script>
<style lang="scss">
@import "~components/MdAnimation/variables";
.md-part-toolbar {
  background-color: #E0F2F1;
  min-height: 36px;
  z-index: 1;
  position: relative;
  &.md-toolbar.md-dense{
    min-height: 36px;
  }
  .md-part-toolbar-crumbs {
    padding: 0 16px;
    .md-part-toolbar-crumb+.md-part-toolbar-crumb:before {
      content: '\E5CC';
      vertical-align: top;
      display: inline-block;
      font-family: 'Material Icons';
      font-weight: normal;
      font-style: normal;
      font-size: 25px;
      -webkit-font-smoothing: antialiased;
    }
  }
  .md-part-toolbar-group {
    .md-button {
      margin: 0;
      min-width: 0;
    }
    .md-input-container {
      padding: 0px;
      min-height: 36px;
      background: rgba(255, 255, 255, 0.88);
      &.md-inset {
        label {
          font-size: inherit;
        }
        >.label {
          min-width: inherit;
        }
      }
    }
  }
  .md-part-toolbar-group+.md-part-toolbar-group:before {
    content: ' ';
    position: absolute;
    height: 20px;
    width: 1px;
    background: rgba(0, 0, 0, 0.12);
    top: 8px;
    display: inline-block;
  }
}

</style>
