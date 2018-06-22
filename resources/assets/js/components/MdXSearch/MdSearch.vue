<template>
  <div :class="b({ 'show-action': showAction })" :style="{ background }">
    <md-x-icon name="search" />
    <md-x-field
      v-bind="$attrs"
      v-on="listeners"
      :value="value"
      type="search"
      icon="clear"
      :border="false"
      @click-icon="$emit('input', '')"
    />
    <div v-if="showAction" :class="b('action')" >
      <slot name="action">
        <div :class="b('cancel')" @click="onBack">取消</div>
      </slot>
    </div>
  </div>
</template>

<script>
import create from 'gmf/core/MdComponent';

export default new create({
  name: 'MdXSearch',
  inheritAttrs: false,
  components: {
  },
  props: {
    value: String,
    showAction: Boolean,
    background: {
      type: String,
      default: '#f2f2f2'
    }
  },

  computed: {
    listeners() {
      return {
        ...this.$listeners,
        input: this.onInput,
        keypress: this.onKeypress
      };
    }
  },

  methods: {
    onInput(value) {
      this.$emit('input', value);
    },

    onKeypress(event) {
      // press enter
      if (event.keyCode === 13) {
        event.preventDefault();
        this.$emit('search', this.value);
      }
      this.$emit('keypress', event);
    },

    onBack() {
      this.$emit('input', '');
      this.$emit('cancel');
    }
  }
});
</script>
<style lang="scss">
.md-x-search {
  display: flex;
  align-items: center;
  box-sizing: border-box;
  padding: 6px 15px;
  position: relative;

  &--show-action {
    padding-right: 0;
  }

  .md-x-cell {
    flex: 1;
    border-radius: 4px;
    padding: 3px 10px 3px 35px;
  }

  input {
    &::-webkit-search-decoration,
    &::-webkit-search-cancel-button,
    &::-webkit-search-results-button,
    &::-webkit-search-results-decoration {
      display: none;
    }
  }

  &__action {
    line-height: 34px;
    font-size: 14px;
    letter-spacing: 1px;
  }

  &__cancel {
    padding: 0 10px;
    color: #666;

    &:active {
      background-color:  #e8e8e8;
    }
  }

  .md-x-icon-search {
    top: 50%;
    left: 25px;
    z-index: 1;
    color:#666;
    position: absolute;
    font-size: 16px;
    transform: translateY(-50%);
  }
  .md-x-icon-clear {
    color: #BBB;
  }
}

</style>
