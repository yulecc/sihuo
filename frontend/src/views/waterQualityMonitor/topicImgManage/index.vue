<template>
  <div class="setting-view">
    <div class="topic-title">水质反演专题图</div>
    <div class="topic-header layout-around">
      <div class="layout-items-center">
        <div class="search-item-title">监测来源</div>
        <el-select v-model="formData.mon_source" placeholder="请选择" clearable>
          <el-option
            v-for="item in options.source"
            :key="item.id"
            :label="item.mon_source"
            :value="item.id"
          >
          </el-option>
        </el-select>
      </div>
      <div class="layout-items-center">
        <div class="search-item-title">监测类型</div>
        <el-select v-model="formData.mon_category" placeholder="请选择" clearable>
          <el-option
            v-for="item in options.category"
            :key="item.id"
            :label="item.value"
            :value="item.id"
          >
          </el-option>
        </el-select>
      </div>
      <div class="layout-items-center">
        <div class="search-item-title">监测时间</div>
        <el-date-picker
          v-model="formData.mon_time"
          type="daterange"
          range-separator="至"
          start-placeholder="开始日期"
          end-placeholder="结束日期"
        >
        </el-date-picker>
      </div>
      <div class="layout-items-center">
        <el-button type="primary" @click="fetchData">查询</el-button>
        <el-button type="primary" @click="onAdd">新增</el-button>
      </div>
    </div>
    <div class="topic-container">
      <div class="topic-content">
        <div v-for="item in list" :key="item.id" class="topic-item text-center">
          <img v-if="item.img" :src="item.img" alt="">
          <div v-else class="layout-abs-center empty-img">未设置图片</div>
          <div class="topic-name ">{{ item.mon_category_text }}</div>
        </div>
      </div>
      <div v-if="pagination.total" class="text-center">
        <el-pagination
          v-model:currentPage="formData.page"
          :page-sizes="[20, 50, 100]"
          :page-size="formData.limit"
          layout="sizes, prev, pager, next"
          :total="pagination.total"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        >
        </el-pagination>
      </div>
    </div>
    <add-dialog v-if="addDialogVisible" :options="options" @close="close" />
  </div>
</template>

<script setup>
import topicApi from '@/api/topic'
import AddDialog from './addDialog.vue'
const formData = reactive({
  mon_source: '',
  mon_category: '',
  mon_time: '',
  page: 1,
  limit: 20
})

const addDialogVisible = ref(false)
const close = () => {
  addDialogVisible.value = false
}

const pagination = reactive({
  total: 0
})
const handleSizeChange = (limit) => {
  formData.limit = limit
}
const handleCurrentChange = (page) => {
  formData.page = page
}

const list = ref([])
const options = reactive({
  source: [],
  category: []
})

const formatRows = (rows) => {
  const res = []
  rows.forEach(row => {
    const imgsStr = row.mon_images
    const imgs = (imgsStr && JSON.parse(imgsStr)) || []
    if (!imgs.length) {
      res.push({
        ...row,
        img: ''
      })
      return
    }
    imgs.forEach(img => {
      res.push({
        ...row,
        img: img.replace('https', 'http')
      })
    })
  })
  return res
}

const fetchData = () => {
  topicApi.list(formData)
    .then(res => {
      list.value = formatRows(res.rows)
      options.category = res.category
      options.source = res.source
    })
}

onMounted(() => {
  fetchData()
})

const onSubmit = () => {
  console.log('submit!')
}
const onAdd = () => {
  addDialogVisible.value = true
}
</script>

<style lang="scss" scoped>
.setting-view {
  margin-bottom: 10px;
}
.topic-title{
  font-size: 24px;
  text-align: center;
  padding: 15px 10px;
  background-color: #eee;
  color: #333;
  font-weight: bold;
}
.topic-header{
  margin-top: 20px;
}
.search-item-title{
  margin-right: 20px;
}
.topic-container{
  padding: 30px 15px;
}
.topic-content{
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-column-gap: 30px;
  grid-row-gap: 30px;
}
.topic-name{
  margin-top: 10px;
}
.empty-img{
  height: 160px;
  border: 1px solid #eee;
  color: #888;
}
.topic-item img{
  height: 160px;
  object-fit: contain;
}
</style>
