function getIds(){
	var ids = new Array();
	$("input[name='favoriteId']:checked").each(function(){
		ids.push($(this).val());
	});
	return ids;
}

function batchCcl(){
	var ids = getIds();
	if(ids==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请选择要操作的店铺',time:1});
		return;
	}
	layer.open({
		style:'font-size:25px;',
		content:'确定要取消关注这些店铺么？',
		btn:['确定', '取消'],
		yes:function(index){
			var load = layer.open({type: 2,content: '请稍后...'});
			var params = {};
        	params.ids = ids;
        	params.type = 1;
        	$.post(WST.U('home/favorites/batchCcl'),params,function(data,textStatus){
	          layer.close(load);
	          if(data.status=='1'){
	            layer.open({style:'width:60%;font-size:25px;',content:'操作成功'});
	            window.location.href=WST.U('home/favorites/shops');
	          }else{
	            layer.open({style:'width:60%;font-size:25px;',content:'操作失败',time:1});
	          }
	        });
		}
	});
}