const { join } = require('path')
const { mix } = require('laravel-mix');

const resolvePath = (...args) => {
  const path = [__dirname, '/resources/assets/js/vendor', ...args]

  return join.apply(null, path)
}

mix.options({
  extractVueStyles: true,
  purifyCss: false,
  uglify: {
    sourceMap: true,
    uglifyOptions: {
      compress: {
        warnings: false,
        drop_console: true,
      },
      output: {
        comments: false
      }
    }
  },
  clearConsole: false
});
mix.webpackConfig({
  resolve: {
    alias: {
      'gmf': resolvePath('gmf-sys'),
      'vue-material': resolvePath('gmf-sys'),
      'theme': resolvePath('gmf-sys/theme'),
      'base': resolvePath('gmf-sys/base'),
      'core': resolvePath('gmf-sys/core'),
      'components': resolvePath('gmf-sys/components')
    }
  },
});
mix.js('resources/assets/js/app.js', 'public/js')
  .extract(['axios', 'lodash', 'vue', 'vue-router', 'uuid', 'highcharts', 'iscroll', 'd3', 'moment']);
mix.sass('resources/assets/sass/app.scss', 'public/css');

mix.copyDirectory('resources/assets/img', 'public/img');

mix.version();
