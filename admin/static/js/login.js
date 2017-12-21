layui.use(['jquery','common','layer','form','larryMenu'],function(){
    var $ = layui.$,
        layer = layui.layer,
        form = layui.form,
        common = layui.common;
    // 页面上下文菜单
    var larryMenu = layui.larryMenu();

    var mar_top = ($(document).height()-$('#larry_login').height())/2.5;
    $('#larry_login').css({'margin-top':mar_top});
    //common.larryCmsSuccess('用户名：larry 密码：larry 无须输入验证码，输入正确后直接登录后台!','larryMS后台帐号登录提示',20);
    var placeholder = '';
    $("#larry_form input[type='text'],#larry_form input[type='password']").on('focus',function(){
          placeholder = $(this).attr('placeholder');
          $(this).attr('placeholder','');
    });
    $("#larry_form input[type='text'],#larry_form input[type='password']").on('blur',function(){
          $(this).attr('placeholder',placeholder);
    });
    
    common.larryCmsLoadJq('../static/common/plus/jquery.supersized.min.js', function() {
        $.supersized({
            // 功能
            slide_interval: 3000,
            transition: 1,
            transition_speed: 5000,
            performance: 1,
            // 大小和位置
            min_width: 0,
            min_height: 0,
            vertical_center: 1,
            horizontal_center: 1,
            fit_always: 0,
            fit_portrait: 1,
            fit_landscape: 0,
            // 组件
            slide_links: 'blank',
            slides: [{
                image: '../static/images/login/1.jpg'
            }, {
                image: '../static/images/login/2.jpg'
            }, {
                image: '../static/images/login/3.jpg'
            }, {
                image: '../static/images/login/4.jpg'
            }, {
                image: '../static/images/login/5.jpg'
            }, {
                image: '../static/images/login/6.jpg'
            }, {
                image: '../static/images/login/7.jpg'
            }, {
                image: '../static/images/login/8.jpg'
            }, {
                image: '../static/images/login/9.jpg'
            }, {
                image: '../static/images/login/10.jpg'
            }]
        });
    });

    form.on('submit(login)',function(data){
        loading =layer.load(1, {shade: [0.1,'#fff'] });//0.1透明度的白色背景
        var url = $('form#larry_form').attr('action');
        $.post(url,data.field,function(res){
            layer.close(loading);
            if(res.code == 1){
                layer.msg(res.msg, {icon: 1, time: 1000}, function(){
                    location.href = res.url;
                });
            }else{
                $('#captcha').val('');
                if(res.data==1) var id = $('#mobile');
                else if(res.data==2) var id = $('#password');
                else if(res.data==3) var id = $('#larry_code');
                layer.tips(res.msg, id, {
                    tips: [2, '#FF5722']
                });
                $('#codeimage').click();
            }
        });
        return false;
    });

    // 右键菜单控制
    var larrycmsMenuData = [
        [{
            text: "刷新页面",
            func: function() {
                top.document.location.reload();
            }
        }],
        [{
            text: "访问户外媒宝官网",
            func: function() {
                window.open('http://www.huwaimeibao.com');
            }
        }]
    ];
    larryMenu.ContentMenu(larrycmsMenuData,{
         name: "html" 
    },$('html'));
});
    