<template>
  <md-layout md-flex="100">
    <md-table-card class="flex">
      <md-table @select="onItemSelect" class="flex">
        <md-table-header>
          <md-table-row>
            <md-table-head>名称</md-table-head>
            <md-table-head>排序</md-table-head>
          </md-table-row>
        </md-table-header>
        <md-table-body>
          <md-table-row v-for="(item, rind) in options.orders" :key="rind" :md-item="item" :md-auto-select="false" :md-selection="true">
            <md-table-cell>{{ item.comment||item.name}}</md-table-cell>
            <md-table-cell>
              <md-button class="md-icon-button" @click.native="orderFieldSwap(item,$event)">
                <md-icon v-if="item.direction=='desc'">arrow_downward</md-icon>
                <md-icon v-else>arrow_upward</md-icon>
              </md-button>
            </md-table-cell>
          </md-table-row>
        </md-table-body>
      </md-table>
      <md-table-tool>
        <md-button class="md-icon-button" @click.native="onItemAdd()">
          <md-icon>add</md-icon>
        </md-button>
        <md-button class="md-icon-button" @click.native="onItemRemove()">
          <md-icon>clear</md-icon>
        </md-button>
        <span class="flex"></span>
      </md-table-tool>
    </md-table-card>
    <md-dialog ref="newItemDialog" @open="onNewItemDialogOpen">
      <md-dialog-content class="no-padding layout-column layout-fill">
      </md-dialog-content>
    </md-dialog>
  </md-layout>
</template>
<script>
export default {
  props: {
    options: Object
  },
  data() {
    return {
      allItems: [],
    }
  },
  methods: {
    onItemSelect(datas) {

    },
    onItemAdd() {
      this.$refs.newItemDialog.open();
    },
    onNewItemDialogOpen() {

    },
    onItemRemove() {

    },
    orderFieldSwap(item, event) {
      item.direction = item.direction === 'desc' ? 'asc' : 'desc';
    }
  },
  created() {

  },
  mounted() {},
};
</script>