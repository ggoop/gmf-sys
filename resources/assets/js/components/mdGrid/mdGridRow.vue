<template>
  <tr>
    <md-grid-cell type="th">
      <md-checkbox v-model="row.checkbox"></md-checkbox>
      <p> {{ row.name }} wrote {{ row.qty }} songs.</p>
    </md-grid-cell>
    <md-grid-cell v-for="(column,index) in visibleColumns" :row="row" :key="index" :column="column"></md-grid-cell>
  </tr>
</template>
<script>
import mdGridCell from './mdGridCell';

export default {
  props: ['columns', 'row'],

  components: {
    mdGridCell,
  },

  computed: {
    visibleColumns() {
      return this.columns.filter(column => !column.hidden);
    },
  },
};
</script>