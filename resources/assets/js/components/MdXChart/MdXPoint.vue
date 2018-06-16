<script>
export default {
  name:'MdXPoint',
  props: {
    styles: {
      type: Object
    },
    colors: {
      type: Array
    },
    seriesField: String
  },
  created () {
    this.$parent.setPoint({
      ...this.$props,
      ...this.$attrs
    })
  },
  render () {}
}
</script>