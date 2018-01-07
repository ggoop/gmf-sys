<template>
  <md-layout md-flex="100">
    <md-grid :datas="mdItems" :row-focused="false" :showRemove="true" :showReload="false" :showAdd="true" @onAdd="onItemAdd" @onRemove="onItemRemove" class="flex">>
      <md-grid-column field="id" label="id" :hidden="true"></md-grid-column>
      <md-grid-column field="comment" label="名称"></md-grid-column>
      <md-grid-column field="operator_enum" label="操作符" width="100px">
        <template slot-scope="row">
          <md-field>
            <md-enum v-model="row.operator_enum" :items="row.operators" md-enum-id="gmf.sys.query.operator.enum"></md-enum>
          </md-field>
        </template>
      </md-grid-column>
      <md-grid-column label="条件值" width="200px">
        <template slot-scope="row">
          <template v-if="row.operator_enum&&row.operator_enum.indexOf('null')>=0">
          </template>
          <template v-else-if="row.operator_enum&&row.operator_enum.indexOf('like')>=0">
            <md-field>
              <md-input v-model="row.value"></md-input>
            </md-field>
          </template>
          <template v-else-if="row.operator_enum&&row.operator_enum.indexOf('between')>=0">
            <template v-if="row.type_enum=='date'">
                <md-datepicker v-model="row.value"></md-datepicker>
            </template>
            <template v-else>
              <md-field>
                <md-input v-model="row.value"></md-input>
              </md-field>
            </template>
          </template>
          <template v-else>
            <template v-if="row.type_enum=='value'">
              <md-field>
                <md-input v-model="row.value"></md-input>
              </md-field>
            </template>
            <template v-else-if="row.type_enum=='ref'">
              <md-ref-input v-model="row.value" :md-ref-id="row.ref_id"></md-ref-input>
            </template>
            <template v-else-if="row.type_enum=='enum'">
              <md-field>
                <md-enum v-model="row.value" :md-enum-id="row.ref_id"></md-enum>
              </md-field>
            </template>
            <template v-else-if="row.type_enum=='date'">
              <md-datepicker v-model="row.value"></md-datepicker>
            </template>
            <template v-else>
              <md-field>
                <md-input v-model="row.value"></md-input>
              </md-field>
            </template>
          </template>
        </template>
      </md-grid-column>
      <md-grid-column label="" width="200px">
        <template slot-scope="row">
          <template v-if="row.operator_enum&&row.operator_enum.indexOf('between')>=0">
            <template v-if="row.type_enum=='date'">
              <md-datepicker v-model="row.value2"></md-datepicker>
            </template>
            <template v-else>
              <md-field>
                <md-input v-model="row.value2"></md-input>
              </md-field>
            </template>
          </template>
        </template>
      </md-grid-column>
    </md-grid>
    <md-query-field :md-active.sync="newItemDialogShow" @confirm="onNewItemConfirm" :md-entity-id="mdEntityId"></md-query-field>
  </md-layout>
</template>
<script>
import commonMixin from './common';
export default {
  name:'mdQueryCaseWhere',
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
      } else if (field.type_type === 'number' ||
        field.type_type === 'integer' ||
        field.type_type === 'bigInteger' ||
        field.type_type === 'float' ||
        field.type_type === 'decimal') {
        item.type_enum = 'number';
      } else {
        item.type_enum = 'string';
      }
      if (item.type_enum === 'string') {
        item.operator_enum = 'like';
      } else if (item.type_enum === 'date') {
        item.operator_enum = 'greater_than_equal';
      } else {
        item.operator_enum = 'equal';
      }
      this.initOperators(item)
      return item;
    },
    initOperators(item) {
      if (item.type_enum === 'enum' || item.type_enum === 'ref') {
        item.operators = ['equal', 'not_equal', 'in', 'not_in', 'null', 'not_null'];
      } else if (item.type_enum === 'ref') {
        item.operators = ['equal', 'not_equal', 'in', 'not_in', 'null', 'not_null'];
      } else if (item.type_enum == 'date') {
        item.operators = ['equal', 'not_equal',
          'greater_than_equal', 'less_than_equal',
          'between', 'null', 'not_null'
        ];
      } else if (item.type_enum == 'number') {
        item.operators = ['equal', 'not_equal',
          'greater_than', 'less_than', 'greater_than_equal', 'less_than_equal',
          'between', 'null', 'not_null'
        ];
      } else {
        item.operators = ['equal', 'not_equal',
          'greater_than_equal', 'less_than_equal',
          'between', 'null', 'not_null',
          'like', 'left_like', 'right_like', 'not_like'
        ];
      }
    }
  },
  created() {

  },
  mounted() {
    const aa=this.mdItems;
  },
};
</script>