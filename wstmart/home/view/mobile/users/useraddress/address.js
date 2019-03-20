function changeDefault(id){
	var w = layer.open({
		style:'font-size:25px;',
		content:'您确定设置为默认地址吗？',
		btn:['确定','取消'],
		yes:function(index){
			var load = layer.open({type: 2,content: '请稍后...'});
			$.post(WST.U('Home/UserAddress/setDefault'),{id:id},function(data,textStatus){
				if(data.status>0){
					layer.open({style:'width:60%;font-size:25px;',content:data.msg});
					setTimeout(function(){
						location.reload();
					},800);
				}else{
					layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
				}
			});
		}
	});
}

function toEdit(id){
	location.href=WST.U('home/useraddress/edit',"id="+id);
}

function delAddress(id){
	var w = layer.open({
		style:'font-size:25px;',
		content:'您确定要删除该地址吗？',
		btn:['确定','取消'],
		yes:function(index){
			var load = layer.open({type: 2,content: '请稍后...'});
			$.post(WST.U('Home/UserAddress/del'),{id:id},function(data,textStatus){
				if(data.status>0){
					layer.open({style:'width:60%;font-size:25px;',content:data.msg});
					setTimeout(function(){
						location.reload();
					},800);
				}else{
					layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
				}
			});
		}
	});
}

function save(){
	var params = {};
	params.addressId = $("#addressId").val();
	params.userName = $("#userName").val();
	params.userPhone = $("#userPhone").val();
	var areaIds = $("#city-picker").attr('data-codes');
	params.areaIds = areaIds.split(',');
	params.areaId = $("#city-picker").attr('data-code');
	params.userAddress = $("#userAddress").val();
	params.isDefault = $("#isDefault").is(':checked')?1:0;
	if(params.userName == ''){
		layer.open({style:'width:60%;font-size:25px;',content:'收货人不能为空',time:1});return;
	}
	if(!(/^1[34578]\d{9}$/.test(params.userPhone))){
		layer.open({style:'width:60%;font-size:25px;',content:'请输入正确的手机号',time:1});return;
	}
	if(areaIds == ''){
		layer.open({style:'width:60%;font-size:25px;',content:'地址不能为空',time:1});return;
	}
	if(params.userAddress == ''){
		layer.open({style:'width:60%;font-size:25px;',content:'详细地址不能为空',time:1});return;
	}
	var load = layer.open({type:2,content:'请稍后...'});
	$.post(WST.U('home/useraddress/'+((params.addressId==0)?"add":"toEdit")),params,function(data){
		if(data.status=='1'){
         	layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
         	setTimeout(function(){
				location.href=WST.U('home/useraddress/index');
			},800);      		
      	}else{
			layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
      	}
	});
}