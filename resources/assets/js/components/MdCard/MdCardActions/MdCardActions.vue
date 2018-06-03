<template>
  <div class="md-card-actions" :class="`md-alignment-${mdAlignment}`">
    <slot />
  </div>
</template>

<script>
  import MdPropValidator from 'gmf/core/utils/MdPropValidator'

  const alignments = ['left', 'right', 'space-between','center']

  export default {
    name: 'MdCardActions',
    props: {
      mdAlignment: {
        type: String,
        default: 'right',
        ...MdPropValidator('md-alignment', alignments)
      }
    }
  }
</script>

<style lang="scss">
@import "~gmf/components/MdAnimation/variables";
@import "~gmf/components/MdLayout/mixins";
  .md-card-actions {
    padding: 8px 16px;
    display: flex;
    align-items: center;

    &.md-alignment-right {
      justify-content: flex-end;
    }

    &.md-alignment-left {
      justify-content: flex-start;
    }
    &.md-alignment-center {
      justify-content:center;
    }

    &.md-alignment-space-between {
      justify-content: space-between;
    }

    .md-button {
      margin: 0;

      &:first-child {
        margin-left: 0;
      }

      &:last-child {
        margin-right: 0;
      }

      + .md-button {
        margin-left: 4px;
      }
    }
    @include md-layout-small {
      padding-left: 8px;
      padding-right: 8px;
    }
  }
</style>
