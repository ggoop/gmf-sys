<template>
  <md-layout md-flex="100">
    <md-grid :datas="mdItems" ref="grid" :showRemove="true" :showReload="false" :showAdd="true" @select="onItemSelect" @onAdd="onItemAdd" class="flex">>
      <md-grid-column field="id" label="id" :hidden="true"></md-grid-column>
      <md-grid-column field="comment" label="名称"></md-grid-column>
      <md-grid-column field="operator_enum" label="操作符"></md-grid-column>
      <md-grid-column label="条件值">
        <template scope="row">
          <md-input-container>
            <md-input v-if="row.type_enum=='value'" v-model="row.value"></md-input>
            <md-input-ref v-else-if="row.type_enum=='ref'" v-model="row.value" :md-ref-id="row.ref_id"></md-input-ref>
            <md-enum v-else-if="row.type_enum=='enum'" v-model="row.value" :md-enum-id="row.ref_id"></md-enum>
            <md-date v-else-if="row.type_enum=='date'" v-model="row.value"></md-date>
            <md-input v-else v-model="row.value"></md-input>
          </md-input-container>
        </template>
      </md-grid-column>
    </md-grid>
    <md-dialog ref="newItemDialog">
      <md-toolbar>
        <h1 class="md-title">选择更多内容</h1>
      </md-toolbar>
      <md-dialog-content class="no-padding layout-column layout-fill">
        <md-query-field ref="onNewItemTree" :md-entity-id="mdEntityId"></md-query-field>
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
import commonMixin from './common';
export default {
  mixins: [commonMixin],
  methods: {
    formatFieldToItem(field) {
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