<template>
  <div class="md-date md-input-ref layout-row">
    <input type="text" 
      ref="input"
      class="md-input" 
       @focus="onFocus"
       @blur="onBlur" 
       :value="value" 
       :disabled="disabled" 
       :required="required" 
       v-on:input="updateValue($event.target.value)" />
    <md-select-date :value="value" @change="updateValue" :option="option"></md-select-date>
  </div>
</template>
<script>
import common from './common';
import getClosestVueParent from '../../core/utils/getClosestVueParent';
import moment from 'moment';
export default {
  mixins: [common],
  props: {
    option: {
      type: Object,
      default () {
        return {
          type: 'day',
          SundayFirst: false,
          week: ['一', '二', '三', '四', '五', '六', '日'],
          month: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
          format: 'YYYY-MM-DD',
          color: {
            checked: '#F50057',
            header: '#3f51b5',
            headerText: '#fff'
          },
          wrapperClass: '',
          placeholder: '',
          buttons: {
            ok: '确定',
            cancel: '取消'
          },
          overlayOpacity: 0.5,
          dismissible: true
        }
      }
    }
  },
  watch: {
    value(value) {
      value=this.formattedValue(value);
      this.setParentValue(value);
      this.updateValues(value);
      this.updateValue(value);
    },
  },
  methods: {
    formattedValue(value){
      value= moment(value||this.value).format(this.option.format);
      return value=='Invalid date'?'':value;
    },
    updateValue: function (value) {
      var formattedValue =this.formattedValue(value);
      if (formattedValue !== value||this.$refs.input.value !=formattedValue) {
        this.$refs.input.value = formattedValue;
      }
      this.setParentValue(formattedValue);
      this.$emit('input',formattedValue);
      this.$emit('change',formattedValue);
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.parentContainer = getClosestVueParent(this.$parent, 'md-input-container');

      if (!this.parentContainer) {
        this.$destroy();
        throw new Error('You should wrap the md-textarea in a md-input-container');
      }
      this.setParentDisabled();
      this.setParentRequired();
      this.updateValues();
      this.updateValue();
    });
  },
}
</script>