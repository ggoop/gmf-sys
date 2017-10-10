<template>
  <div>
  </div>
</template>
<script>
import getClosestVueParent from '../../core/utils/getClosestVueParent';
export default {
  props: {
    column: { type: Object },
    row: { type: Object },
  },
  data() {
    return {
      parentTable: {}
    };
  },
  methods: {
    beginEdit() {

    },
    endEdit() {

    },
  },
  mounted() {
    this.parentTable = getClosestVueParent(this.$parent, 'md-grid');
    this.$nextTick(() => {
      this.canFireEvents = true;
    });
  },
};
</script>