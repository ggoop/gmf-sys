<template>
  <div class="md-chart"></div>
</template>
<script>
import Highcharts from 'highcharts';
var defaultOpts = {
  credits: { enabled: false },
  title: {
    style: { "color": "#222222", "fontSize": "24px" }
  },
  subtitle: {
    style: { "fontSize": "14px" }
  },
  chart: {
    style: { fontFamily: '\"PingFang SC\",\"Hiragino Sans GB\",\"Microsoft YaHei\","\"Lucida Grande\"' }
  }
};
export default {
  name:'MdChart',
  props: {
    options: Object,
    autoResize: Boolean
  },
  data() {
    return {
      msg: 1,
      chart: null,
      resizeEvt: ''
    }
  },
  methods: {
    formatOption(options) {
      return this._.extend({}, defaultOpts, options);
    },
    addSeries(options) {
      this.delegateMethod('addSeries', this.formatOption(options));
    },
    removeSeries() {
      while (this.chart.series.length > 0) {
        this.chart.series[0].remove(false);
      }
      this.chart.redraw();
    },
    mergeOption(options) {
      this.init(options);
      this.delegateMethod('update', this.formatOption(options))
    },
    redraw() {
      this.delegateMethod('redraw');
    },
    showLoading(txt) {
      this.chart && this.chart.showLoading(txt);
    },
    hideLoading() {
      this.chart && this.chart.hideLoading();
    },
    delegateMethod(name, ...args) {
      if (!this.chart) {
        console.error('Cannot call  before the chart is initialized');
        return
      }
      return this.chart[name](...args)
    },
    callback(chart) {
      this.$emit('callback', chart);
    },
    init(options) {
      options = options || this.options;
      if (!this.chart && options) {
        this.chart = new Highcharts.Chart(this.$el, this.formatOption(options), (c) => {
          this.callback(c);
        });
        if (this.autoResize) {
          this.resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize';
          this.resizeHanlder = this._.debounce(() => {
            this.chart && this.chart.reflow();
          }, 100, { leading: true });

          if (document.addEventListener) {
            window.addEventListener(this.resizeEvt, this.resizeHanlder, false);
          }
        }
      }
    }
  },
  watch: {
    options: function(options) {
      if (!this.chart && options) {
        this.init();
      } else {
        this.chart.update(this.formatOption(this.options));
      }
    }
  },
  mounted() {
    if (!this.chart && this.options) {
      this.init();
    }
  },
  beforeDestroy() {
    if (this.autoResize) {
      if (document.removeEventListener) {
        window.removeEventListener(this.resizeEvt, this.resizeHanlder, false);
      }
    }
    if (this.chart) {
      this.chart.destroy();
    }
  }
}
</script>

<style lang="scss">
@import "~components/MdAnimation/variables";
.md-chart {
    min-width: 100%;
}

.md-chart-default {
    .highcharts-title {
        font-size: .18rem!important;
        color: #000!important;
        fill: #000!important;
        margin: 5px!important;
    }
    .md-chart-yaxis {
        color: #dddddd!important;
        fill: #dddddd!important;
    }
    .md-chart-yaxis text {
        fill: #666 !important;
        font-size: 0.14rem !important;
    }
    .md-chart-xaxis {
        color: #ddd!important;
        fill: #ddd!important;
    }
    .md-chart-xaxis text {
        fill: #666666 !important;
        font-size: 0.14rem !important;
    }
    .md-chart-yaxis>highcharts-grid-line {
        stroke: #ddd!important;
    }
    .md-chart-data-labels,
    .md-chart-data-labels text {
        font-size: .13rem!important;
        font-weight: 500!important;
    }
    .highcharts-legend-item>text {
        color: #666!important;
        font-size: .13rem!important;
        fill: #666!important;
    }
    .highcharts-legend-item>rect {
        width: .1rem!important;
        height: .1rem!important;
    }
}
  
</style>
