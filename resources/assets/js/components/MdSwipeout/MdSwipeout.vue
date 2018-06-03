<template>
  <div class="md-swipeout">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: 'MdSwipeout'
}
</script>

<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
.md-swipeout {
  width: 100%;
  overflow: hidden;
}
.md-swipeout-item {
  position: relative;
}
.md-swipeout-button-box {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  font-size: 0;
  text-align: right;
}
.md-swipeout-button-box-left {
  text-align: left;
}
.md-swipeout-button-box > div {
  height: 100%;
}
.md-swipeout-button {
  height: 100%;
  text-align: center;
  font-size: 14px;
  color: #FFF;
  border: none;
}
.md-swipeout-content {
  position: relative;
  background: #FFF;
}
.md-swipeout-content.md-swipeout-content-animated {
  transition: transform 0.2s;
}
.md-swipeout-button-primary {
  background-color: #1AAD19;
}
.md-swipeout-button-warn {
  background-color: #E64340;
}
.md-swipeout-button-default {
  background-color:#c8c7cd;
}
</style>