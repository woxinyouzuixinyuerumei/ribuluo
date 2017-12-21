var map;
var marker;
//初始化地图
function initializeMap(div, lat, lng, zoom) {
    div = div || 'map_canvas';
    lat = lat || 39.915;
    lng = lng || 116.404;
    zoom = zoom || 12;
	map = new BMap.Map(div,{enableMapClick:false});            // 创建Map实例
	var point = new BMap.Point(lng,lat);    // 创建点坐标
	map.centerAndZoom(point,zoom);  //初始化地图,设置中心点坐标和地图级别
    map.addControl(new BMap.NavigationControl({type: BMAP_NAVIGATION_CONTROL_SMALL}));
    map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放

    //var ac = new BMap.Autocomplete({"input" : "address","location" : map}); //建立一个自动完成的对象
	//map.addEventListener('click', function(e){codeLatLng(e.point);}); //点击地图标记
}
//根据城市名设置中心点
function setCenter(city) {
    map.setCenter(city);
}
//地址标记
function codeAddress() {
    var address = document.getElementById("address").value;
    if(address==''){
        alert("请输入地址！");
        return ;
	}
	var ls = new BMap.LocalSearch(map);
	ls.search(address);
	ls.setSearchCompleteCallback(function(rs){
		if (ls.getStatus() == BMAP_STATUS_SUCCESS){
			var poi = rs.getPoi(0);
			if(poi){
				AddMarkerObject(poi.point,address);
			}else{
			   alert("标记失败！");
			}
		}else{
			alert("标记失败！");
		}								  
	});
}
//坐标点标记
function codeLatLng(point) {
    var geocoder = new BMap.Geocoder();
    geocoder.getLocation(point, function(rs){
        var address = rs.address;
		if (address) {
			document.getElementById("address").value = address;
		    AddMarkerObject(point,address)
		}else {
		    alert("标记失败！" );
		}
    });        

}
//增加标记
function AddMarkerObject(center,address){
    deleteMarker();
	marker = new BMap.Marker(center,{
		enableDragging: true,					 
		title: address,
	});
    var zoom = map.getZoom();
	if(zoom<16)zoom = 16;
	map.centerAndZoom(center,zoom);
	map.addOverlay(marker);
	CreateData();
	Mark(marker);
}
//增加标记 查看编辑使用
function addTags(lat,lng,ads,mapId,zoom) {	
	var point = new BMap.Point(lng, lat);
	if(mapId=="map_canvas"){
		AddMarkerObject(point,ads);
		map.centerAndZoom(point,zoom);
	}else{
		return ;
	}	
}
//增加事件处理
function Mark(_Marker){
	var myMarker = _Marker;
	var content = '<div class="layui-infowindow">您可以拖动气球到您要标记的地方。</div><a class="layui-btn layui-btn-xs layui-fr" href="javascript:void(0);" onclick="deleteMarker()">删除标记</a>';
    var SearchInfoWindow = new BMapLib.SearchInfoWindow(map, content, {
        title: "信息框", //标题
        width: 250, //宽度
        height: 50, //高度
        panel : "panel", //检索结果面板
        enableAutoPan : true, //自动平移
        enableSendToPhone : false, //是否启动发送到手机功能
        searchTypes :[]
    });
	//var infoWindow = new BMap.InfoWindow(content);
    SearchInfoWindow.open(myMarker);
    //myMarker.openInfoWindow(infoWindow);
    myMarker.addEventListener("mouseover", function(){
    	//this.openInfoWindow(infoWindow);
    	SearchInfoWindow.open(this);
    });
	myMarker.addEventListener("dragstart", function(){
        //this.closeInfoWindow();
		SearchInfoWindow.close();
	});
	myMarker.addEventListener("dragend", function(){
        //this.openInfoWindow(infoWindow);
        SearchInfoWindow.open(this);
		CreateData();
	});
	map.addEventListener("zoomend", function(){
		//myMarker.closeInfoWindow();
		SearchInfoWindow.close();
		CreateData();
	});
	map.addEventListener("dragstart", function(){
        //myMarker.closeInfoWindow();
		SearchInfoWindow.close();
	});
	map.addEventListener("mouseout", function(){
        //myMarker.closeInfoWindow();
		//SearchInfoWindow.close();
	});

}
//设置数据
function CreateData(){
	if(marker==null){
		document.getElementById("lng").value = "";
		document.getElementById("lat").value = "";
		document.getElementById("zoom").value = "";
	}else{
		var gll = marker.getPosition();
		var Lng = gll.lng;
		var Lat = gll.lat;
		var Zoom = map.getZoom();
		document.getElementById("lng").value = Lng;
		document.getElementById("lat").value = Lat;
		document.getElementById("zoom").value = Zoom;
	}
	
}
// 删除指定标记
function deleteMarker(){
	if(marker==null ) return ;
	map.clearOverlays();
	marker = null;
    CreateData();
}
// 隐藏指定标记
function clearMarker() {
   map.clearOverlays();
}
// 显示所有标记
function showMarker() {
    if(marker==null ) return ;
    map.addOverlay(marker);
}

//增加标记
function AddMarker(){
    var lat = document.getElementById("lat").value;
    var lng = document.getElementById("lng").value;
    var zoom = document.getElementById("zoom").value;
    var center = new BMap.Point(lng,lat);

    if(zoom<16)zoom = 16;
    map.centerAndZoom(center,zoom);

    var marker = new BMap.Marker(center);  // 创建标注
    map.addOverlay(marker);               // 将标注添加到地图中
    marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
}