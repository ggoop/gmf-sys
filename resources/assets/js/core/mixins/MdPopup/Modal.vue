<template>
  <transition name="md-fade">
    <div
      v-show="visible"
      class="md-modal"
      :class="className"
      :style="style"
      @touchmove.prevent.stop
      @click="$emit('click', $event)"
    />
  </transition>
</template>

<script>
export default {
  name: 'modal',

  props: {
    visible: Boolean,
    zIndex: Number,
    className: String,
    customStyle: Object
  },

  computed: {
    style() {
      return {
        zIndex: this.zIndex,
        ...this.customStyle
      };
    }
  }
};
</script>
<style lang="scss">
.md-modal {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.7);
}
</style>
