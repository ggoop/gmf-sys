<template>
  <div class="md-toast-wrap">
    <md-snackbar class="md-toast" :md-position="mdPosition" @md-closed="cleanAll" :md-duration="isInfinity ? Infinity : mdDuration" :md-active.sync="showSnackbar" md-persistent>
      <md-button class="md-icon-button md-primary md-button-lock" @click="toggleLock">
        <md-icon v-if="isInfinity">lock</md-icon>
        <md-icon v-else>lock_open</md-icon>
      </md-button>
      <div class="flex main">
        <p v-for="(item,itemInd) in toastList" :key="item.id" v-html="item.text"></p>
      </div>
      <md-button class="md-icon-button md-primary md-button-close" @click="cleanAll">
        <md-icon>clear</md-icon>
      </md-button>
    </md-snackbar>
    <md-dialog class="gmf-auth-dialog" :md-fullscreen="false" v-if="showAuthDialog" :md-active.sync="showAuthDialog">
      <md-dialog-title>快速安全登录</md-dialog-title>
      <md-dialog-actions>
        <md-button class="md-primary" @click="onLogin">去登录</md-button>
        <md-button class="md-primary" @click="showAuthDialog = false">取消</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>
<script>
export default {
  props: {
    id: [String, Number],
    mdPosition: {
      type: String,
      default: 'center'
    },
    mdDuration: {
      type: [String, Number],
      default: 10000
    }
  },
  data() {
    return {
      showAuthDialog: false,
      showSnackbar: false,
      isInfinity: false,
      toastList: [],
      timeout: false
    };
  },
  watch: {
    toastList() {
      this.showSnackbar = this.toastList.length > 0;
    }
  },
  methods: {
    onLogin() {
      this.showAuthDialog = false;
      this.$go(this.$root.configs.auth.route);
    },
    toggleLock() {
      this.isInfinity = !this.isInfinity;

      window.clearTimeout(this.timeout);

      var list = this.toastList.map(item => { return { id: item.id, text: item.text } });
      var infinity = this.isInfinity;
      this.toastList.splice(0, this.toastList.length);
      this.showSnackbar = this.toastList.length > 0;

      this.timeout = window.setTimeout(() => {
        this.isInfinity = infinity;
        this.toastList = list;
        this.showSnackbar = this.toastList.length > 0;
      }, 200);
    },
    toast: function(toastInfo) {
      const toast = { id: this._.uniqueId() };
      if (this._.isString(toastInfo)) {
        toast.text = toastInfo;
      }
      if (this._.isObject(toastInfo)) {
        if (toastInfo.response && toastInfo.response.status == 401 && this.$root.configs.auth.route) {
          this.showAuthDialog = true;
          return;
        }
        if (toastInfo.response && toastInfo.config && toastInfo.request) {
          toastInfo = toastInfo.response;
        }
        if (toastInfo.request && toastInfo.config && toastInfo.status && toastInfo.data) {
          toastInfo = toastInfo.data;
          if (toastInfo && toastInfo.errors) {
            toast.text = toastInfo.errors;
          } else if (toastInfo && toastInfo.msg) {
            toast.text = toastInfo.msg;
          }
        }
      }
      if (!toast.text) {
        toast.text = toastInfo;
      }
      if (toast.text == 'Unauthenticated') {
        this.showAuthDialog = true;
        return;
      }
      this.toastList.splice(0, 0, toast);
    },
    cleanAll() {
      this.isInfinity = false;
      this.toastList.splice(0, this.toastList.length);
    },
  },
  mounted() {

  },
  beforeDestroy() {

  }
};

</script>
<style lang="scss">
@import "~components/MdAnimation/variables";
.md-toast-wrap {
  height: 0;
  width: 0;
  max-height: 0px;
}

.md-toast.md-snackbar {
  max-height: initial;
  width: 1000px;
  max-width: 100%;

  .md-snackbar-content {
    position: relative;
  }
  .md-button-lock {
    position: absolute;
    left: -30px;
    top: -18px;
    margin: 0;
  }
  .md-button-close {
    position: absolute;
    right: -10px;
    top: 0;
  }
  .main {
    margin-left: 16px;
    max-height: 400px;
    overflow: auto;
    >p {
      margin-top: 0px;
      word-wrap: break-word;
      word-break: break-all;
    }
  }
}

</style>
