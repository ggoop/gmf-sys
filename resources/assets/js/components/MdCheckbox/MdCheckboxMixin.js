import MdRipple from 'components/MdRipple/MdRipple'

export default {
  components: {
    MdRipple
  },
  props: {
    model: [String, Number, Boolean, Array],
    value: {
      type: [String, Number, Boolean],
      default: '1'
    },
    falseValue:{
      type:[String,Number,Boolean],
      default:'0'
    },
    name: [String, Number],
    required: Boolean,
    disabled: Boolean
  },
  model: {
    prop: 'model',
    event: 'change'
  },
  data: () => ({
    rippleActive: false
  }),
  computed: {
    isSelected () {
      if (this.isModelArray) {
        return this.model.includes(this.value)
      }

      if (this.isModelBoolean && this.value === '1') {
        return this.model
      }
      if(this.model === this.value){
        return true;
      }
      if(this.model&&this.value&&this.model.toString() == this.value.toString()){
        return true;
      }
      return false;
    },
    isModelArray () {
      return Array.isArray(this.model)
    },
    isModelBoolean () {
      return typeof this.model === 'boolean'
    },
    checkClasses () {
      return {
        'md-checked': this.isSelected,
        'md-disabled': this.disabled,
        'md-required': this.required
      }
    }
  },
  methods: {
    removeItemFromModel (newModel) {
      const index = newModel.indexOf(this.value)

      if (index !== -1) {
        newModel.splice(index, 1)
      }
    },
    handleArrayCheckbox () {
      const newModel = this.model

      if (!this.isSelected) {
        newModel.push(this.value)
      } else {
        this.removeItemFromModel(newModel)
      }

      this.$emit('change', newModel)
    },
    handleStringCheckbox () {
      if (!this.isSelected) {
        this.$emit('change', this.value)
      } else {
        this.$emit('change', this.falseValue)
      }
    },
    handleBooleanCheckbox () {
      this.$emit('change', !this.model)
    },
    toggleCheck () {
      if (!this.disabled) {
        this.rippleActive = true

        if (this.isModelArray) {
          this.handleArrayCheckbox()
        } else if (this.isModelBoolean) {
          this.handleBooleanCheckbox()
        } else {
          this.handleStringCheckbox()
        }
      }
    }
  }
}
