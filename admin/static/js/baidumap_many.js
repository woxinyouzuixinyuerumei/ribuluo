var map;
var allMarkers=[];
function initializeMap(mapId, lat_1, lng_1, zoom_1) {
	lat_1 = lat_1 || 39.915;
	lng_1 = lng_1 || 116.404;
	zoom_1 = zoom_1 || 12;

	map = new BMap.Map(mapId,{enableMapClick:false});            // 创建Map实例
	var point = new BMap.Point(lng_1, lat_1);    // 创建点坐标
	map.centerAndZoom(point,zoom_1);
	map.addControl(new BMap.NavigationControl());
	map.addControl(new BMap.ScaleControl());
	map.addControl(new BMap.OverviewMapControl({size:new BMap.Size(100, 100)})); 
	map.enableScrollWheelZoom();

	deleteOverlays();
}
//根据城市名设置中心点
function setCenter(city) {
    map.setCenter(city);
    // var txt_search = new BMap.Autocomplete({
    //     "input":"key",
    //     "location":map
    // });
    // txt_search.addEventListener("onhighlight", function(e) {  //鼠标放在下拉列表上的事件
    //     if (e.toitem.index > -1) {
    //         _value = e.toitem.value;
    //         myValue =  _value.street +  _value.business;
    //         txt_search.setInputValue(myValue);
    //     }
    // });
    // txt_search.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
    //     var _value = e.item.value;
    //     //myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
    //     myValue =  _value.street +  _value.business;
    //     txt_search.setInputValue(myValue);
    // });
}
function getSideHtml(data) {
    var html= '';
    if(data.length>0) {
        for(var i=0;i<data.length;i++){
            if(data[i].photo=='')var photo = '/static/images/blank.gif';
            else var photo =data[i].photo;
            html = html+'<div class="srh_contnet_side_box" lodgeunitid="lodgeunit_'+data[i]['id']+'" latlng="'+data[i]['lat']+','+data[i]['lng']+'" detailurl="'+data[i]['viewurl']+'" ><div class="srh_contnet_side_box_pic"><img src="'+photo+'" width="85" height="110" alt=""/></div><div class="srh_contnet_side_box_con"><dl><dt><h2>'+data[i]['name']+'</h2><p>'+data[i]['area']+'</p><p>'+data[i]['building_number']+'栋  '+data[i]['floor_number']+'层</p><p>' +data[i]['occupancy_rate']+'%入住率</p> </dt> <dd> <p><span>&nbsp;</span>&nbsp;</p><p><i class="layui-icon">&#xe61f;</i></p></dd></dl></div></div>';
        }
    }else{
        html= '<div class="srh_contnet_side_nodata" >暂无数据！请更改筛选条件重新搜索。</div>';
    }
    return html;
}
//增加标记
function addMakers(data) {
    deleteOverlays();
    var points = Array();
    if(data.length>0) {
        for(var i=0;i<data.length;i++){
            var lat = data[i]['lat'];
            var lng = data[i]['lng'];
            var lodgeUnitId = data[i]['id'];
			var content=getLabelContent(data[i]);
            var point = new BMap.Point(lng,lat);
            points.push(point);
            var icon = new BMap.Icon('/static/images/map_place.png', new BMap.Size(24, 25));
            var marker = new BMap.Marker(point, {
                icon : icon
            });
            map.addOverlay(marker);             // 将标注添加到地图中
            addClickHandler(marker,lodgeUnitId);
            var labelOpts = {
                position : point,
            };
            //allMarkers.push(marker);
            var defaultLabel = new BMap.Label(content,labelOpts);
            map.addOverlay(defaultLabel);
        }
        map.setViewport(points);
    }
}
//
function getLabelContent(data) {
    if(data.photo=='')var photo = '/static/images/lazy_loadimage.png';
    else var photo =data.photo;
    var content= '<div class="site_pop" detailurl="'+data.viewurl+'" id="lodgeunit_'+data.id+'"><div class="site_room"><div class="resule_img_div"><img class="maplazyimage" lazysrc="'+photo+'" src="/static/images/blank.gif"></div><div class="result_btm_con"><div class="result_intro"><a class="resule_img_a"><span class="result_title hiddenTxt">'+data.name+'<em class="hiddenTxt">入住率'+data.occupancy_rate+'%</em></span></a><em class="hiddenTxt">'+data.address+'</em></div></div></div><span class="site_tri"></span><span class="icon_logo"></span></div>';
    return content;
}
//添加标记点击事件
function addClickHandler(marker,lodgeUnitId){
    marker.addEventListener("click",function(e){
        var leftoffset = 0;
        var rightoffset = 0;
        var topoffset = 0;
        if(($('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().left - $('#map_area').offset().left) < 132) {
            leftoffset = 140 -($('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().left - $('#map_area').offset().left);
        }
        if(($('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().left - $('#map_area').offset().left + 132) > $('#map_area').width()) {
            rightoffset = $('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().left - $('#map_area').offset().left + 144 - $('#map_area').width();
        }
        if($('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().top-332 < 340) {
            if($('.tj_box').is(':visible')){
                var tjBoxHeight=46;
            }else{
                var tjBoxHeight=0;
            }
            topoffset = 365 - $('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().top + 276+tjBoxHeight;
        }else if($('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().top-$(window).scrollTop()-46< 340) {
            topoffset = 340 - $('#lodgeunit_'+lodgeUnitId).parent('.BMapLabel').offset().top +$(window).scrollTop()+ 55;
        }

        if(leftoffset != 0) {
            map.panBy(leftoffset,topoffset);
        }
        if(rightoffset != 0) {
            map.panBy(-rightoffset,topoffset);
        }
        if(leftoffset == 0 && rightoffset == 0) {
            map.panBy(0,topoffset);
        }
        $('.site_pop').hide();
        $('#lodgeunit_'+lodgeUnitId).find('.maplazyimage').attr('src',$('#lodgeunit_'+lodgeUnitId).find('.maplazyimage').attr('lazysrc'));
        $('#lodgeunit_'+lodgeUnitId).show();
    });
}
// 删除所有标记
function deleteOverlays() {
	map.clearOverlays();
}