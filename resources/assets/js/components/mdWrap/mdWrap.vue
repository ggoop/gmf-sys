<script>
  import common from 'gmf/core/utils/common';

  export default {
    name:'MdWrap',
    render: function(createElement) {
        var tag=common.snakeCase(this.name);
        return createElement(tag);
    },
    props: {
        name: {
            type: String,
            required: true
        }
    }
};
</script>
<style lang="scss">
  @import "~components/MdAnimation/variables";
</style>