$(function(){
	var loadEnd = false;		
    var counter = 1;
    var dropload = $('#dropload').dropload({
    	scrollArea : window,
    	loadDownFn : function(me){
    		$.post(WST.U('home/userscores/pageQueryMob'), {page:counter,type:0}, function(data){
            	// console.log(data);
                var result = '';
                counter++;
                if(data.length > 0){
                	$.each(data,function(n,value){
                		result += '<div class="scoreDetail"><div class="dataSrc">【'+value.dataSrc+'】</div><div class="scoreNum">';
                		if(value.scoreType == 1){
                			result += '<span style="color:#cc3333;font-size: 25px;">+'+value.score+'积分</span>　';
                		}else{
                			result += '<span style="color:green;font-size: 25px;">-'+value.score+'积分</span>　';
                		}
                		result += value.createTime+'</div>';
                		result += '<div class="remark">'+value.dataRemarks+'</div></div>';
                	});                        	
                }else{
                	loadEnd = true;
                	// 锁定
                    me.lock();
                    // 无数据
                    me.noData();
                }
                $("#detail-list").append(result);
                me.resetload();
    		});
        }
    });
    if(!loadEnd){
        // 解锁
        dropload.unlock();
        dropload.noData(false);
    }else{
        // 锁定
        dropload.lock('down');
        dropload.noData();
    }
});