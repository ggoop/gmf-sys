<template>
  <div class="md-loading layout layout-align-center-center" v-if="loading">
    <md-progress-spinner md-mode="indeterminate"></md-progress-spinner>
  </div>
</template>

<script>
  export default {
    props: {
      loading: Number,
    },
    data() {
      return {
        isFinished: null,
      };
    }
  };
</script>
<style lang="scss">
  .md-loading{
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0px;
    background: rgba(0,0,0,0.2);
    left: 0px;
    z-index: 500;
}
</style>