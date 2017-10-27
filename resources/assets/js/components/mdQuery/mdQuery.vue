<template>
  <div class="layout layout-full flex md-query">
    <md-grid :title="gridTitle" ref="grid" :datas="fetchData" :multiple="multiple" showQuery showDownload @select="onSelected" @dblclick="dblclick" @onQuery="openQueryCase">
    </md-grid>
    <md-query-case :md-query-id="mdQueryId" ref="queryCase" @init="initQueryCase" @query="queryQueryCase">
    </md-query-case>
    <md-loading :loading="loading"></md-loading>
  </div>
</template>
<script>
import theme from '../../core/components/mdTheme/mixin';
import common from '../../core/utils/common';

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
          orders: {}
        }
      }
    }
  },
  mixins: [theme],
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
      caseModel: {}
    };
  },
  methods: {
    openQueryCase() {
      this.$refs.queryCase.open();
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
      this.$refs.grid.refresh();
    },
    async fetchData({ pager, filter, sort }) {
      this.loading++;
      var options = this._.extend({}, { q: filter }, this.options, this.caseModel, pager);

      this.$emit('init', options);
      const response = await this.$http.post('sys/queries/query/' + this.mdQueryId, options);

      this.refInfo = response.data.schema;
      this.$refs.grid.setColumns(this.refInfo.fields.map(col => this.formatFieldToColumn(col)));

      this.loading--;
      return response;
    },
    dblclick({ data }) {
      this.$emit('dblclick', data);
    },
  },
  mounted() {
    if (this.mdQueryId) {
      this.pagination();
    }
  },
};
</script>