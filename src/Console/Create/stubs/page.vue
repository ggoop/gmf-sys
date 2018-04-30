<template>
  <div class="page-container">
    <md-app md-waterfall md-mode="flexible">
      <md-app-toolbar class="md-large md-primary">
        <div class="md-toolbar-row">
          <div class="md-toolbar-section-start">
            <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
              <md-icon>menu</md-icon>
            </md-button>
          </div>
          <div class="md-toolbar-section-end">
            <md-button class="md-icon-button">
              <md-icon>more_vert</md-icon>
            </md-button>
          </div>
        </div>
        <div class="md-toolbar-row md-toolbar-offset">
          <span class="md-display-1">My Title</span>
        </div>
      </md-app-toolbar>
      <md-app-drawer :md-active.sync="menuVisible">
        <md-toolbar class="md-transparent" md-elevation="0">Navigation</md-toolbar>
        <md-list>
          <md-list-item>
            <md-icon>move_to_inbox</md-icon>
            <span class="md-list-item-text">Inbox</span>
          </md-list-item>
          <md-list-item>
            <md-icon>send</md-icon>
            <span class="md-list-item-text">Sent Mail</span>
          </md-list-item>
          <md-list-item>
            <md-icon>delete</md-icon>
            <span class="md-list-item-text">Trash</span>
          </md-list-item>
          <md-list-item>
            <md-icon>error</md-icon>
            <span class="md-list-item-text">Spam</span>
          </md-list-item>
        </md-list>
      </md-app-drawer>
      <md-app-content>
        <p>this is content</p>
        <p>loadInfo:{{ loadInfo }}</p>
        <p>loadText:{{ loadText }}</p>
        <md-button @click="doClick">
          <md-icon>click me</md-icon>
        </md-button>
      </md-app-content>
    </md-app>
  </div>
</template>
<script>
export default {
  name: 'DummyName',
  data: () => ({
    menuVisible: false,
    loadInfo: '',
  }),
  computed: {
    loadText() {
      return this.loadInfo;
    }
  },
  watch: {
    '$route': function() {
      this.fetchData();
    }
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.routeLoadData();
    });
  },
  beforeRouteUpdate(to, from, next) {
    this.routeLoadData();
    next();
  },
  methods: {
    routeLoadData() {
      this.loadInfo += ',routeLoadData';
    },
    doClick() {
      this.loadInfo += ',doClick';
    },
  },
  created() {
    this.loadInfo += ',created';
  },
  mounted() {
    this.loadInfo += ',mounted';
  },
}

</script>
<style lang="scss" scoped>
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdIcon/mixins";
@import "~gmf/components/MdLayout/mixins";
.md-app {
  max-height: 400px;
  border: 1px solid rgba(#000, .12);
}

.md-app-toolbar {
  height: 196px;
} // Demo purposes only
.md-drawer {
  width: 230px;
  max-width: calc(100vw - 125px);
}

</style>
