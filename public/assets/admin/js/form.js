/** License By http://www.lanru.cn/ **/
var Form = {
    config: {
        fieldlisttpl: '<dd class="form-inline"><input type="text" name="{{name}}[{{index}}][key]" class="form-control" value="{{row.key}}" size="10" /> <input type="text" name="{{name}}[{{index}}][value]" class="form-control" value="{{row.value}}" /> <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span> <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span></dd>'
    },
    events: {
        validator: function (form, success, error, submit) {
            if (!form.is("form") || form.hasClass('form-commonsearch'))
                return;

            //绑定表单事件
            form.validator($.extend({
                validClass: 'has-success',
                invalidClass: 'has-error',
                bindClassTo: '.form-group',
                formClass: 'n-default n-bootstrap',
                msgClass: 'n-right',
                stopOnError: true,
                display: function (elem) {
                    return $(elem).closest('.form-group').find(".control-label").text().replace(/\:/, '');
                },
                dataFilter: function (data) {
                    if (data.code === 1) {
                        return "";
                    } else {
                        return data.msg;
                    }
                },
                target: function (input) {
                    var target = $(input).data("target");
                    if (target && $(target).size() > 0) {
                        return $(target);
                    }
                    var $formitem = $(input).closest('.form-group'),
                        $msgbox = $formitem.find('span.msg-box');
                    if (!$msgbox.length) {
                        return [];
                    }
                    return $msgbox;
                },
                valid: function (ret) {
                    var that = this, submitBtn = $(".layer-footer [type=submit]", form);
                    that.holdSubmit(true);
                    submitBtn.addClass("disabled");
                    //验证通过提交表单
                    var submitResult = Form.api.submit($(ret), function (data, ret) {
                        that.holdSubmit(false);
                        submitBtn.removeClass("disabled");
                        if (false === $(this).triggerHandler("success.form", [data, ret])) {
                            return false;
                        }
                        if (typeof success === 'function') {
                            if (false === success.call($(this), data, ret)) {
                                return false;
                            }
                        }
                        //提示及关闭当前窗口
                        var msg = ret.hasOwnProperty("msg") && ret.msg !== "" ? ret.msg : '操作成功!';
                        parent.toastr.success(msg);
                        parent.$(".btn-refresh").trigger("click");
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        return false;
                    }, function (data, ret) {
                        that.holdSubmit(false);
                        if (false === $(this).triggerHandler("error.form", [data, ret])) {
                            return false;
                        }
                        submitBtn.removeClass("disabled");
                        if (typeof error === 'function') {
                            if (false === error.call($(this), data, ret)) {
                                return false;
                            }
                        }
                    }, submit);
                    //如果提交失败则释放锁定
                    if (!submitResult) {
                        that.holdSubmit(false);
                        submitBtn.removeClass("disabled");
                    }
                    return false;
                }
            }, form.data("validator-options") || {}));

            //移除提交按钮的disabled类
            $(".layer-footer [type=submit],.fixed-footer [type=submit],.normal-footer [type=submit]", form).removeClass("disabled");
        },
        selectpicker: function (form) {
            //绑定select元素事件
            if ($(".selectpicker", form).length > 0) {
                $('.selectpicker', form).selectpicker();
                $(form).on("reset", function () {
                    setTimeout(function () {
                        $('.selectpicker').selectpicker('refresh').trigger("change");
                    }, 1);
                });
            }
        },
        datetimepicker: function (form) {
            //绑定日期时间元素事件
            if ($(".datepicker", form).length > 0) {
                $('.datepicker', form).each(function(){
                    laydate.render({
                        elem: this
                        ,trigger: 'click'
                        ,type: 'date'
                    });
                });
            }

            if ($(".timepicker", form).length > 0) {
                $('.timepicker', form).each(function(){
                    laydate.render({
                        elem: this
                        ,trigger: 'click',
                        type: 'time'
                    });
                });
            }

            if ($(".datetimepicker", form).length > 0) {
                $('.datetimepicker', form).each(function(){
                    laydate.render({
                        elem: this
                        ,trigger: 'click',
                        type: 'datetime'
                    });
                });
            }
        },
        daterangepicker: function (form) {
            //绑定日期时间元素事件
            if ($(".daterange", form).length > 0) {
                $('.daterange', form).each(function(){
                    laydate.render({
                        elem: this
                        ,trigger: 'click'
                        ,type: 'date'
                        ,range: true
                    });
                });
            }

            if ($(".timerange", form).length > 0) {
                $('.timerange', form).each(function(){
                    laydate.render({
                        elem: this
                        ,trigger: 'click',
                        type: 'time'
                        ,range: true
                    });
                });
            }

            if ($(".datetimerange", form).length > 0) {
                $('.datetimerange', form).each(function(){
                    laydate.render({
                        elem: this
                        ,trigger: 'click',
                        type: 'datetime'
                        ,range: true
                    });
                });
            }
        },
        plupload: function (form) {
            //绑定plupload上传元素事件
            if ($(".plupload", form).length > 0) {
                Upload.api.plupload($(".plupload", form));
            }
        },
        fieldlist: function (form) {
            //绑定fieldlist
            if ($(".fieldlist", form).length > 0) {
                //刷新隐藏textarea的值
                var refresh = function (name) {
                    var data = {};
                    var textarea = $("textarea[name='" + name + "']", form);
                    var container = textarea.closest("dl");
                    var templateStr = container.data("template");
                    $.each($("input,select", container).serializeArray(), function (i, j) {
                        var reg = /\[(\w+)\]\[(\w+)\]$/g;
                        var match = reg.exec(j.name);
                        if (!match)
                            return true;
                        match[1] = "x" + parseInt(match[1]);
                        if (typeof data[match[1]] == 'undefined') {
                            data[match[1]] = {};
                        }
                        data[match[1]][match[2]] = j.value;
                    });
                    var result = templateStr ? [] : {};
                    $.each(data, function (i, j) {
                        if (j) {
                            if (!templateStr) {
                                if (j.key != '') {
                                    result[j.key] = j.value;
                                }
                            } else {
                                result.push(j);
                            }
                        }
                    });
                    textarea.val(JSON.stringify(result));
                };
                //监听文本框改变事件
                $(document).on('change keyup', ".fieldlist input,.fieldlist textarea,.fieldlist select", function () {
                    refresh($(this).closest("dl").data("name"));
                });
                //追加控制
                $(".fieldlist", form).on("click", ".btn-append,.append", function (e, row) {
                    var container = $(this).closest("dl");
                    var index = container.data("index");
                    var name = container.data("name");
                    var templateStr = container.data("template");
                    var data = container.data();
                    index = index ? parseInt(index) : 0;
                    container.data("index", index + 1);
                    var row = row ? row : {};
                    var vars = {index: index, name: name, data: data, row: row};
                    var html = templateStr ? template(templateStr, vars) : template.render(Form.config.fieldlisttpl, vars);
                    $(html).insertBefore($(this).closest("dd"));
                    $(this).trigger("fa.event.appendfieldlist", $(this).closest("dd").prev());
                });
                //移除控制
                $(".fieldlist", form).on("click", "dd .btn-remove", function () {
                    var container = $(this).closest("dl");
                    $(this).closest("dd").remove();
                    refresh(container.data("name"));
                });
                //拖拽排序
                $("dl.fieldlist", form).dragsort({
                    itemSelector: 'dd',
                    dragSelector: ".btn-dragsort",
                    dragEnd: function () {
                        refresh($(this).closest("dl").data("name"));
                    },
                    placeHolderTemplate: "<dd></dd>"
                });
                //渲染数据
                $(".fieldlist", form).each(function () {
                    var container = this;
                    var textarea = $("textarea[name='" + $(this).data("name") + "']", form);
                    if (textarea.val() == '') {
                        return true;
                    }
                    var templateStr = $(this).data("template");
                    var json = {};
                    try {
                        json = JSON.parse(textarea.val());
                    } catch (e) {
                    }

                    $.each(json, function (i, j) {

                        $(".btn-append,.append", container).trigger('click', templateStr ? j : {
                            key: i,
                            value: j
                        });
                    });
                });

            }
        },
        faselect: function (form) {
            //绑定fachoose选择附件事件
            if ($(".fachoose", form).length > 0) {
                $(".fachoose", form).on('click', function () {
                    var that = this;
                    var multiple = $(this).data("multiple") ? $(this).data("multiple") : false;
                    var mimetype = $(this).data("mimetype") ? $(this).data("mimetype") : '';
                    var admin_id = $(this).data("admin-id") ? $(this).data("admin-id") : '';
                    var user_id = $(this).data("user-id") ? $(this).data("user-id") : '';
                    var openUrl = $(this).data("url");

                    parent.Lanru.api.open(openUrl + "?element_id=" + $(this).attr("id") + "&multiple=" + multiple + "&mimetype=" + mimetype + "&admin_id=" + admin_id + "&user_id=" + user_id, '选择', {
                        callback: function (data) {
                            var button = $("#" + $(that).attr("id"));
                            var maxcount = $(button).data("maxcount");
                            var input_id = $(button).data("input-id") ? $(button).data("input-id") : "";
                            maxcount = typeof maxcount !== "undefined" ? maxcount : 0;
                            if (input_id && data.multiple) {
                                var urlArr = [];
                                var inputObj = $("#" + input_id);
                                var value = $.trim(inputObj.val());
                                if (value !== "") {
                                    urlArr.push(inputObj.val());
                                }
                                urlArr.push(data.url)
                                var result = urlArr.join(",");
                                if (maxcount > 0) {
                                    var nums = value === '' ? 0 : value.split(/\,/).length;
                                    var files = data.url !== "" ? data.url.split(/\,/) : [];
                                    var remains = maxcount - nums;
                                    if (files.length > remains) {
                                        layer.msg('你最多可以选择' + remains + '个文件');
                                        return false;
                                    }
                                }
                                inputObj.val(result).trigger("change").trigger("validate");
                            } else {
                                $("#" + input_id).val(data.url).trigger("change").trigger("validate");
                            }
                        }
                    });
                    return false;
                });
            }
        },
        bindevent: function (form) {

        }
    },
    api: {
        submit: function (form, success, error, submit) {
            if (form.size() === 0) {
                toastr.error("表单未初始化完成,无法提交");
                return false;
            }
            if (typeof submit === 'function') {
                if (false === submit.call(form, success, error)) {
                    return false;
                }
            }
            var type = form.attr("method") ? form.attr("method").toUpperCase() : 'GET';
            type = type && (type === 'GET' || type === 'POST') ? type : 'GET';
            url = form.attr("action");
            url = url ? url : location.href;
            //修复当存在多选项元素时提交的BUG
            var params = {};
            var multipleList = $("[name$='[]']", form);
            if (multipleList.size() > 0) {
                var postFields = form.serializeArray().map(function (obj) {
                    return $(obj).prop("name");
                });
                $.each(multipleList, function (i, j) {
                    if (postFields.indexOf($(this).prop("name")) < 0) {
                        params[$(this).prop("name")] = '';
                    }
                });
            }
            //调用Ajax请求方法
            Lanru.api.ajax({
                type: type,
                url: url,
                data: form.serialize() + (Object.keys(params).length > 0 ? '&' + $.param(params) : ''),
                dataType: 'json',
                complete: function (xhr) {
                    var token = xhr.getResponseHeader('__token__');
                    if (token) {
                        $("input[name='__token__']", form).val(token);
                    }
                }
            }, function (data, ret) {
                $('.form-group', form).removeClass('has-feedback has-success has-error');
                if (data && typeof data === 'object') {
                    //刷新客户端token
                    if (typeof data.token !== 'undefined') {
                        $("input[name='__token__']", form).val(data.token);
                    }
                    //调用客户端事件
                    if (typeof data.callback !== 'undefined' && typeof data.callback === 'function') {
                        data.callback.call(form, data);
                    }
                }
                if (typeof success === 'function') {
                    if (false === success.call(form, data, ret)) {
                        return false;
                    }
                }
            }, function (data, ret) {
                if (data && typeof data === 'object' && typeof data.token !== 'undefined') {
                    $("input[name='__token__']", form).val(data.token);
                }
                if (typeof error === 'function') {
                    if (false === error.call(form, data, ret)) {
                        return false;
                    }
                }
            });
            return true;
        },
        bindevent: function (form, success, error, submit) {

            form = typeof form === 'object' ? form : $(form);

            var events = Form.events;

            events.bindevent(form);

            events.validator(form, success, error, submit);

            events.selectpicker(form);

            events.daterangepicker(form);

            events.datetimepicker(form);

            events.plupload(form);

            events.faselect(form);

            events.fieldlist(form);
        }
    }
};
