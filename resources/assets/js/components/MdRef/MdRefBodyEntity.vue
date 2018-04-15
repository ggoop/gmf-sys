<template>
  <div class="layout flex md-ref-body-entity">
    <md-grid :auto-select="true" :auto-load="false" ref="grid" :datas="fetchData" :multiple="multiple" showConfirm showCancel showQuery showDownload @select="onSelected" @dblclick="dblclick" @onQuery="openQueryCase" @onConfirm="onConfirm" @onCancel="onCancel">
    </md-grid>
    <md-query-case v-if="canQueryCaseOpen" :md-query-id="mdRefId" :md-active.sync="showQueryCase" @init="initQueryCase" @query="queryQueryCase">
    </md-query-case>
  </div>
</template>
<script>
import common from 'gmf/core/utils/common';
import MdRefMixin from 'gmf/core/mixins/MdRef/MdRef';
import extend from 'lodash/extend'
import remove from 'lodash/remove'
export default {
  name: 'MdRefBodyEntity',
  mixins: [MdRefMixin],
  data() {
    return {
      caseModel: {},
      canQueryCaseOpen: false,
      showQueryCase: false,
    };
  },
  methods: {
    openQueryCase() {
      this.canQueryCaseOpen = true;
      this.$nextTick(() => {
        this.showQueryCase = true;
      });
    },
    initQueryCase(options, promise) {
      promise && promise.resolve(true);
    },
    loadData(q){
      this.pagination();
    },
    queryQueryCase(caseModel) {
      this.caseModel = caseModel;
      this.pagination();
    },
    formatFieldToColumn(field) {
      return {
        field: field.alias,
        label: field.comment,
        hidden: field.alias == 'id' || field.hide || field.hidden
      };
    },
    pagination() {
      this.selectedRows = [];
      this.$refs.grid && this.$refs.grid.refresh();
    },
    async fetchData({ pager, filter, sort }) {
      var options = extend({}, { q: this.mdQ }, this.options, this.caseModel, pager);
      if (options.orders && sort && sort.field) {
        options.orders.length && remove(options.orders, function(n) {
          return n.name === sort.field;
        });
        options.orders.splice && options.orders.splice(0, 0, { name: sort.field, direction: sort.order });
      }
      const response = await this.$http.post('sys/queries/query/' + this.mdRefId, options);

      const schema = response.data.schema;
      this.$refs.grid && this.$refs.grid.setColumns(schema.fields.map(col => this.formatFieldToColumn(col)));
      return response;
    },
    onSelected({ data }) {
      if (!this.canFireEvents) return;
      this.selectedRows = [];
      Object.keys(data).forEach((row, index) => {
        this.selectedRows[index] = data[row];
      });
      this.$emit('select', this.selectedRows);
    },
    dblclick({ data }) {
      this.selectedRows = [data];
      this.onConfirm();
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.canFireEvents = true;
    });
  },
};

</script>
<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
.md-ref-body-entity {
  @include md-layout-xsmall {

    .md-pagination {
      display: none;
    }
  }
}

</style>
