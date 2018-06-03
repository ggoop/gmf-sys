<template>
  <button class="md-swipeout-button"
  :class="{'md-swipeout-button-primary': type === 'primary', 'md-swipeout-button-warn': type === 'warn', 'md-swipeout-button-default': type === 'default'}" :style="{width: `${width}px`, backgroundColor: backgroundColor}" @click="onButtonClick" type="button">
    <slot>{{text}}</slot>
  </button>
</template>

<script>
export default {
  name: 'MdSwipeoutButton',
  props: {
    text: String,
    backgroundColor: String,
    type: String,
    width: {
      type: Number,
      default: 80
    }
  },
  methods: {
    onButtonClick () {
      if (this.$parent.$options._componentTag === 'md-swipeout-item') {
        this.$parent.onItemClick(this.text)
      }
    }
  }
}
</script>