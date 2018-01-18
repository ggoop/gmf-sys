<template>
  <md-card-content class="login-sns" v-if="canSns">
    <div class="md-body-1">使用合作账号登录</div>
    <div class="layout-row">
      <md-avatar class="md-avatar-icon">
        <md-ripple>
          <md-icon md-src="/assets/vendor/gmf-sys/svg/auth-qq.svg"></md-icon>
        </md-ripple>
      </md-avatar>
      <md-avatar class="md-avatar-icon">
        <md-ripple>
          <md-icon md-src="/assets/vendor/gmf-sys/svg/auth-weixin.svg"></md-icon>
        </md-ripple>
      </md-avatar>
      <md-avatar class="md-avatar-icon">
        <md-ripple>
          <md-icon md-src="/assets/vendor/gmf-sys/svg/auth-weibo.svg"></md-icon>
        </md-ripple>
      </md-avatar>
    </div>
  </md-card-content>
</template>
<script>
export default {
  name: 'GmfPagesAuthSns',
  data() {
    return {
      sending: false,
    };
  },
  computed: {
    canSns(){
      if(!this.$root.configs.auth||!this.$root.configs.auth.sns)return false;
      return !!this.$root.configs.auth.sns;
    },
  },
  methods: {
    fetchData() {

    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>
<style lang="scss" scoped>
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
.login-sns {
  .md-avatar-icon {
    margin: 10px 16px 0px 0px;
  }
}
</style>