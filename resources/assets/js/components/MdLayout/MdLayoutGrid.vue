<template>
  <div class="md-layout-grid" :class="{'md-layout-grid-no-lr-borders': !mdShowLrBorders}">
    <slot></slot>
  </div>
</template>
<script>
export default {
  name: 'MdLayoutGrid',
  methods: {
    countColumn() {
      this.childrenSize = this.$children.length
      this.$children.forEach((c, index) => (c.index = index))
    }
  },
  props: {
    mdCols: {
      type: Number
    },
    mdShowLrBorders: {
      type: Boolean,
      default: true
    },
    mdShowVerticalDividers: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    column() {
      return this.mdCols || this.childrenSize
    }
  },
  data() {
    return {
      childrenSize: 3
    }
  }
}

</script>
<style lang="scss">
@import "~gmf/style/mixins.scss";
.md-layout-grid {
  position: relative;
  overflow: hidden;

  &:before {
    @include setTopLine();
  }
  &:after {
    @include setLeftLine();
  }
  &.md-layout-grid-no-lr-borders {
    &:after {
      display: none;
    }
  }
}
</style>
