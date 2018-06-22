<template>
  <div :class="[
      b({
        center,
        required,
        clickable: isLink || clickable
      }),
      { 'md-1px': border }
    ]" @click="onClick">
    <slot name="icon">
      <md-x-icon v-if="icon" :class="b('left-icon')" :name="icon" />
    </slot>
    <div v-if="isDef(title) || $slots.title" :class="b('title')">
      <slot name="title">
        <h3 v-text="title" />
        <md-x-tag v-if="tag">{{tag}}</md-x-tag>
        <p v-if="label" v-text="label" />
      </slot>
    </div>
    <div v-if="isDef(value) || $slots.default" :class="b('value', { alone: !$slots.title && !title })">
      <slot>
        <span v-text="value&&value.constructor === Object?value.name:value " />
      </slot>
    </div>
    <slot name="right-icon">
      <md-x-icon v-if="isLink" :class="b('right-icon')" name="arrow" />
    </slot>
    <slot name="extra" />
  </div>
</template>

<script>
  import RouterLink from "gmf/core/mixins/MdRouterLink";
  import create from "gmf/core/MdComponent";

  export default new create({
    name: "MdXCell",
    mixins: [RouterLink],
    props: {
      icon: String,
      tag:String,
      label: String,
      center: Boolean,
      isLink: Boolean,
      required: Boolean,
      clickable: Boolean,
      title: [String, Number],
      value: [String, Number, Object],
      border: {
        type: Boolean,
        default: true
      }
    },

    methods: {
      onClick() {
        this.$emit("click");
        this.routerLink();
      }
    }
  });
</script>
<style lang="scss">
  .md-x-cell {
    width: 100%;
    display: flex;
    padding: 10px 15px;
    box-sizing: border-box;
    line-height: 24px;
    position: relative;
    background-color: #fff;
    color: #333;
    font-size: 14px;
    overflow: hidden;

    &:not(:last-child)::after {
      left: 15px;
      right: 0;
      width: auto;
      transform: scale(1, 0.5);
      border-bottom-width: 1px;
    }

    &-group {
      background-color: #fff;
    }
    &__title {
      h2,
      h3,
      h4,
      h5 {
        margin: 0px;
        font-weight: normal;
        font-size: 14px;
        display: inline-block;
      }
      p {
        font-size: 12px;
        line-height: 1.2;
        color: #666;
        padding: 0px;
        margin: 0px;
      }
    }

    &__title,
    &__value {
      flex: 1;
    }

    &__value {
      overflow: hidden;
      text-align: right;
      vertical-align: middle;

      &--alone {
        text-align: left;
      }
    }

    &__left-icon {
      font-size: 16px;
      line-height: 24px;
      margin-right: 5px;
    }

    &__right-icon {
      color: #666;
      font-size: 12px;
      line-height: 24px;
      margin-left: 5px;
    }

    &--clickable {
      &:active {
        background-color: #e8e8e8;
      }
    }

    &--required {
      overflow: visible;

      &::before {
        content: "*";
        position: absolute;
        left: 7px;
        font-size: 14px;
        color: #f44;
      }
    }

    &--center {
      align-items: center;
    }
  }
</style>