<template>
  <!-- Never render the contents -->
  <!-- The scoped slot won't have the required data -->
  <div v-if="false">
    <slot></slot>
  </div>
</template>
<script>
export default {
  props: {
    field: { required: false, type: String },
    label: { default: null, type: String },
    dataType: { default: 'string', type: String },

    sortable: { default: false, type: Boolean },
    sortBy: { default: null },

    filterable: { default: false, type: Boolean },
    filterOn: { default: null },

    formatter: { default: v => v, type: Function },

    hidden: { default: false, type: Boolean },
    isTool: { default: false, type: Boolean },
    cellClass: { default: '' },
    headerClass: { default: '' },
    width: { default: '150px' },
  },
};
</script>