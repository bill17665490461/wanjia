$(function(){
	var loadEnd = false;		
    var counter = 1;
    var dropload = $('#dropload').dropload({
    	scrollArea : window,
    	loadDownFn : function(me){
    		$.post(WST.U('home/cashdraws/pageQueryMob'), {page:counter,type:-1}, function(data){
            	// console.log(data);return;
                var result = '';
                counter++;
                if(data.length > 0){
                	$.each(data,function(n,value){
                		result += '<div class="cashDetail"><div class="no_status">';
                        result += '<div class="cashNo">提现单号：'+value.cashNo+'</div>';
                        if(value.cashSatus==1){
                            result += '<div class="cashStatus_one">已通过</div></div>';
                        }else{
                            result += '<div class="cashStatus_two">待处理</div></div>';
                        }
                        result += '<div class="bank_area">'+value.accTargetName+'　　'+value.accAreaName+'</div>';
                        result += '<div class="user_no">'+value.accUser+'　　'+value.accNo+'</div>';
                        result += '<div class="money_div">提现金额：<span class="money">￥'+value.money+'</span></div></div>';
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

//申请提现
function toDrawMoney(){
    if(isSetPayPwd==0){
        layer.open({style:'width:60%;font-size:25px',content:'请先设置支付密码',time:1});
        return;
    }
    var load = layer.open({type:2,content:'请稍后....'});
    $.post(WST.U('home/cashdraws/toEdit'),{},function(data,textStatus){
        layer.close(load);
        layer.open({
            content: data
            ,title: '申请提现'
            ,btn: ['确定', '取消']
            ,yes: function(index){            
                drawMoney();
            }
        });
    });
}

function drawMoney(){
    var params = WST.getParams('.j-ipt');
    if(params.accId==''){
        layer.open({style:'width:60%;font-size:25px',content:'提现账号不能为空',time:1});return;
    }
    if(params.money==''){
        layer.open({style:'width:60%;font-size:25px',content:'提现金额不能为空',time:1});return;
    }
    if(params.payPwd==''){
        layer.open({style:'width:60%;font-size:25px',content:'支付密码不能为空',time:1});return;
    }
    $.post(WST.U('home/cashdraws/drawMoney'),params,function(data,textStatus){
        var json = WST.toJson(data);
        if(json.status==1){
            layer.open({style:'width:60%;font-size:25px',content:json.msg,time:1});
            setTimeout(function(){
                location.reload();
            }, 800);
        }else{
            layer.open({style:'width:60%;font-size:25px',content:json.msg,time:1});
        }
    });
}

//删除银行卡
function delConfig(id){
    layer.open({
        content: '您确定要该提现账号么？'
        ,btn: ['删除', '取消']
        ,yes: function(index){
            $.post(WST.U('home/cashconfigs/del'),{id:id},function(data,textStatus){
                var json = WST.toJson(data);
                if(json.status==1){
                    layer.open({style:'width:60%;font-size:25px',content:json.msg,time:1});
                    setTimeout(function(){
                        location.reload();
                    }, 800);
                }else{
                    layer.open({style:'width:60%;font-size:25px',content:json.msg,time:1});
                }
            });
        }
    });
}
