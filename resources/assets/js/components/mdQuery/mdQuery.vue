<template>
  <md-table-card class="flex md-query">
    <md-table @select="onTableSelect" class="flex">
      <md-table-header>
        <md-table-row>
          <md-table-head v-for="(column, columnIndex) in refInfo.fields" v-if="!column.hide">
          {{column.name}}
          </md-table-head>
        </md-table-row>
      </md-table-header>
      <md-table-body>
        <md-table-row v-for="(row, rowIndex) in refData" 
          :key="row" 
          :md-item="row" 
          :md-auto-select="mdAutoSelect" 
          :md-selection="mdSelection" 
          @dblclick.native="dblclick(row)">
          <md-table-cell v-for="(column, columnIndex) in refInfo.fields" :key="columnIndex" v-if="!column.hide">
            {{ row[column.path] }}
          </md-table-cell>
        </md-table-row>
      </md-table-body>
    </md-table>
    <md-table-tool>
      <md-layout></md-layout>
      <md-table-pagination
        md-size="5"
        md-total="10"
        md-page="1"
        md-label="Rows"
        md-separator="of"
        :md-page-options="[5, 10, 25, 50]"
        @pagination="onTablePagination">
      </md-table-pagination>
    </md-table-tool>
    
    <md-loading :loading="loading"></md-loading>
  </md-table-card>
</template>

<script>
  import theme from '../../core/components/mdTheme/mixin';

  export default {
    props: {
      mdQueryId: String,
      mdKey:{
        type:String,
        default:'id'
      },
      mdAutoSelect:{
        type:[String,Boolean],
        default:true
      },
      mdSelection:{
        type:[String,Boolean],
        default:true
      }
    },
    mixins: [theme],
    data() {
      return {
        selectedRows:[],
        refInfo:{},
        refData:[],
        loading:0,
      };
    },
    methods: {
      onTablePagination(page){
         this.pagination(page);
      },
      onTableSelect(items){
        this.selectedRows=[];
        Object.keys(items).forEach((row, index) =>{
          this.selectedRows[index]=items[row];
        });
        this.select();
      },
      pagination(page){
        this.loading++;
        this.$http.get('sys/queries/query/'+this.mdQueryId).then(response => {
          this.refInfo = response.data.schema;
          this.refData = response.data.data;
          this.loading--;
          this.$emit('page');
        }, response => {
          this.loading--;
          this.$emit('page');
        });
      },
      select() {
        this.$emit('select',this.selectedRows);
      },
      dblclick(item){
        this.$emit('dblclick',item);
      },
    },
    mounted() {
      if(this.mdQueryId){
        this.pagination();
      }
    },
  };
</script>
