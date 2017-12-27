<template>
  <md-card>
    <md-card-header>
      <md-card-header-text>
        <div class="md-title">从此浏览器中移除</div>
      </md-card-header-text>
    </md-card-header>
    <md-list>
      <md-list-item v-for="item in mainDatas" :key="item.id">
        <md-avatar>
          <md-image :md-src="item.avatar"></md-image>
        </md-avatar>
        <div class="md-list-item-text">
          {{ item.name }}
        </div>
        <md-button class="md-icon-button md-list-action" @click="removeItem(item)">
          <md-icon>clear</md-icon>
        </md-button>
      </md-list-item>
      <md-list-item :to="{name:'auth.identifier'}">
        <md-avatar>
          <md-icon class="md-size-2x">account_circle</md-icon>
        </md-avatar>
        <div class="md-list-item-text">
          使用其他帐号
        </div>
      </md-list-item>
    </md-list>
    <md-divider></md-divider>
    <md-card-content class="layout layout-align-center-center">
      <md-button :to="{name:'auth.login'}">
        <md-icon>完成</md-icon>
      </md-button>
    </md-card-content>
    <md-progress-bar md-mode="indeterminate" v-if="sending" />
  </md-card>
</template>
<script>
import AuthSns from './Sns';
import authCache from './AuthCache';
export default {
  name: 'AppAuthRemove',
  components: {
    AuthSns
  },
  data() {
    return {
      mainDatas: [],
      sending: false,
    };
  },
  computed: {

  },
  methods: {
    removeItem(item) {
      authCache.remove(item);
    },
    fetchData() {
      this.mainDatas = authCache.get() || [];
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>