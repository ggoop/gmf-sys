<template>
  <div
    class="md-1px-b"
    :class="b({ fixed })"
    :style="style"
  >
    <div :class="b('left')" @click="$emit('click-left')">
      <slot name="left">
        <md-x-icon v-if="leftArrow" :class="b('arrow')" name="arrow" />
        <span v-if="leftText" v-text="leftText" :class="b('text')" />
      </slot>
    </div>
    <div :class="b('title')" class="van-ellipsis">
      <slot name="title">{{ title }}</slot>
    </div>
    <div :class="b('right')" @click="$emit('click-right')">
      <slot name="right">
        <span v-if="rightText" v-text="rightText" :class="b('text')" />
      </slot>
    </div>
  </div>
</template>

<script>
import create from "gmf/core/MdComponent";

export default new create({
  name: 'MdXNavBar',
  props: {
    title: String,
    leftText: String,
    rightText: String,
    leftArrow: Boolean,
    fixed: Boolean,
    zIndex: {
      type: Number,
      default: 1
    }
  },

  computed: {
    style() {
      return {
        zIndex: this.zIndex
      };
    }
  }
});
</script>
<style lang="scss">
@import '~gmf/style/variables';

.md-x-nav-bar {
  height: 46px;
  position: relative;
  user-select: none;
  text-align: center;
  line-height: 46px;
  background-color: $white;

  .md-x-icon {
    color: $blue;
    vertical-align: middle;
  }

  &__arrow {
    transform: rotate(180deg);

    + .md-x-nav-bar__text {
      margin-left: -20px;
      padding-left: 25px;
    }
  }

  &--fixed {
    top: 0;
    left: 0;
    width: 100%;
    position: fixed;
  }

  &__title {
    margin: 0 auto;
    max-width: 60%;
    font-size: 16px;
  }

  &__left,
  &__right {
    bottom: 0;
    font-size: 14px;
    position: absolute;
  }

  &__left {
    left: 15px;
  }

  &__right {
    right: 15px;
  }

  &__text {
    color: $blue;
    margin: 0 -15px;
    padding: 0 15px;
    display: inline-block;
    vertical-align: middle;

    &:active {
      background-color: $active-color;
    }
  }
}

</style>
