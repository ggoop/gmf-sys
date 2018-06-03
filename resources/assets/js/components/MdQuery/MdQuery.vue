<template>
  <div class="layout layout-full flex md-query">
    <md-grid :title="gridTitle" :autoLoad="!!mdQueryId" ref="grid" :datas="fetchData" :multiple="multiple" showQuery showDownload @select="onSelected" @dblclick="dblclick" @onQuery="openQueryCase">
    </md-grid>
    <md-query-case :md-query-id="mdQueryId" :md-active.sync="showCase" @init="initQueryCase" @query="queryQueryCase">
    </md-query-case>
    <md-loading :loading="loading"></md-loading>
  </div>
</template>
<script>
import common from 'gmf/core/utils/common';
import extend from 'lodash/extend'
import remove from 'lodash/remove'
export default {
  props: {
    mdQueryId: String,
    title: { type: String },
    multiple: {
      type: Boolean,
      default: true
    },
    options: {
      type: Object,
      default () {
        return {
          wheres: {},
          orders: []
        }
      }
    },
    mdInit: { type: Function },
  },
  computed: {
    gridTitle() {
      if (this.title) return this.title;
      if (this.refInfo) return this.refInfo.comment;
      return '';
    },
  },
  data() {
    return {
      selectedRows: [],
      refInfo: {},
      loading: 0,
      showCase:false,
      caseModel: {}
    };
  },
  methods: {
    openQueryCase() {
      this.showCase=true;
    },
    initQueryCase(options, promise) {
      promise && promise.resolve(true);
    },
    queryQueryCase(caseModel) {
      this.caseModel = caseModel;
      this.pagination();
    },
    onSelected({ data }) {
      this.selectedRows = [];
      Object.keys(data).forEach((row, index) => {
        this.selectedRows[index] = data[row];
      });
      this.$emit('select', this.selectedRows);
    },
    formatFieldToColumn(field) {
      return {
        field: field.alias,
        label: field.comment,
        hidden: field.alias == 'id' || field.hide || field.hidden
      };
    },
    pagination(page) {
      this.selectedRows = [];
      this.$refs.grid && this.$refs.grid.refresh();
    },
    async fetchData({ pager, filter, sort }) {
      this.loading++;
      var options = extend({}, { q: filter }, this.options, this.caseModel, pager);
      if (options.orders && sort && sort.field) {
        options.orders.length && remove(options.orders, function(n) {
          return n.name === sort.field;
        });
        options.orders.splice && options.orders.splice(0, 0, { name: sort.field, direction: sort.order });
      }
      this.mdInit&&this.mdInit(options);
      const response = await this.$http.post('sys/queries/query/' + this.mdQueryId, options);

      this.refInfo = response.data.schema;
      this.$refs.grid && this.$refs.grid.setColumns(this.refInfo.fields.map(col => this.formatFieldToColumn(col)));

      this.loading--;
      return response;
    },
    dblclick({ data }) {
      this.$emit('dblclick', data);
    },
  },
  mounted() {
  },
};

</script>
<style lang="scss">
.md-query {}

</style>
