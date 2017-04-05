<template>
  <md-sidenav class="md-left md-app-menu has-extend" ref="mainSidenav">
  <div class="md-app-menu-container layout-row">
    <div class="md-app-menu-content layout-column">
      <md-toolbar>
        <div class="md-toolbar-container">
          <md-button class="md-icon-button" @click.native="toggle()">
            <md-icon>arrow_back</md-icon>
          </md-button>
          <h2 class="md-title"><img src="/img/logo.png"/></h2>
        </div>
      </md-toolbar>
      <div class="nav-body flex">
        <nav class="nav">
          <a v-wave @click="goNav('mdDashboard',$event)">
            <span class="icon"><md-icon v-colors="{color:'red-700-0.8'}">dashboard</md-icon></span>
            <span>首页</span>
          </a>
          <a v-wave>
            <span class="icon"><md-icon v-colors="{color:'cyan'}">verified_user</md-icon></span>
            <span>基础数据</span>
          </a>
          <a v-wave>
            <span class="icon"><md-icon v-colors="{color:'green'}">donut_small</md-icon></span>
            <span>组织架构</span>
          </a>
          <a v-wave>
            <span class="icon"><md-icon v-colors="{color:'indigo'}">assessment</md-icon></span>
            <span>阿米巴经营</span>
          </a>
        </nav>
        <nav class="nav">
          <a v-wave>
            <span class="icon"><md-icon v-colors="{color:'teal'}">feedback</md-icon></span>
            <span>通知</span>
          </a>
          <a v-wave>
            <span class="icon"><md-icon v-colors="{color:'teal'}">account_circle</md-icon></span>
            <span>个人资料</span>
          </a>
        </nav>
        <nav class="nav">
          <a v-wave>
            <span class="icon"><md-icon v-colors="{color:'teal'}">settings</md-icon></span>
            <span>系统管理</span>
          </a>
          <a v-wave>
            <span class="icon"><md-icon v-colors="{color:'teal'}">bug_report</md-icon></span>
            <span>系统建设</span>
          </a>
        </nav>
      </div>
      <div class="nav-footer"> <p>©2017 amiba.com</p> </div>
    </div>
    <div class="md-app-menu-extend">
      <section v-for="item in extendMenu">
        <div></div>
        <md-subheader>{{item.name}}</md-subheader>
        <ul v-if="item.childs&&item.childs.length" class="nav">
          <li v-for="sItem in item.childs" :class="{'has-child layout':sItem.childs&&sItem.childs.length}">
            <a @click="goNav(sItem,$event)" :class="{'title':sItem.childs&&sItem.childs.length}">{{sItem.name}}</a>
            <ul v-if="sItem.childs&&sItem.childs.length" class="nav">
              <li v-for="ssItem in sItem.childs">
                <a @click="goNav(ssItem,$event)">{{ssItem.name}}</a>
              </li>
            </ul>
          </li>
        </ul>
      </section>
    </div>
  </div>
  </md-sidenav>
</template>
<script>
  export default {
    props: {
      mdToken: String,
      mdTitle: String
    },
    data() {
      return {
        rootMenu: [],
        extendMenu:[],
      };
    },
    methods: {
      toggle() {
        this.$refs.mainSidenav.toggle();
        this.$emit('toggle');
      },
      goNav(nav,event){
        if(!nav)return;
        if(typeof nav==='string'){
          this.$router.push({ name: 'module', params: { module: nav }});
        }else if(nav&&nav.uri){
          this.$router.push({ name: 'module', params: { module:  nav.uri }});
        }
        this.$refs.mainSidenav.close();
      },
      loadData(){
        this.$http.get('sys/menus/all').then(response => {
            this.extendMenu = response.data.data;
          }, response => {
            console.log(response);
          });
      }
    },
    created(){
      this.loadData();
    }
  };
</script>
