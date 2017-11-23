const { join } = require('path')
const { mix } = require('laravel-mix');

const resolvePath = (...args) => {
  const path = [__dirname, '/resources/assets/js/vendor/gmf-material', ...args]

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
      'vue-material': resolvePath('src'),
      theme: resolvePath('src/theme'),
      base: resolvePath('src/base'),
      core: resolvePath('src/core'),
      components: resolvePath('src/components')
    }
  },
});
mix.js('resources/assets/js/app.js', 'public/js')
  .extract(['axios', 'lodash', 'vue', 'vue-router', 'uuid', 'highcharts', 'iscroll', 'd3', 'moment']);
mix.sass('resources/assets/sass/app.scss', 'public/css');

mix.copyDirectory('resources/assets/img', 'public/img');

mix.version();
