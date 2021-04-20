import fetch from '../utils/fetch';

export default function makeService(endpoint) {
  return {
    find(id) {
      console.log('find');
      console.log(id);
      let options = {params: {getFile: true}};
      return fetch(`${id}`, options);
    },
    findAll(params) {
      console.log('findAll');console.log(params);
      return fetch(endpoint, params);
    },
    async createFile(payload) {
      return fetch(endpoint, { method: 'POST', body: payload });
      //return fetch(endpoint, { method: 'POST', body: JSON.stringify(payload) });
    },
    create(payload) {
      return fetch(endpoint, { method: 'POST', body: payload });
      //return fetch(endpoint, { method: 'POST', body: JSON.stringify(payload) });
    },
    del(item) {
      return fetch(item['@id'], { method: 'DELETE' });
    },
    update(payload) {
      console.log('api.js - update');
      //console.log(JSON.stringify(payload));

      return fetch(payload['@id'], {
        method: 'PUT',
        body: JSON.stringify(payload)
      });
    }
  };
}
