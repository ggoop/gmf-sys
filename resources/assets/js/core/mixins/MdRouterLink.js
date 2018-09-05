/**
 * add Vue-Router support
 */

export default {
  props: {
    // url: String,
    to: [String, Object],
    replace: Boolean,
    append: Boolean,
    activeClass: String,
    exact: Boolean,
    event: [String, Array],
    exactActiveClass: String
  },

  methods: {
    routerLink() {
      const { to, url, $router, replace } = this;
      if (to && $router) {
        $router[replace ? 'replace' : 'push'](to);
      } else if (url) {
        replace ? location.replace(url) : location.href = url;
      }
    }
  }
};
