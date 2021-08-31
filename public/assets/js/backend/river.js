define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'river/index' + location.search,
                    add_url: 'river/add',
                    edit_url: 'river/edit',
                    del_url: 'river/del',
                    multi_url: 'river/multi',
                    import_url: 'river/import',
                    table: 'river',
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
                        {field: 'code', title: __('Code'), operate: 'LIKE'},
                        {field: 'river_circle', title: __('River_circle'), operate: 'LIKE'},
                        {field: 'river_type', title: __('River_type')},
                        {field: 'river_name', title: __('River_name'), operate: 'LIKE'},
                        {field: 'river_level', title: __('River_level')},
                        {field: 'river_attr', title: __('River_attr')},
                        {field: 'radmin_id', title: __('Radmin_id')},
                        {field: 'circle_code', title: __('Circle_code'), operate: 'LIKE'},
                        {field: 'circle_name', title: __('Circle_name'), operate: 'LIKE'},
                        {field: 'tributary', title: __('Tributary'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'radmin.id', title: __('Radmin.id')},
                        {field: 'radmin.name', title: __('Radmin.name'), operate: 'LIKE'},
                        {field: 'radmin.plevel', title: __('Radmin.plevel')},
                        {field: 'radmin.type', title: __('Radmin.type'), operate: 'LIKE'},
                        {field: 'radmin.autharea', title: __('Radmin.autharea')},
                        {field: 'radmin.company_id', title: __('Radmin.company_id')},
                        {field: 'radmin.job', title: __('Radmin.job'), operate: 'LIKE'},
                        {field: 'radmin.createtime', title: __('Radmin.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'radmin.updatetime', title: __('Radmin.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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