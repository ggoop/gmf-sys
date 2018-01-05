<template>
  <md-app class="gmf-pages-md" md-waterfall md-mode="fixed">
    <md-app-toolbar class="md-primary">
      <div class="md-toolbar-row">
        <div class="md-toolbar-section-start">
          <md-button class="md-icon-button" @click="goHome">
            <md-icon-home></md-icon-home>
          </md-button>
          <a class="md-title" @click="goIndex">元数据</a>
        </div>
        <form novalidate class="flex search-box" @submit.stop.prevent="fetchSearch">
          <md-autocomplete autocomplete="off" type="search" @md-changed="term_change" @md-selected="fetchSearch" v-model="search_model" :md-options="search_options" md-layout="box">
            <label>Search...</label>
            <template slot="md-autocomplete-item" slot-scope="{ item }">{{ item.title }}</template>
            <template slot="md-autocomplete-empty" slot-scope="{ term }">
              <div @click="fetchSearch(term)">
                <md-ripple>搜索"{{ term }}"!</md-ripple>
              </div>
            </template>
          </md-autocomplete>
        </form>
        <div class="md-toolbar-section-end">
          <md-button class="md-icon-button">
            <md-icon>more_vert</md-icon>
          </md-button>
        </div>
      </div>
    </md-app-toolbar>
    <md-app-content>
      <router-view></router-view>
    </md-app-content>
  </md-app>
</template>
<script>
export default {
  data: () => ({
    search_model: null,
    search_options: []
  }),
  computed: {

  },
  methods: {
    goHome() {
      this.$go(this.$root.configs.home);
    },
    goIndex() {
      this.$go({ name: 'md.home' });
    },
    term_change(term) {
      this.search_model = term;
    },
    fetchSearch(e) {
      const options = { name: 'md.search', params: {} };
      if (e && e.constructor.name == 'Object' && e.title) {
        options.params.q = e.title;
        this.$go(options, true);
      } else if (e && e.constructor.name == 'Event' && this.search_model) {
        options.params.q = this.search_model;
        this.$go(options, true);
      } else if (typeof e === 'string') {
        options.params.q = e;
        this.$go(options, true);
      } else if (this.search_model) {
        options.params.q = this.search_model;
        this.$go(options, true);
      }
    },
  },
  created() {
    if (this.$route.params.q) {
      this.search_model = this.$route.params.q;
    }
  }
}

</script>
<style lang="scss" scoped>
.search-box {
  padding-left: 40px;
  .md-autocomplete {
    max-width: 500px;
  }
}

</style>
