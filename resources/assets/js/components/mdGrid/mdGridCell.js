export default {
  functional: true,

  props: ['column', 'row', 'type'],

  render(createElement, { props, children, slots }) {
    const type = props.type || 'td';
    const data = {
      class: "md-grid-table-" + type
    };

    if (props.column && props.column.cellClass) {
      data.class = data.class + " " + props.column.cellClass;
    }
    if (props.column && props.column.template) {
      return createElement(type, data, props.column.template(props.row.data));
    }
    if (children && children.length && slots) {
      return createElement(type, data, children);
    }
    data.domProps = {};
    if (props.column) {
      data.domProps.innerHTML = props.column.formatter(props.row.getValue(props.column.field), props.row.data);
    }
    return createElement(type, data);
  },
};