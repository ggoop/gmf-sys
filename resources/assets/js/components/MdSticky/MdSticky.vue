<template>
  <div class="md-sticky-box">
    <slot></slot>
  </div>
</template>

<script>
import sticky from './sticky'

export default {
  name: 'MdSticky',
  props: ['mdScrollBox', 'mdOffset', 'mdCheckStickySupport', 'disabled'],
  data () {
    return {
      initTimes: 0
    }
  },
  created () {
    
  },
  activated () {
    if (this.initTimes > 0) {
      this.bindSticky()
    }
    this.initTimes++
  },
  mounted () {
    this.$nextTick(() => {
      this.bindSticky()
    })
  },
  beforeDestroy () {
    
  },
  methods: {
    bindSticky () {
      if (this.disabled) {
        return
      }
      this.$nextTick(() => {
        sticky(this.$el, {
          scrollBox: this.mdScrollBox,
          offset: this.mdOffset,
          checkStickySupport: typeof this.mdCheckStickySupport === 'undefined' ? true : this.mdCheckStickySupport
        })
      })
    }
  },
  
}
</script>

<style lang="scss">
.md-sticky-box {
  z-index:20;
}
.md-sticky {
  width: 100%;
  position: sticky;
  top: 0;
}
.md-sticky-fixed {
  width: 100%;
  position: fixed;
  top: 0;
  transform: translate3d(0, 0, 0);
}
.md-sticky-fill {
  display: none;
}
.md-sticky-fixed + .md-sticky-fill {
  display: block;
}
</style>
