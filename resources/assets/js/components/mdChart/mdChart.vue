<template>
  <div class="md-chart"></div>
</template>
<script>
  import Highcharts from 'highcharts'
  require('highcharts/highcharts-3d')(Highcharts);
  var defaultOpts= {
    credits: {enabled: false},
    title:{
      style:{ "color": "#222222", "fontSize": "24px" }
    },
    subtitle:{
      style:{"fontSize": "14px" }
    },
    chart:{
      style:{fontFamily:'\"PingFang SC\",\"Hiragino Sans GB\",\"Microsoft YaHei\","\"Lucida Grande\"'}
    }
  };
  export default {
    props: {
      options: Object,
      autoResize: Boolean
    },
    data () {
      return {
        msg: 1,
        chart: null
      }
    },
    mounted(){
      if (!this.chart && this.options) {
        this._init();
      }
    },
    methods: {
      formatOption(options){
        return Object.assign(defaultOpts,options);
      },
      addSeries(options){
        this.delegateMethod('addSeries', this.formatOption(options));
      },
      mergeOption(options){
        this.delegateMethod('update', this.formatOption(options))
      },
      showLoading(txt){
        this.chart.showLoading(txt);
      },
      hideLoading(){
        this.chart.hideLoading();
      },
      delegateMethod(name, ...args){
        if (!this.chart) {
          console.error('Cannot call  before the chart is initialized');
          return
        }
        return this.chart[name](...args)
      },
      _init(){
        if (!this.chart && this.options) {
          this.chart = new Highcharts.Chart(this.$el,this.formatOption(this.options) );
        }
      }
    },
    watch: {
      options: function (options) {
        if (!this.chart && options) {
          this._init()
        } else {
          this.chart.update(this.formatOption(this.options));
        }
      }
    },
    beforeDestroy(){
      if (this.chart) {
        this.chart.destroy();
      }
    }
  }
</script>