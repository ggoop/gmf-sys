import MdTheme from 'gmf/core/MdTheme'
import deepmerge from 'deepmerge'
import mdBem from 'gmf/core/mixins/MdBem/MdBem';

function isDef(value) {
    return value !== undefined && value !== null;
}

function isObj(x) {
    const type = typeof x;
    return x !== null && (type === 'object' || type === 'function');
}
export default function(newComponent) {
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
        },
        methods: {
            isDef,
            isObj
        }
    }

    return deepmerge(defaults, newComponent)
}