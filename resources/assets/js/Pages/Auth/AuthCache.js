class AuthCache {
  constructor() {
    this.storageKey = `gmf.sys.auth.${window.location.host}`;
  }
  get() {
    return JSON.parse(localStorage.getItem(this.storageKey)) || [];
  }
  has(user) {
    return false;
  }
  remove(user){
    if (!user) return;
    const users = this.get();
    var ind=-1;
    for (var i = 0; i < users.length; i++) {
      if (users[i].id == user.id) {
        ind=i;
      }
    }
    if(i>=0){
      users.slice(i,1);
      localStorage.setItem(this.storageKey, JSON.stringify(users));
    }    
  }
  add(user) {
    if (!user) return;
    const users = this.get();
    var isExists = false;
    users.forEach(item => {
      if (item.id == user.id) {
        isExists = true;
      }
    });
    if (!isExists) {
      users.push(user);
    }
    localStorage.setItem(this.storageKey, JSON.stringify(users));
  }
}

export default new AuthCache();