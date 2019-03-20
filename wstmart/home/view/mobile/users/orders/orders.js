function cancel(orderId,type){
	var load = layer.open({type: 2,content: '请稍后...'});
	$.post(WST.U('home/orders/toCancel'),{id:orderId},function(data){
		layer.close(load);
		var w = layer.open({
				content:data,
				btn:['提交','关闭'],
				yes:function(index){
					var reason = $.trim($('#reason').val());
					load = layer.open({type: 2,content: '请稍后...'});
					$.post(WST.U('home/orders/cancellation'),{id:orderId,reason:reason},function(res){
						layer.close(w);
						layer.close(load);
						if(res.status==1){
							layer.open({style:'width:60%;font-size:25px;',content:res.msg});
							if(type==0){
								setTimeout(function(){
									location.href=WST.U('home/orders/waitPay');
								},800);								
							}else{
								setTimeout(function(){
									location.href=WST.U('home/orders/waitReceive');
								},800);									
							}
						}else{
							layer.open({style:'width:60%;font-size:25px;',content:res.msg,time:1});
						}
					});
				}
		});
	});
}

function toReceive(id){
	var w = layer.open({
		style:'font-size:25px;',
		content:'您确定已收货么？',
		btn:['确定','取消'],
		yes:function(index){
			var load = layer.open({type: 2,content: '请稍后...'});
			$.post(WST.U('home/orders/receive'),{id:id},function(data){
				if(data.status>0){
					layer.open({style:'width:60%;font-size:25px;',content:data.msg});
					setTimeout(function(){
						location.href=WST.U('home/orders/waitReceive');
					},800);
				}else{
					layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
				}
			});
		}
	});
}

function changeRejectType(v){
	if(v==10000){
		$('#rejectTr').show();
	}else{
		$('#rejectTr').hide();
	}
}

function toReject(id){
	var load = layer.open({type: 2,content: '请稍后...'});
	$.post(WST.U('home/orders/toReject'),{id:id},function(data){
		layer.close(load);
		var w = layer.open({
				content:data,
				btn:['提交','关闭'],
				yes:function(index){
					var params = {};
		        	params.reason = $.trim($('#reason').val());
		        	params.content = $.trim($('#content').val());
		        	params.id = id;
		        	if(params.reason==10000 && params.content==''){						
		        		layer.open({style:'width:60%;font-size:25px;',content:'请输入拒收原因',time:1});
		        		return;
		        	}
					load = layer.open({type: 2,content: '请稍后...'});
					$.post(WST.U('home/orders/reject'),params,function(res){
						layer.close(w);
						layer.close(load);
						if(res.status==1){
							layer.open({style:'width:60%;font-size:25px;',content:res.msg});
							setTimeout(function(){
								location.href=WST.U('home/orders/waitReceive');
							},800);	
						}else{
							layer.open({style:'width:60%;font-size:25px;',content:res.msg,time:1});
						}
					});
				}
		});		
	});
}

//订单投诉图片上传
function comUploadPic(obj){
	if (!obj.files.length) return;
	var files = Array.prototype.slice.call(obj.files);
	if (files.length > 5) {
        layer.open({style:'width:60%;font-size:25px;',content:'只可同时上传5张图片',time:1});
        return;
    }    
    files.forEach(function (file, i) {    	
    	if (!/\/(?:jpeg|png|gif)/i.test(file.type)) return;
	 	var reader = new FileReader();
	 	reader.readAsDataURL(file);
	 	reader.onload = function () {
	 		createCanvas(this.result);
	 	}	 	
    });
}
function createCanvas(src){
	var load = layer.open({type: 2,content: '请稍后...'});
	var canvas = document.getElementById("comCanvas");
	var cxt = canvas.getContext('2d');
    var img = new Image();
    img.src = src;
    img.onload = function(){
    	canvas.width = 800;
    	var count =  800/img.width;
    	canvas.height = Math.round(img.height * count);
    	cxt.drawImage(img, 0, 0,800,canvas.height);
        $.ajax({
            url: WST.U('home/index/mobUploadPic'),
            type: "POST",
            data: {
                "uploadPic": canvas.toDataURL("image/jpeg", 0.9).split(',')[1],
                "dir":'complains'
            },
            success: function(data) {
                if(data.status==1){
                	$("#pics input").before('<div class="complain_pic" v="'+data.url+'"><img src="'+WST.conf.ROOT+'/'+data.url+'"/><span class="removeImg"><i class="fa fa-close"></i></span></div>');
                	if($(".complain_pic").length > 4){
                		$("#pics label").hide();
                	}else{
                		$("#pics label").show();
                	}
                	layer.close(load);
                }else{
                	layer.close(load);
                	layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
                }
            }
        })
    }
}
//订单投诉提交
function complainSubmit(){
	
	var params = {};
	params.orderId = $("#orderId").val();
	params.complainType = $("#complainType").val();
	params.complainContent = $("#complainContent").val();
	if(params.complainType==''){
		layer.open({style:'width:60%;font-size:25px;',content:'投诉类型不能为空',time:1});
		return;
	}
	if(params.complainContent==''){
		layer.open({style:'width:60%;font-size:25px;',content:'请输入投诉内容',time:1});
		return;
	}else if(params.complainContent.length > 60){
		layer.open({style:'width:60%;font-size:25px;',content:'投诉内容长度不得超过60',time:1});
		return;
	}
	var load = layer.open({type: 2,content: '请稍后...'});
	var img = [];
	$('.complain_pic').each(function(){
    	img.push($(this).attr('v'));
  	});
  	params.complainAnnex = img.join(',');
  	$.post(WST.U('home/orderComplains/saveComplain'),params,function(data,textStatus){
  		layer.close(load);
  		if(data.status=='1'){
            layer.open({style:'width:60%;font-size:25px;',content:'投诉已提交'});
            setTimeout(function(){
				location.href = WST.U('home/ordercomplains/index');
			},800);            
        }else{
            layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
        }
  	});
}

//订单评价页面图片提交
function appUploadPic(obj,k){
	if (!obj.files.length) return;
	var files = Array.prototype.slice.call(obj.files);
	if (files.length > 5) {
        layer.open({style:'width:60%;font-size:25px;',content:'只可同时上传5张图片',time:1});
        return;
    }
    files.forEach(function (file, i) {    	
    	if (!/\/(?:jpeg|png|gif)/i.test(file.type)) return;
	 	var reader = new FileReader();
	 	reader.readAsDataURL(file);
	 	reader.onload = function () {
	 		appCreateCanvas(this.result,k);
	 	}
    });
}
function appCreateCanvas(src,k){
	var load = layer.open({type: 2,content: '请稍后...'});
	var canvas = document.getElementById("appCanvas"+k);
	// console.log(canvas);return;
	var cxt = canvas.getContext('2d');
    var img = new Image();
    img.src = src;
    img.onload = function(){
    	canvas.width = 800;
    	var count =  800/img.width;
    	canvas.height = Math.round(img.height * count);
    	cxt.drawImage(img, 0, 0,800,canvas.height);
        $.ajax({
            url: WST.U('home/index/mobUploadPic'),
            type: "POST",
            data: {
                "uploadPic": canvas.toDataURL("image/jpeg", 0.9).split(',')[1],
                "dir":'appraises'
            },
            success: function(data) {
                if(data.status==1){
                	$("#appPics"+k+" input").before('<div class="appraise_pic appPic'+k+'" v="'+data.url+'"><img src="'+WST.conf.ROOT+'/'+data.url+'"/><span class="removeImg"><i class="fa fa-close"></i></span></div>');
                	if($("#appPics"+k+" .appraise_pic").length > 4){
                		$("#appPics"+k+" label").hide();
                	}else{
                		$("#appPics"+k+" label").show();
                	}
                	layer.close(load);
                }else{
                	layer.close(load);
                	layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
                }
            }
        })
    }
}

//评价提交
function appSubmit(k){
	var params = {};
	params.goodsId = $('#gid'+k).val();
	params.orderId = $('#oid'+k).val();
	params.goodsSpecId = $('#gsid'+k).val();
	params.goodsScore = $('#goodsScore'+k).val();
	params.timeScore = $('#timeScore'+k).val();
	params.serviceScore = $('#serviceScore'+k).val();
	params.content = $("#appContent"+k).val();
	var photo=[];
	var images = [];
	$('.appPic'+k).each(function(){
	  	var img = $(this).attr('v');
      	// 用于评价成功后的显示
      	photo.push(WST.conf.ROOT+'/'+img);
	  	images.push(img);
	});
	params.images = images.join(',');var img = [];
	$('.appPic'+k).each(function(){
    	img.push($(this).attr('v'));
  	});
  	params.complainAnnex = img.join(',');
	if(params.goodsScore == 0){
		layer.open({style:'width:60%;font-size:25px;',content:'商品评分不能为0',time:1});
	}
	if(params.timeScore == 0){
		layer.open({style:'width:60%;font-size:25px;',content:'时效评分不能为0',time:1});
	}
	if(params.serviceScore == 0){
		layer.open({style:'width:60%;font-size:25px;',content:'服务评分不能为0',time:1});
	}	
	if(params.content==''){
		layer.open({style:'width:60%;font-size:25px;',content:'评价内容不能为空',time:1});
	}else if(params.content.length > 120){
		layer.open({style:'width:60%;font-size:25px;',content:'评价内容不能超过120字符',time:1});
	}
	$.post(WST.U('home/goodsAppraises/add'),params,function(data,dataStatus){
		if(data.status==1){
			//商品评价
			var goodsApp = '<span>　商品评分</span>';
			for(var i=1;i<=params.goodsScore;i++){
              	goodsApp +='<span class="starLock"><i class="fa fa-star"></i></span>';
           	}
           	//时效评价
           	var timeApp = '<span>时效评分</span>';
           	for(var i=1;i<=params.timeScore;i++){
              	timeApp +='<span class="starLock"><i class="fa fa-star"></i></span>';
           	}
           	//服务评价
           	var serviceApp = '<span>服务评分</span>';
           	for(var i=1;i<=params.serviceScore;i++){
              	serviceApp +='<span class="starLock"><i class="fa fa-star"></i></span>';
           	}
           	//评价内容 params.content
           	//评价图片
           	var appPics = '';
           	var count = photo.length;
           	for(var m=0;m<count;m++){
           		appPics +='<div class="appraise_pic"><img src="'+photo[m]+'"></div>';
           	}
           	appPics +='<div style="clear:both"></div>';
           	$("#goodsApp"+k).html(goodsApp);
           	$("#appContent"+k).html(params.content);
           	$("#appPics"+k).html(appPics);
           	$("#timeS"+k).html(timeApp);
           	$("#serviceS"+k).html(serviceApp);
           	$("#submit"+k).remove();
           	layer.open({style:'width:60%;font-size:25px;',content:'评价成功',time:1});
		}else{
			layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
		}
	});
}

//申请退款窗口
function refund(id,realTotalMoney){
	var load = layer.open({type: 2,content: '请稍后...'});
	$.post(WST.U('home/orders/toRefund'),{id:id},function(data){
		layer.close(load);
		var w = layer.open({
			content:data,
			btn:['提交','关闭'],
			yes:function(index){
				var params = {};
	        	params.reason = $.trim($('#reason').val());
	        	params.content = $.trim($('#content').val());
	        	params.money = $.trim($('#money').val());
	        	params.id = id;
	        	if(params.money<=0){
	        		layer.open({style:'width:60%;font-size:25px;',content:'无效的退款金额',time:1});
	        		return;
	        	}else if(params.money > realTotalMoney){
	        		layer.open({style:'width:60%;font-size:25px;',content:'退款金额不得超过订单金额',time:1});
	        		return;
	        	}
	        	if(params.reason==10000 && params.content==''){
	        		layer.open({style:'width:60%;font-size:25px;',content:'请输入退款原因',time:1});
	        		return;
	        	}
	        	load = layer.open({type: 2,content: '请稍后...'});
	        	$.post(WST.U('home/orderrefunds/refund'),params,function(data){
	        		layer.close(load);
	        		if(data.status==1){
	        			layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
	        			setTimeout(function() {
	        				location.reload();
	        			}, 800);
	        		}else{
	        			layer.open({style:'width:60%;font-size:25px;',content:data.msg,time:1});
	        		}
	        	});
			}
		});
	});
}