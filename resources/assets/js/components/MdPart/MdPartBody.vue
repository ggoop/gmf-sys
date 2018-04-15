<template>
  <div class="layout-fill flex md-part-body" :class="classes">
    <slot></slot>
  </div>
</template>
<script>
  export default {
    props: {
      direction:{
        type:String,
        default:'column'
      }
    },
    computed: {
      classes() {
        const s={};
        s['layout-'+this.direction]=true;
        return s;
      },
    },
  };
</script>


<style lang="scss">
@import "~gmf/components/MdAnimation/variables";


.md-part-body {
  overflow: auto;
  background-color: #fff;
  position: relative;
  padding: 0 16px;
  &.md-no-scroll {
    overflow: hidden;
  }
  &.md-transparent {
    background-color: transparent;
  }
  .md-part-side {
    box-shadow: 0 1px 5px rgba(0, 0, 0, .2), 0 2px 2px rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .12);
    z-index: 1;
    position: relative;
    min-width: 0;
    max-width: 0;

    >.md-navigation {
      display: none;
      position: absolute;
      top: 38px;
      right:-12px;
      z-index: 1;
      margin: 0;
      padding: 0;
    }
    &.md-active {
      display: block;
      overflow: hidden;
      min-width: 190px;
      max-width: 100%;
      >.md-navigation {
        right:38px;
      }
    }
  }
}

</style>

