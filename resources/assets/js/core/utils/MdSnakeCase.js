//驼峰命名法
let snakeCase = (name, separator) => {
  var regexp = /[A-Z.]/g;
  separator = separator || '-';
  name = name.replace(regexp, function(letter, pos) {
    return (pos ? separator : '') + letter.toLowerCase();
  });
  return name.replace(/\./g, '');
};

export default snakeCase;