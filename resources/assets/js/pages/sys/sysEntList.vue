<template>
  <md-part>
    <md-subheader>我的企业</md-subheader>
    <md-content class="md-cards padding">
      <md-card v-for="item in datas" :key="item">
        <md-card-area>
          <md-card-media>
            <img src="/img/vendor/gmf-sys/cover/1.jpg">
          </md-card-media>
          <md-card-header>
            <div class="md-title">{{ item.name }}</div>
            <div class="md-subhead">{{ item.memo }}</div>
          </md-card-header>
        </md-card-area>
        <md-card-actions>
          <md-button>Action</md-button>
          <md-button>Action</md-button>
        </md-card-actions>
      </md-card>
    </md-content>
  </md-part>
</template>
<script>
  export default {
    data() {
      return {
        datas:[],
        loading:0
      };
    },
    methods: {
      loadData(){
        this.$http.get('sys/ents/my').then(response => {
            //this.datas=response.data.data;
            for (var i = 0; i <20; i++) {
              this.datas.push(response.data.data[0]);
            }
          }, response => {
        });
      },
    },
    mounted() {
      this.loadData();
    },
  };
</script>
