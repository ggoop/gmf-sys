<template>
  <tr class="md-table-row" :class="classes" @click="onRowClick">
    <md-table-cell class="md-table-selection" v-if="hasSelection">
      <md-checkbox v-model="checkbox" :disabled="isDisabled" @change="onSelect"></md-checkbox>
    </md-table-cell>
    <slot></slot>
  </tr>
</template>
<script>
import getClosestVueParent from '../../core/utils/getClosestVueParent';
import common from '../../core/utils/common';
const transitionClass = 'md-transition-off';

export default {
  props: {
    mdAutoSelect: Boolean,
    mdSelection: Boolean,
    mdItem: Object
  },
  data() {
    return {
      parentTable: {},
      headRow: false,
      checkbox: false,
      index: 0,
      multiple: false,
      rowId:this._.uniqueId('row')
    };
  },
  computed: {
    isDisabled() {
      return !this.mdSelection && !this.headRow;
    },
    hasSelection() {
      return this.mdSelection || this.headRow && this.parentTable.hasRowSelection;
    },
    classes() {
      return {
        'md-selected': this.checkbox
      };
    }
  },
  watch: {
    mdItem(newValue, oldValue) {
      this.parentTable.datas[this.rowId] = this.mdItem;
      this.parentTable.refreshTotal();
      this.handleMultipleSelection(newValue === oldValue);
    }
  },
  methods: {
    setSelectedRow(value) {
      if (value) {
        this.parentTable.selectedRows[this.rowId] = this.parentTable.datas[this.rowId];
      } else {
        delete this.parentTable.selectedRows[this.rowId];
      }
      this.parentTable.refreshTotal();
    },
    handleSingleSelection(value) {
      if (!this.multiple) {
        this.parentTable.$children.forEach((row, index) => {
          if (!row.headRow && row.rowId != this.rowId) {
            row.checkbox = false;
            row.setSelectedRow(row.checkbox);
          }
        });
      }
      this.setSelectedRow(value);
      this.parentTable.$children[0].checkbox = this.parentTable.numberOfSelected === this.parentTable.numberOfRows;
    },
    handleMultipleSelection(value) {
      if (this.parentTable.numberOfRows > 25) {
        this.parentTable.$el.classList.add(transitionClass);
      }
      this.parentTable.$children.forEach((row, index) => {
        row.checkbox = value;
        if (!row.headRow) {
          this.setSelectedRow(value);
        }
      });
      window.setTimeout(() => this.parentTable.$el.classList.remove(transitionClass));
    },
    onSelect(value) {
      if (this.hasSelection) {
        if (this.headRow) {
          this.handleMultipleSelection(value);
        } else {
          this.handleSingleSelection(value);
        }
        this.parentTable.emitSelection();
      }
    },
    onRowClick() {
      if (this.mdAutoSelect && !this.headRow) {
        this.checkbox = !this.checkbox;
        this.handleSingleSelection(this.checkbox);
        this.parentTable.emitSelection();
      }
    },
  },
  mounted() {
    this.parentTable = getClosestVueParent(this.$parent, 'md-table');
    this.multiple = this.parentTable.multiple && this.mdSelection;
    if (this.$el.parentNode.tagName.toLowerCase() === 'thead') {
      this.headRow = true;
    } else {
      if (this.mdSelection) {
        this.parentTable.hasRowSelection = true;
      }
      if(!this.rowId){
        this.rowId=this._.uniqueId('row');
      }
      if (this.mdItem) {
        this.parentTable.datas[this.rowId]=this.mdItem;
      }
    }
    this.parentTable.refreshTotal();
  },
  destroyed() {
    if (!this.headRow) {
      if (this.mdItem) {
        delete this.parentTable.datas[this.rowId];
      }
    }
    this.parentTable.refreshTotal();
  }
};
</script>