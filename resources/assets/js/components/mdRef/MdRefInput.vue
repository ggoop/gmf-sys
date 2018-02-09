<template>
  <md-field class="md-chips md-ref-input" ref="MdField" @blur="onBlur" :class="[$mdActiveTheme]">
    <label v-if="mdLabel">{{mdLabel}}</label>
    <slot v-if="!mdStatic" />
    <md-chip v-for="(chip, key) in selectedValues" :key="chip.id" :md-deletable="!mdStatic&&!disabled" :md-clickable="!mdStatic&&!disabled" @keydown.enter="!disabled&&$emit('md-click', chip, key)" @click.native="!disabled&&$emit('md-click', chip, key)" @md-delete.stop="!disabled&&removeChip(chip)">
      <slot name="md-chip" :chip="chip" v-if="$scopedSlots['md-chip']">{{ chip }}</slot>
      <template v-else>{{ chip.name }}</template>
    </md-chip>
    <md-input ref="input" v-model.trim="inputValue" :disabled="disabled" v-show="!disabled&&!mdStatic && modelRespectLimit" :type="mdInputType" :id="id" :placeholder="mdPlaceholder" @keydown.enter="insertChip" @keydown.8="handleBackRemove" @dblclick.native="openRef()">
    </md-input>
    <md-button v-if="!disabled" class="md-dense md-icon-button md-ref-filter" @click.native="openRef()">
      <md-icon>search</md-icon>
    </md-button>
    <md-ref v-if="mdRefId" :multiple="multiple" :md-active.sync="showRefDia" @confirm="onRefConfirm" :md-ref-id="mdRefId" :options="options"></md-ref>
  </md-field>
</template>
<script>
import MdComponent from 'core/MdComponent'
import MdField from 'components/MdField/MdField'
import MdInput from 'components/MdField/MdInput/MdInput'
import MdUuid from 'core/utils/MdUuid'
import MdPropValidator from 'core/utils/MdPropValidator'
import common from 'core/utils/common';
export default new MdComponent({
  name: 'MdRefInput',
  components: {
    MdField,
    MdInput
  },
  props: {
    value: [Array, Object],
    disabled: Boolean,
    multiple: Boolean,
    mdRefId: String,
    id: {
      type: [String, Number],
      default: () => 'md-chips-' + MdUuid()
    },
    mdInputType: {
      type: [String, Number],
      ...MdPropValidator('md-input-type', ['email', 'number', 'password', 'search', 'tel', 'text', 'url'])
    },
    mdLabel: String,
    mdPlaceholder: [String, Number],
    mdStatic: Boolean,
    mdLimit: Number,
    mdFormat: {
      type: Function
    }
  },
  data: () => ({
    inputValue: '',
    selectedValues: [],
    hasValue: false,
    options: { wheres: {}, orders: [] },
    showRefDia:false
  }),
  computed: {
    chipsClasses() {
      return {
        'md-has-value': this.hasValue
      }
    },

    modelRespectLimit() {
      return !this.mdLimit || this.value.length < this.mdLimit
    }
  },
  watch: {
    value(value) {
      if (!common.isObject(value)) {
        value = null;
      }
      this.setValue(value);
    },
    selectedValues() {
      this.checkHasValue();
    }
  },
  methods: {
    checkHasValue() {
      this.$refs.MdField.MdField.value = this.selectedValues && this.selectedValues.length > 0 ? "1" : '';
    },
    setValue(value) {
      if (!common.isObject(value)) {
        value = null;
      }
      if (!value) {
        this.selectedValues = [];
      } else {
        if (common.isArray(value)) {
          this.selectedValues = value;
        } else {
          this.selectedValues = [value];
        }
      }
    },
    addValue(value) {
      if (!value || !value.id) {
        return;
      }
      if (this.multiple && this.mdLimit > 0 && this.selectedValues.length >= this.mdLimit) {
        return;
      }
      if (!this.multiple && this.selectedValues.length > 0) {
        return;
      }
      const index = this.getValueIndex(value);
      if (index < 0) {
        this.selectedValues.push(value);
        const nv = this.formatValue();
        this.$emit('input', nv);
      }
    },
    getValueIndex(value) {
      for (var i = 0; i < this.selectedValues.length; i++) {
        if (value.id && this.selectedValues[i].id == value.id) {
          return i;
        }
        if (value.code && this.selectedValues[i].code == value.code) {
          return i;
        }
        if (this._.isString(value) && this.selectedValues[i].code == value) {
          return i;
        }
        if (this._.isString(value) && this.selectedValues[i].name == value) {
          return i;
        }
      }
      return -1;
    },
    formatValue() {
      if (!this.multiple) {
        return this.selectedValues.length ? this.selectedValues[0] : null;
      }
      return this.selectedValues;
    },
    openRef() {
      if (this.disabled) return;
      this.$emit('mdPick', this.options);
      if (this.mdRefId) {
        this.showRefDia=true;
      }
    },
    onRefConfirm(data) {
      if (!data || data.length == 0) return;
      if (!this.multiple) this.selectedValues = [];
      data && data.forEach((row, index) => {
        this.addValue(row);
      });
    },
    insertChip({ target }) {
      if (this.disabled) return;
      if (!this.inputValue ||
        this.getValueIndex(this.inputValue) >= 0 ||
        !this.modelRespectLimit
      ) {
        return
      }
      const value = { name: this.inputValue.trim() };
      value.id = value.name;
      this.inputValue = '';
      this.addValue(value);
    },
    removeChip(chip) {
      const index = this.getValueIndex(chip);
      if (index >= 0) {
        this.selectedValues.splice(index, 1);
        const nv = this.formatValue();
        this.$emit('input', nv);
      }
      this.$nextTick(() => this.$refs.input.$el.focus())
    },
    handleBackRemove() {
      if (!this.inputValue && this.selectedValues.length > 0) {
        this.removeChip(this.selectedValues[this.selectedValues.length - 1])
      }
    },
    onBlur() {
      this.checkHasValue();
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.setValue(this.value);
    });
  }
});

</script>
<style lang="scss">
@import "~components/MdAnimation/variables";
.md-ref-input.md-field {
  .md-chip {
    font-size: 16px;
    border-radius: 0 10px 10px 0px;
    margin-bottom: 1px;
    padding-left: 0px;
    overflow: hidden;
    .md-ripple,
    {
      padding-left: 0px;
    }
    &:last-of-type {
      margin-right: 8px;
    }
    &:not(:hover) {
      .md-input-action {
        transform: translate3d(220%, 0px, 0px);
      }
    }
  }
  .md-chip.md-theme-default {
    background-color: transparent;
  }
  .md-ref-filter {
    min-height: auto;
    min-width: 32px;
    height: 32px;
    width: 32px;
    margin: 0px;
    padding: 0px;
  }
  .md-input {
    min-width: auto;
  }
}

</style>
