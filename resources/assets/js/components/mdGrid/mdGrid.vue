<template>
  <div class="md-grid">
    <div v-if="showFilter && filterableColumnExists" class="md-grid-filter">
      <input :class="fullFilterInputClass" type="text" v-model="filter" :placeholder="filterPlaceholder">
      <a v-if="filter" @click="filter = ''" class="md-grid-filter-clear">×</a>
    </div>
    <div class="md-grid-wrapper">
      <table :class="fullTableClass">
        <caption v-if="showCaption" class="md-grid-caption" role="alert" aria-live="polite">
          {{ ariaCaption }}
        </caption>
        <thead :class="fullTableHeadClass">
          <tr>
            <md-grid-column-header @click="changeSorting" v-for="column in columns" :key="column.field" :sort="sort" :column="column"></md-grid-column-header>
          </tr>
        </thead>
        <tbody :class="fullTableBodyClass">
          <md-grid-row v-for="row in displayedRows" :key="row.vueRowId" :row="row" :columns="columns"></md-grid-row>
        </tbody>
      </table>
    </div>
    <div v-if="displayedRows.length === 0" class="md-grid-message">
      {{ filterNoResults }}
    </div>
    <div style="display:none;">
      <slot></slot>
    </div>
    <md-grid-pagination v-if="pager" :pager="pager" @pagination="onPagination"></md-grid-pagination>
  </div>
</template>
<script>
import Column from './classes/Column';
import expiringStorage from './expiring-storage';
import Row from './classes/Row';
import mdGridColumnHeader from './mdGridColumnHeader';
import mdGridRow from './mdGridRow';
import isArray from 'lodash/isArray';
import pick from 'lodash/pick';
import { classList } from './helpers';
import mdGridCell from './mdGridCell';

export default {
  components: {
    mdGridColumnHeader,
    mdGridRow,
    mdGridCell
  },

  props: {
    datas: { default: () => [], type: [Array, Function] },
    autoSelect: Boolean,
    selection: Boolean,
    showFilter: { default: true },
    showCaption: { default: false },

    sortBy: { default: '', type: String },
    sortOrder: { default: '', type: String },

    cacheKey: { default: null },
    cacheLifetime: { default: 5 },

    tableClass: { type: String },
    theadClass: { type: String },
    tbodyClass: { type: String },
    filterInputClass: { type: String },
    filterPlaceholder: { default: 'Filter table…' },
    filterNoResults: { default: 'There are no matching rows' },
  },

  data: () => ({
    columns: [],
    rows: [],
    filter: '',
    sort: {
      field: '',
      order: '',
    },
    pager: null,

    localSettings: {},
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
    fullTableClass() {
      return classList('md-grid-table', this.tableClass);
    },

    fullTableHeadClass() {
      return classList('md-grid-table-head', this.theadClass);
    },

    fullTableBodyClass() {
      return classList('md-grid-table-body', this.tbodyClass);
    },

    fullFilterInputClass() {
      return classList('md-grid-filter-field', this.filterInputClass);
    },

    ariaCaption() {
      if (this.sort.field === '') {
        return 'Table not sorted';
      }

      return `Table sorted by ${this.sort.field} ` +
        (this.sort.order === 'asc' ? '(ascending)' : '(descending)');
    },

    usesLocalData() {
      return isArray(this.datas);
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
    async onPagination(page) {
      this.pager.page = page.page;
      this.pager.size = page.size;
      await this.mapDataToRows();
    },

    async mapDataToRows() {
      const data = this.usesLocalData ?
        this.prepareLocalData() :
        await this.fetchServerData();
      this.rows = data
        .map(rowData => {
          rowData.vueRowId = this._.uniqueId('row');
          return rowData;
        })
        .map(rowData => new Row(rowData, this.columns));
    },

    prepareLocalData() {
      this.pager = null;

      return this.datas;
    },

    async fetchServerData() {
      const response = await this.datas({
        filter: this.filter,
        sort: this.sort,
        pager: this.pager
      });
      this.pager = response.data.pager;
      return response.data.data;
    },

    async refresh() {
      await this.mapDataToRows();
    },

    changeSorting(column) {
      if (this.sort.field !== column.field) {
        this.sort.field = column.field;
        this.sort.order = 'asc';
      } else {
        this.sort.order = (this.sort.order === 'asc' ? 'desc' : 'asc');
      }

      if (!this.usesLocalData) {
        this.mapDataToRows();
      }

      this.saveState();
    },

    getColumn(columnName) {
      return this.columns.find(column => column.field === columnName);
    },

    saveState() {
      expiringStorage.set(this.storageKey, pick(this.$data, ['filter', 'sort']), this.cacheLifetime);
    },

    restoreState() {
      const previousState = expiringStorage.get(this.storageKey);

      if (previousState === null) {
        return;
      }

      this.sort = previousState.sort;
      this.filter = previousState.filter;

      this.saveState();
    },
  },
};
</script>