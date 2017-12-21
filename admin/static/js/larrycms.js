var larryTab;
layui.use(['jquery','larryElem','layer','common','form','larryMenu','larryTab'],function(){
	var $ = layui.$,
		larryElem = layui.larryElem,
		layer = layui.layer,
		common = layui.common,
		form = layui.form,
		// 页面上下文菜单
		larryMenu = layui.larryMenu();
		//核心操作
		larryTab = layui.larryTab({
			//top_menu: '#larry_top_menu',
			left_menu: '#larry_left_menu',
			larry_elem: '#larry_tab',
			spreadOne: true
		});
    // 页面禁止双击选中
    $('body').bind("selectstart", function() {return false;});

    // 菜单初始化
    // 方法1：
	larryTab.menuSet({
		tyep:'GET',
		url: '/index/meun/',
		topFilter: undefined,
		lefFilter: 'LarrySide',
		tabSession: false
	});
    larryTab.menu();
    // 方法2：
    /*$.ajaxSettings.async = false;
	$.getJSON('../datas/data.json?t=' + Math.random(), function(menuData) {
		larryTab.menuSet({
			data: menuData,
			spreadOne: false,
			topFilter: 'TopMenu',
			lefFilter: 'LarrySide'
		});
		larryTab.menu();
	});
    $.ajaxSettings.async = true;*/
    // 1监听导航菜单点击事件 请注释2
    // $('#larry_top_menu li').on('click',function(){
    // 	 var group = $(this).children('a').data('group');
    // 	 larryTab.on('click(TopMenu)',group);
    // 	 //监听左侧菜单点击事件
    // 	 larryTab.on('click(LarrySide)',group,function(data){
    //           larryTab.tabAdd(data.field);
    // 	 });
    // })
    //$('#larry_top_menu li').eq(0).click();
    // 2若不存在顶级菜单 注释以上顶级菜单监听js
	larryTab.on('click(LarrySide)','0', function(data) {
		larryTab.tabAdd(data.field);
	});


	// 刷新iframe
	$("#refresh_iframe").click(function() {
		$('#larry_tab_content').children('.layui-show').children('iframe')[0].contentWindow.location.reload(true);
	});
	// 常用操作
	$('#buttonRCtrl').find('dd').each(function() {
		$(this).on('click', function() {
			var eName = $(this).children('a').attr('data-eName');
			larryTab.tabCtrl(eName);
		});
	});

    //修改密码
    $('#setpassword').on('click',function(){
        var url = $(this).attr('data_href');
        var index = layer.open({
            title: "修改密码",
            type: 2,
            skin:'larry-green',
            area: ['400px', '340px'],
            content: url
        });
    });

	// 登出系统
	$('#logout').on('click',function(){
		var url = $(this).attr('data_href');
		console.log(url);
		common.logOut('退出登陆提示！','您真的确定要退出系统吗？',url);
	});

	//个人资料
    $('#userinfo').on('click', function() {
        var title = $(this).children('a').attr('data_title');
        var href = $(this).children('a').attr('data_href');
        var icon = $(this).children('a').attr('data_icon');
        var data = {
            id: 99999999,
            title: title,
            href: href,
            icon: icon
        }
        larryTab.tabAdd(data);
    });

    var larryMenuData = [
		[{
			text: "刷新当前页",
		    func: function() {
		    	$(".layui-tab-content .layui-tab-item").each(function() {
		    		if ($(this).hasClass('layui-show')) {
		    			$(this).children('iframe')[0].contentWindow.location.reload(true);
		    		}
		    	});
		    }
		},{
			text: "重载主框架",
			func: function() {
				document.location.reload();
			}
		}, {
			text: "选项卡常用操作",
			data: [
				[{
					text: "定位当前选项卡",
					func: function() {
						$("#tabCtrD").trigger("click");
					}
				},{
					text: "关闭当前选项卡",
					func: function() {
						$("#tabCtrA").trigger("click");
					}
				}, {
					text: "关闭其他选项卡",
					func: function() {
						$("#tabCtrB").trigger("click");
					}
				}, {
					text: "关闭全部选项卡",
					func: function() {
						$("#tabCtrC").trigger("click");
					}
				}]
			]
		}],
		[{
            text: "访问户外媒宝官网",
            func: function() {
                window.open('http://www.huwaimeibao.com');
            }
		}]
	];
	larryMenu.ContentMenu(larryMenuData,{
         name: "body" 
	},$('body'));
	$('#larry_body').mouseover(function(){
        larryMenu.remove();
	});

	$('.pressKey').on('click', function() {
		var titW = parseInt($('#larry_tab').width() - 270);
		var $tabUl = $('#larry_tab').find('li'),
			all_li_w = 0;
		$tabUl.each(function(i, n) {
			all_li_w += $(n).outerWidth(true);
		});
		if (titW >= all_li_w) {
			layer.tips('当前没有可移动的选项卡！', $(this), {
				tips: [3, '#ff5722']
			});
		}
	});

    // 兼容蛋疼的移动端
	$('#larryMobile').on('click', function() {
		if ($('.larry-mobile #larry_left').css("display") == "none") {
			$('.larry-mobile #larry_left').show();
		} else {
			$('.larry-mobile #larry_left').hide();
		}
        
        if ($('.larry-mobile .larrycms-top-menu').css("display") == "none") {
			$('.larry-mobile .larrycms-top-menu').show();
		} else {
			$('.larry-mobile .larrycms-top-menu').hide();
		}

	});
    var device = layui.device();
	// 兼容IE8 chrome 60以下版本 calc
	if(device.ie && device.ie <9){
          $('.larrycms-top').width($('#larry_admin_out').width()-200);
	}else if(navigator.userAgent.indexOf("Chrome") <= 60 && navigator.userAgent.indexOf("Chrome") > -1){
          $('.larrycms-top').width($('#larry_admin_out').width()-200);
	}

});