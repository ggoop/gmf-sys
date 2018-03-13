<template>
  <div class="md-grid-head">
    <table class="md-grid-table" :width="width" :style="tableStyle">
      <thead>
        <md-grid-empty-row :columns="columns"></md-grid-empty-row>
      </thead>
      <tbody>
        <tr>
          <md-grid-cell v-if="multiple" :selection="true" container-class="layout layout-align-center-center">
            <md-checkbox class="md-primary" v-model="selected" @change="handleSelected"></md-checkbox>
          </md-grid-cell>
          <md-grid-head-cell v-for="column in visibleColumns" :key="column.field" :column="column">
            {{ column.label||column.field }}
          </md-grid-head-cell>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import mdGridCell from './MdGridCell';
import mdGridHeadCell from './MdGridHeadCell';

import mdGridEmptyRow from './MdGridEmptyRow';
import { classList } from './helpers';
import getClosestVueParent from 'core/utils/getClosestVueParent';
export default {
  props: ['columns', 'width', 'isSelectedPage', 'scrollLeft'],

  components: {
    mdGridCell,
    mdGridHeadCell,
    mdGridEmptyRow
  },
  data() {
    return {
      parentTable: {},
      selected: false,
      multiple: false,
    };
  },
  watch: {
    isSelectedPage(v) {
      this.selected = v;
    }
  },
  computed: {
    visibleColumns() {
      return this.columns && this.columns.filter(column => !column.hidden);
    },
    tableStyle() {
      return {
        left: (0 - (parseInt(this.scrollLeft) || 0)) + 'px'
      }
    },
  },
  methods: {
    handleSelected(value) {
      if (this.multiple) {
        this.parentTable.$children.forEach((body, index) => {
          if (body.elType == 'body') {
            body.$children.forEach((row, index) => {
              if (row.elType == 'bodyRow') {
                row.setSelected(value);
              }
            });
          }
        });
        this.parentTable.emitSeleced();
      }
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
