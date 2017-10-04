<template>
  <md-layout md-flex="100">
    <md-table-card class="flex">
      <md-table @select="onItemSelect" class="flex">
        <md-table-header>
          <md-table-row>
            <md-table-head>名称</md-table-head>
            <md-table-head>显示名称</md-table-head>
            <md-table-head></md-table-head>
          </md-table-row>
        </md-table-header>
        <md-table-body>
          <md-table-row v-for="(item, rind) in mdItems" :key="rind" :md-item="item" :md-auto-select="false" :md-selection="true">
            <md-table-cell>{{ item.comment||item.name}}</md-table-cell>
            <md-table-cell>{{ item.comment||item.name}}</md-table-cell>
            <md-table-cell :md-is-tool="true">
              <md-button class="md-icon-button" @click.native="onItemUp(item,rind)">
                <md-icon>vertical_align_top</md-icon>
              </md-button>
              <md-button class="md-icon-button" @click.native="onItemDown(item,rind)">
                <md-icon>vertical_align_bottom</md-icon>
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