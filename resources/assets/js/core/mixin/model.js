export default {
  data() {
    return {
      model: {
        main: {},
        entity: '',
        pager: {
          firstId: '',
          lastId: '',
          prevId: '',
          nextId: '',
          total_items: 0
        },
        order: 'created_at',
        wheres: {}
      },
      loading: 0,
      route: ''
    };
  },
  computed: {
    canCopy() {
      return !!this.model.main.id;
    }
  },
  watch: {
    'model.main.id': function(value, oldValue) {
      value && this.loadData(value);
    }
  },
  methods: {
    validate() {
      return true;
    },
    initModel() { return {}; },

    createInitData() {
      var m = this.initModel();
      if (m) {
        this._.forOwn(m, (value, key) => {
          this.$set(this.model, key, value);
        });
      }
      this.afterInitData();
    },
    afterInitData() {},
    beforeCreate() { return true },
    create() {
      if (this.beforeCreate() !== false) {
        this.$goID('-' + this._.uniqueId(), {}, true);
      }
    },
    cancel() {
      this.loadData(this.getRouteIdValue());
    },
    afterCancel() {},

    copy() {
      if (this.model.main && this.model.main.id) {
        this.model.main.id = null;
        if (this.model.main.code) {
          this.model.main.code = '';
        }
      }
      this.afterCopy();
      this.$toast('复制成功，请保存!');
    },
    afterCopy() {},

    async loadData(id) {
      if (!id && this.model.main && this.model.main.id) {
        id = this.model.main.id;
      }
      if (id) {
        this.model.main.id = id;
        this.loading++;
        try {
          const response = await this.$http.get(this.route + '/' + id);
          this.$set(this.model, 'main', response.data.data || {});
          this.afterLoadData(response.data.data);
          this.loading--;
        } catch (error) {
          this.loading--;
          this.$toast(error);
          this.afterLoadData(false);
        }
      } else {
        this.createInitData();
      }
      await this.loadPagerInfo(id);
    },
    afterLoadData(data) {},

    async serverStore() {
      if (!this.validate()) {
        return false;
      }
      if (this.beforeSave(this.model.main) === false) {
        return false;
      }
      try {
        var response;
        this.loading++;
        if (this.model.main && this.model.main.id) {
          response = await this.$http.put(this.route + '/' + this.model.main.id, this.model.main);
        } else {
          response = await this.$http.post(this.route, this.model.main);
        }
        this.$set(this.model, 'main', response.data.data || {});
        this.afterSave(response.data.data);
        this.loading--;
        return true;
      } catch (error) {
        this.$toast(error);
        this.loading--;
        return false;
      }
      return true;
    },
    beforeSave(data) {},
    async save() {
      const tag = await this.serverStore();
      if (tag) {
        this.$toast(this.$lang.LANG_SAVESUCCESS);
        this.$goID(this.model.main.id, {}, true);
      }
    },
    afterSave(data) {},

    async importData(file){
      try {
        var response = await this.$http.post(this.route+'/import',{files:file});
        this.$toast('导入成功!');
        this.loading--;
        return true;
      } catch (error) {
        this.$toast(error);
        this.loading--;
        return false;
      }
      return true;
    },
    paging(id) {
      this.$goID(id, {}, true);
    },
    async loadPagerInfo(id) {
      try {
        const response = await this.$http.get('sys/entities/pager', {
          params: {
            entity: this.model.entity,
            id: id,
            order: this.model.order,
            wheres: this.model.wheres
          }
        });
        this.$set(this.model, 'pager', response.data.data);
        return true;
      } catch (error) {
        return false;
      }
    },
    getRouteIdValue() {
      if (this.$route &&
        this.$route.params &&
        this.$route.params.id &&
        this.$route.params.id.substr(0, 1) !== '-') {
        return this.$route.params.id;
      }
      return false;
    }
  },
  created() {
    this.createInitData();
  },
  mounted() {
    const rid = this.getRouteIdValue();
    if (rid) {
      this.model.main.id = rid;
    } else {
      this.loadPagerInfo();
    }
  },
};