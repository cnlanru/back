/** License By http://www.lanru.cn/ **/
var Lanru = {
    events: {
        //请求成功的回调
        onAjaxSuccess: function (ret, onAjaxSuccess) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '操作成功';

            if (typeof onAjaxSuccess === 'function') {
                var result = onAjaxSuccess.call(this, data, ret);
                if (result === false)
                    return;
            }
            toastr.success(msg);
        },
        //请求错误的回调
        onAjaxError: function (ret, onAjaxError) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            if (typeof onAjaxError === 'function') {
                var result = onAjaxError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            toastr.error(ret.msg);
        },
        //服务器响应数据后
        onAjaxResponse: function (response) {
            try {
                var ret = typeof response === 'object' ? response : JSON.parse(response);
                if (!ret.hasOwnProperty('code')) {
                    $.extend(ret, {code: -2, msg: response, data: null});
                }
            } catch (e) {
                var ret = {code: -1, msg: e.message, data: null};
            }
            return ret;
        }
    },
    api: {
        refreshmenu: function () {
            $(".sidebar-menu").trigger("refresh");
        },
        //发送Ajax请求
        ajax: function (options, success, error) {
            options = typeof options === 'string' ? {url: options} : options;
            var index;
            if (typeof options.loading === 'undefined' || options.loading) {
                index = layer.load(options.loading || 0);
            }
            options = $.extend({
                type: "POST",
                dataType: "json",
                success: function (ret) {
                    index && layer.close(index);
                    ret = Lanru.events.onAjaxResponse(ret);
                    if (ret.code === 1) {
                        Lanru.events.onAjaxSuccess(ret, success);
                    } else {
                        Lanru.events.onAjaxError(ret, error);
                    }
                },
                error: function (xhr) {
                    index && layer.close(index);
                    var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    Lanru.events.onAjaxError(ret, error);
                }
            }, options);
            $.ajax(options);
        },
        //打开一个弹出窗口
        open: function (url, title, options) {
            title = options && options.title ? options.title : (title ? title : "");

            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "dialog=1";
            var area = [$(window).width() > 800 ? '800px' : '100%', $(window).height() > 600 ? '600px' : '100%'];

            options = $.extend({
                type: 2,
                title: title,
                shadeClose: true,
                shade: false,
                maxmin: true,
                moveOut: true,
                area: area,
                content: url,
                zIndex: layer.zIndex,
                success: function (layero, index) {
                    var that = this;
                    //存储callback事件
                    $(layero).data("callback", that.callback);

                    layer.setTop(layero);
                    try {
                        var frame = layer.getChildFrame('html', index);
                        var layerfooter = frame.find(".layer-footer");
                        Lanru.api.layerfooter(layero, index, that);
                        //绑定事件
                        if (layerfooter.size() > 0) {
                            // 监听窗口内的元素及属性变化
                            // Firefox和Chrome早期版本中带有前缀
                            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
                            if (MutationObserver) {
                                // 选择目标节点
                                var target = layerfooter[0];
                                // 创建观察者对象
                                var observer = new MutationObserver(function (mutations) {
                                    Lanru.api.layerfooter(layero, index, that);
                                    mutations.forEach(function (mutation) {
                                    });
                                });
                                // 配置观察选项:
                                var config = {attributes: true, childList: true, characterData: true, subtree: true}
                                // 传入目标节点和观察选项
                                observer.observe(target, config);
                                // 随后,你还可以停止观察
                                // observer.disconnect();
                            }
                        }
                    } catch (e) {

                    }
                    if ($(layero).height() > $(window).height()) {
                        //当弹出窗口大于浏览器可视高度时,重定位
                        layer.style(index, {
                            top: 0,
                            height: $(window).height()
                        });
                    }
                }
            }, options ? options : {});
            if ($(window).width() < 480 || (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream && top.$(".tab-pane.active").size() > 0)) {
                options.area = [$(window).width() + "px", $(window).height() + "px"];
                options.offset = ["0px", "0px"];
            }

            return layer.open(options);
        },
        //关闭窗口并回传数据
        close: function (data) {
            var index = parent.layer.getFrameIndex(window.name);
            var callback = parent.$("#layui-layer" + index).data("callback");
            //再执行关闭
            parent.layer.close(index);
            //再调用回传函数
            if (typeof callback === 'function') {
                callback.call(undefined, data);
            }
        },
        layerfooter: function (layero, index, that) {
            var frame = layer.getChildFrame('html', index);
            var layerfooter = frame.find(".layer-footer");
            if (layerfooter.size() > 0) {
                $(".layui-layer-footer", layero).remove();
                var footer = $("<div />").addClass('layui-layer-btn layui-layer-footer');
                footer.html(layerfooter.html());
                if ($(".row", footer).size() === 0) {
                    $(">", footer).wrapAll("<div class='row'></div>");
                }
                footer.insertAfter(layero.find('.layui-layer-content'));
                //绑定事件
                footer.on("click", ".btn", function () {
                    if ($(this).hasClass("disabled") || $(this).parent().hasClass("disabled")) {
                        return;
                    }
                    var index = footer.find('.btn').index(this);
                    $(".btn:eq(" + index + ")", layerfooter).trigger("click");
                });

                var titHeight = layero.find('.layui-layer-title').outerHeight() || 0;
                var btnHeight = layero.find('.layui-layer-btn').outerHeight() || 0;
                //重设iframe高度
                $("iframe", layero).height(layero.height() - titHeight - btnHeight);
            }
            //修复iOS下弹出窗口的高度和iOS下iframe无法滚动的BUG
            if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
                var titHeight = layero.find('.layui-layer-title').outerHeight() || 0;
                var btnHeight = layero.find('.layui-layer-btn').outerHeight() || 0;
                $("iframe", layero).parent().css("height", layero.height() - titHeight - btnHeight);
                $("iframe", layero).css("height", "100%");
            }
        },
        //查询Url参数
        query: function (name, url) {
            if (!url) {
                url = window.location.href;
            }
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&/]" + name + "([=/]([^&#/?]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results)
                return null;
            if (!results[2])
                return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        },
        gettablecolumnbutton: function (options) {
            if (typeof options.tableId !== 'undefined' && typeof options.fieldIndex !== 'undefined' && typeof options.buttonIndex !== 'undefined') {
                var tableOptions = $("#" + options.tableId).bootstrapTable('getOptions');
                if (tableOptions) {
                    var columnObj = null;
                    $.each(tableOptions.columns, function (i, columns) {
                        $.each(columns, function (j, column) {
                            if (typeof column.fieldIndex !== 'undefined' && column.fieldIndex === options.fieldIndex) {
                                columnObj = column;
                                return false;
                            }
                        });
                        if (columnObj) {
                            return false;
                        }
                    });
                    if (columnObj) {
                        return columnObj['buttons'][options.buttonIndex];
                    }
                }
            }
            return null;
        },
        replaceids: function (elem, url) {
            //如果有需要替换ids的
            if (url.indexOf("{ids}") > -1) {
                var ids = 0;
                var tableId = $(elem).data("table-id");
                if (tableId && $(tableId).size() > 0 && $(tableId).data("bootstrap.table")) {
                    ids = table.api.selectedids($(tableId)).join(",");
                }
                url = url.replace(/\{ids\}/g, ids);
            }
            return url;
        },
    }
};