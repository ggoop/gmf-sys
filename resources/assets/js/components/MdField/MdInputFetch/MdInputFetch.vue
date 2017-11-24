<template>
  <input
    class="md-input"
    v-model="model"
    ref="input"
    v-bind="attributes"
    v-on="$listeners"
    @focus="onFocus"
    @blur="onBlur">
</template>

<script>
  import MdComponent from 'core/MdComponent'
  import MdUuid from 'core/utils/MdUuid'
  import MdFieldMixin from '../MdFieldMixin'

  export default new MdComponent({
    name: 'MdInputFetch',
    mixins: [MdFieldMixin],
    inject: ['MdField'],
    props: {
      id: {
        type: String,
        default: () => 'md-input-' + MdUuid()
      },
      type: {
        type: String,
        default: 'text'
      },
      fetch: {
        type: Function
      },
      debounce: {
        type: Number,
        default: 1E2
      },
    },
    data: () => ({
      timeout: 0,
      loading:false
    }),
    computed: {
      toggleType () {
        return this.MdField.togglePassword
      },
      isPassword () {
        return this.type === 'password'
      }
    },
    watch: {
      type (type) {
        this.setPassword(this.isPassword)
      },
      toggleType (toggle) {
        if (toggle) {
          this.setTypeText()
        } else {
          this.setTypePassword()
        }
      }
    },
    methods: {
      setPassword (state) {
        this.MdField.password = state
        this.MdField.togglePassword = false
      },
      setTypePassword () {
        this.$el.type = 'password'
      },
      setTypeText () {
        this.$el.type = 'text'
      },
      debounceUpdate() {
        if(!this.fetch||typeof this.fetch!=='function'){
          return;
        }
        if (this.timeout) {
          window.clearTimeout(this.timeout);
        }
        this.timeout = window.setTimeout(() => {
          this.loading = true;
          const queryObject = this.getElementValue();
          return this.makeFetchRequest(queryObject);
        }, this.debounce);
      },
      makeFetchRequest(queryObject) {
        this.fetch(queryObject);
        this.loading = false;
      },
      getElementValue(){
        return this.$refs.input?this.$refs.input.value:this.$el.value;
      },
    },
    created () {
      this.setPassword(this.isPassword)
    },
    beforeDestroy () {
      this.setPassword(false)
    }
  });
</script>
