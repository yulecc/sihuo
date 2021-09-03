import Cookies from 'js-cookie'

const state = {
  device: 'desktop',
  size: Cookies.get('size') || 'medium',
  sidebar: {
    opened: Cookies.get('sidebarStatus') ? !!Cookies.get('sidebarStatus') : true,
    withoutAnimation: false
  },
  checkNav: 'overview',
  mainHeaderNavList: []
}

const mutations = {
  TOGGLE_SIDEBAR: (state) => {
    state.sidebar.opened = !state.sidebar.opened
    state.sidebar.withoutAnimation = false
    if (state.sidebar.opened) {
      Cookies.set('sidebarStatus', '1')
    } else {
      Cookies.set('sidebarStatus', '')
    }
  },
  CLOSE_SIDEBAR: (state, withoutAnimation) => {
    state.sidebar.opened = false
    state.sidebar.withoutAnimation = withoutAnimation
    Cookies.set('sidebarStatus', '')
  },
  TOGGLE_DEVICE: (state, device) => {
    state.device = device
  },
  SET_SIZE: (state, size) => {
    state.size = size
    Cookies.set('size', size)
  },
  SET_CHECK_NAV: (state, navName) => {
    state.checkNav = navName
  },
  SET_MAIN_HEADER_NAV_LIST: (state, list) => {
    state.mainHeaderNavList = list
  }
}

const actions = {
  toggleSideBar({ commit }) {
    commit('TOGGLE_SIDEBAR')
  },
  closeSideBar({ commit }, { withoutAnimation }) {
    commit('CLOSE_SIDEBAR', withoutAnimation)
  },
  toggleDevice({ commit }, device) {
    commit('TOGGLE_DEVICE', device)
  },
  setSize({ commit }, size) {
    commit('SET_SIZE', size)
  },
  setCheckNav({ commit }, navName) {
    commit('SET_CHECK_NAV', navName)
  },
  setMainHeaderNavList({ commit }, list) {
    commit('SET_MAIN_HEADER_NAV_LIST', list)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
