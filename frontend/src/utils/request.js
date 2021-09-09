import axios from 'axios'
import { ElMessage } from 'element-plus'
import { getToken, removeToken, removeRoles, removeName, removeAvatar } from './auth'

const service = axios.create({
  baseURL: '/api/',
  timeout: 10000, // request timeout
  headers: {
    'Content-Type': 'application/json'
  }
})

// 请求拦截器
service.interceptors.request.use(
  (config) => {
    const token = getToken()
    // 如果有token 就携带tokon
    if (token) {
      config.headers['Authorization'] = 'Bearer__' + token
    }
    // 加上取消请求
    config.cancelToken = new axios.CancelToken((cancel) => {
      if (Array.isArray(window.axiosCancelTokenList)) {
        window.axiosCancelTokenList.push(cancel)
      } else {
        window.axiosCancelTokenList = [cancel]
      }
    })
    return config
  },
  (error) => Promise.reject(error)
)

// 响应拦截器
service.interceptors.response.use(
  (response) => {
    console.log(response)
    const res = response.data
    if (response.request.responseType === 'blob') {
      return res
    }
    if (res.code !== 1) {
      ElMessage({
        type: 'error',
        message: res.msg
      })
      return Promise.reject()
    }
    return res.data
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      removeToken()
      removeRoles()
      removeName()
      removeAvatar()
      location.reload()
    }
    ElMessage({
      type: 'error',
      message: error.message
    })
    return Promise.reject(error)
  }
)

export default service
