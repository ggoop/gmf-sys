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
  &.md-toolbar.md-dense {
    min-height: 40px;
    z-index: 1;
    position: relative;
    padding: 0px 10px;
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
    .md-button:not(.md-icon-button) {
      margin: 0;
      min-width: 0;
      line-height: 40px;
      height: 40px;
    }
    .md-field {
      padding: 0px;
      min-height: 40px;
      margin: 0px;
      flex-flow: row nowrap;
      label {
        top: 10px;
      }
      &:after {
        background-color: transparent;
      }
      >.md-icon {
        margin: 0px;
        margin-top: 10px;
      }
      >.md-select>.md-input{
        padding-top: 16px;
      }
      .md-chip {
        margin: 0px;
        margin-top: 16px;
        line-height: 24px;
        height: 24px;
      }
      .md-input {
        line-height: 40px;
        height: 40px;
        min-width: auto;
      }
      label+.md-input {
        padding-top: 16px;
      }
      &.md-ref-input {
        label+.md-input {
          padding-top: 10px;
        }
        .md-ref-filter {
          margin-top: 5px;
        }
      }
      &.md-autocomplete.md-fetch {
        height: 38px;
        min-height: 38px;
        border-radius: 2px;
        .md-input {
          -webkit-text-fill-color: #4b4747;
          color: #4b4747;
        }
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
