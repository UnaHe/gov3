jQuery.extend(jQuery.validator.messages, {
    required: "<i class='fa fa-exclamation-circle yellow'></i>请填写此字段",
    remote: "<i class='fa fa-exclamation-circle yellow'></i>请修该此字段",
    email: "<i class='fa fa-exclamation-circle yellow'></i>请输入正确格式的电子邮件",
    url: "<i class='fa fa-exclamation-circle yellow'></i>请输入合法的网址",
    date: "<i class='fa fa-exclamation-circle yellow'></i>请输入合法的日期",
    dateISO: "<i class='fa fa-exclamation-circle yellow'></i>请输入合法的日期 (ISO).",
    number: "<i class='fa fa-exclamation-circle yellow'></i>请输入合法的数字",
    digits: "<i class='fa fa-exclamation-circle yellow'></i>只能输入整数",
    creditcard: "<i class='fa fa-exclamation-circle yellow'></i>请输入合法的信用卡号",
    equalTo: "<i class='fa fa-exclamation-circle yellow'></i>输入不一致，请再次输入相同的值",
    accept: "<i class='fa fa-exclamation-circle yellow'></i>请输入拥有合法后缀名的字符串",
    maxlength: jQuery.validator.format("<i class='fa fa-exclamation-circle yellow'></i>请输入一个 长度最多是 {0} 的字符串"),
    minlength: jQuery.validator.format("<i class='fa fa-exclamation-circle yellow'></i>请输入一个 长度最少是 {0} 的字符串"),
    rangelength: jQuery.validator.format("<i class='fa fa-exclamation-circle yellow'></i>请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
    range: jQuery.validator.format("<i class='fa fa-exclamation-circle yellow'></i>请输入一个介于 {0} 和 {1} 之间的值"),
    max: jQuery.validator.format("<i class='fa fa-exclamation-circle yellow'></i>请输入一个最大为{0} 的值"),
    min: jQuery.validator.format("<i class='fa fa-exclamation-circle yellow'></i>请输入一个最小为{0} 的值")
});