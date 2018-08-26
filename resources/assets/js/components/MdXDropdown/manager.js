import context from './context';

export default {
  open(vm) {
    /* istanbul ignore next */
    if (!context.stack.some(item => item.vm._popupId === vm._popupId)) {
      if (context.stack.length) {
        context.top.vm.close();
      };
      context.stack.push({
        vm
      });
    }
  },

  close(id) {
    const {
      stack
    } = context;
    if (stack.length) {
      if (context.top.vm._popupId === id) {
        stack.pop();
      } else {
        context.stack = stack.filter(item => item.vm._popupId !== id);
      }
    }
  },
};