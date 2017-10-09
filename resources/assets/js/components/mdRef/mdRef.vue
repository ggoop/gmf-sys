<template>
  <md-dialog ref="dialog" :md-click-outside-to-close="false" @open="onRefOpen" @close="onRefClose" class="md-refs-dialog">
    <md-toolbar>
      <h1 class="md-title">{{refInfo.comment}}</h1>
      <md-input-container class="md-flex md-header-search">
        <md-input class="md-header-search-input" :fetch="autoFetch" placeholder="search" @keyup.enter.native="doSearch({isMana:true})"></md-input>
        <md-button class="md-icon-button md-inset" @click.native="doSearch({isMana:true})">
          <md-icon v-if="autoquery">filter_list</md-icon>
          <md-icon v-else>search</md-icon>
        </md-button>
      </md-input-container>
      <md-button class="md-icon-button" @click.native="onCancel()">
        <md-icon>close</md-icon>
      </md-button>
    </md-toolbar>
    <md-dialog-content class="no-padding layout flex">
      <md-grid :auto-select="true" ref="grid" :datas="fetchData" :multiple="multiple" showConfirm showCancel showQuery @select="onSelected" @dblclick="dblclick" @onQuery="openQueryCase" @onConfirm="onConfirm" @onCancel="onCancel">
      </md-grid>
    </md-dialog-content>
    <md-loading :loading="loading"></md-loading>
    <md-query-case v-if="canQueryCaseOpen" :md-query-id="mdRefId" ref="queryCase" @init="initQueryCase" @query="queryQueryCase">
    </md-query-case>
  </md-dialog>
</template>
<script>
import theme from '../../core/components/mdTheme/mixin';
import common from '../../core/utils/common';

export default {
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
          orders: {}
        }
      }
    }
  },
  mixins: [theme],
  data() {
    return {
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
        this.$refs.queryCase.open();
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
      this.doSearch({ q: q });
    },
    doSearch({ q, isMana }) {
      if ((this.autoquery && this.currentQ != q) || isMana) {
        this.currentQ = q;
        this.pagination();
      }
      this.currentQ = q;
      if (isMana) {
        this.autoquery = false;
      }
    },
    formatFieldToColumn(field) {
      return {
        field: field.alias,
        label: field.comment,
        hidden: field.alias == 'id' || field.hide || field.hidden
      };
    },
    pagination() {
      this.$refs.grid.refresh();
    },
    async fetchData({ pager, filter, sort }) {
      var options = this._.extend({}, { q: this.currentQ }, this.options, this.caseModel, pager);
      const response = await this.$http.post('sys/queries/query/' + this.mdRefId, options);

      this.refInfo = response.data.schema;
      this.$refs.grid.setColumns(this.refInfo.fields.map(col => this.formatFieldToColumn(col)));
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
      if (!this.canFireEvents) return;
      this.$emit('init', this.options);
      this.$refs['dialog'].open();
      this.$emit('open');
    },
    onCancel() {
      if (!this.canFireEvents) return;
      this.$refs['dialog'].close();
      this.$emit('cancel', false);
    },
    onConfirm() {
      if (!this.canFireEvents) return;
      this.$refs['dialog'].close();
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