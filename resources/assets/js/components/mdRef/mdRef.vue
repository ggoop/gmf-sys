<template>
  <md-dialog ref="dialog" @open="onRefOpen" class="md-refs-dialog">
      <md-toolbar>
        <h1 class="md-title">{{refInfo.name}}</h1>
        <md-input-container class="md-flex md-header-search">
          <md-input class="md-header-search-input" placeholder="search"></md-input>
        </md-input-container>
        <md-button class="md-icon-button">
          <md-icon>filter_list</md-icon>
        </md-button>
        <md-button class="md-icon-button"  @click.native="cancel()">
          <md-icon>close</md-icon>
        </md-button>
      </md-toolbar>
      <md-dialog-content class="no-padding">
        <md-table @select="onTableSelect" ref="table">
          <md-table-header>
            <md-table-row>
              <md-table-head v-for="(column, columnIndex) in refInfo.fields" v-if="!column.hide&&column.alias!='id'" :key="column">
              {{column.comment||column.name}}
              </md-table-head>
            </md-table-row>
          </md-table-header>
          <md-table-body>
            <md-table-row v-for="(row, rowIndex) in refData" 
              :key="row" 
              :md-item="row" 
              :md-auto-select="!!multiple" 
              :md-selection="!!multiple"
              @dblclick.native="dblclick(row)">
              <md-table-cell v-for="(column, columnIndex) in refInfo.fields" v-if="!column.hide&&column.alias!='id'" :key="columnIndex">
               {{ row[column.alias||column.name] }}
              </md-table-cell>
            </md-table-row>
          </md-table-body>
        </md-table>
      </md-dialog-content>
      <md-dialog-actions>
        <md-table-pagination
          :md-size="pageInfo.size"
          :md-total="pageInfo.total"
          :md-page="pageInfo.page"
          :md-page-options="[5, 10,20]"
          @pagination="onTablePagination">
        </md-table-pagination>
        <span class="flex"></span>
        <md-button class="md-accent md-raised" @click.native="close()">确定</md-button>
        <md-button class="md-warn" @click.native="cancel()">取消</md-button>
      </md-dialog-actions>
      <md-loading :loading="loading"></md-loading>
    </md-dialog>
</template>

<script>
  import theme from '../../core/components/mdTheme/mixin';
  import common from '../../core/utils/common';

  export default {
    props: {
      value: {
        type: Object,
      },
      multiple: {
        type: Boolean,
        default: true
      },
      mdMax: {
        type: Number,
        default: Infinity
      },
      mdPageSize:{
        type:[Number,String],
        default:'5'
      },
      mdRefId: String,
      options:{
        type:Object,
        default(){
          return {
            wheres:{},
            orders:{}
          }
        }
      }
    },
    mixins: [theme],
    data() {
      return {
        currentChip: null,
        selectedRows:[],
        refInfo:{},
        refData:[],
        loading:0,
        refCache:{},
        pageInfo:{
          size:this.mdPageSize,
          total:0,
          page:1
        },
      };
    },
    watch: {
      value(value) {
        if(!value){
           this.selectedRows=[];
        }else{
          if(common.isArray(value)){
            this.selectedRows=value;
          }else{
             this.selectedRows= [value];
          }
        }
      }
    },
    methods: {
      onRefOpen() {
        if(!this.refInfo||!this.refInfo.id||this.refInfo.id!==this.mdRefId){
          if(!this.refCache[this.mdRefId]){
            this.onTablePagination();
          }
        }
      },
      onTablePagination(pager){
        this.selectedRows=[];
        if(this.$refs['table']&&this.$refs['table'].$data){
          this.$refs['table'].$data.selectedRows={};
        }

        this.loading++;
        const params={};
        pager=pager||this.pageInfo;

        this._.extend(params,this.options,pager);
        this.$http.post('sys/queries/query/'+this.mdRefId,params).then(response => {
          this.refInfo = response.data.schema;
          this.refData = response.data.data;
          this.pageInfo.size=response.data.pager.size;
          this.pageInfo.page=response.data.pager.page;
          this.pageInfo.total=response.data.pager.total;
          this.loading--;
        }, response => {
          this.loading--;
        });
      },
      onTableSelect(items){
        this.selectedRows=[];
        Object.keys(items).forEach((row, index) =>{
          this.selectedRows[index]=items[row];
        });
      },
      getReturnValue(){
        return this.selectedRows;
      },
      dblclick(item){
        this.close(item);
      },
      open() {
        this.$emit('init', this.options);
        this.selectedRows=[];
        this.$refs['table'].$data.selectedRows={};
        this.$refs['dialog'].open();
        this.$emit('open');
      },
      cancel(){
        this.$refs['dialog'].close();
        this.$emit('close',false);
      },
      close(data){
        this.$refs['dialog'].close();
        this.$emit('close',data?[data]:this.getReturnValue());
      }
    }
  };
</script>
