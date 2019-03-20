window.onscroll = function () {
	if (document.documentElement.scrollTop + document.body.scrollTop > 700) {
	    document.getElementById("backTop").style.display = "block";
	}else {
	    document.getElementById("backTop").style.display = "none";
	}
}
function pageScroll() {
	// window.scrollBy(0,-50);
	// scrolldelay=setTimeout('pageScroll()',1);
	// if(document.documentElement.scrollTop==0)clearTimeout(scrolldelay);
	$('body,html').animate({scrollTop:0},500);
	return false;
}
WST.cancelFavorite = function(obj,type,id,fId){
	if(window.conf.IS_LOGIN==0){
		layer.open({
			style:'font-size:25px;'
			,content: '你还没有登录，是否跳转登录？'
			,btn: ['前往登录', '取消']
			,skin: 'footer'
			,yes: function(index){
			  location.href = WST.U('home/users/login');
			}
		});
		return;
	}
	var param = {},str = '商品';
	param.id = fId;
	param.type = type;
	str = (type==1)?'店铺':'商品';
	$.post(WST.U('home/favorites/cancel'),param,function(data,textStatus){
	    if(data.status=='1'){
	    	layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
	    	if(type==1){
	    		$(obj).html('<span><i class="fa fa-heart-o"></i></span> <span>关注店铺</span>');
	    	}else if(type==0){
	    		$(obj).html('<i class="fa fa-heart-o"></i>');		
	    	}
	    	$(obj)[0].onclick = function(){WST.addFavorite(obj,type,id,fId)};   	
	    }else{
	        layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
	    }
    });
}
WST.addFavorite = function(obj,type,id,fId){
	if(window.conf.IS_LOGIN==0){
		layer.open({
			style:'font-size:25px;'
			,content: '你还没有登录，是否跳转登录？'
			,btn: ['前往登录', '取消']
			,skin: 'footer'
			,yes: function(index){
			  location.href = WST.U('home/users/login');
			}
		});
		return;
	}
	$.post(WST.U('home/favorites/add'),{type:type,id:id},function(data,textStatus){
	     if(data.status==1){
	    	layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
	    	if(type==1){
	    		$(obj).html('<span><i class="fa fa-heart"></i></span> <span>已关注</span>');
	    	}else if(type==0){
	    		$(obj).html('<i style="color: #cc3333" class="fa fa-heart"></i>');
	    	}	    	
	    	$(obj)[0].onclick = function(){WST.cancelFavorite(obj,type,id,data.data.fId)};
	     }else{
	    	 layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
	     }
	});
}
//修改购物车状态
WST.changeCartGoods = function(id,buyNum,isCheck){
	$.post(WST.U('home/carts/changeCartGoods'),{id:id,isCheck:isCheck,buyNum:buyNum,rnd:Math.random()},function(data,textStatus){
	    if(data.status!=1){
	    	layer.open({style:'width:60%;font-size:25px;font-size:25px',content:data.msg,time:1});
	    }
	});
}

WST.toJson = function(str){
	var json = {};
	try{
		if(typeof(str )=="object"){
			json = str;
		}else{
			json = eval("("+str+")");
		}
		if(json.status && json.status=='-999'){
			layer.open({style:'width:60%;font-size:25px',content:'对不起，您已经退出系统！请重新登录',time:1});			
			setTimeout(function(){
				location.reload();
			}, 800);				
		}else if(json.status && json.status=='-998'){
			layer.open({style:'width:60%;font-size:25px',content:'对不起，您没有操作权限，请与管理员联系',time:1});
			return;
		}
	}catch(e){
		layer.open({style:'width:60%;font-size:25px',content:"系统发生错误:"+e.getMessage,time:1});
		json = {};
	}
	return json;
}