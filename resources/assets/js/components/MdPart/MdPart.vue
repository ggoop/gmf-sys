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
      return s;
    },
  },
  methods: {
    handleFlexibleMode() {
      
    },
  },
  mounted() {
    this.handleFlexibleMode()
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
}

</style>
