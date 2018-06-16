import material from 'gmf/material'
import MdXArea from './MdXArea'
import MdXAxis from './MdXAxis'
import MdXBar from './MdXBar'
import MdXChart from './MdXChart'
import MdXGuide from './MdXGuide'
import MdXLegend from './MdXLegend'
import MdXLine from './MdXLine'
import MdXPie from './MdXPie'
import MdXPoint from './MdXPoint'
import MdXScale from './MdXScale'
import MdXTooltip from './MdXTooltip'
export default Vue => {
  material(Vue)
  Vue.component(MdXArea.name, MdXArea)
  Vue.component(MdXAxis.name, MdXAxis)
  Vue.component(MdXBar.name, MdXBar)
  Vue.component(MdXChart.name, MdXChart)
  Vue.component(MdXGuide.name, MdXGuide)
  Vue.component(MdXLegend.name, MdXLegend)
  Vue.component(MdXLine.name, MdXLine)
  Vue.component(MdXPie.name, MdXPie)
  Vue.component(MdXPoint.name, MdXPoint)
  Vue.component(MdXScale.name, MdXScale)
  Vue.component(MdXTooltip.name, MdXTooltip)
}
