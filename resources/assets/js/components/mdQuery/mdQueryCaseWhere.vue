<template>
  <md-layout md-flex="100">
    <md-table-card class="flex">
      <md-table @select="onItemSelect" class="flex">
        <md-table-header>
          <md-table-row>
            <md-table-head>名称</md-table-head>
            <md-table-head>操作符</md-table-head>
            <md-table-head>条件值</md-table-head>
          </md-table-row>
        </md-table-header>
        <md-table-body>
          <md-table-row v-for="(item, rind) in options.wheres" :key="rind" :md-item="item" :md-auto-select="false" :md-selection="true">
            <md-table-cell>{{ item.comment||item.name}}</md-table-cell>
            <md-table-cell>{{ item.operator_enum}}</md-table-cell>
            <md-table-cell>
              <div class="input">
                <md-input v-if="item.type_enum=='value'" v-model="item.value"></md-input>
                <md-input-ref v-else-if="item.type_enum=='ref'" v-model="item.value" :md-ref-id="item.ref_id"></md-input-ref>
                <md-date v-else-if="item.type_enum=='date'" v-model="item.value"></md-date>
                <md-input v-else v-model="item.value"></md-input>
              </div>
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
        <md-tree-view :nodes="allItems" @focus="focusNewItemNode" @select="selectNewItemNodes">
          
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
    options: Object
  },
  data() {
    return {
      allItems: [],
      selectItems:[]
    }
  },
  methods: {
    onItemSelect(datas) {
      this.selectItems=datas;
    },
    onItemAdd() {
      this.$refs.newItemDialog.open();
    },
    onItemRemove() {

    },

    onNewItemDialogOpen() {

    },
    onNewItemConfirm(){
      this.$refs.newItemDialog.close();
    },
    onNewItemCancel(){
      this.$refs.newItemDialog.close();
    },
    focusNewItemNode(node){

    },
    selectNewItemNodes(nodes){
      
    }
  },
  created() {

  },
  mounted() {},
};
</script>