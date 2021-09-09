<template>
  <div class="setting-view">
    <div class="search-form">
      <el-form :inline="true" :model="formData" class="search-form">
        <el-form-item label="属性">
          <el-select v-model="formData.river_attribute" multiple placeholder="请选择">
            <el-option
              v-for="item in attrOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="水域名称">
          <el-input v-model="formData.water_name" placeholder="水域名称"></el-input>
        </el-form-item>
        <el-form-item label="河湖长等级">
          <el-select v-model="formData.people" placeholder="请选择">
            <el-option
              v-for="item in peopleOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="监测名称">
          <el-input v-model="formData.test_name" placeholder="监测名称"></el-input>
        </el-form-item>
        <!-- <el-form-item label="行政区划">
          <el-cascader
            v-model="formData.area"
            placeholder="行政区划"
            :options="areaOptions"
            clearable
            filterable
          ></el-cascader>
        </el-form-item> -->
        <el-form-item label="控制级别">
          <el-select v-model="formData.control_level" placeholder="请选择">
            <el-option
              v-for="item in controlLevelOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="断面类型">
          <el-select v-model="formData.section_type" placeholder="请选择">
            <el-option
              v-for="item in sectionTypeOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="圈码">
          <el-input v-model="formData.circle_code" placeholder="监测名称"></el-input>
        </el-form-item>
        <el-form-item label="水质等级">
          <el-input v-model="formData.water_level" placeholder="水质等级"></el-input>
        </el-form-item>
        <el-form-item label="评价断面">
          <el-select v-model="formData.evaluate" placeholder="请选择">
            <el-option
              v-for="item in evaluateOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="所在干流">
          <el-input v-model="formData.river_name" placeholder="所在干流" clearable></el-input>
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
    </div>
    <div class="table-main">
      <table-list :tableData="tableData" :pagination="pagination"></table-list>
    </div>
    <el-dialog
      v-model="importFormData.visible"
      title="导入文件"
      width="30%"
    >
      <el-form ref="formRef" :model="importFormData" :rules="rules" label-width="80px">
        <el-form-item prop="explode_date" label="日期" required>
          <el-date-picker
            v-model="importFormData.explode_date"
            type="date"
            value-format="YYYY-MM-DD"
            placeholder="选择日期"
          >
          </el-date-picker>
        </el-form-item>
        <el-form-item prop="file" label="文件" required>
          <el-upload
            ref="uploadRef"
            action="/api/water/import"
            :data="importFormData"
            :auto-upload="false"
            :limit="1"
            :on-success="handleUploadSuccess"
            :on-change="handleUploadChange"
          >
            <i class="el-icon-plus"></i>
          </el-upload>
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="closeDialog">取 消</el-button>
          <el-button type="primary" @click="submitImportDate">确 定</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import waterApi from '@/api/water'
import { downloadFile } from '@/utils/util'
import TableList from './tableList.vue'
import { ElMessage } from 'element-plus'
import topicApi from '@/api/topic'

// form
const defaultFormData = {
  river_attribute: [],
  water_name: '',
  people: '',
  test_name: '', // 监测名称
  // area: '',
  control_level: '',
  section_type: '',
  circle_code: '',
  water_level: '',
  evaluate: '',
  river_name: '',
}

const formData = ref({ ...defaultFormData })

// filter
const attrOptions = ref([]) // 属性
const peopleOptions = ref([]) // 河湖长等级
const controlLevelOptions = ref([]) // 控制级别
const sectionTypeOptions = ref([]) // 断面类型
const evaluateOptions = [{value: 1, label: '是'},{value: 2, label: '否'}] // 评价断面

// list
const tableData = ref([])

const pagination = reactive({
  total: 0,
  page: 1,
  limit: 20,
})


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
  waterApi.list(formData.value).then(res => {
    console.log(res)
  })
}
const onClear = () => {
  formData.value = { ...defaultFormData }
}

// import
const formRef = ref(null)
const uploadRef = ref(null)
const validateFile = (rule, value, callback) => {
  if (!importFormData.file) {
    callback(new Error('请选择文件'))
  } else {
    callback()
  }
}
const rules = {
  explode_date: [
    { required: true, message: '请选择日期', trigger: 'blur' }
  ],
  file: [
    { validator: validateFile, message: '请选择日期', trigger: ['blur', 'change'] }
  ]
}



const onImport = () => {
  importFormData.visible = true
  console.log('submit!')
}
const onExport = () => {
  waterApi.export(formData.value).then(res => {
    const url = URL.createObjectURL(res)
    downloadFile(url, 'xx.xlsx')
  })
  console.log('submit!')
}

// dialog
const importFormData = reactive({
  explode_date: '',
  file: '',
  visible: false
})
const closeDialog = () => {
  importFormData.visible = false
}
const submitImportDate = () => {
  formRef.value.validate((valid) => {
    if (valid) {
      const formdata = new FormData()
      formdata.append('file', uploadRef.value.uploadFiles[0].raw)
      formdata.append('explode_date', importFormData.explode_date)
      waterApi.import(formdata).then(res => {
        ElMessage.success('导入成功')
        closeDialog()
      })
    }
  })
}
const handleUploadSuccess = (response, file, fileList) => {
  // const data = response.data
  // const imgUrl = 'http://' + data.domain + data.filepath
  // formData.mon_images.push(imgUrl)
}
const handleUploadChange = (file, fileList) => {
  importFormData.file = file
}

const fetchData = () => {
  waterApi.list(formData.value).then(res => {
    const {search_list, rows, total} = res;
    const {river_attribute,people,control_level,section_type} = search_list || {};
    attrOptions.value = Object.values(river_attribute).map((v)=>({value:v.id, label: v.value}))
    peopleOptions.value = Object.values(people).map((v=>({value:v.id, label: v.value})))
    controlLevelOptions.value = Object.values(control_level).map((v=>({value:v.id, label: v.value})))
    sectionTypeOptions.value = Object.values(section_type).map((v=>({value:v.id, label: v.value})))
    tableData.value = rows
    pagination.total = total
  })
}

onMounted(() => {
  fetchData()
})


</script>

<style lang="scss" scoped>
.setting-view {
  margin-bottom: 10px;
}
</style>
