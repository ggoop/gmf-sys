<template>
  <md-card>
    <md-card-header>
      <md-card-header-text>
        <div class="md-title">选择您的{{appName}}帐号</div>
      </md-card-header-text>
    </md-card-header>
    <md-list>
      <md-list-item v-for="item in mainDatas" :key="item.id" @click="goItem(item)">
        <md-avatar>
          <md-image :md-src="item.avatar"></md-image>
        </md-avatar>
        <div class="md-list-item-text">
          <span>{{ item.name }}</span>
          <span>{{ item.account }}</span>
        </div>
      </md-list-item>
      <md-list-item :to="{name:'auth.identifier',query:{continue:$route.query.continue}}">
        <md-avatar>
          <md-icon class="md-size-2x">account_circle</md-icon>
        </md-avatar>
        <div class="md-list-item-text">
          <span>使用其他{{appName}}帐号</span>
          <span>使用新的{{appName}}账号登录</span>
        </div>
      </md-list-item>
    </md-list>
    <md-divider></md-divider>
    <auth-sns></auth-sns>
    <template v-if="mainDatas&&mainDatas.length">
      <md-divider></md-divider>
      <md-card-content>
        <md-button :to="{name:'auth.remove',query:{continue:$route.query.continue}}">
          移除账号
        </md-button>
      </md-card-content>
    </template>
    <md-progress-bar md-mode="indeterminate" v-if="sending" />
  </md-card>
</template>
<script>
import AuthSns from "./Sns";
import authCache from "./AuthCache";

export default {
  name: "AppAuthChooser",
  components: {
    AuthSns
  },
  data() {
    return {
      mainDatas: [],
      sending: false
    };
  },
  computed: {
    routeQuery() {
      const q = {};
      if (this.$route.query && this.$route.query.continue)
        q.continue = this.$route.query.continue;
      return q;
    },
    appName() {
      return this.$root.appName ? " " + this.$root.appName + " " : "";
    }
  },
  methods: {
    removeItem(item) {},
    goItem(item) {
      this.sending = true;
      this.$http
        .post("sys/auth/checker", item)
        .then(response => {
          this.sending = false;
          const u = response.data.data;
          if (u) {
            this.$go({
              name: "auth.password",
              params: { id: u.id },
              query: this.routeQuery
            });
          }
        })
        .catch(err => {
          this.sending = false;
          this.$toast(err);
        });
    },
    fetchData() {
      this.mainDatas = authCache.get() || [];
    }
  },
  mounted() {
    this.fetchData();
  }
};
</script>
