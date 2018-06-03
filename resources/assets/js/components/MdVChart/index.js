import material from 'gmf/material'
import MdVArea from './MdVArea'
import MdVAxis from './MdVAxis'
import MdVBar from './MdVBar'
import MdVChart from './MdVChart'
import MdVGuide from './MdVGuide'
import MdVLegend from './MdVLegend'
import MdVLine from './MdVLine'
import MdVPie from './MdVPie'
import MdVPoint from './MdVPoint'
import MdVScale from './MdVScale'
import MdVTooltip from './MdVTooltip'
export default Vue => {
  material(Vue)
  Vue.component(MdVArea.name, MdVArea)
  Vue.component(MdVAxis.name, MdVAxis)
  Vue.component(MdVBar.name, MdVBar)
  Vue.component(MdVChart.name, MdVChart)
  Vue.component(MdVGuide.name, MdVGuide)
  Vue.component(MdVLegend.name, MdVLegend)
  Vue.component(MdVLine.name, MdVLine)
  Vue.component(MdVPie.name, MdVPie)
  Vue.component(MdVPoint.name, MdVPoint)
  Vue.component(MdVScale.name, MdVScale)
  Vue.component(MdVTooltip.name, MdVTooltip)
}
