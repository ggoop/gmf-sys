<template>
  <div class="md-part-toolbar-crumb layout layout-align-center-center">
    <a v-for="item in paths" :key="item" @click.native="goNav(item)">{{ item.name }</a>
  </div>
</template>
<script>
  export default {
    data() {
      return {
        menuID:'',
        paths:[]
      };
    },
    methods: {
      loadPaths() {
        if(!this.menuID)return;
        this.$http.get('sys/menus/path/'+this.menuID).then(response => {
          this.paths=response.data.data;
        }, response => {
        });
      },
      goNav(item){

      },
    },
    mounted() {
      this.menuID=this.$route.params.module||this.$route.params.app;
      this.loadPaths();

    },
  };
</script>
