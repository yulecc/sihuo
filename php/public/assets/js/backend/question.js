define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'question/index' + location.search,
                    add_url: 'question/add',
                    edit_url: 'question/edit',
                    del_url: 'question/del',
                    multi_url: 'question/multi',
                    import_url: 'question/import',
                    table: 'question',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'source', title: __('Source')},
                        {field: 'question_type', title: __('Question_type')},
                        {field: 'question_level', title: __('Question_level'), searchList: {"1":__('Question_level 1'),"2":__('Question_level 2')}, formatter: Table.api.formatter.normal},
                        {field: 'auth_area', title: __('Auth_area')},
                        {field: 'river_id', title: __('River_id')},
                        {field: 'river_name', title: __('River_name'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'longitude', title: __('Longitude'), operate:'BETWEEN'},
                        {field: 'latitude', title: __('Latitude'), operate:'BETWEEN'},
                        {field: 'question_time', title: __('Question_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'status', title: __('Status')},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'admin.username', title: __('Admin.username'), operate: 'LIKE'},
                        {field: 'admin.nickname', title: __('Admin.nickname'), operate: 'LIKE'},
                        {field: 'admin.company', title: __('Admin.company'), operate: 'LIKE'},
                        {field: 'river.river_name', title: __('River.river_name'), operate: 'LIKE'},
                        {field: 'river.river_level', title: __('River.river_level')},
                        {field: 'river.river_attr', title: __('River.river_attr')},
                        {field: 'river.radmin_id', title: __('River.radmin_id')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});