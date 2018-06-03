import MdTheme from 'gmf/core/MdTheme'
import deepmerge from 'deepmerge'
import mdBem from 'gmf/core/mixins/MdBem/MdBem';

export default function (newComponent) {
  newComponent.mixins = newComponent.mixins || [];
  newComponent.methods = newComponent.methods || {};
  newComponent.mixins.push(mdBem);

  const defaults = {
    props: {
      mdTheme: null
    },
    computed: {
      $mdActiveTheme() {
        const { enabled, getThemeName, getAncestorTheme } = MdTheme

        if (enabled && this.mdTheme !== false) {
          return getThemeName(this.mdTheme || getAncestorTheme(this))
        }
        return null
      }
    }
  }

  return deepmerge(defaults, newComponent)
}
