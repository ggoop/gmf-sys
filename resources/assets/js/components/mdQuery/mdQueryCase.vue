<template>
  <div class="md-query-case">
    <slot></slot>
    <md-dialog ref="caseDialog" @open="onOpen"  @close="onClose" class="md-query-case-dialog">
      <md-dialog-content class="no-padding">
        <md-tabs class="md-accent" md-right>
          <md-tab md-label="条件" md-icon="filter_list">
            <md-layout md-gutter>
              <md-layout md-flex="100" v-for="item in options.wheres" :key="item">
                <md-input-container class="md-inset">
                  <md-avatar>
                    <md-icon>attach_file</md-icon>
                  </md-avatar>
                  <label>{{item.name}}</label>
                  <md-input v-if="item.type=='input'"></md-input>
                  <md-input-ref v-else-if="item.type=='ref'"></md-input-ref>
                  <md-date v-else-if="item.type=='date'"></md-date>
                  <md-input v-else></md-input>
                </md-input-container>
              </md-layout>
            </md-layout>
          </md-tab>

          <md-tab md-label="栏目" md-icon="more_horiz">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt dolorum quas amet cum vitae, omnis! Illum quas voluptatem, expedita iste, dicta ipsum ea veniam dolore in, quod saepe reiciendis nihil.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt dolorum quas amet cum vitae, omnis! Illum quas voluptatem, expedita iste, dicta ipsum ea veniam dolore in, quod saepe reiciendis nihil.</p>
          </md-tab>

          <md-tab md-label="排序" md-icon="sort">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt dolorum quas.</p>
          </md-tab>
        </md-tabs>
      </md-dialog-content>
      <md-dialog-actions>
        <span class="flex"></span>
        <md-button class="md-accent md-raised" @click.native="query">确定</md-button>
        <md-button class="md-warn" @click.native="cancel">取消</md-button>
      </md-dialog-actions>
      <md-loading :loading="loading"></md-loading>
    </md-dialog>
  </div>
</template>
<script>
  const defaultOpts={
    wheres:{},
    columns:{},
    orders:{}
  };
  export default {
    props: {
      options:{
        type:Object,
        default(){
          return {
            wheres:{},
            columns:{},
            orders:{}
          }
        }
      }
    },
    data(){
      return {
        loading:0
      }
    },
    methods: {
      query(){
        this.$emit('query',this.options);
      },
      open(){
        this.$refs.caseDialog.open();
      },
      cancel(){
        this.$refs.caseDialog.close();
      },
      onOpen(){
        this.$emit('open',this.options);
      },
      onClose(){
        this.$emit('close',this.options);
      },
    },
    mounted() {
      this.options.wheres={};
      this.options.wheres['w1']={name:'组织',type:'input'};
      this.options.wheres['w2']={name:'日期',type:'date'};
      this.options.wheres['w3']={name:'期间',type:'input'};
      this.options.wheres['w4']={name:'企业',type:'input'};
      this.options.wheres['w5']={name:'核算目的',type:'ref'};
      this.options.wheres['w6']={name:'阿米巴单位',type:'input'};
    },
  };
</script>
