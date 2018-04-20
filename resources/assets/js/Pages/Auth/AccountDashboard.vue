<template>
  <md-card>
    <md-card-header>
      <md-card-header-text>
        <div class="md-title">我的账号关联</div>
        <div class="md-body-1">{{ userName }}</div>
      </md-card-header-text>
      <md-button class="md-icon-button md-list-action" :to="{name:'auth.account.join',query:routeQuery}">
        <md-icon class="md-primary">expand_more</md-icon>
      </md-button>
    </md-card-header>
    <md-list>
      <md-list-item v-for="item in mainDatas" :key="item.id">
        <md-avatar>
          <md-image :md-src="item.avatar"></md-image>
        </md-avatar>
        <div class="md-list-item-text">
          <span>{{ item.name }}</span>
          <span>{{ item.account }}</span>
        </div>
        <md-button class="md-icon-button md-list-action" @click="removeItem(item)">
          <md-icon>clear</md-icon>
        </md-button>
      </md-list-item>
      <md-list-item :to="{name:'auth.account.join'}">
        <md-avatar>
          <md-icon class="md-size-2x">account_circle</md-icon>
        </md-avatar>
        <div class="md-list-item-text">
          添加账号关联
        </div>
      </md-list-item>
    </md-list>
    <md-card-actions md-alignment="center">
      <router-link to="/">返回首页</router-link>
    </md-card-actions>
    <md-progress-bar md-mode="indeterminate" v-if="sending" />
  </md-card>
</template>
<script>
export default {
  name: 'GmfPagesAuthAccountDashboard',
  components: {

  },
  data() {
    return {
      mainDatas: [],
      loading: 0,
      sending: false,
    };
  },
  computed: {
    routeQuery() {
      const q = {};
      if (this.$route.query && this.$route.query.continue) p.continue = this.$route.query.continue;
      return q;
    },
    userName() {
      return this.$root.configs && this.$root.configs.user && this.$root.configs.user.nick_name;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.fetchData();
    });
  },
  beforeRouteUpdate(to, from, next) {
    this.fetchData();
    next();
  },
  methods: {
    removeItem(item) {
      this.sending = true;
      this.$http.delete('sys/auth/joins', { data: { to_user_id: item.id } }).then(response => {
        this.fetchData();
      }).catch(err => {
        this.$toast(err);
        this.sending = false;
      });
    },
    fetchData() {
      this.$http.get('sys/auth/joins').then(response => {
        this.mainDatas = response.data.data;
        this.sending = false;
      }).catch(err => {
        this.$toast(err);
        this.sending = false;
      });
    },
  },
};

</script>
