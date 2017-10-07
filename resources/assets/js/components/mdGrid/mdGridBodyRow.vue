<template>
  <tr @click="rowClicked" :class="[rowClass]">
    <md-grid-cell type="th" v-if="multiple" class="md-grid-selection">
      <div class="layout layout-align-center-center">
        <md-checkbox v-model="selected" @change="handleSelected"></md-checkbox>
      </div>
    </md-grid-cell>
    <md-grid-cell v-for="(column,index) in visibleColumns" :row="row" :key="index" :column="column"></md-grid-cell>
  </tr>
</template>
<script>
import mdGridCell from './mdGridCell';
import { classList } from './helpers';
import getClosestVueParent from '../../core/utils/getClosestVueParent';
export default {
  props: ['columns', 'row'],

  components: {
    mdGridCell,
  },
  data() {
    return {
      parentTable: {},
      autoSelect: false,
      multiple: false,
      focused: false,
      selected: false,
      disabled: false,
      rowId: 'row-1',
      elType: 'bodyRow'
    };
  },
  watch: {
    'row.data.vueRowId' (v) {
      this.rowId = v;
    },
    'rowId' () {
      this.resetStatus();
    }
  },
  computed: {
    visibleColumns() {
      return this.columns && this.columns.filter(column => !column.hidden);
    },
    rowClass() {
      return {
        'focused': this.focused,
        'selected': this.selected,
        'disabled': this.disabled
      };
    }
  },
  methods: {
    resetStatus() {
      this.multiple = this.parentTable.multiple;
      this.autoSelect = this.parentTable.autoSelect;
      this.focused = false;
      this.selected = this.row.data && this.parentTable.isSelected(this.row.data);
      this.disabled = false;
    },
    rowClicked() {
      if (!this.canFireEvents) return;
      if (this.autoSelect) {
        this.handleSelected(true);
      }
      this.handleFocused();
      this.parentTable.emitRowClick(this.row);
    },
    setSelected(value) {
      this.selected = value;
      let items = this.parentTable.selectedRows[this.parentTable.pageCacheKey];
      if (!items) {
        items = {};
        this.parentTable.selectedRows[this.parentTable.pageCacheKey] = items;
      }
      if (value) {
        items[this.rowId] = this.row.data;
      } else {
        delete items[this.rowId];
      }
    },
    handleFocused() {
      if (!this.canFireEvents) return;
      if (!this.parentTable.focusRow || this.parentTable.focusRow.rowId != this.rowId) {
        if (this.parentTable.focusRow) this.parentTable.focusRow.focused = false;
        this.focused = true;
        this.parentTable.focusRow = this;
        this.parentTable.emitFocusRow();
      }
    },
    handleSelected(value) {
      if (!this.canFireEvents) return;
      if (!this.multiple && value) {
        this.parentTable.$children.forEach((body, index) => {
          if (body.elType == 'body') {
            body.$children.forEach((row, index) => {
              if (row.elType == 'bodyRow' && rowId.rowId != this.rowId) {
                row.setSelected(false);
              }
            });
          }
        });
      }
      this.setSelected(value);

      this.parentTable.emitSeleced();
    },
  },
  mounted() {
    this.parentTable = getClosestVueParent(this.$parent, 'md-grid');
    this.multiple = this.parentTable.multiple;
    this.autoSelect = this.parentTable.autoSelect;
    if (this.row && this.row.data.vueRowId) {
      this.rowId = this.row.data.vueRowId;
    }
    this.$nextTick(() => {
      this.canFireEvents = true;
    });
  },
};
</script>