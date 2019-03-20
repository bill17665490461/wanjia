$(function () {
	$('#tab').TabPanel({tab:0,callback:function(tab){
		switch(tab){
		   case 0:pageQuery(0,-1);break;
		   case 1:pageQuery(0,1);break;
		   case 2:pageQuery(0,0);break;
		}	
	}})
});
function pageQuery(p,type){
	var tips = WST.msg('正在获取数据，请稍后...',{time:600000000});
	var params = {};
	params.page = p;
	params.type = type;
	$.post(WST.U('home/logmoneys/pageShopQuery'),params,function(data,textStatus){
		layer.close(tips);
	    var json = WST.toJson(data);
	    if(json.status==1){
	    	json = json.data;
		    var gettpl = document.getElementById('tblist').innerHTML;
		    laytpl(gettpl).render(json.Rows, function(html){
		       	$('#page-list').html(html);
		    });
		    if(json.TotalPage>1){
		       	laypage({
			        cont: 'pager', 
			        pages:json.TotalPage, 
			        curr: json.CurrentPage,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		pageQuery(e.curr,type);
			        	}
			        } 
			    });
		     }else{
		       	 $('#pager').empty();
		     }
	    }
	});
}

function toDrawMoney(){
	w = WST.open({
	    type: 1,
	    title:"申请提现",
	    shade: [0.6, '#000'],
	    border: [0],
	    content: $('#drawBox'),
	    area: ['550px', '220px'],
	    offset: '100px'
	});
}
function layerclose(){
  layer.close(w);
}
function drawMoney(){
	var params = WST.getParams('.j-ipt');
	var tips = WST.msg('正在提交数据，请稍后...',{time:600000000});
	$.post(WST.U('home/cashdraws/shopDrawMoney'),params,function(data,textStatus){
		layer.close(tips);
	    var json = WST.toJson(data);
	    if(json.status==1){
            WST.msg(json.msg,{icon:1},function(){
            	location.reload();
            });
	    }else{
	    	WST.msg(json.msg,{icon:2});
	    }
	});
}