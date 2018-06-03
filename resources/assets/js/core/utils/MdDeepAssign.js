import { isDefined, isObject } from './common';

const { hasOwnProperty } = Object.prototype;

function assignKey(to, from, key) {
  const val = from[key];

  if (!isDefined(val) || (hasOwnProperty.call(to, key) && !isDefined(to[key]))) {
    return;
  }

  if (!hasOwnProperty.call(to, key) || !isObject(val)) {
    to[key] = val;
  } else {
    to[key] = assign(Object(to[key]), from[key]);
  }
}

export default function assign(to, from) {
  for (const key in from) {
    if (hasOwnProperty.call(from, key)) {
      assignKey(to, from, key);
    }
  }
  return to;
}
