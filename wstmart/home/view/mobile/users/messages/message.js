function getChks(){
	var ids = new Array();
	$("input[name='msgId']:checked").each(function(){
		ids.push($(this).val());
	});
	return ids;
}
function batchDel(){
	var ids = getChks();
	if(ids==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请选择要删除的信息',time:1});
		return;
	}
	layer.open({
		style:'font-size:25px;',
		content:'确定要删除这些消息么？',
		btn:['确定', '取消'],
		yes:function(index){
			var load = layer.open({type: 2,content: '请稍后...'});
			var params = {};
        	params.ids = ids;
        	$.post(WST.U('home/messages/batchDel'),params,function(data,textStatus){
	          layer.close(load);
	          if(data.status=='1'){
	            layer.open({style:'width:60%;font-size:25px;',content:'操作成功'});
	            window.location.href=WST.U('home/messages/index');
	          }else{
	            layer.open({style:'width:60%;font-size:25px;',content:'操作失败',time:1});
	          }
	        });
		}
	});
}
function batchRead(){
	var ids = getChks();
	if(ids==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请选择要操作的信息',time:1});
		return;
	}
	layer.open({
		style:'font-size:25px;',
		content:'确定要将这些信息标记为已读么？',
		btn:['确定', '取消'],
		yes:function(index){
			var load = layer.open({type: 2,content: '请稍后...'});
			var params = {};
        	params.ids = ids;
        	$.post(WST.U('home/messages/batchRead'),params,function(data,textStatus){
	          layer.close(load);
	          if(data.status=='1'){
	            layer.open({style:'width:60%;font-size:25px;',content:'操作成功'});
	            window.location.href=WST.U('home/messages/index');
	          }else{
	            layer.open({style:'width:60%;font-size:25px;',content:'操作失败',time:1});
	          }
	        });
		}
	});
}