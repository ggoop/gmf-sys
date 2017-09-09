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
      this.loadPagerInfo(value);
    }
  },
  methods: {
    validate() {
      return true;
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
      if (this.save_extend) {
        this.save_extend();
      }
    },
    initModel() {
      return {};
    },
    create() {
      var m = this.initModel();
      if (m) {
        this._.forOwn(m, (value, key) => {
          this.$set(this.model, key, value);
        });
      }
      if (this.create_extend) {
        this.create_extend();
      }
    },
    cancel() {
      if (this.model.main && this.model.main.id) {
        this.load();
      } else {
        this.create();
      }
    },
    copy() {
      if (this.model.main && this.model.main.id) {
        this.model.main.id = null;
        if (this.model.main.code) {
          this.model.main.code = '';
        }
        if (this.copy_extend) {
          this.copy_extend();
        }
        this.$toast('复制成功，请保存!');
      }
    },
    load(id) {
      if (!id && this.model.main && this.model.main.id) {
        id = this.model.main.id;
      }
      if (id) {
        this.model.main.id = id;
        this.loading++;
        this.$http.get(this.route + '/' + id).then(response => {
          this.$set(this.model, 'main', response.data.data || {});
          this.loading--;
        }, response => {
          this.$toast(response);
          this.$toast(this.$lang.LANG_LOADFAIL);
          this.loading--;
        });
        if (this.load_extend) {
          this.load_extend();
        }
      } else {
        this.create();
      }
    },
    paging(id) {
      this.load(id);
    },
    loadPagerInfo(id) {
      this.$http.get('sys/entities/pager', {
        params: {
          entity: this.model.entity,
          id: id,
          order: this.model.order,
          wheres: this.model.wheres
        }
      }).then(response => {
        this.$set(this.model, 'pager', response.data.data);
      }, response => {});
    },
  },
  created() {
    if (this.$route && this.$route.params && this.$route.params.id) {
      this.model.main.id = this.$route.params.id;
    }
  },
  mounted() {
    this.load(this.$route.params.id);
    this.loadPagerInfo(this.$route.params.id);
  },
};