/** License By http://www.lanru.cn/ **/
var Controller = {
    events: {
        changBtnDel: function (content) {
            var options = Controller.events.selectedIds(content);

            if (options.length > 0) {
                $(".btn-del", content).removeClass('btn-disabled disabled');
            } else {
                if (!$(".btn-del", content).hasClass('btn-disabled disabled')) {
                    $(".btn-del", content).addClass('btn-disabled disabled');
                }
            }
        },
        selectedIds: function (content) {
            var options = [];

            $("[name='btSelectItem']:checked", content).each(function () {
                options.push($(this).val());
            });

            return options;
        },
        btSelectItem: function(content) {
            $("[name='btSelectItem']", content).click(function () {
                Controller.events.changBtnDel(content);
            });
        },
        btnDelOne: function (content) {
            if ($('.btn-delone', content).length) {
                $(".btn-delone", content).click(function () {
                    var that = this, x = $(that).offset().top, y = $(that).offset().left - 280;
                    layer.confirm(
                        '是否确实要删除?删除后不可恢复',
                        {icon: 3, title:'提示', offset: [x + 'px', y + 'px']},
                        function () {
                            var url = $(that).data("url") ? $(that).data("url") : $(that).attr("href");
                            Lanru.api.ajax(url, function () {
                                layer.closeAll();
                                $(":checked", content).attr("checked",false);
                                location.reload();
                            }, function () {
                                layer.closeAll();
                            });
                        }
                    );
                });
            }
        },
        btnSelectAall: function (content) {
            if ($("[name='btSelectAll']", content).length) {
                $("[name='btSelectAll']", content).click(function () {
                    if(this.checked){
                        $("[name='btSelectItem']:not(:disabled)", content).prop("checked", true);
                    }else{
                        $("[name='btSelectItem']:not(:disabled)", content).prop("checked", false);
                    }

                    Controller.events.changBtnDel(content);
                });
            }
        },
        btnDel: function (content) {
            if ($(".btn-del", content).length) {
                var options = Controller.events.selectedIds(content);
                $(".btn-del", content).click(function () {
                    var that = this;

                    layer.confirm(
                        '确定删除选中的 ' + options.length + ' 项?',
                        {icon: 3, title: '提醒', shadeClose: true},
                        function (index) {
                            var url = $(that).data('url');

                            Lanru.api.ajax({ url: url, data:{ids: options} }, function () {
                                layer.closeAll();
                                $(":checked", content).attr("checked",false);
                                location.reload();
                            });
                        }
                    );
                });
            }
        }
    },
    api: {
        init: function (id) {
            var content = $(id);
            Controller.events.btnDelOne(content);
            Controller.events.btnSelectAall(content);
            Controller.events.btnDel(content);
            Controller.events.btSelectItem(content);
        }
    }
};