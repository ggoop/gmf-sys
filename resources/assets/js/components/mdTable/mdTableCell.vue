<template>
  <td class="md-table-cell" :class="classes">
    <div class="md-table-cell-container">
      <slot></slot>
    </div>
  </td>
</template>

<script>
  export default {
    props: {
      mdNumeric: Boolean,
      mdIsTool:Boolean
    },
    data: () => ({
      hasAction: false
    }),
    computed: {
      classes() {
        return {
          'md-numeric': this.mdNumeric,
          'md-has-action': this.hasAction,
          'md-table-cell-tool':this.mdIsTool
        };
      }
    },
    mounted() {
      if (this.$children.length > 0) {
        this.hasAction = true;
      }
    }
  };
</script>
