export default {
  props: {
    value: [String, Number,Object],
    disabled: Boolean,
    required: Boolean,
    maxlength: [Number, String],
    placeholder: String,
    mdContainer:{
      type: String,
      default: 'parent'
    }
  },
  watch: {
    value(value) {
      this.setParentValue(value);
      this.updateValues(value);
    },
    disabled() {
      this.setParentDisabled();
    },
    required() {
      this.setParentRequired();
    },
    placeholder() {
      this.setParentPlaceholder();
    },
    maxlength() {
      this.handleMaxLength();
    }
  },
  methods: {
    handleMaxLength() {
      if(this.mdContainer&&this.parentContainer){
        this.parentContainer.enableCounter = this.maxlength > 0;
        this.parentContainer.counterLength = this.maxlength;
      }
    },
    setParentValue(value) {
      if(this.mdContainer&&this.parentContainer){
        this.parentContainer.setValue(value || this.$el.value);
      }
    },
    setParentDisabled() {
      if(this.mdContainer&&this.parentContainer){
        this.parentContainer.isDisabled = this.disabled;
      }
    },
    setParentRequired() {
      if(this.mdContainer&&this.parentContainer){
        this.parentContainer.isRequired = this.required;
      }
    },
    setParentPlaceholder() {
      if(this.parentContainer){
        this.parentContainer.hasPlaceholder = !!this.placeholder;
      }
    },
    updateValues(value) {
      const newValue = value || this.$el.value || this.value;
      this.setParentValue(newValue);
      if(this.mdContainer&&this.parentContainer){
        this.parentContainer.inputLength = newValue ? newValue.length : 0;
      }
    },
    onFocus() {
      if (this.parentContainer) {
        this.parentContainer.isFocused = true;
      }
    },
    onBlur() {
      if(this.parentContainer){
        this.parentContainer.isFocused = false;
      }
      this.setParentValue();
    },
    onInput() {
      this.updateValues();
      this.$emit('change', this.$el.value);
      this.$emit('input', this.$el.value);
    }
  }
};
