<template>
  <div class="md-grid">
    <div v-if="showFilter && filterableColumnExists" class="md-grid-filter">
      <input type="text" v-model="filter" :placeholder="filterPlaceholder">
      <a v-if="filter" @click="filter = ''" class="md-grid-filter-clear">×</a>
    </div>
    <div class="md-grid-wrapper">
      <md-grid-head :columns="columns" :is-selected-page="isSelectedPage" @sort="onSorting" :width="width"></md-grid-head>
      <md-grid-body :columns="columns" :rows="rows" :width="width" :filter-no-results="filterNoResults"></md-grid-body>
      <md-grid-foot :columns="columns" :show-sum="showSum" :width="width">
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
    autoSelect: { default: false, type: Boolean },
    multiple: { default: true, type: Boolean },
    showFilter: { default: false, type: Boolean },
    showSum: { default: false, type: Boolean },
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
    selectedRows: {}, //选择的数据
    cacheRows: {},
    width: '',
    isSelectedPage: false,
    pageCacheKey: 'p1'
  }),
  watch: {
    filter() {
      this.mapDataToRows();
      this.saveState();
    },
    'pager.page' (v) {
      this.pageCacheKey = 'p' + v;
    },
    datas() {
      if (this.usesLocalData) {
        this.mapDataToRows();
      }
    },
    columns() {
      this.width = this.getWidth();
    }
  },

  computed: {
    usesLocalData() {
      return this._.isArray(this.datas);
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
    refreshStatus() {
      this.isSelectedPage = this.rows &&
        this.rows.length &&
        this.selectedRows[this.pageCacheKey] &&
        this.rows.length == Object.keys(this.selectedRows[this.pageCacheKey]).length;
    },
    cleanCache() {
      this.cacheRows = {};
      this.selectedRows = {};
      this.refreshStatus()
    },
    async onPagination(pager) {
      if (this.pager.size != pager.size) {
        this.cleanCache();
      }
      this.pager.page = pager.page;
      this.pager.size = pager.size;
      await this.mapDataToRows();
      this.refreshStatus();
    },

    async mapDataToRows() {
      if (this.cacheRows[this.pager.page]) {
        this.rows = this.cacheRows[this.pager.page];
        return;
      }
      const data = this.usesLocalData ?
        this.fetchLocalData() :
        await this.fetchServerData();
      this.rows = data
        .map(rowData => {
          rowData.vueRowId = this._.uniqueId('row');
          return rowData;
        })
        .map(rowData => new Row(rowData, this.columns));

      this.cacheRows[this.pager.page] = this.rows;
    },

    fetchLocalData() {
      var allDatas = this.datas;
      if (this.columns.length && this.showFilter && this.filter && this.columns.filter(column => column.isFilterable()).length) {
        // allDatas = this.allDatas.filter((row) => {
        //   var r = new Row(row, this.columns);
        //   return r.passesFilter(this.filter);
        // });
      }
      if (this.columns.length && this.sort && this.sort.field) {
        // const sortColumn = this.getColumn(this.sort.field);
        // if (sortColumn) {
        //   allDatas = allDatas.sort(function(r1, r2) {
        //     return 1;
        //   });
        // }
      }
      this.pager.total = this.datas.length;
      var ds = this._.chunk(allDatas, this.pager.size);
      if (ds.length >= this.pager.page) {
        return ds[this.pager.page - 1];
      }
      return [];
    },

    async fetchServerData() {
      const response = await this.datas({
        filter: this.filter,
        sort: this.sort,
        pager: this.pager
      });
      if (response.data.pager) {
        if (this.pager.size != response.data.pager.size) {
          this.cleanCache();
        }
        this.pager.page = response.data.pager.page;
        this.pager.size = response.data.pager.size;
        this.pager.total = response.data.pager.total;
      }
      return response.data.data;
    },

    async refresh() {
      this.cleanCache();
      await this.mapDataToRows();
      this.refreshStatus();
    },
    onSorting(sort) {
      this.sort = sort;
      this.cleanCache();
      this.mapDataToRows();
      this.saveState();
      this.refreshStatus();
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
    getWidth() {
      var w = 40;
      this.columns.forEach((c) => {
        if (!c.hidden)
          w += parseInt(c.width);
      });
      return w + "px";
    },
    getSelectedRows() {
      const rows = [];
      this._.forEach(this.selectedRows, (cv, ck) => {
        this._.forEach(cv, (v, k) => {
          rows.push(v);
        });
      });
      return rows;
    },

    emitRowClick(row) {
      if (this.canFireEvents) {
        this.$emit('rowClick', row);
      }
    },
    isSelected(row) {
      let selected = false,
        vueRowId = row && row.vueRowId || row;
      const rows = this.getSelectedRows();
      this._.forEach(rows, (v, k) => {
        if (v.vueRowId == vueRowId)
          selected = true;
      });
      return selected;
    },
    emitSeleced() {
      if (this.canFireEvents) {
        this.$emit('select', this.getSelectedRows());
        this.refreshStatus();
      }
    },
    emitFocusRow() {
      if (this.canFireEvents) {
        this.$emit('focus', this.focusRow);
        this.refreshStatus();
      }
    }
  },
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
    this.width = this.getWidth();
    await this.mapDataToRows();
    this.$nextTick(() => {
      this.canFireEvents = true;
      this.refreshStatus();
    });
  },
};
</script>