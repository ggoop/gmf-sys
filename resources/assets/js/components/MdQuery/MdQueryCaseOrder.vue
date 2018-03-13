<template>
  <md-layout md-flex="100">
    <md-grid :datas="mdItems" :row-focused="false" :showRemove="true" :showReload="false" :showAdd="true" @onAdd="onItemAdd" @onRemove="onItemRemove" class="flex">>
      <md-grid-column field="id" label="id" :hidden="true"></md-grid-column>
      <md-grid-column field="comment" label="名称"></md-grid-column>
      <md-grid-column label="排序">
        <template slot-scope="row">
          <md-button class="md-icon-button" @click.native="orderFieldSwap(row,$event)">
            <md-icon v-if="row.direction=='desc'">arrow_downward</md-icon>
            <md-icon v-else>arrow_upward</md-icon>
          </md-button>
        </template>
      </md-grid-column>
      <md-grid-column label="..." :isTool="true">
        <template slot-scope="row">
          <md-button class="md-icon-button" @click.native="onItemUp(row,$event)">
            <md-icon>vertical_align_top</md-icon>
          </md-button>
          <md-button class="md-icon-button" @click.native="onItemDown(row,$event)">
            <md-icon>vertical_align_bottom</md-icon>
          </md-button>
        </template>
      </md-grid-column>
    </md-grid>
    <md-query-field :md-active.sync="newItemDialogShow" @confirm="onNewItemConfirm" :md-entity-id="mdEntityId"></md-query-field>
  </md-layout>
</template>
<script>
import commonMixin from './common';
export default {
  name:'mdQueryCaseOrder',
  mixins: [commonMixin],
  methods: {
    formatFieldToItem(field) {
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
      if (event) {
        event.preventDefault && event.preventDefault();
        event.stopPropagation && event.stopPropagation();
      }
      item.direction = item.direction === 'desc' ? 'asc' : 'desc';
    },
  },
  created() {

  },
  mounted() {
    const aa=this.mdItems;
  },
};
</script>