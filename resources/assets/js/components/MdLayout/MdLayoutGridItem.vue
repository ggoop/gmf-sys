<template>
  <a href="javascript:;" class="md-layout-grid-item" :class="{
      'md-layout-grid-item-no-border': (isLast && !$parent.mdShowLrBorders) || (!isLast && !$parent.mdShowVerticalDividers),
    }" @click="onClick" :style="style">
    <div class="md-layout-grid-item-icon" v-if="hasIconSlot || mdIcon">
      <slot name="icon">
        <img :src="mdIcon" alt="">
      </slot>
    </div>
    <p v-if="hasLabelSlot || mdLabel" class="md-layout-grid-item-label">
      <slot name="label">
        <span v-html="mdLabel"></span>
      </slot>
    </p>
    <slot></slot>
  </a>
</template>
<script>
import { go } from 'gmf/core/utils/MdRouter'
export default {
  name: 'MdLayoutGridItem',
  props: ['mdIcon', 'mdLabel', 'mdLink'],
  created() {
    this.$parent.countColumn()
  },
  mounted() {
    this.$slots.icon && (this.hasIconSlot = true)
    this.$slots.label && (this.hasLabelSlot = true)
  },
  destroyed() {
    this.$parent.countColumn()
  },
  computed: {
    isLast() {
      return !((this.index + 1) % this.$parent.column)
    },
    style() {
      const column = this.$parent.column
      if (!column || column === 3) {
        return
      }
      const styles = {}
      styles.width = `${100 / column}%`
      return styles
    }
  },
  methods: {
    onClick() {
      this.$emit('on-item-click')
      go(this.mdLink, this.$router)
    }
  },
  data() {
    return {
      index: 0,
      hasIconSlot: false,
      hasLabelSlot: false
    }
  }
}

</script>
<style lang="scss">
@import "~gmf/style/mixins.scss";
.md-layout-grid-item {
  position: relative;
  float: left;
  padding: 20px 10px;
  width: 33.33333333%;
  box-sizing: border-box;

  &.md-layout-grid-item-no-border {
    &:before {
      display: none;
    }
  }

  &:before {
    @include setRightLine();
  }
  &:after {
    @include setBottomLine();
  }

  &:active {
    background-color: #ECECEC;
  }
}

.md-layout-grid-item-icon {
  width: 28px;
  height: 28px;
  margin: 0 auto;

  img {
    display: block;
    width: 100%;
    height: 100%;
  }

  &+.md-layout-grid-item-label {
    margin-top: 5px;
  }
}

.md-layout-grid-item-label {
  display: block;
  text-align: center;
  color: #000000;
  font-size: 14px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

</style>
