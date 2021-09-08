<template>
  <div class="setting-view">
    <div class="topic-title">水质反演专题图</div>
    <div class="topic-header layout-around">
      <div class="layout-items-center">
        <div class="search-item-title">监测来源</div>
        <el-select v-model="formData.mon_source" placeholder="请选择">
          <el-option
            v-for="item in options.source"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          >
          </el-option>
        </el-select>
      </div>
      <div class="layout-items-center">
        <div class="search-item-title">监测类型</div>
        <el-select v-model="formData.mon_category" placeholder="请选择">
          <el-option
            v-for="item in options.category"
            :key="item.value"
            :label="item.label"
            :value="item.value"
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
        <el-button type="primary" @click="onSubmit">查询</el-button>
        <el-button type="primary" @click="onAdd">新增</el-button>
      </div>
    </div>
    <div class="topic-container">
      <div class="topic-content">
        <div class="topic-item">
          <img src="" alt="">
          <div class="topic-name"></div>
        </div>
      </div>
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
    <!--    <main-content-nav-list :nav-list="navList" @navClick="navClick"></main-content-nav-list>-->
  </div>
</template>

<script setup>
import topicApi from '@/api/topic'
const formData = reactive({
  mon_source: '',
  mon_category: '',
  mon_time: '',
  page: 1,
  limit: 20
})

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

const fetchData = () => {
  topicApi.list(formData)
    .then(res => {
      console.log(res)
      list.value = res.rows
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

}
</script>

<style lang="scss" scoped>
.setting-view {
  margin-bottom: 10px;
  margin-top: -50px;
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
</style>
