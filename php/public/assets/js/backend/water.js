define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'water/index' + location.search,
                    add_url: 'water/add',
                    edit_url: 'water/edit',
                    del_url: 'water/del',
                    multi_url: 'water/multi',
                    import_url: 'water/import',
                    table: 'water',
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
                        {field: 'river_name', title: __('River_name'), operate: 'LIKE'},
                        {field: 'point_number', title: __('Point_number'), operate: 'LIKE'},
                        {field: 'point_name', title: __('Point_name'), operate: 'LIKE'},
                        {field: 'point_position', title: __('Point_position'), operate: 'LIKE'},
                        {field: 'ph', title: __('Ph'), operate:'BETWEEN'},
                        {field: 'transparency', title: __('Transparency'), operate:'BETWEEN'},
                        {field: 'permanganate', title: __('Permanganate'), operate:'BETWEEN'},
                        {field: 'ammonia_nitrogen', title: __('Ammonia_nitrogen'), operate:'BETWEEN'},
                        {field: 'phosphorus', title: __('Phosphorus'), operate:'BETWEEN'},
                        {field: 'water_category', title: __('Water_category'), searchList: {"Ⅰ":__('Ⅰ'),"Ⅱ":__('Ⅱ'),"Ⅲ":__('Ⅲ'),"Ⅳ":__('Ⅳ'),"Ⅴ":__('Ⅴ'),"Ⅵ":__('Ⅵ'),"Ⅶ":__('Ⅶ')}, formatter: Table.api.formatter.normal},
                        {field: 'pollution_factor', title: __('Pollution_factor'), operate: 'LIKE'},
                        {field: 'reason', title: __('Reason'), operate: 'LIKE'},
                        {field: 'collection_date', title: __('Collection_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'responsible_unit', title: __('Responsible_unit'), operate: 'LIKE'},
                        {field: 'river_type', title: __('River_type')},
                        {field: 'code', title: __('Code'), operate: 'LIKE'},
                        {field: 'river_attr', title: __('River_attr')},
                        {field: 'section_type', title: __('Section_type')},
                        {field: 'test_name', title: __('Test_name'), operate: 'LIKE'},
                        {field: 'associated_waters', title: __('Associated_waters'), operate: 'LIKE'},
                        {field: 'plevel', title: __('Plevel')},
                        {field: 'p_name', title: __('P_name'), operate: 'LIKE'},
                        {field: 'monitoring_mode', title: __('Monitoring_mode'), operate: 'LIKE'},
                        {field: 'control_level', title: __('Control_level')},
                        {field: 'monitoring_frequency', title: __('Monitoring_frequency'), operate: 'LIKE'},
                        {field: 'explode_date', title: __('Explode_date'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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