<template>
  <th @click="clicked" :class="headerClass" role="columnheader" scope="col" :aria-sort="ariaSort" :aria-disabled="ariaDisabled" v-if="this.isVisible">
    {{ label }}
  </th>
</template>
<script>
import { classList } from './helpers';

export default {
  props: ['column', 'sort'],
  computed: {
    ariaDisabled() {
      if (!this.column.isSortable()) {
        return 'true';
      }

      return false;
    },

    ariaSort() {
      if (!this.column.isSortable()) {
        return false;
      }

      if (this.column.field !== this.sort.field) {
        return 'none';
      }

      return this.sort.order === 'asc' ? 'ascending' : 'descending';
    },

    headerClass() {
      if (!this.column.isSortable()) {
        return classList('md-grid-table-th', this.column.headerClass);
      }

      if (this.column.field !== this.sort.field) {
        return classList('md-grid-table-th has-sort', this.column.headerClass);
      }
      return classList(`md-grid-table-th has-sort sort-${this.sort.order}`, this.column.headerClass);
    },

    isVisible() {
      return !this.column.hidden;
    },

    label() {
      if (this.column.label === null) {
        return this.column.field;
      }

      return this.column.label;
    },
  },

  methods: {
    clicked() {
      if (this.column.isSortable()) {
        this.$emit('click', this.column);
      }
    },
  },
};
</script>