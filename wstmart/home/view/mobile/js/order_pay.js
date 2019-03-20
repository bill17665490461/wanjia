function openPayFroms(){
	$("#payFormNames").popup();
}

function changePayFrom(n){
	$("#payFrom").val(n);
	if(n==3){
		$('#payFromName').html('钱包支付 >');
	}else if(n==2){
		$('#payFromName').html('微信支付 >');
	}
	$.closePopup();
}


function payNow(orderNo){
	var params = WST.getParams('.j-ipt');
	if(params.payFrom == 3){
		$.prompt({
		  	title: '请输入支付密码',
		  	empty: false, // 是否允许为空
		  	onOK: function (input) {
		  		params.payPassword = input;
		    	$.post(WST.U('home/walletpays/walletpay'),params,function(data) {
					var json = WST.toJson(data);
					if(json.status==-2){
						layer.open({style:'font-size:25px;width:60%',content:json.msg});
						setTimeout(function(){
							location.href = json.data;
						}, 1000);
					}else if(json.status==-1){
						layer.open({style:'font-size:25px;width:60%',content:json.msg,time:1});
					}else if(json.status==1){
						layer.open({style:'font-size:25px;width:60%',content:json.msg,time:1});
						setTimeout(function(){
							window.location = WST.U('home/orders/waitReceive');
						},1000);
					}
				});
		  	},
		  	onCancel: function () {		    
		  	}
		});
		$(".weui-prompt-input").attr('type','password');
	}else{
		//alert(orderNo);
		window.location.href=WST.U('home/weixinjspays/wxjspay','orderNo='+orderNo);
	}
}