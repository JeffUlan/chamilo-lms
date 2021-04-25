import axios from "axios";
import { isArray, isObject, isUndefined, forEach } from 'lodash';
import { ENTRYPOINT } from '../config/entrypoint';
import SubmissionError from '../error/SubmissionError';
import { normalize } from './hydra';

const MIME_TYPE = 'application/ld+json';

const transformRelationToIri = (payload) => {
    forEach(payload, (value, property) => {
        if (isObject(value) && !isUndefined(value['@id'])) {
            payload[property] = value['@id'];
        }

        if (isArray(value)) payload[property] = transformRelationToIri(value);
    });

    return payload;
};

const makeParamArray = (key, arr) =>
  arr.map(val => `${key}[]=${val}`).join('&');

export default function(id, options = {}) {
    console.log('fetch');
    console.log(options.method, 'method');

    if ('undefined' === typeof options.headers) options.headers = new Headers();

    if (null === options.headers.get('Accept')) {
        options.headers.set('Accept', MIME_TYPE);
    }

    /*if (
      'undefined' !== options.body &&
      !(options.body instanceof FormData) &&
      null === options.headers.get('Content-Type')
    )
      options.headers.set('Content-Type', MIME_TYPE);*/

    if (options.params) {
        console.log('params');
        console.log(options.params);
        const params = normalize(options.params);
        //const params = options.params;
        let queryString = Object.keys(params)
            .map(key =>
                Array.isArray(params[key])
                    ? makeParamArray(key, params[key])
                    : `${key}=${params[key]}`
            )
            .join('&');
        id = `${id}?${queryString}`;

        console.log(id, 'id');
    }

    const entryPoint = ENTRYPOINT + (ENTRYPOINT.endsWith('/') ? '' : '/');

    /*let useAxios = false;
    let originalBody = options.body;*/
    let formData = new FormData();
    if ('POST' === options.method) {
        if (options.body) {
            Object.keys(options.body).forEach(function (key) {
                /*if (key === 'uploadFile') {
                    useAxios = true;
                }*/
                // key: the name of the object key
                // index: the ordinal position of the key within the object
                formData.append(key, options.body[key]);
            });
            options.body = formData;
        }
    }

    if ('PUT' === options.method) {
        const payload = options.body && JSON.parse(options.body);
        if (isObject(payload) && payload['@id']) {
            options.body = JSON.stringify(normalize(payload));
            //options.body = JSON.stringify(transformRelationToIri(payload));
        }
    }

    /*if (useAxios) {
        console.log('axios');
        let url = new URL(id, entryPoint);
        console.log(formData);
        return axios({
            url: url.toString(),
            method: 'POST',
            //headers: options.headers,
            data: formData,
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: function (progressEvent) {
                console.log('progress');
                //console.log(progressEvent);
                console.log(options.body);

                let uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100));
                options.body['__progress'] = uploadPercentage;
                options.body['__progressLabel'] = uploadPercentage;
                this.uploadPercentage = uploadPercentage;
                console.log(options.body);
                //options.body.set('__progressLabel', uploadPercentage);
            }.bind(this)
        }
        ).then(response => {
            options.body['__uploaded'] = 1;
            options.body['uploadFile']['__uploaded'] = 1

            console.log(response);
            console.log('SUCCESS!!');

            return response.data;
        })
            .catch(function (response) {
                console.log(response);
                console.log('FAILURE!!');
            });
    }*/

  return global.fetch(new URL(id, entryPoint), options).then(response => {
    if (response.ok) return response;

    return response.json().then(json => {
        const error =
          json['hydra:description'] ||
          json['hydra:title'] ||
          'An error occurred.';

        if (!json.violations) throw Error(error);

        let errors = { _error: error };
        json.violations.forEach(violation =>
          errors[violation.propertyPath]
            ? (errors[violation.propertyPath] +=
            '\n' + errors[violation.propertyPath])
            : (errors[violation.propertyPath] = violation.message)
        );

        throw new SubmissionError(errors);
      },
      () => {
        throw new Error(response.statusText || 'An error occurred.');
      }
    );
  }).catch(error => console.error('Error:', error));
}
