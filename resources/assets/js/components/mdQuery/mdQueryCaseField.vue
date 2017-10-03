<template>
  <md-layout md-flex="100">
    <md-table-card class="flex">
      <md-table @select="onItemSelect" class="flex">
        <md-table-header>
          <md-table-row>
            <md-table-head>名称</md-table-head>
            <md-table-head>显示名称</md-table-head>
          </md-table-row>
        </md-table-header>
        <md-table-body>
          <md-table-row v-for="(item, rind) in options.fields" :key="rind" :md-item="item" :md-auto-select="false" :md-selection="true">
            <md-table-cell>{{ item.comment||item.name}}</md-table-cell>
            <md-table-cell>{{ item.comment||item.name}}</md-table-cell>
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
      <md-toolbar>
        <h1 class="md-title">选择更多内容</h1>
      </md-toolbar>
      <md-dialog-content class="no-padding layout-column layout-fill">
        <md-tree-view :nodes="allItems" @focus="focusNewItemNode">
        </md-tree-view>
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
    options: {
      type: Object
    }
  },
  data() {
    return {
      allItems: [],
      selectItems: []
    }
  },
  methods: {
    loadAllNodes(entity_id, nodes) {
      if (!entity_id) {
        return;
      }
      this.$http.get('sys/entities/' + entity_id).then(response => {
        this._.forEach(response.data.data.fields, (v, k) => {
          var item = {
            field: v.name,
            name: v.comment || v.name,
            type_id: v.type.id,
            type_name: v.type.name,
            childs: []
          };
          nodes.push(item);
        });
      }, response => {});
    },
    onItemSelect(datas) {
      this.selectItems = datas;
    },
    onItemAdd() {
      this.$refs.newItemDialog.open();
    },
    onItemRemove() {

    },
    onNewItemDialogOpen() {
      this.allItems.splice(0, this.allItems.length);
      this.loadAllNodes(this.options.entity_id,this.allItems);
    },
    onNewItemConfirm() {
      var selectedItems = this.$refs.newItemDialog.selecteds;
      this.$refs.newItemDialog.close();
    },
    onNewItemCancel() {
      this.$refs.newItemDialog.close();
    },
    focusNewItemNode(node) {
      if(node.childs.length==0){
        this.loadAllNodes(node.type_id,node.childs);
      }
    }
  },
  created() {

  },
  mounted() {},
};
</script>