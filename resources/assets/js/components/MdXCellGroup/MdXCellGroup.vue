<template>
  <div :class="[b(), { 'md-1px-tb': border }]">
    <slot />
  </div>
</template>

<script>
import create from "gmf/core/MdComponent";

export default new create({
  name: 'MdXCellGroup',

  props: {
    border: {
      type: Boolean,
      default: true
    }
  }
});
</script>
