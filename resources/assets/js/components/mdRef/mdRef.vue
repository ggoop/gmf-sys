<template>
  <md-dialog :md-active.sync="showDialog" :md-click-outside-to-close="false" @md-opened="onRefOpen" @md-closed="onRefClose" class="md-refs-dialog">
    <md-toolbar md-elevation="0" class="md-primary">
      <div class="md-toolbar-row">
        <div class="md-toolbar-section-start">
          <h1 class="md-title">{{refInfo.comment}}</h1>
        </div>
        <md-fetch class="search" :fetch="autoFetch"></md-fetch>
        <div class="md-toolbar-section-end">
          <md-button class="md-icon-button md-dialog-button-close" @click.native="onCancel()">
            <md-icon>close</md-icon>
          </md-button>
        </div>
      </div>
    </md-toolbar>
    <md-dialog-content class="no-padding layout flex">
      <md-grid :auto-select="true" ref="grid" :datas="fetchData" :multiple="multiple" showConfirm showCancel showQuery showDownload @select="onSelected" @dblclick="dblclick" @onQuery="openQueryCase" @onConfirm="onConfirm" @onCancel="onCancel">
      </md-grid>
    </md-dialog-content>
    <md-loading :loading="loading"></md-loading>
    <md-query-case v-if="canQueryCaseOpen" :md-query-id="mdRefId" ref="queryCase" @init="initQueryCase" @query="queryQueryCase">
    </md-query-case>
  </md-dialog>
</template>
<script>
import common from 'core/utils/common';

export default {
  name: 'MdRef',
  props: {
    value: {
      type: Object,
    },
    multiple: {
      type: Boolean,
      default: true
    },
    mdRefId: String,
    options: {
      type: Object,
      default () {
        return {
          wheres: {},
          orders: []
        }
      }
    }
  },
  data() {
    return {
      showDialog: false,
      currentQ: '',
      autoquery: true,
      selectedRows: [],
      refInfo: {},
      loading: 0,
      caseModel: {},
      canQueryCaseOpen: false
    };
  },
  watch: {
    value(value) {
      if (!value) {
        this.selectedRows = [];
      } else {
        if (common.isArray(value)) {
          this.selectedRows = value;
        } else {
          this.selectedRows = [value];
        }
      }
    },
  },
  methods: {
    openQueryCase() {
      this.canQueryCaseOpen = true;
      this.$nextTick(() => {
        this.$refs.queryCase&&this.$refs.queryCase.open();
      });
    },
    initQueryCase(options, promise) {
      promise && promise.resolve(true);
    },
    queryQueryCase(caseModel) {
      this.caseModel = caseModel;
      this.pagination();
    },
    onRefOpen() {
      this.pagination();
    },
    onRefClose() {
      if (!this.canFireEvents) return;
      this.$emit('close');
    },
    autoFetch(q) {
      this.doSearch(q);
    },
    doSearch(q) {
      if ((this.autoquery && this.currentQ != q)) {
        this.currentQ = q;
        this.pagination();
      }
      this.currentQ = q;
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
      this.$refs.grid&&this.$refs.grid.refresh();
    },
    async fetchData({ pager, filter, sort }) {
      var options = this._.extend({}, { q: this.currentQ }, this.options, this.caseModel, pager);
      if (options.orders && sort && sort.field) {
        options.orders.length && this._.remove(options.orders, function(n) {
          return n.name === sort.field;
        });
        options.orders.splice && options.orders.splice(0, 0, { name: sort.field, direction: sort.order });
      }
      const response = await this.$http.post('sys/queries/query/' + this.mdRefId, options);

      this.refInfo = response.data.schema;
      this.$refs.grid&&this.$refs.grid.setColumns(this.refInfo.fields.map(col => this.formatFieldToColumn(col)));
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
    getReturnValue() {
      return this.selectedRows.map(row => this._.pick(row, Object.keys(row).filter(f => f !== 'vueRowId')));
    },
    dblclick({ data }) {
      this.selectedRows = [data];
      this.onConfirm();
    },
    open() {
      this.showDialog = true;
      this.$emit('init', this.options);
    },
    onCancel() {
      if (!this.canFireEvents) return;
      this.showDialog = false;
      this.$emit('cancel', false);
    },
    onConfirm() {
      if (!this.canFireEvents) return;
      this.showDialog = false;
      this.$emit('confirm', this.getReturnValue());
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.canFireEvents = true;
    });
  },
};

</script>
<style lang="scss">
@import "~components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
.md-refs-dialog {
  min-width: 50%;
  min-height: 70%;
  @include md-layout-xsmall {
    min-width: 100%;
    min-height: 100%;

    .md-pagination {
      display: none;
    }
  }
  .md-dialog-content {
    background-attachment: inherit;
  }
  .md-toolbar {
    .search {
      margin: 0 10px;
    }
  }
}

</style>
