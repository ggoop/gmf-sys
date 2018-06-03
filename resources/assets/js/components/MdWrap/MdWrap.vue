<script>
import common from 'gmf/core/utils/common';

export default {
  name: 'MdWrap',
  functional: true,
  render: function(createElement, context) {
    var tag = common.snakeCase(context.props.name);
    return createElement(tag, context.data, context.children);
  },
  props: {
    name: {
      type: String,
      required: true
    }
  }
};

</script>
