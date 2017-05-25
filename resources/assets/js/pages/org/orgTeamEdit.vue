<template>
  <md-part>
    <md-part-toolbar>
      <md-part-toolbar-group>
        <md-button @click.native="save" :disabled="!canSave">保存</md-button>
        <md-button @click.native="cancel">放弃</md-button>
        <md-button @click.native="create">新增</md-button>
      </md-part-toolbar-group>
      <md-part-toolbar-group>
        <md-button @click.native="list">列表</md-button>
      </md-part-toolbar-group>
      <div class="flex"></div>
      <md-part-toolbar-crumb></md-part-toolbar-crumb>
    </md-part-toolbar>
    <md-part-body>
      <md-content>
        <md-input-container>
          <label>组织</label>
          <md-input-ref md-ref-id="gmf.org.org.ref" placeholder="选择或添加组织" v-model="model.main.org"></md-input-ref>
        </md-input-container>
        <md-input-container>
          <label>部门</label>
          <md-input-ref md-ref-id="gmf.org.dept.ref" placeholder="选择或添加部门" v-model="model.main.dept"></md-input-ref>
        </md-input-container>
        <md-input-container>
          <label>工作中心</label>
          <md-input-ref md-ref-id="gmf.org.work.ref" placeholder="选择或添加工作中心" v-model="model.main.work"></md-input-ref>
        </md-input-container>
        <md-input-container>
          <label>编码</label>
          <md-input required maxlength="10" v-model="model.main.code"></md-input>
        </md-input-container>
        <md-input-container>
          <label>名称</label>
          <md-input required v-model="model.main.name"></md-input>
        </md-input-container>
        <md-input-container>
          <label>备注</label>
          <md-textarea maxlength="70" v-model="model.main.memo"></md-textarea>
        </md-input-container>
      </md-content>
      <md-loading :loading="loading"></md-loading>
    </md-part-body>
  </md-part>
</template>
<script>
 import model from '../../core/mixin/model';
  export default {
    data() {
      return {
      };
    },
    mixins: [model],
    computed: {
      canSave() {
        return this.validate(true);
      }
    },
    methods: {
      validate(notToast){
        var validator=this.$validate(this.model.main,{
          'code':'required|max:255|min:3',
          'name':'required'
        });
        var fail=validator.fails();
        if(fail&&!notToast){
          this.$toast(validator.errors.all());
        }
        return !fail;
      },
      initModel(){
        return {
          main:{'code':'','name':'','memo':'','org':null,'dept':null,'work':null}
        }
      },
      list() {
        this.$router.push({ name: 'module', params: { module: 'org.team.list' }});
      },
    },
    created() {
      this.route='org/teams';
    },
  };
</script>
