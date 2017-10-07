export default {
  functional: true,

  props: ['column', 'row', 'type'],

  render(createElement, context) {
    const type = context.props.type || 'td';
    const data = context.data;
    data.class=data.class||[];
    data.domProps = data.domProps || {};

    if (context.props.column && context.props.column.cellClass) {
      data.class.push(context.props.column.cellClass);
    }
    if (context.props.column && context.props.column.template) {
      return createElement(type, data, context.props.column.template(context.props.row.data));
    }
    if (context.children && context.children.length && context.slots) {
      return createElement(type, data, context.children);
    }
    if (context.props.column) {
      data.domProps.innerHTML = context.props.column.formatter(context.props.row.getValue(context.props.column.field), context.props.row.data);
    }
    return createElement(type, data);
  },
};