function createAdr(){
	location.href = WST.U('home/useraddress/index');
}

function openAdrList(){
	$("#allAdr").popup();
}

function changeAddrId(id){
	$.post(WST.U('home/useraddress/getById'),{id:id},function(data){
		var json = WST.toJson(data);
		console.log(json);
		if(json.status==1){
			$(".name_phone").html("收货人："+json.data.userName+'　　'+json.data.userPhone);
			$('.defaultAdr').html('<i class="fa fa-map-marker" style="color: #cc3333"></i> '+json.data.areaName+json.data.userAddress);
			$('#s_addressId').val(json.data.addressId);
			var areaIdPath = json.data.areaIdPath.split("_");
			// 设置收货地区市级id
			$('#s_areaId').val(areaIdPath[1]);
	     	// 计算运费
			// getCartMoney(areaIdPath[1]);
			$.closePopup();
		}
	})
}
function getCartMoney(areaId2){
	var deliverType = $('#deliverType').val();

	// 自提的话减去运费
	if(deliverType==1){
		//将每个店铺的运费设为零
		$('span[id^="shopF_"]').each(function(k,v){$(v).html(0);})
		$('span[id^="shopC_"]').each(function(k,v){$(v).html($(v).attr('v'))})
		$('#deliverMoney').html(0);
		$('#totalMoney').html($('#totalMoney').attr('v'));
	}else{
		var load = WST.load({msg:'正在计算运费，请稍后...'});
		$.post(WST.U('home/carts/getCartMoney'),{areaId2:areaId2,rnd:Math.random()},function(data,textStatus){
			layer.close(load);
			var json = WST.toJson(data);
		    if(json.status==1){
		    	var shopFreight = 0;
		    	for(var key in json.shops){
		    	 	// 设置每间店铺的运费及总价格
		    	 	$('#shopF_'+key).html(json.shops[key]);
		    	 	var gMoney = parseInt($('#shopC_'+key).attr('v'))+parseInt(json.shops[key]);
		    	 	$('#shopC_'+key).html(gMoney);
		    		shopFreight = shopFreight + json.shops[key];
		    	}
		    	$('#deliverMoney').html(shopFreight);
		 		$('#totalMoney').html(json.total);
		    }
		});
	}
}

function openPayments(){
	$("#payments").popup();
}
function changePaytype(n){
	$('#payType').val(n);
	if(n==0){
		$('#payName').html('货到付款 >');
	}else if(n==1){
		$('#payName').html('在线支付 >');
	}
	$.closePopup();
}

function openDelivers(){
	$("#deliverTypes").popup();
}
function changeDeliverType(n){
	$('#deliverType').val(n);
	if(n==0){
		$('#deliverName').html('快递运输 >');
	}else if(n==1){
		$('#deliverName').html('自提 >');
	}
	var areaId2 = $('#s_areaId').val();
	getCartMoney(areaId2);
	$.closePopup();
}
function openInvoices(){
	$("#invoiceTypes").popup();
}
function changeInvoiceType(n){
	$('#isInvoice').val(n);
	if(n==0){
		$('#invoiceName').html('不需要开发票 >');
		$("#invoiceClientDiv").hide();
	}else if(n==1){
		$('#invoiceName').html('需要开发票 >');
		$("#invoiceClientDiv").show();
	}
	$.closePopup();
}

function getCartMoney(areaId2){
	var deliverType = $('#deliverType').val();

	// 自提的话减去运费
	if(deliverType==1){
		//将每个店铺的运费设为零
		$('span[id^="shopF_"]').each(function(k,v){$(v).html(0);})
		$('span[id^="shopC_"]').each(function(k,v){$(v).html($(v).attr('v'))})
		$('#deliverMoney').html(0);
		$('#totalMoney').html($('#totalMoney').attr('v'));
		$('#submitMoney').html($('#totalMoney').attr('v'));
	}else{
		var load = layer.open({type: 2,content: '请稍后...'});
		$.post(WST.U('home/carts/getCartMoney'),{areaId2:areaId2,rnd:Math.random()},function(data,textStatus){
			layer.close(load);  
			var json = WST.toJson(data);
		     if(json.status==1){
		    	var shopFreight = 0;
		    	for(var key in json.shops){
		    	 	// 设置每间店铺的运费及总价格
		    	 	$('#shopF_'+key).html(json.shops[key]);
		    	 	var gMoney = parseInt($('#shopC_'+key).attr('v'))+parseInt(json.shops[key]);
		    	 	$('#shopC_'+key).html(gMoney);
		    		shopFreight = shopFreight + json.shops[key];
		    	}
		    	$('#deliverMoney').html(shopFreight);
		 		$('#totalMoney').html(json.total);
		 		$('#submitMoney').html(json.total);
		     }
		});
	}
}
function submitOrder(){
	var params = WST.getParams('.j-ipt');
	var load = layer.open({type: 2,content: '请稍后...'});
	$.post(WST.U('home/orders/submit'),params,function(data,textStatus){
		layer.close(load);
		var json = WST.toJson(data);
	    if(json.status==1){
	    	layer.open({style:'width:60%;font-size:25px;',content:json.msg,time:1});
	    	setTimeout(function(){
	    		location.href=WST.U('home/orders/succeed','orderNo='+json.data);
	    	}, 800);
	    }else{
	    	layer.open({style:'width:60%;font-size:25px;',content:json.msg,time:1});
	    }
	});
}