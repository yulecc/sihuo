import baseRequest from './base'

const baseURL = '/watermon/'
const request = (url, ...arg) => baseRequest(baseURL + url, ...arg)

export default {
  list(data) {
    return request('index', data)
  }
}
