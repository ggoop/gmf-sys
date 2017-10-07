<template>
  <div class="md-grid-foot">
    <table class="md-grid-table" :width="width">
      <thead>
        <md-grid-empty-row :columns="columns"></md-grid-empty-row>
      </thead>
      <tbody>
        <tr>
          <md-grid-cell type="th" v-if="multiple" class="md-grid-selection">
            <div class="layout layout-align-center-center"></div>
          </md-grid-cell>
          <md-grid-cell v-for="column in visibleColumns" :key="column.field">
            <div></div>
          </md-grid-cell>
        </tr>
      </tbody>
    </table>
    <slot></slot>
  </div>
</template>
<script>
import mdGridCell from './mdGridCell';
import mdGridEmptyRow from './mdGridEmptyRow';
import { classList } from './helpers';
import getClosestVueParent from '../../core/utils/getClosestVueParent';
export default {
  props: ['columns','width'],

  components: {
    mdGridCell,
    mdGridEmptyRow
  },
  data() {
    return {
      parentTable: {},
      selected: false,
      multiple: false
    };
  },
  computed: {
    visibleColumns() {
      return this.columns && this.columns.filter(column => !column.hidden);
    },
  },
  mounted() {
    this.parentTable = getClosestVueParent(this.$parent, 'md-grid');
    this.multiple = this.parentTable.multiple;
    this.$nextTick(() => {
      this.canFireEvents = true;
    });

  },
};
</script>