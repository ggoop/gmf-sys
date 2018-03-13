<template>
  <canvas v-bind="attributes"></canvas>
</template>
<script>
import Chart from 'chart.js';
import MdUuid from 'gmf/core/utils/MdUuid';
export default {
  name: 'MdChartJs',
  props: {
    id: {
      type: String,
      default: () => 'md-chart-' + MdUuid()
    },
    mdType: {
      type:String,
      default:'line'
    },
    mdData: Object,
    mdOptions:Object
  },
  data() {
    return {
      chart: null
    }
  },
  computed: {
    attributes () {
      return {
        id: this.id
      }
    }
  },
  methods: {
    init(options) {
      options = options || this.mdOptions;
      if (!this.chart && options) {
        this.chart = new Chart(this.$el,{type:this.mdType,data:this.mdData,options:this.mdOptions});
      }
    }
  },
  watch: {
    mdOptions: function(options) {
      
    }
  },
  mounted() {
    if (!this.chart && this.mdOptions) {
      this.init();
    }
  },
  beforeDestroy() {
    if (this.chart) {
      this.chart.destroy();
    }
  }
}

</script>
<style lang="scss">
@import "~components/MdAnimation/variables";

</style>
