import { join } from 'path'
import packageJson from '../package.json'

export const resolvePath = (...args) => {
  const path = [__dirname, '..', ...args]

  return join.apply(null, path)
};

export const getRandomInt = (min, max) => {
  const minNotAlowed = 8080
  const maxNotAlowed = 8090
  const generated = Math.floor(Math.random() * (max - min + 1)) + min

  if (generated >= minNotAlowed && generated <= maxNotAlowed) {
    return getRandomInt(min, max)
  }

  return generated
};

export const config = {
  dist: 'dist',
  public: '/',
  index: 'docs/index.html',
  assets: 'docs/assets',
  docs: 'docs',
  server: {
    port: process.env.PORT || '8080'
  },
  resolve: ['.vue', '.scss', '.js', '.json'],
  alias: {
    'gmf': resolvePath('resources/assets/js'),
    'theme': resolvePath('resources/assets/js/theme'),
    base: resolvePath('resources/assets/js/base'),
    core: resolvePath('resources/assets/js/core'),
    components: resolvePath('resources/assets/js/components'),
    docs: resolvePath('docs')
  }
};

export const pack = packageJson
