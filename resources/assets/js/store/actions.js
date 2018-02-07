import Vue from 'vue'
import * as types from './mutation-types'

export default {
  [types.SET_PAGE_TITLE]({ commit }, title) {
    const metaTitle = document.querySelector('meta[property="og:title"]');
    const metaUrl = document.querySelector('meta[property="og:url"]');

    document.title = `${title}`;

    if (metaTitle) {
      metaTitle.setAttribute('content', document.title)
    }
    if (metaUrl) {
      metaUrl.setAttribute('content', window.location.href)
    }
    commit(types.SET_PAGE_TITLE, title)
  },
  [types.CHANGE_THEME]({ commit }, theme) {
    Vue.material.theming.theme = theme
    commit(types.CHANGE_THEME, theme)
  },
  [types.SET_APP_NAME]({ commit }, name) {
    commit(types.SET_APP_NAME, name)
  }
}
