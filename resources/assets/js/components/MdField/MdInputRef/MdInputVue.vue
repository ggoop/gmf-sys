<template>
  <div class="md-input-value" :class="[classes]" tabindex="0">
    <slot></slot>
    <md-button
      class="md-icon-button md-dense md-delete"
      v-if="mdDeletable"
      @click.native="!disabled && $emit('delete')"
      @keyup.native.delete="!disabled && $emit('delete')"
      tabindex="-1">
      <md-icon class="md-icon-delete">cancel</md-icon>
    </md-button>
  </div>
</template>
<script>
  

  export default {
    props: {
      disabled: Boolean,
      mdDeletable: Boolean
    },
    computed: {
      classes() {
        return {
          'md-deletable': this.mdDeletable,
          'md-disabled': this.disabled
        };
      }
    }
  };
</script>
<style lang="scss">
@import "~components/MdAnimation/variables";
//md-input-value
$md-input-value-height: 32px !default;
$md-input-value-radius:50px !default;
$md-input-value-icon-size: 24px !default;
$md-input-value-icon-font: 18px !default;
.md-input-value {
  padding: 2px 0;
  margin: 1px 0;
  display: inline-block;
  transition: $swift-ease-out;
  display: flex;
  justify-content: center;
  align-items: center;
  white-space: nowrap;
  &.md-deletable {
    position: relative;
    padding-right: 32px;
  }
  &:focus,
  &:active {
    outline: none;
    &:not(.md-disabled) {
      cursor: pointer;
    }
  }
  &.md-disabled {
    .md-button {
      pointer-events: none;
      cursor: default;
    }
  }
  &:hover {
    .md-button.md-delete {
      color: rgba(0, 0, 0, 0.74);
      display: block;
    }
  }
  .md-button.md-delete {
    width: $md-input-value-icon-size;
    min-width: $md-input-value-icon-size;
    height: $md-input-value-icon-size;
    min-height: $md-input-value-icon-size;
    margin: 0;
    padding: 0;
    position: absolute;
    top: 4px;
    right: 4px;
    border-radius: $md-input-value-icon-size;
    transition: $swift-ease-out;
    color: rgba(0, 0, 0, 0.14);
    display: none;
    .md-icon {
      width: $md-input-value-icon-font;
      min-width: $md-input-value-icon-font;
      height: $md-input-value-icon-font;
      min-height: $md-input-value-icon-font;
      margin: 0;
      font-size: $md-input-value-icon-font;
    }
    .md-ink-ripple {
      border-radius: $md-input-value-radius;
    }
    .md-ripple {
      opacity: .54;
    }
  }
}
</style>