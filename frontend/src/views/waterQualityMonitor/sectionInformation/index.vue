<template>
  <div class="setting-view">
    <div class="search-form">
      <el-form :inline="true" :model="formData" class="demo-form-inline">
        <el-form-item label="属性">
          <el-select v-model="formData.attrs" multiple placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="水域名称">
          <el-input v-model="formData.name" placeholder="水域名称"></el-input>
        </el-form-item>
        <el-form-item label="河湖长等级">
          <el-select v-model="formData.level" placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="监测名称">
          <el-input v-model="formData.watchName" placeholder="监测名称"></el-input>
        </el-form-item>
        <el-form-item label="行政区划">
          <el-cascader
            v-model="formData.area"
            placeholder="行政区划"
            :options="areaOptions"
            clearable
            filterable
          ></el-cascader>
        </el-form-item>
        <el-form-item label="控制级别">
          <el-select v-model="formData.controlLevel" placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="断面类型">
          <el-select v-model="formData.sectionType" placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="圈码">
          <el-input v-model="formData.quanma" placeholder="监测名称"></el-input>
        </el-form-item>
        <el-form-item label="水质等级">
          <el-input v-model="formData.waterQualityLevel" placeholder="水质等级"></el-input>
        </el-form-item>
        <el-form-item label="评价断面">
          <el-select v-model="formData.assessSection" placeholder="请选择">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="所在干流">
          <el-input v-model="formData.addressMainStream" placeholder="所在干流" clearable></el-input>
        </el-form-item>

        <!--      <el-form-item label="活动区域">-->
        <!--        <el-select v-model="formData.region" placeholder="活动区域">-->
        <!--          <el-option label="区域一" value="shanghai"></el-option>-->
        <!--          <el-option label="区域二" value="beijing"></el-option>-->
        <!--        </el-select>-->
        <!--      </el-form-item>-->
        <el-form-item>
          <el-button type="primary" @click="onSubmit">查询</el-button>
          <el-button type="info" @click="onClear">清空</el-button>
          <el-button type="primary" @click="onImport">导入</el-button>
          <el-button type="success" @click="onExport">导出</el-button>
        </el-form-item>
      </el-form>
      <el-dialog
        v-model="importDate.visible"
        title="请先选择日期"
        width="300px"
      >
        <el-date-picker v-model="importDate.value" type="date" placeholder="选择日期"></el-date-picker>
        <template #footer>
          <span class="dialog-footer">
            <el-button @click="closeDialog">取 消</el-button>
            <el-button type="primary" @click="submitImportDate">确 定</el-button>
          </span>
        </template>
      </el-dialog>
    </div>
    <div class="table-main">
      <table-list></table-list>
    </div>
  </div>
</template>

<script setup>
import TableList from './tableList.vue'
import { ElMessage } from 'element-plus'

// form
const defaultFormData = {
  attrs: [],
  name: '',
  level: '',
  watchName: '',
  area: '',
  controlLevel: '',
  sectionType: '',
  quanma: '',
  waterQualityLevel: '',
  assessSection: '',
  addressMainStream: ''
}

const formData = ref({ ...defaultFormData })

const options = [
  {
    value: '选项1',
    label: '黄金糕'
  },
  {
    value: '选项2',
    label: '双皮奶'
  },
  {
    value: '选项3',
    label: '蚵仔煎'
  },
  {
    value: '选项4',
    label: '龙须面'
  },
  {
    value: '选项5',
    label: '北京烤鸭'
  }
]

const areaOptions = [
  {
    value: 'zhinan',
    label: '指南',
    children: [
      {
        value: 'shejiyuanze',
        label: '设计原则',
        children: [
          {
            value: 'yizhi',
            label: '一致'
          },
          {
            value: 'fankui',
            label: '反馈'
          },
          {
            value: 'xiaolv',
            label: '效率'
          },
          {
            value: 'kekong',
            label: '可控'
          }
        ]
      },
      {
        value: 'daohang',
        label: '导航',
        children: [
          {
            value: 'cexiangdaohang',
            label: '侧向导航'
          },
          {
            value: 'dingbudaohang',
            label: '顶部导航'
          }
        ]
      }
    ]
  },
  {
    value: 'zujian',
    label: '组件',
    children: [
      {
        value: 'basic',
        label: 'Basic',
        children: [
          {
            value: 'layout',
            label: 'Layout 布局'
          },
          {
            value: 'color',
            label: 'Color 色彩'
          },
          {
            value: 'typography',
            label: 'Typography 字体'
          },
          {
            value: 'icon',
            label: 'Icon 图标'
          },
          {
            value: 'button',
            label: 'Button 按钮'
          }
        ]
      },
      {
        value: 'form',
        label: 'Form',
        children: [
          {
            value: 'radio',
            label: 'Radio 单选框'
          },
          {
            value: 'checkbox',
            label: 'Checkbox 多选框'
          },
          {
            value: 'input',
            label: 'Input 输入框'
          },
          {
            value: 'input-number',
            label: 'InputNumber 计数器'
          },
          {
            value: 'select',
            label: 'Select 选择器'
          },
          {
            value: 'cascader',
            label: 'Cascader 级联选择器'
          },
          {
            value: 'switch',
            label: 'Switch 开关'
          },
          {
            value: 'slider',
            label: 'Slider 滑块'
          },
          {
            value: 'time-picker',
            label: 'TimePicker 时间选择器'
          },
          {
            value: 'date-picker',
            label: 'DatePicker 日期选择器'
          },
          {
            value: 'datetime-picker',
            label: 'DateTimePicker 日期时间选择器'
          },
          {
            value: 'upload',
            label: 'Upload 上传'
          },
          {
            value: 'rate',
            label: 'Rate 评分'
          },
          {
            value: 'form',
            label: 'Form 表单'
          }
        ]
      },
      {
        value: 'data',
        label: 'Data',
        children: [
          {
            value: 'table',
            label: 'Table 表格'
          },
          {
            value: 'tag',
            label: 'Tag 标签'
          },
          {
            value: 'progress',
            label: 'Progress 进度条'
          },
          {
            value: 'tree',
            label: 'Tree 树形控件'
          },
          {
            value: 'pagination',
            label: 'Pagination 分页'
          },
          {
            value: 'badge',
            label: 'Badge 标记'
          }
        ]
      },
      {
        value: 'notice',
        label: 'Notice',
        children: [
          {
            value: 'alert',
            label: 'Alert 警告'
          },
          {
            value: 'loading',
            label: 'Loading 加载'
          },
          {
            value: 'message',
            label: 'Message 消息提示'
          },
          {
            value: 'message-box',
            label: 'MessageBox 弹框'
          },
          {
            value: 'notification',
            label: 'Notification 通知'
          }
        ]
      },
      {
        value: 'navigation',
        label: 'Navigation',
        children: [
          {
            value: 'menu',
            label: 'NavMenu 导航菜单'
          },
          {
            value: 'tabs',
            label: 'Tabs 标签页'
          },
          {
            value: 'breadcrumb',
            label: 'Breadcrumb 面包屑'
          },
          {
            value: 'dropdown',
            label: 'Dropdown 下拉菜单'
          },
          {
            value: 'steps',
            label: 'Steps 步骤条'
          }
        ]
      },
      {
        value: 'others',
        label: 'Others',
        children: [
          {
            value: 'dialog',
            label: 'Dialog 对话框'
          },
          {
            value: 'tooltip',
            label: 'Tooltip 文字提示'
          },
          {
            value: 'popover',
            label: 'Popover 弹出框'
          },
          {
            value: 'card',
            label: 'Card 卡片'
          },
          {
            value: 'carousel',
            label: 'Carousel 走马灯'
          },
          {
            value: 'collapse',
            label: 'Collapse 折叠面板'
          }
        ]
      }
    ]
  },
  {
    value: 'ziyuan',
    label: '资源',
    children: [
      {
        value: 'axure',
        label: 'Axure Components'
      },
      {
        value: 'sketch',
        label: 'Sketch Templates'
      },
      {
        value: 'jiaohu',
        label: '组件交互文档'
      }
    ]
  }
]

const onSubmit = () => {
  console.log('submit!')
}
const onClear = () => {
  formData.value = { ...defaultFormData }
}
const onImport = () => {
  importDate.visible = true
  console.log('submit!')
}
const onExport = () => {
  console.log('submit!')
}

// dialog
const importDate = reactive({
  value: '',
  visible: false
})
const closeDialog = () => {
  importDate.visible = false
}
const submitImportDate = () => {
  if (!importDate.value) {
    ElMessage.warning('请选择日期')
  } else {
    closeDialog()
  }
}

</script>

<style lang="scss" scoped>
.setting-view {
  margin-bottom: 10px;
  margin-top: -50px;
}
</style>
