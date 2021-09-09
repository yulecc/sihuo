<template>
  <el-menu class="navbar" mode="horizontal">
    <div class="layout-slide">
      <div class="left-nav layout-items-center">
        <div class="nav-date">{{ nowDate }}</div>
        <div class="weather-box layout-items-center">
          <div class="temperature">32 °C</div>
          <div class="weather">
            <div class="">雷阵雨</div>
            <div class="">东北风1-2级</div>
          </div>
        </div>
      </div>
      <div class="navbar-title">江北区智慧河长监管平台</div>
      <!--    <Hamburger class="hamburger-container" :is-active="opened" @toggleClick="toggleSideBar" />-->
      <!--    <Breadcrumb class="breadcrumb-container" />-->
      <div class="right-menu">
        <!--      <el-tooltip effect="dark" content="全屏" placement="bottom">-->
        <!--        <Screenfull class="screenfull" />-->
        <!--      </el-tooltip>-->
        <div class="layout-items-center">
          <div class="lock-icon"></div>

          <el-dropdown class="avatar-container right-menu-item">
            <div class="avatar-wrapper">
              <div class="user-icon"></div>
              <i class="el-icon-caret-bottom" />
            </div>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item @click="$router.push('/')">首页</el-dropdown-item>
                <el-dropdown-item divided @click="editPossword">修改密码</el-dropdown-item>
                <el-dropdown-item divided @click="loginOut">登出</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
        <div class="nav-list layout-items-center">
          <div
            v-for="item in navList.list"
            :key="item.name"
            class="nav-item"
            :class="{checked:item.name===navList.checked }"
            @click="checkNav(item)"
          >{{ item.name }}</div>
        </div>
      </div>
    </div>
  </el-menu>
</template>

<script setup>
import { computed, reactive, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useStore } from 'vuex'
import { ElMessage, ElMessageBox } from 'element-plus'
import dayjs from 'dayjs'
import Hamburger from '@/components/Hamburger/index.vue'
import Breadcrumb from '@/components/Breadcrumb/index.vue'
import Screenfull from '@/components/Screenfull/index.vue'

const router = useRouter()
const route = useRoute()
const store = useStore()
const opened = computed(() => store.state.app.sidebar.opened)
const avatar = computed(() => store.state.user.avatar)
const navbarHeight = computed(() => store.state.ui.NavBarHeight)

onMounted(() => {
  setInterval(() => {
    nowDate.value = dayjs().format('YYYY年MM月DD日 HH:mm:ss')
  }, 1000)
  console.log(JSON.parse(JSON.stringify(route)))
  const belong = route.meta?.belong[0]
  const target = navList.list.find(value => value.value === belong)
  checkNav(target)
})

const navList = reactive({
  list: [
    { name: '总览', value: 'overview' },
    { name: '水质监测', value: 'waterQualityMonitor' },
    { name: '问题发现', value: 'test1' },
    { name: '河道管理', value: 'test2' },
    { name: '排口排查', value: 'test3' }
  ],
  checked: '总览'
})

const nowDate = ref('')

const checkNav = (nav) => {
  navList.checked = nav.name
  store.dispatch('app/setCheckNav', nav.value)
  if (nav.name === '总览') {
    store.dispatch('ui/setSliderMenuState', false)
  } else {
    store.dispatch('ui/setSliderMenuState', true)
  }
}

const toggleSideBar = () => {
  store.dispatch('app/toggleSideBar')
}

const editPossword = () => {
  ElMessage.warning('请联系管理员')
}

const loginOut = () => {
  ElMessageBox.confirm('退出登录', '提示', {
    confirmButtonText: '确认',
    cancelButtonText: '取消',
    type: 'warning'
  })
    .then(() => {
      store.dispatch('user/logout').then(() => {
        router.push('/login')
      })
    })
    .catch(() => {})
}
</script>

<style lang="scss" scoped>
.left-nav{
  width: 400px;
}

.navbar-title{
  background-color: rgba(15, 41, 91, 1);
  //width: 657px;
  height: 91px;
  line-height: 91px;
  text-align: center;
  font-weight: 700;
  font-size: 32px;
  color: #fff;
  margin: auto;
}
.temperature{
  margin-left: 20px;
}
.weather{
  margin-left: 20px;
}
.navbar {
  overflow: hidden;
  line-height: 50px;
  height: v-bind('navbarHeight');
  background: url("/img/bg.svg");
  color: #fff;
  border-bottom: none;
  z-index: 999;

  .nav-list{
    margin-top: 20px;
    font-family: 'Arial Normal', 'Arial';
  }

  .nav-item{
    font-size: 20px;
    text-align: center;
    width: 110px;
    height: 48px;
    border-radius: 10px;
    margin-left: 20px;
    background-color: rgba(11, 56, 144, 1);
    cursor: pointer;
    &.checked{
      background-color: rgba(12, 78, 207, 1)
    }
  }

  .lock-icon, .user-icon{
    width: 24px;
    height: 24px;
    background-size: 24px;
    margin-left: 30px;
  }
  .lock-icon{
    background: url("/img/lock.svg");
  }
  .user-icon{
    background: url("/img/user.svg");
  }

  .hamburger-container {
    float: left;
    height: 50px;
    padding: 0 10px;
  }

  .breadcrumb-container {
    float: left;
  }

  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }

  :deep(.right-menu) {
    //width: 400px;
    padding: 20px;
    //height: 100%;

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      margin: 0 4px;
      vertical-align: middle;
    }

    .international {
      vertical-align: top;
    }

    .theme-switch {
      vertical-align: 15px;
    }

    .avatar-container {
      // height: 50px;
      margin-right: 30px;

      .avatar-wrapper {
        position: relative;
        margin-top: 5px;

        .user-avatar {
          width: 40px;
          height: 40px;
          cursor: pointer;
          border-radius: 10px;
        }

        .el-icon-caret-bottom {
          position: absolute;
          top: 20px;
          right: -16px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>
