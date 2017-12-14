import * as types from './mutation-types'

export default {
  [types.SET_PAGE_TITLE] (state, title) {
    state.pageTitle = title
  },
  [types.SHOW_MENU] (state) {
    state.menuVisible = true
  },
  [types.HIDE_MENU] (state) {
    state.menuVisible = false
  },
  [types.CHANGE_THEME] (state, theme) {
    state.theme = theme
  }
}
