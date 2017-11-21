<template>
  <div class="md-empty-state" :class="[emptyStateClasses]" :style="emptyStateStyles">
    <div class="md-empty-state-container layout layout-column layout-align-center-center">
      <md-icon v-if="mdIcon" class="md-empty-state-icon">{{ mdIcon }}</md-icon>       
      <strong class="md-empty-state-label" v-if="mdLabel">{{ mdLabel }}</strong>
      <p class="md-empty-state-description" v-if="mdDescription">{{ mdDescription }}</p>
      <slot />
    </div>
  </div>
</template>
<script>
  import MdEmptyStateProps from './MdEmptyStateProps'
  export default {
    name: 'MdEmptyState',
    props: MdEmptyStateProps,
    computed: {
      emptyStateClasses () {
        return {
          'md-rounded': this.mdRounded
        }
      },
      emptyStateStyles () {
        if (this.mdRounded) {
          const size = this.mdSize + 'px'

          return {
            width: size,
            height: size
          }
        }
      }
    }
  };
</script>