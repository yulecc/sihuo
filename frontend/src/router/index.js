import { createRouter, createWebHashHistory } from 'vue-router'

export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index.vue'),
    meta: {
      title: '登录'
    }
  },
  {
    path: '/404',
    component: () => import('@/views/error-page/404.vue'),
    meta: {
      title: '404'
    }
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401.vue'),
    meta: {
      title: '401'
    }
  },
  {
    path: '/',
    component: () => import('@/layout/index.vue'),
    redirect: '/home',
    children: [
      {
        path: '/home',
        component: () => import('@/views/home/index.vue'),
        name: 'Home',
        meta: { title: '首页', icon: 'el-icon-s-home', affix: true, belong: ['overview'] }
      },
      {
        path: '/setting',
        component: () => import('@/views/setting/index.vue'),
        name: 'Setting',
        meta: { title: '设置', icon: 'el-icon-s-tools', roles: ['admin'], belong: ['test1'] }
      },
      {
        path: '/menu',
        name: 'Menu',
        component: () => import('@/views/menu/index.vue'),
        meta: { title: '菜单', icon: 'el-icon-menu', roles: ['admin', 'editor'], belong: ['test2'] },
        redirect: '/menu2',
        children: [
          {
            path: '/menu1',
            name: 'Menu1',
            component: () => import('@/views/menu/menu10.vue'),
            meta: { title: '菜单1', roles: ['admin', 'editor'] },
            redirect: '/menu1-2',
            children: [
              {
                path: '/menu1-1',
                component: () => import('@/views/menu/menu11.vue'),
                name: 'Menu1-1',
                meta: { title: '菜单1-1', roles: ['admin'] }
              },
              {
                path: '/menu1-2',
                component: () => import('@/views/menu/menu12.vue'),
                name: 'Menu1-2',
                meta: { title: '菜单1-2', roles: ['admin', 'editor'] }
              }
            ]
          },
          {
            path: '/menu2',
            component: () => import('@/views/menu/menu2.vue'),
            name: 'Menu2',
            meta: { title: '菜单2', roles: ['admin', 'editor'] }
          }
        ]
      },
      {
        path: '/star',
        component: () => import('@/views/star/index.vue'),
        name: 'Star',
        meta: { title: '图表', icon: 'el-icon-s-data', roles: ['admin', 'editor'], belong: ['test3'] }
      },
      {
        path: '/xueyue',
        component: () => import('@/views/xueyue/index.vue'),
        name: 'Xueyue',
        meta: { title: '雪月', icon: 'el-icon-s-flag', roles: ['admin'] }
      },

      // 水质检测 waterQualityMonitor
      {
        path: '/waterQualityMonitorMap',
        component: () => import('@/views/waterQualityMonitor/map.vue'),
        name: '水质监测地图',
        meta: { title: '水质监测地图',
          icon: 'el-icon-s-flag',
          roles: ['admin'],
          belong: ['waterQualityMonitor']
        }
      },
      {
        path: '/topicImgManage',
        component: () => import('@/views/waterQualityMonitor/topicImgManage.vue'),
        name: '专题图管理',
        meta: { title: '专题图管理',
          icon: 'el-icon-s-flag',
          roles: ['admin'],
          belong: ['waterQualityMonitor']
        }
      },
      {
        path: '/sectionInformation',
        component: () => import('@/views/waterQualityMonitor/sectionInformation/index.vue'),
        name: '水质断面信息',
        meta: { title: '水质断面信息',
          icon: 'el-icon-s-flag',
          roles: ['admin'],
          belong: ['waterQualityMonitor']
        }
      }
    ]
  }
]

export const asyncRoutes = []

export const router = createRouter({
  history: createWebHashHistory(),
  routes: constantRoutes
})

export default router
