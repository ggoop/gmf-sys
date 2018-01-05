<template>
  <md-app class="gmf-pages-md" md-waterfall md-mode="fixed">
    <md-app-toolbar class="md-primary">
      <div class="md-toolbar-row">
        <div class="md-toolbar-section-start">
          <md-button class="md-icon-button">
            <md-icon-home></md-icon-home>
          </md-button>
          <span class="md-title">元数据</span>
        </div>
        <md-autocomplete class="search" v-model="selectedEmployee" :md-options="employees" md-layout="box">
          <label>Search...</label>
        </md-autocomplete>
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
    selectedEmployee: null,
    employees:[]
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
@import "~gmf/components/MdLayout/mixins";
.gmf-pages-md {
  min-height: 100%;
  min-width: 100%;
}

</style>
<style lang="scss" scoped>
  .search {
    max-width: 500px;
  }
</style>