export default {
  data() {
    return {
      loading:0,
    };
  },
  watch: {

  },
  methods: {
    initQuery(options,promise) {
      //options.wheres=[];
      options.wheres.push({ name: '组织', type: 'ref', value: 12, multiple: true, refs: { id: 'gmf.cbo.org.ref' } });

      options.wheres.push({ name: '日期', type: 'date', value: '' });
      options.wheres.push( { name: '期间', type: 'input' });
      options.wheres.push( { name: '企业', type: 'input' });
      options.wheres.push( { name: '核算目的', type: 'ref', multiple: true, refs: { id: 'gmf.amiba.purpose.ref' } });
      options.wheres.push( { name: '阿米巴单位', type: 'ref', multiple: true, refs: { id: 'gmf.amiba.group.ref' } });
      options.wheres.push( { name: '组织', type: 'enum', value: 'aa', multiple: true });
      if(this.atferInitQuery){
        this.atferInitQuery(options,promise);
      }else{
        promise.resolve('ok');
      }
    },
    doQuery(options) {
      if (this.afterQuery) {

      }
    },
    cancelQuery(options) {

    },
  }
};