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
        <md-button class="md-icon-button" @click.native="onItemRemove()">
          <md-icon>clear</md-icon>
        </md-button>
        <span class="flex"></span>
      </md-table-tool>
      <md-button class="md-fab md-mini md-fab-bottom-right" @click.native="onItemAdd()">
        <md-icon>add</md-icon>
      </md-button>
    </md-table-card>
    <md-dialog ref="newItemDialog">
      <md-toolbar>
        <h1 class="md-title">选择更多内容</h1>
      </md-toolbar>
      <md-dialog-content class="no-padding layout-column layout-fill">
        <md-query-field ref="onNewItemTree" :md-entity-id="options.entity_id"></md-query-field>
      </md-dialog-content>
      <md-dialog-actions>
        <span class="flex"></span>
        <md-button class="md-accent md-raised" @click.native="onNewItemConfirm">确定</md-button>
        <md-button class="md-warn" @click.native="onNewItemCancel">取消</md-button>
      </md-dialog-actions>
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
      selectItems: []
    }
  },
  methods: {
    onItemSelect(datas) {
      this.selectItems = datas;
    },
    onItemAdd() {
      this.$refs.newItemDialog.open();
    },
    onItemRemove() {

    },
    onNewItemConfirm() {
      var selectedItems = this.$refs.onNewItemTree.getItems();
      this._.forEach(selectedItems, (v, k) => {
        var need = false,
          item = this.formatFieldToOrder(v);
        this._.forEach(this.options.orders, (va, ka) => {
          if (va.name == item.name) {
            need = true;
          }
        });
        if (need === false) {
          this.options.orders.push(item);
        }
      });
      this.$refs.newItemDialog.close();
    },
    onNewItemCancel() {
      this.$refs.newItemDialog.close();
    },
    formatFieldToOrder(field) {
      return {
        name: field.path,
        comment: field.path_name,
        type_id: field.type_id,
        type_name: field.type_name,
        type_type: field.type_type,
        direction: 'desc'
      };
    },

    orderFieldSwap(item, event) {
      item.direction = item.direction === 'desc' ? 'asc' : 'desc';
    },
  },
  created() {

  },
  mounted() {},
};
</script>