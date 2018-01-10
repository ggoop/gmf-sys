<template>
  <md-field class="md-autocomplete md-fetch" :class="fieldClasses" :md-inline="isBoxLayout">
    <md-input v-model="searchTerm" v-bind="$attrs" :maxlength="mdMaxlength" :placeholder="mdPlaceholder" @input="onInput" @keyup.enter.native="debounceUpdate" />
    <md-button class="md-icon-button md-mini">
      <md-icon>{{ mdIcon }}</md-icon>
    </md-button>
    <md-tooltip md-direction="bottom" v-if="mdTooltip">{{mdTooltip}}</md-tooltip>
  </md-field>
</template>
<script>
export default {
  name: 'MdFetch',
  props: {
    value: {
      type: null
    },
    mdIcon: {
      type: String,
      default: 'filter_list'
    },
    mdMaxlength: [String, Number],
    mdTooltip: String,
    mdPlaceholder: {
      type: [String, Number],
      default: '搜索...'
    },
    fetch: {
      type: Function
    },
    mdDebounce: {
      type: Number,
      default: 1E2
    },
  },
  data() {
    return {
      searchTerm: this.value,
      timeout: 0,
      loading: false
    }
  },
  computed: {
    isBoxLayout() {
      return true
    },
    fieldClasses() {
      if (this.isBoxLayout) {
        return 'md-autocomplete-box'
      }
    },
  },
  watch: {
    value(val) {
      this.searchTerm = val
    }
  },
  methods: {
    onInput() {
      this.debounceUpdate();
      this.$emit('input', this.searchTerm);
    },
    debounceUpdate() {
      if (!this.fetch || typeof this.fetch !== 'function') {
        return;
      }
      if (this.timeout) {
        window.clearTimeout(this.timeout);
      }
      this.timeout = window.setTimeout(() => {
        this.loading = true;
        return this.makeFetchRequest(this.searchTerm);
      }, this.mdDebounce);
    },
    makeFetchRequest(queryObject) {
      this.fetch(queryObject);
      this.loading = false;
    },
  }
}

</script>
<style lang="scss">
@import "~components/MdAnimation/variables";
@import "~components/MdLayout/mixins";
.md-fetch.md-focused {
  >.md-button .md-icon {
    color: rgba(0, 0, 0, 0.87)!important;
    color: var(--md-theme-default-text-primary-on-background-variant, rgba(0, 0, 0, 0.87))!important;
  }
}

</style>
