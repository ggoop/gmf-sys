<template>
  <md-dialog ref="dialog" @open="onRefOpen" @close="onRefClose" class="md-refs-dialog">
    <md-toolbar>
      <h1 class="md-title">{{refInfo.comment}}</h1>
      <md-input-container class="md-flex md-header-search">
        <md-input class="md-header-search-input" :fetch="doFetch" placeholder="search" @keyup.enter.native="doSearch(false)"></md-input>
        <md-button class="md-icon-button md-inset" @click.native="doSearch(true)">
          <md-icon v-if="autoquery">filter_list</md-icon>
          <md-icon v-else>search</md-icon>
        </md-button>
      </md-input-container>
      <md-button class="md-icon-button" @click.native="cancel()">
        <md-icon>close</md-icon>
      </md-button>
    </md-toolbar>
    <md-dialog-content class="no-padding">
      <md-table @select="onTableSelect" ref="table">
        <md-table-header>
          <md-table-row>
            <md-table-head v-for="(column, columnIndex) in refInfo.fields" v-if="!column.hide&&column.alias!='id'" :key="columnIndex">
              {{column.comment||column.name}}
            </md-table-head>
          </md-table-row>
        </md-table-header>
        <md-table-body>
          <md-table-row v-for="(row, rowIndex) in refData" :key="row[mdKeyField]" :md-item="row" :md-auto-select="true" :md-selection="!!multiple" @dblclick.native="dblclick(row)">
            <md-table-cell v-for="(column, columnIndex) in refInfo.fields" v-if="!column.hide&&column.alias!='id'" :key="columnIndex">
              {{ row[column.alias||column.name] }}
            </md-table-cell>
          </md-table-row>
        </md-table-body>
      </md-table>
    </md-dialog-content>
    <md-dialog-actions>
      <md-query-case :md-query-id="mdRefId" ref="queryCase" @init="initQueryCase" @query="queryQueryCase">
        <md-button class="md-icon-button" @click.native="openQueryCase()">
          <md-icon>search</md-icon>
        </md-button>
      </md-query-case>
      <md-table-pagination :md-size="pageInfo.size" :md-total="pageInfo.total" :md-page="pageInfo.page" :md-page-options="[10,20]" @pagination="onTablePagination">
      </md-table-pagination>
      <span class="flex"></span>
      <md-button class="md-accent md-raised" @click.native="onConfirm()">确定</md-button>
      <md-button class="md-warn" @click.native="cancel()">取消</md-button>
    </md-dialog-actions>
    <md-loading :loading="loading"></md-loading>
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
    mdKeyField: {
      type: String,
      default: 'id'
    },
    mdMax: {
      type: Number,
      default: Infinity
    },
    mdPageSize: {
      type: [Number, String],
      default: '20'
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
      currentChip: null,
      currentQ: '',
      autoquery: true,
      selectedRows: [],
      refInfo: {},
      refData: [],
      loading: 0,
      refCache: {},
      pageInfo: {
        size: 0,
        total: 0,
        page: 1
      },
      caseModel: {}
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
    mdPageSize(val) {
      this.pageInfo.size = val;
    },
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
      this.pagination(1);
    },
    onRefOpen() {
      this.selectedRows = [];
      if (!this.refInfo || !this.refInfo.id || this.refInfo.id !== this.mdRefId) {
        if (!this.refCache[this.mdRefId]) {
          
        }
      }
      this.onTablePagination();
    },
    onRefClose() {
      this.$emit('close');
    },
    doFetch(q) {
      if (this.currentQ != q && this.autoquery) {
        this.currentQ = q;
        this.pagination(1);
      }
      this.currentQ = q;
    },
    doSearch(isMana) {
      if (this.autoquery && isMana) {
        this.autoquery = false;
      }
      this.pagination(1);
    },
    onTablePagination(pager) {
      this.pagination(pager);
    },
    pagination(pager) {
      if (common.isString(pager) || common.isNumber(pager)) {
        pager = this._.extend({}, this.pageInfo, { page: pager });
      } else {
        pager = pager || this.pageInfo;
      }
      var options = this._.extend({}, { q: this.currentQ }, this.options, this.caseModel, pager);

      if (this.mdRefId) {
        this.$http.post('sys/queries/query/' + this.mdRefId, options).then(response => {
          this.refInfo = response.data.schema;
          this.refData = response.data.data;
          this.pageInfo.size = response.data.pager.size;
          this.pageInfo.page = response.data.pager.page;
          this.pageInfo.total = response.data.pager.total;
        }, response => {});
      }
    },
    onTableSelect(items) {
      this.selectedRows = [];
      Object.keys(items).forEach((row, index) => {
        this.selectedRows[index] = items[row];
      });
    },
    getReturnValue() {
      return this.selectedRows;
    },
    dblclick(item) {
      this.selectedRows = [item];
      this.onConfirm();
    },
    open() {
      this.$emit('init', this.options);
      this.$refs['dialog'].open();
      this.$emit('open');
    },
    cancel() {
      this.$refs['dialog'].close();
      this.$emit('cancel', false);
    },
    onConfirm() {
      this.$refs['dialog'].close();
      this.$emit('confirm', this.getReturnValue());
    }
  },
  mounted() {
    this.pageInfo.size = this.mdPageSize;
  },
};
</script>