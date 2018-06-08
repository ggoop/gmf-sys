import './style/MdStyle.vue'
//import './style/index.scss'
import MdReactive from 'gmf/core/utils/MdReactive'
import MdTheme from 'gmf/core/MdTheme'

const init = () => {
  let material = new MdReactive({
    ripple: true,
    theming: {},
    locale: {
      startYear: 1900,
      endYear: 2099,
      dateFormat: 'YYYY-MM-DD',
      days: ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
      shortDays: ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
      shorterDays: ['日', '一', '二', '三', '四', '五', '六'],
      months: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
      shortMonths: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
      shorterMonths: ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二']
    }
  })

  Object.defineProperties(material.theming, {
    metaColors: {
      get: () => MdTheme.metaColors,
      set (metaColors) {
        MdTheme.metaColors = metaColors
      }
    },
    theme: {
      get: () => MdTheme.theme,
      set (theme) {
        MdTheme.theme = theme
      }
    },
    enabled: {
      get: () => MdTheme.enabled,
      set (enabled) {
        MdTheme.enabled = enabled
      }
    }
  })

  return material
}

export default Vue => {
  if (!Vue.material) {
    Vue.material = init()
    Vue.prototype.$material = Vue.material
  }
}
