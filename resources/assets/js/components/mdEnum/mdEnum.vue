<template>
  <md-select :name="name" :id="id" :required="required" :disabled="disabled" :placeholder="placeholder" :multiple="multiple" v-model="selectedValue">
    <md-option v-for="item in visibleDatas" :key="item.id" :value="item.name">{{item.comment}}</md-option>
  </md-select>
</template>
<script>
export default {
  name:'MdEnum',
  props: {
    name: String,
    id: String,
    required: Boolean,
    multiple: Boolean,
    value: [String, Number, Array],
    disabled: Boolean,
    placeholder: String,
    mdEnumId: String,
    items: [Array]
  },
  data() {
    return {
      selectedValue: null,
      options: {},
      enumInfo: {},

    };
  },
  computed: {
    visibleDatas() {
      if (this.enumInfo && this.enumInfo.fields) {

        if (this.items) {
          var exps = [];
          if (this._.isString(this.items)) {
            exps = this.items.split(",");
          } else if (this._.isArray(this.items)) {
            exps = this.items;
          }
          if (exps && exps.length > 0)
            return this.enumInfo.fields.filter(f => exps.indexOf(f.name) >= 0);
          else
            return this.enumInfo.fields;
        }
        return this.enumInfo.fields;
      }
      return [];
    }
  },
  watch: {
    value(value) {
      this.setTextAndValue(value);
    },
    selectedValue(value) {
      this.$emit('input', value);
      this.$emit('change', value);
    }
  },
  methods: {
    setTextAndValue(modelValue) {
      this.selectedValue = modelValue;
    },
    loadData(enumID) {
      if (enumID) {
        let datas = this.$root.getCacheEnum(enumID);
        if (datas) {
          this.enumInfo = datas;
        } else {
          this.$http.get('sys/enums/' + enumID).then(response => {
            this.enumInfo = response.data.data;
            this.$root.setCacheEnum(this.enumInfo);
          }, response => {
            console.log(response);
          });
        }

      } else {
        this.enumInfo = {};
      }
    }
  },
  created() {
    this.loadData(this.mdEnumId);
  },
  mounted() {
    this.value && this.setTextAndValue(this.value);
  },
  beforeDestroy() {}
};
</script>