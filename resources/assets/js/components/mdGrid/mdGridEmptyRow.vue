<template>
  <tr class="md-grid-empty-row">
    <th v-if="multiple">
    </th>
    <th v-for="(column,index) in visibleColumns">
    </th>
  </tr>
</template>
<script>
import getClosestVueParent from '../../core/utils/getClosestVueParent';
export default {
  props: ['columns'],

  components: {
  },
  data() {
    return {
      parentTable: {},
      multiple: false
    }
  },
  computed: {
    visibleColumns() {
      return this.columns && this.columns.filter(column => !column.hidden);
    },
  },
  mounted() {
    this.parentTable = getClosestVueParent(this.$parent, 'md-grid');
    this.multiple = this.parentTable.multiple;
  },
};
</script>