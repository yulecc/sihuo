<template>
  <div class="sidebar-container">
    <!--    <div class="logo" @click="$router.push('/')">-->
    <!--      <img class="logo-img" :src="logoSrc" alt="logo" />-->
    <!--      <transition name="el-zoom-in-center">-->
    <!--        <h1 v-show="opened" class="logo-text">Vue Element Admin</h1>-->
    <!--      </transition>-->
    <!--    </div>-->
    <el-scrollbar wrap-class="scrollbar-wrapper">
      <el-menu
        :router="true"
        :default-active="$route.path"
        :collapse="isCollapse"
        :show-timeout="200"
        :unique-opened="true"
        text-color="#fff"
        background-color="#1b3463"
        active-text-color="#fff"
      >
        <SidebarItem v-for="item in routerList" :key="item.path" :index="item.path" :nav="item" />
      </el-menu>
    </el-scrollbar>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch } from 'vue'
import { useStore } from 'vuex'
import { constantRoutes } from '@/router'
import { getRoles } from '@/utils/auth'
import SidebarItem from './SidebarItem.vue'
import logoSrc from '@img/logo.png'

const roles = getRoles()
const store = useStore()
const routerList = ref([])

const opened = computed(() => store.state.app.sidebar.opened)
const isCollapse = computed(() => !opened.value)
const checkNav = computed(() => store.state.app.checkNav)

onMounted(() => {
  filterRoutes()
})

watch(checkNav, () => {
  filterRoutes()
})

/**
 * 权限过滤路由
 */
const filterRoutes = () => {
  const res = []
  constantRoutes.forEach((item) => {
    if (item.path === '/') {
      res.push(...item.children)
    }
  })
  for (let i = 0; i < res.length; i++) {
    if (res[i].meta && res[i].meta.roles && !res[i].meta.belong?.includes(checkNav.value)) {
      res.splice(i, 1)
      i--
    }
  }
  filterChildrens(res)
  routerList.value = res
}

/**
 * 权限过滤子路由
 */
const filterChildrens = (routers) => {
  const childrens = []
  routers.forEach((item) => {
    if ((item.meta && !item.meta.roles) || (item.meta && item.meta.roles && item.meta.roles.includes(roles))) {
      childrens.push(item)
      if (item.children) {
        filterChildrens(item.children)
      }
    }
  })
  routers.length = 0
  routers.push(...childrens)
}
</script>

<style lang="scss">
.logo {
  //position: absolute;
  top: 0;
  display: flex;
  width: 100%;
  height: 50px;
  overflow: hidden;
  text-align: center;
  cursor: pointer;
  background-color: #2b2f3a;
  justify-content: center;
  align-items: center;

  .logo-img {
    width: 32px;
    height: 32px;
  }

  .logo-text {
    display: inline-block;
    height: 50px;
    margin-left: 12px;
    font-size: 14px;
    line-height: 50px;
    color: #fff;
  }
}
.el-menu-item.is-active{

}
</style>
