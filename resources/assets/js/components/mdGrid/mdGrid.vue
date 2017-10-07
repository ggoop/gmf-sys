<template>
  <div class="md-grid">
    <div v-if="showFilter && filterableColumnExists" class="md-grid-filter">
      <input type="text" v-model="filter" :placeholder="filterPlaceholder">
      <a v-if="filter" @click="filter = ''" class="md-grid-filter-clear">×</a>
    </div>
    <div class="md-grid-wrapper">
      <md-grid-head :columns="columns" @sort="changeSorting"></md-grid-head>
      <md-grid-body :columns="columns" :rows="displayedRows" :filter-no-results="filterNoResults"></md-grid-body>
      <md-grid-foot :columns="columns" :show-sum="showSum">
        <md-grid-pagination v-if="pager" :pager="pager" @pagination="onPagination"></md-grid-pagination>
      </md-grid-foot>
    </div>
    <div style="display:none;">
      <slot></slot>
    </div>
  </div>
</template>
<script>
import Column from './classes/Column';
import localCache from '../../core/utils/localCache';
import Row from './classes/Row';
import mdGridHead from './mdGridHead';
import mdGridBody from './mdGridBody';
import mdGridFoot from './mdGridFoot';
import { classList } from './helpers';
import mdGridCell from './mdGridCell';

export default {
  components: {
    mdGridHead,
    mdGridBody,
    mdGridFoot,
    mdGridCell
  },

  props: {
    datas: { default: () => [], type: [Array, Function] },
    autoSelect: Boolean,
    multiple: { default: true, type: Boolean },
    showFilter: { default: false },
    showSum: { default: false },
    sortBy: { default: '', type: String },
    sortOrder: { default: '', type: String },

    cacheKey: { default: null },
    cacheLifetime: { default: 5 },
    filterPlaceholder: { default: 'Filter table…' },
    filterNoResults: { default: 'There are no matching rows' },
  },

  data: () => ({
    columns: [],
    rows: [], //当前页数据
    filter: '',
    sort: {
      field: '',
      order: '',
    },
    pager: {
      page: 1,
      size: 20,
      total: 0
    },
    focusRow: false,
    localSettings: {},
    selectedRows: {}, //选择的数据
    cacheRows: {},
    cacheSelectRows: {}
  }),

  created() {
    this.sort.field = this.sortBy;
    this.sort.order = this.sortOrder;
    this.restoreState();
  },

  async mounted() {
    if (this.$slots.default && this.$slots.default.filter) {
      const columnComponents = this.$slots.default
        .filter(column => column.componentInstance)
        .map(column => column.componentInstance);

      this.columns = columnComponents.map(
        column => new Column(column)
      );

      columnComponents.forEach(columnCom => {
        Object.keys(columnCom.$options.props).forEach(
          prop => columnCom.$watch(prop, () => {
            this.columns = columnComponents.map(
              column => new Column(column)
            );
          })
        );
      });
    }
    await this.mapDataToRows();
  },

  watch: {
    filter() {
      if (!this.usesLocalData) {
        this.mapDataToRows();
      }
      this.saveState();
    },

    datas() {
      if (this.usesLocalData) {
        this.mapDataToRows();
      }
    },
  },

  computed: {
    usesLocalData() {
      return this._.isArray(this.datas);
    },
    displayedRows() {
      if (!this.usesLocalData) {
        return this.sortedRows;
      }
      if (!this.showFilter) {
        return this.sortedRows;
      }

      if (!this.columns.filter(column => column.isFilterable()).length) {
        return this.sortedRows;
      }

      return this.sortedRows.filter(row => row.passesFilter(this.filter));
    },
    sortedRows() {
      if (!this.usesLocalData) {
        return this.rows;
      }

      if (this.sort.field === '') {
        return this.rows;
      }

      if (this.columns.length === 0) {
        return this.rows;
      }

      const sortColumn = this.getColumn(this.sort.field);

      if (!sortColumn) {
        return this.rows;
      }

      return this.rows.sort(sortColumn.getSortPredicate(this.sort.order, this.columns));
    },

    filterableColumnExists() {
      return this.columns.filter(c => c.isFilterable()).length > 0;
    },

    storageKey() {
      return this.cacheKey ?
        `md-grid.${this.cacheKey}` :
        `md-grid.${window.location.host}${window.location.pathname}${this.cacheKey}`;
    },
  },

  methods: {
    handleSizeChange() {
      if (!this.usesLocalData) {
        this.cacheRows = {};
      }
      this.cacheSelectRows = {};
      this.selectedRows = {};
    },
    async onPagination(pager) {
      if (this.pager.size != pager.size) {
        this.handleSizeChange();
      }
      this.pager.page = pager.page;
      this.pager.size = pager.size;
      await this.mapDataToRows();
    },

    async mapDataToRows() {
      if (this.cacheRows[this.pager.page]) {
        this.rows = this.cacheRows[this.pager.page];
        return;
      }
      const data = this.usesLocalData ?
        this.prepareLocalData() :
        await this.fetchServerData();
      this.rows = data
        .map(rowData => {
          rowData.vueRowId = this._.uniqueId('row');
          return rowData;
        })
        .map(rowData => new Row(rowData, this.columns));

      this.cacheRows[this.pager.page] = this.rows;
    },

    prepareLocalData() {
      this.pager.page = 1;
      this.pager.total = this.datas.length;
      return this.datas;
    },

    async fetchServerData() {
      const response = await this.datas({
        filter: this.filter,
        sort: this.sort,
        pager: this.pager
      });
      if (response.data.pager) {
        if (this.pager.size != response.data.pager.size) {
          this.handleSizeChange();
        }
        this.pager.page = response.data.pager.page;
        this.pager.size = response.data.pager.size;
        this.pager.total = response.data.pager.total;
      }
      return response.data.data;
    },

    async refresh() {
      await this.mapDataToRows();
    },
    changeSorting(sort) {
      this.sort = sort;
      if (!this.usesLocalData) {
        this.mapDataToRows();
      }
      this.saveState();
    },
    getColumn(columnName) {
      return this.columns.find(column => column.field === columnName);
    },

    saveState() {
      localCache.set(this.storageKey, this._.pick(this.$data, ['filter', 'sort']), this.cacheLifetime);
    },

    restoreState() {
      const previousState = localCache.get(this.storageKey);

      if (previousState === null) {
        return;
      }

      this.sort = previousState.sort;
      this.filter = previousState.filter;

      this.saveState();
    },
    emitRowClick(row) {
      this.$emit('rowClick', row);
    },
    emitSeleced() {
      this.cacheSelectRows[this.pager.page] = this.selectedRows;
      this.$emit('select', this.selectedRows);
    },
    emitFocusRow() {
      this.$emit('focus', this.focusRow);
    }
  },
};
</script>