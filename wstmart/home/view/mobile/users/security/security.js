function changePwd(){
	var params = WST.getParams('.ipt');
	if(params.newPass==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请输入新密码',time:1});return;
	}
	if(params.reNewPass==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请确认新密码',time:1});return;
	}
	if(params.newPass != params.reNewPass){
		layer.open({style:'width:60%;font-size:25px;',content:'两次输入密码不一致',time:1});return;
	}
	var load = layer.open({type:2,content:'加载中'});
	$.post(WST.U('home/users/passEdit'),params,function(data,textStatus){
      	layer.close(load);
      	var json = WST.toJson(data);
      	if(json.status=='1'){
          	layer.open({style:'width:60%;font-size:25px;',content:'操作成功',time:1});
          	setTimeout(function(){
          		location.href=WST.U('home/users/security');
          	}, 800);            	
      	}else{
            layer.open({style:'width:60%;font-size:25px;',content:json.msg,time:1});
      	}
    });
}

function changePayPwd(){
	var params = WST.getParams('.ipt');
	if(params.newPass==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请输入新密码',time:1});return;
	}
	if(params.reNewPass==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请确认新密码',time:1});return;
	}
	if(params.newPass != params.reNewPass){
		layer.open({style:'width:60%;font-size:25px;',content:'两次输入密码不一致',time:1});return;
	}
	var load = layer.open({type:2,content:'加载中'});
	$.post(WST.U('home/users/payPassEdit'),params,function(data,textStatus){
      	layer.close(load);
      	var json = WST.toJson(data);
      	if(json.status=='1'){
          	layer.open({style:'width:60%;font-size:25px;',content:'操作成功',time:1});
          	setTimeout(function(){
          		location.href=WST.U('home/users/security');
          	}, 800);            	
      	}else{
            layer.open({style:'width:60%;font-size:25px;',content:json.msg,time:1});
      	}
    });
}