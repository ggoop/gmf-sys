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
              <md-input-container>
                <md-input v-if="item.type_enum=='value'" v-model="item.value"></md-input>
                <md-input-ref v-else-if="item.type_enum=='ref'" v-model="item.value" :md-ref-id="item.ref_id"></md-input-ref>
                <md-enum v-else-if="item.type_enum=='enum'" v-model="item.value" :md-enum-id="item.ref_id"></md-enum>
                <md-date v-else-if="item.type_enum=='date'" v-model="item.value"></md-date>
                <md-input v-else v-model="item.value"></md-input>
              </md-input-container>
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
          item = this.formatFieldToWhere(v);
        this._.forEach(this.options.wheres, (va, ka) => {
          if (va.name == item.name) {
            need = true;
          }
        });
        if (need === false) {
          this.options.wheres.push(item);
        }
      });
      this.$refs.newItemDialog.close();
    },
    onNewItemCancel() {
      this.$refs.newItemDialog.close();
    },
    formatFieldToWhere(field) {
      var item = {
        name: field.path,
        comment: field.path_name,
        type_id: field.type_id,
        type_name: field.type_name,
        type_type: field.type_type,
      };
      if (field.type_type === 'entity') {
        item.type_enum = 'ref';
        item.ref_id = field.type_name + '.ref';
      } else if (field.type_type === 'enum') {
        item.type_enum = 'enum';
        item.ref_id = field.type_name;
      } else if (field.type_type === 'dateTime' ||
        field.type_type === 'date' ||
        field.type_type === 'time' ||
        field.type_type === 'timestamp') {
        item.type_enum = 'date';
      } else {
        item.type_enum = field.type_type;
      }
      return item;
    },
  },
  created() {

  },
  mounted() {},
};
</script>