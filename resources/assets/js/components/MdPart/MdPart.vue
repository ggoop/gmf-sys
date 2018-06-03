<template>
  <div class="layout-fill flex md-part" :class="classes">
    <slot></slot>
  </div>
</template>
<script>
export default {
  props: {
    direction: {
      type: String,
      default: 'column'
    }
  },
  data: () => ({
    MdPart: {
      toolbar: {
        element: null,
        height: '0px',
        top: 0
      },
      part: {
        element: null,
        screenfull: false
      }
    }
  }),
  provide() {
    return {
      MdPart: this.MdPart
    }
  },
  computed: {
    classes() {
      const s = {};
      s['layout-' + this.direction] = true;
      s['fullscreen'] = this.MdPart.part.screenfull;
      return s;
    },
  },
  methods: {
    handleFlexibleMode() {

    },
  },
  mounted() {
    this.MdPart.part.element = this.$el;
    this.handleFlexibleMode();
  }
};

</script>
<style lang="scss">
@import "~gmf/components/MdAnimation/variables";

.md-part {
  position: relative;
  >.md-content {
    background: #fff;
    &.md-transparent {
      background-color: transparent;
    }
  }
  &.fullscreen {
    z-index: 110;
    position: fixed;
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    overflow: auto;
  }
}

</style>
