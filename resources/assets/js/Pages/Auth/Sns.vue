<template>
  <md-card-content class="login-sns" v-if="canSns">
    <div class="md-subheading">{{ title }}</div>
    <div class="layout-row layout-align-center-center">
      <md-button class="md-icon-button md-raised md-primary" v-if="snsQQ" :href="snsQQ">
        <md-icon md-src="/assets/vendor/gmf-sys/svg/auth-qq.svg"></md-icon>
      </md-button>
      <md-button class="md-icon-button md-raised md-primary" v-if="snsWeixin" :href="snsWeixin">
        <md-icon md-src="/assets/vendor/gmf-sys/svg/auth-weixin.svg"></md-icon>
      </md-button>
      <md-button class="md-icon-button md-raised md-primary" v-if="snsWeibo" :href="snsWeibo">
        <md-icon md-src="/assets/vendor/gmf-sys/svg/auth-weibo.svg"></md-icon>
      </md-button>
    </div>
  </md-card-content>
</template>
<script>
export default {
  name: 'GmfPagesAuthSns',
  props: {
    title: {
      type: String,
      default: '使用合作账号登录'
    },
    type: {
      type: String,
      default: 'login'
    }
  },
  data() {
    return {
      sending: false,
    };
  },
  computed: {
    canSns() {
      if (!this.$root.configs.auth || !this.$root.configs.auth.sns) return false;
      return this.$root.configs.auth.sns;
    },
    snsQQ() {
      if (!this.canSns) return false;
      return this.makeUrl(this.$root.configs.auth.sns.qq);
    },
    snsWeixin() {
      if (!this.canSns) return false;
      return this.makeUrl(this.$root.configs.auth.sns.weixin);
    },
    snsWeibo() {
      if (!this.canSns) return false;
      return this.makeUrl(this.$root.configs.auth.sns.weibo);
    }
  },
  methods: {
    fetchData() {

    },
    makeUrl(old) {
      if (old && this.type) {
        return old.indexOf('?') > 0 ? old + '&type=' + this.type : old + '?type=' + this.type;
      } else if (old) {
        return old;
      }
      return false;
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
  text-align: center;
  .md-icon-button {
    margin: 20px 36px 0px 0px;
    min-width: auto;
    box-shadow: none;
    width: 50px;
    height: 50px;
    .md-icon {
      transition: all .345s;
      width: 30px;
      height: 30px;
      font-size: 30px;
    }
    &:hover {
      .md-icon {
        transform: scale(1.3);
      }
    }
  }
}

</style>
