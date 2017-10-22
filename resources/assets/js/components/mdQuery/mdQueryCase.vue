<template>
  <div class="md-query-case">
    <slot></slot>
    <md-dialog ref="caseDialog" :md-click-outside-to-close="false" @open="onOpen" @close="onClose" class="md-query-case-dialog">
      <md-dialog-content class="no-padding layout-column layout-fill">
        <md-tabs class="md-accent layout-column layout-fill flex" :md-swipeable="true" md-right :md-dynamic-height="false">
          <md-tab md-label="条件" md-icon="filter_list" v-if="mdShowWhere">
            <md-layout md-gutter class="layout-fill">
              <md-query-case-where :md-entity-id="options.entity_id" :md-items="options.wheres"></md-query-case-where>
            </md-layout>
          </md-tab>
          <md-tab md-label="栏目" md-icon="more_horiz" v-if="mdShowField">
            <md-layout md-gutter class="layout-fill">
              <md-query-case-field :md-entity-id="options.entity_id" :md-items="options.fields"></md-query-case-field>
            </md-layout>
          </md-tab>
          <md-tab md-label="排序" md-icon="sort" v-if="mdShowOrder">
            <md-layout md-gutter class="layout-fill">
              <md-query-case-order :md-entity-id="options.entity_id" :md-items="options.orders"></md-query-case-order>
            </md-layout>
          </md-tab>
        </md-tabs>
      </md-dialog-content>
      <md-dialog-actions>
        <md-button class="md-warn" @click.native="cleanCase">不使用方案查询</md-button>
        <span class="flex"></span>
        <md-button class="md-accent md-raised" @click.native="query">确定</md-button>
        <md-button class="md-warn" @click.native="cancel">取消</md-button>
      </md-dialog-actions>
      <md-loading :loading="loading"></md-loading>
    </md-dialog>
  </div>
</template>
<script>
export default {
  props: {
    mdQueryId: String,
    mdCaseId: String,
    mdShowWhere: {
      type: Boolean,
      default: true
    },
    mdShowField: {
      type: Boolean,
      default: true
    },
    mdShowOrder: {
      type: Boolean,
      default: true
    },
  },
  data() {
    return {
      inited: false,
      loading: 0,
      options: {
        name: '',
        comment: '',
        type_enum: '',
        size: 0,
        wheres: [],
        fields: [],
        orders: []
      }
    }
  },
  methods: {
    init() {
      if (this.inited) {
        return;
      }
      this.loading++;
      this.$http.get('sys/queries/' + this.mdQueryId).then(response => {
        this.options.size = response.data.data.size;
        this.options.name = response.data.data.name;
        this.options.comment = response.data.data.comment;
        this.options.type_enum = response.data.data.type_enum;
        this.options.entity_id = response.data.data.entity_id;
        this.options.entity_name = response.data.data.entity_name;
        this.options.entity_comment = response.data.data.entity_comment;
        this.options.filter = response.data.data.filter;

        this.options.wheres = response.data.data.wheres;
        this.options.orders = response.data.data.orders;
        this.options.fields = response.data.data.fields;

        const promise = new Promise((resolve, reject) => {
          this.$emit('init', this.options, { resolve, reject });
        });
        promise.then((value) => {
          this.inited = true;
          this.loading--;
        }, (reason) => {
          this.inited = false;
          this.loading--;
        });
      }, response => {
        this.loading--;
      });
    },
    query() {
      var caseModel = this.getQueryCase();
      this.$emit('query', caseModel);
      this.$refs.caseDialog.close();
    },
    open() {
      this.init();
      this.$refs.caseDialog.open();
    },
    cancel() {
      this.$emit('cancel', this.options);
      this.$refs.caseDialog.close();
    },
    onOpen() {
      this.$emit('open', this.options);
    },
    onClose() {
      this.$emit('close', this.options);
    },
    cleanCase() {
      var caseModel = this.getEmptyCase();
      this.$emit('query', caseModel);
      this.$refs.caseDialog.close();
    },
    getEmptyCase() {
      var qc = {
        size: this.options.size,
        name: this.options.name,
        comment: this.options.comment,
        type_enum: this.options.type_enum,
        wheres: { case: { items: [], boolean: 'and' } },
        orders: [],
        fields: []
      };
      return qc;
    },
    getQueryCase() {
      var qc = this.getEmptyCase();
      this._.each(this.options.wheres, (v) => {
        if (v && (!v.sys_deleted)) {
          var item = this.formatCaseWhereItem(v);
          if (item) qc.wheres.case.items.push(item);
        }
      });
      this._.each(this.options.orders, (v) => {
        if (v && (!v.sys_deleted)) {
          qc.orders.push({ name: v.name, direction: v.direction, comment: v.comment });
        }
      });
      this._.each(this.options.fields, (v) => {
        if (v && (!v.sys_deleted)) {
          qc.fields.push({ name: v.name, comment: v.comment });
        }
      });
      return qc;
    },
    formatCaseWhereItem(where) {
      var has = false;
      var whereItem = {
        name: where.name,
        comment: where.comment,
        operator: where.operator_enum
      };
      whereItem.value = this.getCaseWhereValue(where);
      if (whereItem.value === false) { return false; }
      return whereItem;
    },
    getCaseWhereValue(where) {
      if (where.operator_enum.indexOf('null') >= 0) {
        return '';
      }
      if (!where.value) {
        return false;
      }
      if (where.operator_enum === 'between') {
        if (where.value && where.value2) {
          var values = [];
          if (where.type_type == 'ref') {
            values.push(getRefWhereItemValue(where, where.value));
            values.push(getRefWhereItemValue(where, where.value2));
          } else {
            values.push(where.value);
            values.push(where.value2);
          }
          return values[0] !== false && values[1] !== false ? values : false;
        }
      } else {
        if (where.type_type == 'ref') {
          return this.getRefWhereItemValue(where, where.value);
        } else {
          return where.value;
        }
      }
      return false;
    },
    getRefWhereItemValue(where, values) {
      if (values === false) return false;
      var valueField = 'id',
        temp = false,
        value = false;
      if (where.refs && where.refs.valueField) {
        valueField = where.refs.valueField;
      }
      if (where.multiple && values && values.length > 0) {
        value = [];
        for (var i = 0; i < values.length; i++) {
          temp = values[i][valueField];
          if (temp !== '' && temp !== undefined && temp !== false) {
            value.push(temp);
          }
        }
      } else if (values && values[valueField]) {
        value = values[valueField];
      }
      return value;
    },
  },
  created() {

  },
  mounted() {},
};
</script>