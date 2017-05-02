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
    </md-part-toolbar>
    <md-part-body>
      <md-content class="flex">
        <md-input-container>
          <label>名称</label>
          <md-input required maxlength="10" v-model="model.main.name"></md-input>
        </md-input-container>
        <md-input-container>
          <label>回调地址</label>
          <md-input required v-model="model.main.redirect"></md-input>
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
  export default {
    data() {
      return {
        model: { main: {} },
        loading: 0,
        route:''
      };
    },
    computed: {
      canSave() {
        return this.validate(true);
      }
    },
    methods: {
      validate(notToast){
        var validator=this.$validate(this.model.main,{'name':'required|max:255|min:3'});
        var fail=validator.fails();
        if(fail&&!notToast){
          this.$toast(validator.errors.all());
        }
        return !fail;
      },
      save() {
          if (!this.validate()) {
              return false;
          }
          var iterable;
          if (this.model.main && this.model.main.id) {
              iterable = this.$http.put(this.route + '/' + this.model.main.id, this.model.main);
          } else {
              iterable = this.$http.post(this.route, this.model.main);
          }
          this.loading++;
          iterable && iterable.then(response => {
              this.$set(this.model, 'main', response.data.data || {});
              this.loading--;
              this.$toast(this.$lang.LANG_SAVESUCCESS);
          }, response => {
              this.$toast(response);
              this.$toast(this.$lang.LANG_SAVEFAIL);
              this.loading--;
          });
      },
      initModel(){
        return {
          main:{'name':'','redirect':'','memo':''}
        }
      },
      list() {
        this.$router.push({ name: 'module', params: { module: 'oauth.client.list' }});
      },
      create() {
            var m = this.initModel();
            if (m) {
                this._.forOwn(m, (value, key) => {
                    this.$set(this.model, key, value);
                });
            }
        },
        cancel() {
            if (this.model.main && this.model.main.id) {
                this.load();
            } else {
                this.create();
            }
        },
        load(id) {
            if(this.model.main && this.model.main.id){
                id=this.model.main.id;
            }
            if (id) {
                this.loading++;
                this.$http.get(this.route + '/' + id).then(response => {
                    this.$set(this.model, 'main', response.data.data || {});
                    this.loading--;
                }, response => {
                    this.$toast(response);
                    this.$toast(this.$lang.LANG_LOADFAIL);
                    this.loading--;
                });
            } else {
                this.create();
            }
        },
    },
    created() {
      this.route='/oauth/clients';
      if (this.$route && this.$route.params && this.$route.params.id) {
          this.model.main.id = this.$route.params.id;
      }
    },
    mounted() {
        this.load();
    },
  };
</script>
