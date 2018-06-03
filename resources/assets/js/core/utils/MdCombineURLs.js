'use strict';

/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
export default function(baseURL, relativeURL) {
  return relativeURL
    ?baseURL
      ?baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
      :'/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};
