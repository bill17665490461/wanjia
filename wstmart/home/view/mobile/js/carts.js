function changeNum(obj){
	var maxNum = parseInt($(obj).attr('max-val'));
	var val = parseInt($(obj).val());
	var cartNum = 1;
	if(val > maxNum){
		cartNum = maxNum;
	}else if(val < 1 || isNaN(val)){
		cartNum = 1;
	}else{
		cartNum = val;
	}
	$(obj).val(cartNum);
	var cid = $(obj).attr('id').split('_')[2];
	var isChecked = $('input[value="'+cid+'"]').is(':checked')?1:0;
	WST.changeCartGoods(cid,cartNum,isChecked);
	statCartMoney();
}
function minusNum(obj){
	var val = parseInt($(obj).next().val());
	if(val==1){
		if(!$(obj).hasClass('locked')){
			$(obj).addClass('locked');
		}
		return;
	}else{
		$(obj).next().val(val-1);
		$(obj).next().next().removeClass('locked');
		//修改状态
		var cid = $(obj).next().attr('id').split('_')[2];
		var isChecked = $('input[value="'+cid+'"]').is(':checked')?1:0;
		WST.changeCartGoods(cid,val-1,isChecked);
		statCartMoney();
	}	
}
function addNum(obj){
	var val = parseInt($(obj).prev().val());
	var maxNum = parseInt($(obj).prev().attr('max-val'));
	if(val>=maxNum){
		if(!$(obj).hasClass('locked')){
			$(obj).addClass('locked');
		}
		return
	}else{
		$(obj).prev().val(val+1);
		$(obj).prev().prev().removeClass('locked');
		//修改状态
		var cid = $(obj).prev().attr('id').split('_')[2];
		var isChecked = $('input[value="'+cid+'"]').is(':checked')?1:0;
		WST.changeCartGoods(cid,val+1,isChecked);
		statCartMoney();
	}
}

function checkShop(obj){
	if($(obj).is(':checked')){
		$(obj).parent().next().find('.goodsChk').prop('checked',true);
	}else{
		$(obj).parent().next().find('.goodsChk').prop('checked',false);
	}
	$(obj).parent().next().find('.chk input').each(function(){
		var cid = $(this).val();
		WST.changeCartGoods(cid,$('#buy_num_'+cid).val(),this.checked?1:0);
		statCartMoney();
	});
}
function checkGoods(obj){
	var cid = $(obj).val();
	WST.changeCartGoods(cid,$('#buy_num_'+cid).val(),obj.checked?1:0);
	statCartMoney();
}
function checkAll(obj){
	if($(obj).is(':checked')){
		$('#carts_bottom').siblings().find('.mobCheckbox').prop('checked',true);
	}else{
		$('#carts_bottom').siblings().find('.mobCheckbox').prop('checked',false);
	}
	$(".goodsChk").each(function(){
		var cid = $(this).val();
		WST.changeCartGoods(cid,$('#buy_num_'+cid).val(),this.checked?1:0);
		statCartMoney();
	});
}

function statCartMoney(){
	var cartMoney = 0,id;
	$('.goodsChk').each(function(){
		id = $(this).val();
		goodsTotalPrice = parseFloat($(this).attr('mval'))*parseInt($('#buy_num_'+id).val());
		if($(this).prop('checked')){	
			cartMoney = cartMoney + goodsTotalPrice;
		}
	});
	$('#totalMoney').html("￥"+cartMoney);
}

function toSettlement(){
	var isChk = false;
	$('.goodsChk').each(function(){
		if($(this).prop('checked'))isChk = true;
	});
	if(!isChk){
		layer.open({style:'width:60%;font-size:25px;',content:'请选择要结算的商品!',time:1});
		return;
	}
	var msg = '';
	$('.goodsChk').each(function(){
		if($(this).prop('checked')){
			if($(this).attr('allowbuy')==0){
				msg = '所选商品库存不足';
				return;
			}else if($(this).attr('allowbuy')==1){
				msg = '所选商品购买量大于商品库存';
				return;
			}
		}
	})
	if(msg!=''){
		layer.open({style:'width:60%;font-size:25px;',content:msg,time:1});
		return;
	}
	location.href=WST.U('home/carts/settlement');
}