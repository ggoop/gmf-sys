<template>
  <md-layout md-flex="100">
    <md-grid :datas="mdItems" ref="grid" :showRemove="true" :showReload="false" :showAdd="true" @select="onItemSelect" @onAdd="onItemAdd" class="flex">>
      <md-grid-column field="id" label="id" :hidden="true"></md-grid-column>
      <md-grid-column field="comment" label="名称"></md-grid-column>
      <md-grid-column field="comment" label="显示名称"></md-grid-column>
      <md-grid-column label="..." :isTool="true">
        <template scope="row">
          <md-button class="md-icon-button" @click.native="onItemUp(row,$event)">
            <md-icon>vertical_align_top</md-icon>
          </md-button>
          <md-button class="md-icon-button" @click.native="onItemDown(row,$event)">
            <md-icon>vertical_align_bottom</md-icon>
          </md-button>
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
      return {
        name: field.path,
        comment: field.path_name,
        type_id: field.type_id,
        type_name: field.type_name,
        type_type: field.type_type,
      };
    },
  },
  created() {

  },
  mounted() {},
};
</script>