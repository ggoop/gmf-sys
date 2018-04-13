<template>
  <md-field class="md-chips md-ref-input" ref="MdField" @blur="onBlur" :class="[$mdActiveTheme,chipsClasses]">
    <label v-if="mdLabel">{{mdLabel}}</label>
    <slot v-if="!mdStatic" />
    <md-chip v-for="(chip, key) in selectedValues" :key="chip.id" :md-deletable="!mdStatic&&!disabled" :md-clickable="!mdStatic&&!disabled" @keydown.enter="!disabled&&$emit('md-click', chip, key)" @click.native="!disabled&&$emit('md-click', chip, key)" @md-delete.stop="!disabled&&removeChip(chip)">
      <slot name="md-chip" :chip="chip" v-if="$scopedSlots['md-chip']">{{ chip }}</slot>
      <template v-else>{{ chip.name }}</template>
    </md-chip>
    <md-input ref="input" :readonly="!canInput" v-model.trim="inputValue" :disabled="disabled" v-show="!disabled&&!mdStatic && modelRespectLimit" :type="inputType" :id="id" :placeholder="mdPlaceholder" @keydown.enter="insertChip" @keydown.8="handleBackRemove" @dblclick.native="openRef()">
    </md-input>
    <md-button v-if="!disabled" class="md-dense md-icon-button md-ref-filter" @click.native="openRef()">
      <md-icon>search</md-icon>
    </md-button>
    <md-ref v-if="mdRefId" :multiple="multiple" :md-init="mdInit" :md-active.sync="showRefDia" @confirm="onRefConfirm" :md-ref-type="mdRefType" :md-ref-id="mdRefId" :options="options"></md-ref>
  </md-field>
</template>
<script>
import MdComponent from 'core/MdComponent'
import MdField from 'components/MdField/MdField'
import MdInput from 'components/MdField/MdInput/MdInput'
import MdUuid from 'core/utils/MdUuid'
import MdPropValidator from 'core/utils/MdPropValidator'
import common from 'core/utils/common';

import isString from 'lodash/isString'
import isArray from 'lodash/isArray'
import isObject from 'lodash/isObject'
export default new MdComponent({
  name: 'MdRefInput',
  components: {
    MdField,
    MdInput
  },
  props: {
    value: [Array, String, Object],
    disabled: Boolean,
    multiple: Boolean,
    mdRefId: String,
    id: {
      type: [String, Number],
      default: () => 'md-chips-' + MdUuid()
    },
    mdRefType: {
      type: String,
      default: 'entity',
      ...MdPropValidator('md-ref-type', ['entity', 'enum', 'text'])
    },
    mdLabel: String,
    mdPlaceholder: [String, Number],
    mdStatic: Boolean,
    mdLimit: Number,
    mdFormat: {
      type: Function
    },
    mdInit: { type: Function },
  },
  data: () => ({
    inputValue: '',
    selectedValues: [],
    options: { wheres: {}, orders: [] },
    showRefDia: false
  }),
  computed: {
    inputType() {
      return 'text'
    },
    canInput() {
      return this.mdRefType == 'text'
    },
    chipsClasses() {
      return {
        'md-has-value': this.selectedValues && this.selectedValues.length > 0
      }
    },
    modelRespectLimit() {
      return !this.mdLimit || this.value.length < this.mdLimit
    }
  },
  watch: {
    value(value) {
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
      this.selectedValues = [];
      if (common.isArray(value)) {
        value.forEach(v => {
          this.addValue(v);
        });
      } else {
        this.addValue(value);
      }
      this.emitChange();
    },
    addValue(value) {
      if (!value) return;
      if (isString(value)) {
        value = { name: value, id: value };
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
      }
    },
    emitChange() {
      var values = null;
      if (this.multiple) {
        values = [];
        if (this.mdRefType == 'entity' || this.mdRefType == 'enum') {
          this.selectedValues.each(function(v) {
            v && values.push(v);
          });
          // values = this.selectedValues.filter(v => !!v);
        } else if (this.mdRefType == 'text') {
          this.selectedValues.each(function(v) {
            v && v.name && values.push(v.name);
          });
          // values = this.selectedValues.map(v => v.name).filter(v => !!v);
        }
        if (values.length == 0) values = null;
      } else if (this.selectedValues.length) {
        if (this.mdRefType == 'entity' || this.mdRefType == 'enum') {
          values = this.selectedValues[0];
        } else if (this.mdRefType == 'text') {
          values = this.selectedValues[0] ? this.selectedValues[0].name : '';
        }
      }
      this.$emit('input', values);
    },
    getValueIndex(value) {
      for (var i = 0; i < this.selectedValues.length; i++) {
        if (isObjectvalue) && value.id && isObject(this.selectedValues[i]) && this.selectedValues[i].id == value.id) {
          return i;
        }
        if (isObjectvalue) && value.code && isObject(this.selectedValues[i]) && this.selectedValues[i].code == value.code) {
          return i;
        }
        if (isString(value) && isObject(this.selectedValues[i]) && this.selectedValues[i].code == value) {
          return i;
        }
        if (isString(value) && isObject(this.selectedValues[i]) && this.selectedValues[i].name == value) {
          return i;
        }
        if (isString(value) && isString(this.selectedValues[i]) && this.selectedValues[i] == value) {
          return i;
        }
      }
      return -1;
    },
    openRef() {
      if (this.disabled) return;
      this.$emit('mdPick', this.options);
      if (this.mdRefId) {
        this.showRefDia = true;
      }
    },
    onRefConfirm(data) {
      if (!data || data.length == 0) return;
      if (!this.multiple) this.selectedValues = [];
      data && data.forEach((row, index) => {
        this.addValue(row);
      });
      this.emitChange();
    },
    insertChip({ target }) {
      if (this.disabled) return;
      if (!this.inputValue ||
        this.getValueIndex(this.inputValue) >= 0 ||
        !this.modelRespectLimit
      ) {
        return
      }
      const value = this.inputValue.trim();
      this.inputValue = '';
      this.addValue(value);
      this.emitChange();
    },
    removeChip(chip) {
      const index = this.getValueIndex(chip);
      if (index >= 0) {
        this.selectedValues.splice(index, 1);
        this.emitChange();
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
