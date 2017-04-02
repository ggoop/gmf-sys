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
        </md-toolbar>
        <md-dialog-content class="no-padding">
          <md-table @select="onTableSelect">
            <md-table-header>
              <md-table-row>
                <md-table-head v-for="(column, columnIndex) in refInfo.fields">
                {{column.name}}
                </md-table-head>
              </md-table-row>
            </md-table-header>
            <md-table-body>
              <md-table-row v-for="(row, rowIndex) in refData" 
                :key="rowIndex" 
                :md-item="row" 
                :md-auto-select="!multiple" 
                :md-selection="!multiple"
                @dblclick.native="dblclick(row)">
                <md-table-cell v-for="(column, columnIndex) in refInfo.fields" :key="columnIndex">
                  {{ row[column.path] }}
                </md-table-cell>
              </md-table-row>
            </md-table-body>
          </md-table>
        </md-dialog-content>
        <md-dialog-actions>
          <md-table-pagination
              md-size="5"
              md-total="10"
              md-page="1"
              md-label="Rows"
              md-separator="of"
              :md-page-options="[5, 10, 25, 50]"
              @pagination="onTablePagination"></md-table-pagination>
          <span class="flex"></span>
          <md-button class="md-primary" @click.native="cancel()">取消</md-button>
          <md-button class="md-primary" @click.native="close()">确定</md-button>
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
      multiple: Boolean,
      mdMax: {
        type: Number,
        default: Infinity
      },
      mdRefId: String,
    },
    mixins: [theme],
    data() {
      return {
        currentChip: null,
        selectedRows:[],
        refInfo:{},
        refData:[],
        loading:0,
        refCache:{}
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
            this.loadRef(this.mdRefId);
          }
        }
      },
      loadRef(refId){
        if(refId){
          this.loading++;
          this.$http.get('sys/queries/query/'+refId).then(response => {
            this.refInfo = response.data.schema;
            this.refData = response.data.data;
            this.loading--;
          }, response => {
            console.log(response);
            this.loading--;
          });
        }else{
          this.refInfo={};
        }
      },
      onTablePagination(page){
         console.log('onTablePagination page', page);
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
        this.selectedRows=[];
        this.$refs['dialog'].open();
        this.$emit('open');
      },
      cancel(){
        this.$refs['dialog'].close();
        this.$emit('close',null);
      },
      close(data){
        this.$refs['dialog'].close();
        this.$emit('close',data?[data]:this.getReturnValue());
      }
    }
  };
</script>
