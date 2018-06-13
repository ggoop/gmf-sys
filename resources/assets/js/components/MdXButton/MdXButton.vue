<template>
  <component :is="tag" :type="nativeType" :disabled="disabled" :class="b([
      type,
      size,
      {
        block,
        loading,
        disabled,
        unclickable: disabled || loading,
        'bottom-action': bottomAction
      }
    ])" @click="onClick">
    <slot name="icon" v-if="!loading">
      <md-x-icon v-if="icon" :class="b('left-icon')" :name="icon" />
    </slot>
    <md-x-loading v-if="loading" size="20px" :color="type === 'default' ? 'black' : 'white'" />
    <span :class="b('text')">
      <slot>{{ text }}</slot>
    </span>
  </component>
</template>

<script>
  import create from "gmf/core/MdComponent";
  import RouterLink from 'gmf/core/mixins/MdRouterLink';
  export default new create({
    name: 'MdXButton',
    mixins: [RouterLink],
    props: {
      text: String,
      icon: String,
      block: Boolean,
      loading: Boolean,
      disabled: Boolean,
      nativeType: String,
      bottomAction: Boolean,
      tag: {
        type: String,
        default: 'button'
      },
      type: {
        type: String,
        default: 'default'
      },
      size: {
        type: String,
        default: 'normal'
      }
    },

    methods: {
      onClick(event) {
        if (!this.loading && !this.disabled) {
          this.$emit('click', event);
          if (this.url || this.to) {
            this.routerLink();
          }
        }
      }
    }
  });
</script>
<style lang="scss">
  .md-x-button {
    position: relative;
    padding: 0;
    display: inline-block;
    display: flex;
    align-items: center;
    align-content: center;
    justify-content: center;
    height: 45px;
    line-height: 43px;
    border-radius: 3px;
    box-sizing: border-box;
    font-size: 16px;
    text-align: center;
    -webkit-appearance: none;
    overflow: hidden;
    &.md-full {
      width: 100%;
    }

    .md-x-button__left-icon {
      font-size: 16px;
      margin-right: 5px;
    }

    &::before {
      content: " ";
      position: absolute;
      top: 50%;
      left: 50%;
      opacity: 0;
      width: 100%;
      height: 100%;
      border: inherit;
      border-color: #000;
      background-color: #000;
      border-radius: inherit;
      /* inherit parent's border radius */
      transform: translate(-50%, -50%);
    }

    &:active::before {
      opacity: .3;
    }

    &--unclickable:before {
      display: none;
    }

    &--default {
      color: #333;
      background-color: #fff;
      border: 1px solid #eee;
    }

    &--primary {
      color: #fff;
      background-color: #4b0;
      border: 1px solid #4b0;
    }

    &--danger {
      color: #fff;
      background-color: #f44;
      border: 1px solid #f44;
    }

    &--large {
      width: 100%;
      height: 50px;
      line-height: 48px;
    }

    &--normal {
      padding: 0 15px;
      font-size: 14px;
    }

    &--small {
      height: 30px;
      padding: 0 8px;
      min-width: 60px;
      font-size: 12px;
      line-height: 28px;
    }

    &--loading {
      .md-x-loading {
        display: inline-block;
      }

      .md-x-button__text {
        display: none;
      }
    }

    /* mini图标默认宽度50px，文字不能超过4个 */
    &--mini {
      display: inline-block;
      width: 50px;
      height: 22px;
      line-height: 20px;
      font-size: 10px;

      &+.md-x-button--mini {
        margin-left: 5px;
      }
    }

    &--block {
      width: 100%;
      display: block;
    }

    &--bottom-action {
      width: 100%;
      height: 50px;
      line-height: 50px;
      border: 0;
      border-radius: 0;
      font-size: 16px;
      color: #fff;
      background-color: #f85;

      &.md-x-button--primary {
        background-color: #f44;
      }
    }

    &--disabled {
      color: #999;
      background-color: #eee;
      border: 1px solid #e5e5e5;
    }
  }
</style>