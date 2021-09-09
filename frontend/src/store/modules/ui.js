const state = {
  isShowSliderMenu: false,
  NavBarHeight: '132px'
}

const mutations = {
  SET_SLIDERMENU_STATE: (state, isShow) => {
    state.isShowSliderMenu = isShow
    state.NavBarHeight = isShow ? '180px' : '132px'
  }
}

const actions = {
  setSliderMenuState({ commit }, isShow) {
    commit('SET_SLIDERMENU_STATE', isShow)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
