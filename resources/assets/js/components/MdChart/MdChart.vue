<template>
  <div class="md-chart"></div>
</template>
<script>
import extend from 'lodash/extend'
import debounce from 'lodash/debounce'
import Highcharts from 'highcharts';
var defaultOpts = {
  credits: { enabled: false },
};
export default {
  name: 'MdChart',
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
  watch: {
    options: function(options) {
      if (!this.chart && options) {
        this.init();
      } else {
        this.chart.update(this.formatOption(this.options));
      }
    }
  },
  methods: {
    formatOption(options) {
      return extend({}, defaultOpts, options);
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
          this.resizeHanlder = debounce(() => {
            this.chart && this.chart.reflow();
          }, 100, { leading: true });

          if (document.addEventListener) {
            window.addEventListener(this.resizeEvt, this.resizeHanlder, false);
          }
        }
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
</style>
