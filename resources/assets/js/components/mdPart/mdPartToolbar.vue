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
  min-height: 40px;
  z-index: 1;
  position: relative;
  &.md-toolbar.md-dense {
    min-height: 40px;
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
    .md-field {
      padding: 0px;
      min-height: 40px;
      margin: 0px;
      flex-flow: row nowrap;

      .md-chip {
        padding-top: 10px;
        font-size: 16px;
        height: 38px;
      }
      .md-input {
        line-height: 40px;
        height: 40px;
        padding-top: 10px;
        min-width: auto;
      }
      label {
        top: 15px;
      }
    }
    .md-field.md-focused label,
    .md-field.md-has-value label {
      top: 0px;
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
