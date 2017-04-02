<template>
  <button class="md-button" v-wave="!disabled" :class="[themeClass]" :type="type" :disabled="disabled" v-if="!href">
    <slot></slot>
  </button>
  <a class="md-button" v-wave="!disabled" :class="[themeClass]" :href="href" :disabled="disabled" :target="target" :rel="newRel" v-else>
    <slot></slot>
  </a>
</template>
<script>
  import theme from '../../core/components/mdTheme/mixin';
  export default {
    props: {
      href: String,
      target: String,
      rel: String,
      type: {
        type: String,
        default: 'button'
      },
      disabled: Boolean
    },
    mixins: [theme],
    computed: {
      newRel() {
        if (this.target === '_blank') {
          return this.rel || 'noopener';
        }
        return this.rel;
      }
    }
  };
</script>
