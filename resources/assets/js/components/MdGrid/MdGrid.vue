<template>
  <div class="md-grid layout layout-full flex">
    <div v-if="showFilter && filterableColumnExists" class="md-grid-filter">
      <input type="text" v-model="filter" :placeholder="filterPlaceholder">
      <a v-if="filter" @click="filter = ''" class="md-grid-filter-clear">×</a>
    </div>
    <div class="md-grid-wrapper layout layout-column">
      <md-grid-head :columns="columns" :is-selected-page="isSelectedPage" :scrollLeft="scrollLeft" @sort="onSorting" :width="width"></md-grid-head>
      <md-grid-body :columns="columns" :rows="displayedRows" :width="width" :showAdd="showAdd&&!readonly" :filter-no-results="filterNoResults" class="flex"></md-grid-body>
      <md-grid-foot :columns="columns" v-if="showSum" :scrollLeft="scrollLeft" :width="width"></md-grid-foot>
      <md-grid-actions :pager-info="pager" :showQuery="showQuery" :showDownload="showDownload" :showAdd="showAdd&&!readonly" :showInsert="showInsert&&!readonly" :showRemove="showRemove&&!readonly" :showReload="showReload&&!readonly" :showConfirm="showConfirm" :showCancel="showCancel" @pagination="onPagination" @onQuery="onQuery" @onAdd="onAdd" @onInsert="onInsert" @onRemove="onRemove" @onReload="onReload" @onDownload="onDownload" @onConfirm="onConfirm" @onCancel="onCancel">
      </md-grid-actions>
    </div>
    <div style="display:none;">
      <slot></slot>
    </div>
  </div>
</template>
<script>
import Column from './classes/Column';
import localCache from 'gmf/core/utils/localCache';
import Row from './classes/Row';
import DataExport from './classes/DataExport';
import mdGridHead from './MdGridHead';
import mdGridBody from './MdGridBody';
import mdGridFoot from './MdGridFoot';
import mdGridActions from './MdGridActions';
import { classList } from './helpers';
import mdGridCell from './MdGridCell';

import isArray from 'lodash/isArray'
import uniqueId from 'lodash/uniqueId'
import chunk from 'lodash/chunk'
import pick from 'lodash/pick'
import forEach from 'lodash/forEach'
export default {
  name: 'MdGrid',
  components: {
    mdGridHead,
    mdGridBody,
    mdGridFoot,
    mdGridCell,
    mdGridActions
  },

  props: {
    datas: { default: () => [], type: [Array, Function] },
    readonly: { default: false, type: Boolean },
    autoSelect: { default: false, type: Boolean },
    autoLoad: { default: true, type: Boolean },
    multiple: { default: true, type: Boolean },
    showFilter: { default: false, type: Boolean },
    showSum: { default: false, type: Boolean },
    showQuery: { default: false, type: Boolean },
    showAdd: { default: false, type: Boolean },
    showInsert: { default: false, type: Boolean },
    showRemove: { default: false, type: Boolean },
    showReload: { default: true, type: Boolean },
    showDownload: { default: false, type: Boolean },
    showConfirm: { default: false, type: Boolean },
    showCancel: { default: false, type: Boolean },

    rowFocused: { default: true, type: Boolean },
    title: { type: String },

    sortField: { default: '', type: String },
    sortOrder: { default: '', type: String },

    cacheKey: { default: null },
    cacheLifetime: { default: 5 },
    filterPlaceholder: { default: 'Filter table…' },
    filterNoResults: { default: '暂无数据！' },
    pagerSize: { default: 20, type: Number },
  },

  data: () => ({
    columns: [],
    rows: [], //当前页数据
    displayedRows: [],
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
    focusCell: false,
    selectedRows: {}, //选择的数据
    cacheRows: {},
    scrollLeft: 0,
    isSelectedPage: false,
    pageCacheKey: 'p1'
  }),
  watch: {
    pagerSize(v) {
      this.pager.size = v;
    },
    filter() {
      this.mapDataToRows();
      this.saveState();
    },
    'pager.page' (v) {
      this.pageCacheKey = 'p' + v;
    },
    datas() {
      if (this.usesLocalData) {
        this.cleanCache();
        this.mapDataToRows();
      }
    },

    rows() {
      this.refreshDisplayRow();
    }
  },

  computed: {
    usesLocalData() {
      return isArray(this.datas);
    },
    filterableColumnExists() {
      return this.columns.filter(c => c.isFilterable()).length > 0;
    },
    storageKey() {
      return this.cacheKey ?
        `md-grid.${this.cacheKey}` :
        `md-grid.${window.location.host}${window.location.pathname}${this.cacheKey}`;
    },
    width() {
      var w = 40;
      this.columns.forEach((c) => {
        if (!c.hidden)
          w += (parseInt(c.width) || 0);
      });
      return w + "px";
    }
  },
  methods: {
    onConfirm() {
      if (!this.canFireEvents) return;
      var options = {};
      this.$emit('onConfirm', options);
    },
    onCancel() {
      if (!this.canFireEvents) return;
      var options = {};
      this.$emit('onCancel', options);
    },
    onQuery() {
      if (!this.canFireEvents) return;
      var options = {};
      this.$emit('onQuery', options);
    },
    onAdd() {
      if (!this.canFireEvents) return;
      var options = {};
      this.$emit('onAdd', options);
      this.refreshDisplayRow();
    },
    onInsert() {
      if (!this.canFireEvents) return;
      var options = {};
      this.$emit('onInsert', options);
      this.refreshDisplayRow();
    },
    onRemove() {
      if (!this.canFireEvents) return;
      let rs = this.getSelectedDatas();
      rs.forEach(r => {
        r.sys_deleted = true;
      });
      var options = {};
      options.data = rs;
      this.$emit('onRemove', options);
      this.refreshDisplayRow();
    },
    async onDownload(title) {
      title = title || this.title || '我的表格';
      var datas = await this.getExportData();

      const columns = this.columns && this.columns.filter(column => !column.hidden);
      const de = new DataExport(datas, columns);
      de.toExcel(title);
    },
    onReload() {
      if (!this.canFireEvents) return;
      var options = {};
      this.refresh();
      this.$emit('onReload', options);
    },
    emitRowClick(row) {
      if (!this.canFireEvents) return;
      var options = {};
      options.data = row.data;
      this.$emit('click', options);
    },
    emitRowDbClick(row) {
      if (!this.canFireEvents) return;
      var options = {};
      options.data = row.data;
      this.$emit('dblclick', options);
    },
    emitSeleced() {
      if (!this.canFireEvents) return;
      var options = {};
      options.data = this.getSelectedDatas(true);
      this.$emit('select', options);
      this.refreshStatus();

    },
    emitFocusRow() {
      if (!this.canFireEvents) return;
      var options = {};
      options.data = this.focusRow ? this.focusRow.row.data : null;
      this.$emit('focus', options);
      this.refreshStatus();
    },
    endEdit() {
      this.focusCell && this.focusCell.endEdit();
    },
    refreshStatus() {
      this.isSelectedPage = this.rows &&
        this.rows.length &&
        this.selectedRows[this.pageCacheKey] &&
        this.rows.length == Object.keys(this.selectedRows[this.pageCacheKey]).length;
    },
    refreshDisplayRow() {
      this.displayedRows = this.rows.filter(row => row.displayed());
    },
    cleanCache() {
      this.cacheRows = {};
      this.selectedRows = {};
      this.refreshStatus();
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

    formatDataToRow(data) {
      data.vueRowId = data.vueRowId || uniqueId('row');
      data.sys_deleted = data.sys_deleted || false;
      data.sys_updated = data.sys_updated || false;
      data.sys_created = data.sys_created || false;
      return new Row(data, this.columns);
    },

    async mapDataToRows() {
      if (this.cacheRows[this.pager.page]) {
        const rs = this.cacheRows[this.pager.page];
        if (rs && rs.length) {
          this.rows = rs;
          return;
        }
      }
      const data = this.usesLocalData ?
        this.fetchLocalData() :
        await this.fetchServerData();
      this.rows = data
        .map(rowData => this.formatDataToRow(rowData))
        .filter(row => row.displayed());

      this.cacheRows[this.pager.page] = this.rows;
    },
    async getExportData() {
      var datas = this.getSelectedDatas(true),
        rds = [],
        response, pager = { size: 1000, page: 1 };
      if (datas && datas.length > 0) {
        return datas;
      }
      if (this.usesLocalData) {
        return this.datas;
      }
      datas = [];
      while (true) {
        rds = [];
        response = await this.datas({
          filter: this.filter,
          sort: this.sort,
          pager: pager
        });
        if (isArray(response) && response.length > 0) {
          rds = response;
        }
        if (response && response.data && isArray(response.data.data) && response.data.data.length > 0) {
          rds = response.data.data;
        }
        if (rds && rds.length > 0) {
          datas.push.apply(datas, rds);
        }
        pager.page++;
        if (!rds || !rds.length || rds.length < pager.size || pager.size <= 0) {
          break;
        }
      }
      return datas;
    },
    fetchLocalData() {
      var allDatas = this.datas.filter(r => !r.sys_deleted);
      if (this.columns.length && this.showFilter && this.filter && this.columns.filter(column => column.isFilterable()).length) {
        const tds = allDatas.map(rowData => new Row(rowData, this.columns)).filter(row => row.passesFilter(this.filter));
        allDatas = tds.map(rowData => rowData.data);
      }
      if (this.columns.length && this.sort && this.sort.field) {
        const sortColumn = this.getColumn(this.sort.field);
        if (sortColumn) {
          const tds = allDatas.map(rowData => new Row(rowData, this.columns))
            .sort(sortColumn.getSortPredicate(this.sort.order, this.columns));
          allDatas = tds.map(rowData => rowData.data);
        }
      }
      this.pager.total = allDatas.length;
      var ds = chunk(allDatas, this.pager.size);
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
      if (isArray(response)) {
        return response;
      }
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
    saveState() {
      localCache.set(this.storageKey, pick(this.$data, ['filter', 'sort']), this.cacheLifetime);
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
    getColumn(columnName) {
      return this.columns.find(column => column.field === columnName);
    },
    getSelectedDatas(isAll) {
      const rows = [];
      if (isAll) {
        forEach(this.selectedRows, (cv, ck) => {
          forEach(cv, (v, k) => {
            rows.push(v);
          });
        });
      } else {
        let items = this.selectedRows[this.pageCacheKey];
        items && forEach(items, (v, k) => {
          rows.push(v);
        });
      }
      return rows;
    },
    isSelected(row) {
      let selected = false,
        vueRowId = row && row.vueRowId || row;
      const rows = this.getSelectedDatas(true);
      forEach(rows, (v, k) => {
        if (v.vueRowId == vueRowId)
          selected = true;
      });
      return selected;
    },

    setColumns(instances) {
      this.columns = instances.map(column => new Column(column));
    },
    addColumn(instance) {
      this.columns.push(new Column(instance));
    },
    addDatas(datas) {
      if (isArray(datas)) {
        datas.forEach((data) => {
          data.sys_created = true;
          this.rows.push(this.formatDataToRow(data));
        });
      } else {
        datas.sys_created = true;
        this.rows.push(this.formatDataToRow(datas));
      }
    },
    getAllDatas() {
      const datas = [];
      forEach(this.cacheRows, (cv, ck) => {
        forEach(cv, (v, k) => {
          datas.push(v.data);
        });
      });
      return datas;
    },
    getPostDatas() {
      return this.getAllDatas().map(v => {
        if (v.sys_deleted && !v.sys_created) {
          v.sys_state = 'd';
        } else if (v.sys_created && !v.sys_deleted) {
          v.sys_state = 'c';
        } else if (v.sys_updated && !v.sys_created && !v.sys_deleted) {
          v.sys_state = 'u';
        }
        return v;
      }).filter(v => (v.sys_state == 'c' || v.sys_state == 'u' || v.sys_state == 'd'));
    }
  },
  created() {
    this.sort.field = this.sortField;
    this.sort.order = this.sortOrder;
    this.restoreState();
  },

  async mounted() {
    if (this.$slots.default && this.$slots.default.filter) {
      const columnComponents = this.$slots.default
        .filter(column => column.componentInstance)
        .map(column => column.componentInstance);
      this.setColumns(columnComponents);

      columnComponents.forEach(columnCom => {
        Object.keys(columnCom.$options.props).forEach(
          prop => columnCom.$watch(prop, () => {
            this.setColumns(columnComponents);
          })
        );
      });
    }
    this.pager.size = this.pagerSize;
    if (this.autoLoad) {
      await this.mapDataToRows();
    }
    this.$nextTick(() => {
      this.canFireEvents = true;
      this.refreshStatus();
    });
  },
};

</script>
<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
.md-grid {
  border: 1px solid #c7c7c7;
  background: #fff;
  width: 100%;
  max-width: 100%;
  overflow: hidden;
  .md-grid-cell-container {
    padding: 4px;
    min-height: 32px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    .md-field {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      margin: 0;
      padding: 0;
      min-height: auto;
    }
    .md-select-date>div {
      height: 100%;
      max-height: 100%;
      .md-icon-button {
        max-height: 100%;
        height: 100%;
        min-height: auto;
        margin: 0px;
        width: 30px;
        min-width: auto;
        height: 30px;
      }
    }
  }
  td,
  th {
    border-bottom: 1px solid rgba(15, 157, 88, 0.3);
    border-right: 1px solid rgba(15, 157, 88, 0.3);
    overflow: hidden;
    position: relative;
  }
  td:last-child,
  th:last-child {
    border-right-width: 0;
  }
  td.multiple,
  th.multiple {
    .md-grid-cell-container {
      white-space: normal;
    }
  }
  .md-grid-head,
  .md-grid-foot {
    tr:last-child td,
    tr:last-child th {
      border-bottom-width: 0;
    }
  }
  .md-grid-selection {
    width: 40px;
    position: relative;
    >.md-grid-cell-container {
      text-align: center !important;
      padding: 0!important;
      margin: 0!important;
    }
    .md-checkbox {
      margin: 0;
    }
  }
  .md-grid-empty-row {
    td,
    th {
      height: 0!important;
      padding: 0!important;
      max-height: 0!important;
      border-bottom: 0 !important;
    }
  }

  .md-grid-wrapper {
    width: 100%;
    border-bottom: none;
  }
  .md-grid-table {
    border-collapse: collapse;
    table-layout: fixed;
    position: relative;
  }
  .md-grid-head {
    overflow: hidden;
    background-color: #f4f4f4;
    border-bottom: 2px solid rgba(15, 157, 88, 1);
    position: relative;
    .md-grid-cell-container {
      padding-right: 7px;
      padding-left: 8px;
      padding-top: 8px;
      padding-bottom: 7px;
    }
    td,
    th {
      color: #999;
      text-transform: uppercase;
      white-space: nowrap;
      text-align: left;
      position: relative;
      &.has-sort {
        text-decoration: underline;
        &:after {
          position: absolute;
          left: 1px;
          display: flex;
          align-items: center;
          align-content: center;
          color: #bbb;
          top: 0;
          height: 100%;
        }
        &.sort-asc:after {
          content: '↑';
        }
        &.sort-desc:after {
          content: '↓';
        }
      }
      &.is-sorting,
      &.can-sorting {
        cursor: s-resize;
      }
      &.is-resizing,
      &.can-resizing {
        cursor: col-resize;
      }
    }
  }
  .md-grid-body-empty {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0px;
    left: 0px;
    background: rgba(73, 151, 142, 0.03);
  }
  .md-grid-body {
    background: #fff;
    color: #000;
    overflow: auto;
    position: relative;
    tr {
      .is-tool {
        overflow: visible;
        position: relative;

        .md-grid-cell-container {
          z-index: -1;
          position: absolute;
          right: 0px;
          top: 0px;
          bottom: 0px;

          .md-button.md-icon-button {
            margin: 0px;
            padding: 0px;
            padding: 0px;
            margin: 0px;
            min-width: 0;
            min-height: 0;
            max-height: 100%;
          }
        }
      }
      &:hover {
        .is-tool>.md-grid-cell-container {
          z-index: inherit;
        }
      }
      &:nth-child(even) {
        background-color: rgba(245, 247, 250, 0.7);
      }
      &.selected {
        background-color:rgba(102, 187, 106, 0.5);
      }
    }
    tr:hover>:first-child:before,
    tr.focused>:first-child:before {
      content: " ";
      position: absolute;
      left: 0;
      background: rgba(15, 157, 88, 0.8);
      width: 3px;
      height: 100%;
      top: 0;
    }
    td.cell-focused,
    th.cell-focused {
      .md-input-value .md-button.md-delete {
        display: block;
      }
      .md-grid-cell-container:before {
        content: " ";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 2px solid rgba(15, 157, 88, 1);
      }
    }

    tr.focused.row-focused {
      background-color: rgba(15, 157, 88, 0.8);
      color: #fff;
      td,
      th {
        border-right-color: rgba(15, 157, 88, 0.8);
      }
    }
  }
  .md-grid-foot {
    overflow: hidden;
    background-color: #fafafa;
    border-top: 1px solid #dfdfdf;
    min-height: 40px;
    position: relative;
  }
  .md-grid-actions {
    background-color: #fafafa;
    border-top: 1px solid #dfdfdf;
    min-height: 40px;
    color: #666;
    >div>.md-icon-button {
      margin: 0px;
      padding: 0px;
    }
    .md-button-query {
      color: #009688;
    }
  }
}

</style>
