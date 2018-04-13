<template>
  <div class="gmf-pages-auth layout layout-align-center-center">
    <div class="content">
      <router-view></router-view>
    </div>
    <div class="bg-wrap">
      <md-svg-loader md-src="/assets/vendor/gmf-sys/svg/auth-bg.svg" />
    </div>
  </div>
</template>
<script>
import MdSvgLoader from 'gmf/components/MdSvgLoader/MdSvgLoader'
export default {
  name: 'GmfPagesAuthApp',
  components: {
    MdSvgLoader
  },
  data: () => ({
    loading: false
  }),
  computed: {

  },
  methods: {

  },
  created() {}
}
</script>
<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdIcon/mixins";
@import "~gmf/components/MdLayout/mixins";
.gmf-pages-auth {
  min-height: 100%;
  min-width: 100%;
  >.content {
    z-index: 2;
    width: 450px;
    max-width: 100%;
    @include md-layout-xsmall {
      min-height: 100vh;
      width: 100%;
      background:#fff;
    }
    >.md-card {
      padding: 10px 20px 30px 20px;
      @include md-layout-xsmall {
        padding-left: 0px;
        padding-right: 0px;
        min-height: 100%;
        box-shadow: none;
      }
    }
  }
  .md-progress-bar {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
  }
  .bg-wrap {
    background: #fff;
    bottom: 0;
    direction: ltr;
    left: 0;
    overflow: hidden;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 1;
    .md-svg-loader,
    svg {
      display: block;
      height: 100%;
      position: relative;
      width: 100%;
    }
  }
  .md-button-full {
    font-size: 18px;
    padding: 8px;
    height: auto;
    line-height: 30px;
  }
  .md-card-actions {
    a+a {
      margin-left: 16px;
    }
    .md-button+.md-button {
      margin-left: 20px;
    }
  }
}
</style>