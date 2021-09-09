import baseRequest from './base'

const baseURL = '/water/'
const request = (url, ...arg) => baseRequest(baseURL + url, ...arg)

export default {
  list(data) {
    return request('section_index', data)
  },
  import(data) {
    return request('import', data)
  },
  export(data) {
    return request('download', data, 'post', {
      responseType: 'blob'
    })
  }
}
