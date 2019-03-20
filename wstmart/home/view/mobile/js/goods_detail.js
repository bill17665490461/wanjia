

function openAddCart(goodsId,type){
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
	$.post(WST.U('home/goods/btmLayer'), {id:goodsId}, function(data){
	  	layer.open({
			content: data
			,btn: ['确定']
			,skin: 'footer'
			,yes: function(index){
		  		var goodsSpecId = 0;
				if(goodsInfo.isSpec==1){
					var specIds = [];
					$('.spec-selected').each(function(){
						specIds.push($(this).attr('data-val'));
					});
					if(specIds.length==0){
						layer.open({style:'width:60%;font-size:25px',content:'请选择要购买的商品',time:1});
					}
					specIds = specIds.sort(function(a,b){
						return a-b;
					});
					if(goodsInfo.sku[specIds.join(':')]){
						goodsSpecId = goodsInfo.sku[specIds.join(':')].id;
					}	
				}
				var buyNum = parseInt($('.currentNum').html());
				$.post(WST.U('home/carts/addCart'),{goodsId:goodsInfo.id,goodsSpecId:goodsSpecId,buyNum:buyNum,rnd:Math.random()},function(data,textStatus){
					if(data.status==1){
			    	 	layer.open({style:'with:60%;font-size:25px',content:data.msg,time:1});
			    	 	if(type==1){
			    	 		setTimeout(function(){
			    	 			location.href=WST.U('home/carts/index');
			    	 		},800);	
				    	}
				    }else{
				    	layer.open({style:'with:60%;font-size:25px',content:data.msg,time:1});
				    }
				});
			}
	  	});
	});
}

function changeSpec(obj){
	if($(obj).hasClass('spec-selected')){
		return;
	}else{
		$(obj).addClass('spec-selected');
		$(obj).siblings().removeClass('spec-selected');
		var arr = new Array();
		arr.push($(obj).attr('data-val'));
		$(obj).parents('.spec').siblings().find('.spec-selected').each(function(){
			arr.push($(this).attr('data-val'));
		});
		//对数组从新排序
		arr = arr.sort(function(a,b){
			return a-b;
		});
		var curSpec = arr.join(':');
		var saleSpec = goodsInfo.sku;
		// console.log(saleSpec[curSpec]['specStock']);
		$("#goodsStore").html(saleSpec[curSpec]['specStock']);
		$(".currentNum").attr('lockNum',saleSpec[curSpec]['specStock']);
		$(".add-goodsPrice").html("￥"+saleSpec[curSpec]['specPrice']);
		//判断添加数量是否超过库存
		var currentNum = parseInt($(".currentNum").html());
		if(currentNum > saleSpec[curSpec]['specStock']){
			$(".currentNum").html(saleSpec[curSpec]['specStock']);
		}else{
			$(".addNum").removeClass('locked');
		}
	}
}

function addNum(obj){
	if($(obj).hasClass('locked')){
		return
	}
	var currentNum = parseInt($(".currentNum").html());
	var lockNum = parseInt($(".currentNum").attr('lockNum'));
	if(currentNum < lockNum){
		$(".cutNum").removeClass('locked');
		$(".currentNum").html(currentNum+1);
	}else{
		$(obj).addClass('locked');
	}
}

function cutNum(obj){
	if($(obj).hasClass('locked')){
		return;
	}
	var currentNum = parseInt($(".currentNum").html());
	if(currentNum > 1){
		$(".addNum").removeClass('locked');
		$(".currentNum").html(currentNum-1);
	}else{
		$(obj).addClass('locked');
	}
}
//滚动加载
$(function(){
	var itemIndex = 0;
	var allLoadEnd = false;
	var picLoadEnd = false;
	var bestLoadEnd = false;
	var goodLoadEnd = false;
	var badLoadEnd = false;
	var allPage = 1;
	var picPage = 1;
	var bestPage = 1;
	var goodPage = 1;
	var badPage = 1;
	$('.appr_cate').on('click',function(){
		itemIndex = $(this).index();
		$(this).addClass('cate_checked').siblings('.appr_cate').removeClass('cate_checked');
        $('.appr_list').eq(itemIndex).show().siblings('.appr_list').hide();

        if(itemIndex == '0'){
            // 如果数据没有加载完
            if(!allLoadEnd){
                // 解锁
                dropload.unlock();
                dropload.noData(false);
            }else{
                // 锁定
                dropload.lock('down');
                dropload.noData();
            }
        // 如果选中菜单二
        }else if(itemIndex == '1'){
            if(!picLoadEnd){
                // 解锁
                dropload.unlock();
                dropload.noData(false);
            }else{
                // 锁定
                dropload.lock('down');
                dropload.noData();
            }
        }else if(itemIndex == '2'){
            if(!bestLoadEnd){
                // 解锁
                dropload.unlock();
                dropload.noData(false);
            }else{
                // 锁定
                dropload.lock('down');
                dropload.noData();
            }
        }else if(itemIndex == '3'){
            if(!goodLoadEnd){
                // 解锁
                dropload.unlock();
                dropload.noData(false);
            }else{
                // 锁定
                dropload.lock('down');
                dropload.noData();
            }
        }else if(itemIndex == '4'){
            if(!badLoadEnd){
                // 解锁
                dropload.unlock();
                dropload.noData(false);
            }else{
                // 锁定
                dropload.lock('down');
                dropload.noData();
            }
        }
        dropload.resetload();
	});
	//dropload
	var dropload = $('#appr_content').dropload({
        scrollArea : window,
        loadDownFn : function(me){
            // 加载菜单一的数据
            if(itemIndex == '0'){
            	$.post(WST.U('home/goodsappraises/getByIdMob'), {goodsId:goodsInfo.id,type:'all',page:allPage}, function(data){
            		var result = '';
                    allPage++;

                    if(data!=-1){
                    	$.each(data, function(n,value){
                    		result += '<div class="goodsApp"><div class="app_one">';
                    		if(value.userPhoto){
                    			result += '<img src="'+window.conf.ROOT+'/'+value.userPhoto+'">';
                    		}else{
                    			result += '<img src="'+window.conf.ROOT+'/'+window.conf.USER_LOGO+'">';
                    		}
                    		result += '<span class="loginName"> '+value.loginName+'</span>';
                    		result += '<span class="rankName"> '+value.rankName+'</span></div><div class="app_two">';
                    		result += '<span class="creatTime"> '+value.createTime+'</span>';
                    		result += '<span class="goodsSpecNames"> '+value.goodsSpecNames+'</span></div>';
                    		result += '<div class="app_third">'+value.content+'</div>';
                    		if(value.images!=''){
                    			result += '<div class="app_fourth">';
                    			$.each(value.image,function(k,img){
                    				result += '<div class="appImg"><img src="'+window.conf.ROOT+'/'+img+'"></div>';
                    			});
                    			result += '<div style="clear: both;"></div></div>';
                    		}
                    		result += '</div>';
                    	});
                    	$('#all_appr').append(result);
                        // 每次数据加载完，必须重置
                        me.resetload();
                    }else{
                    	allLoadEnd = true;
                    	// 锁定
                        me.lock();
                        // 无数据
                        $(".dropload-down").html('<div class="dropload-noData">暂无数据</div>');
                    }
            	});
            // 加载菜单二的数据
            }else if(itemIndex == '1'){
                $.post(WST.U('home/goodsappraises/getByIdMob'), {goodsId:goodsInfo.id,type:'pic',page:picPage}, function(data){
            		var result = '';
                    picPage++;

                    if(data!=-1){
                    	$.each(data, function(n,value){
                    		result += '<div class="goodsApp"><div class="app_one">';
                    		if(value.userPhoto){
                    			result += '<img src="'+window.conf.ROOT+'/'+value.userPhoto+'">';
                    		}else{
                    			result += '<img src="'+window.conf.ROOT+'/'+window.conf.USER_LOGO+'">';
                    		}
                    		result += '<span class="loginName"> '+value.loginName+'</span>';
                    		result += '<span class="rankName"> '+value.rankName+'</span></div><div class="app_two">';
                    		result += '<span class="creatTime"> '+value.createTime+'</span>';
                    		result += '<span class="goodsSpecNames"> '+value.goodsSpecNames+'</span></div>';
                    		result += '<div class="app_third">'+value.content+'</div>';
                    		if(value.images!=''){
                    			result += '<div class="app_fourth">';
                    			$.each(value.image,function(k,img){
                    				result += '<div class="appImg"><img src="'+window.conf.ROOT+'/'+img+'"></div>';
                    			});
                    			result += '<div style="clear: both;"></div></div>';
                    		}
                    		result += '</div>';
                    	});
                    	$('#pic_appr').append(result);
                        // 每次数据加载完，必须重置
                        me.resetload();
                    }else{
                    	picLoadEnd = true;
                    	// 锁定
                        me.lock();
                        // 无数据
                        $(".dropload-down").html('<div class="dropload-noData">暂无数据</div>');
                    }
            	});
            }else if(itemIndex == '2'){
                $.post(WST.U('home/goodsappraises/getByIdMob'), {goodsId:goodsInfo.id,type:'best',page:bestPage}, function(data){
            		var result = '';
                    bestPage++;

                    if(data!=-1){
                    	$.each(data, function(n,value){
                    		result += '<div class="goodsApp"><div class="app_one">';
                    		if(value.userPhoto){
                    			result += '<img src="'+window.conf.ROOT+'/'+value.userPhoto+'">';
                    		}else{
                    			result += '<img src="'+window.conf.ROOT+'/'+window.conf.USER_LOGO+'">';
                    		}
                    		result += '<span class="loginName"> '+value.loginName+'</span>';
                    		result += '<span class="rankName"> '+value.rankName+'</span></div><div class="app_two">';
                    		result += '<span class="creatTime"> '+value.createTime+'</span>';
                    		result += '<span class="goodsSpecNames"> '+value.goodsSpecNames+'</span></div>';
                    		result += '<div class="app_third">'+value.content+'</div>';
                    		if(value.images!=''){
                    			result += '<div class="app_fourth">';
                    			$.each(value.image,function(k,img){
                    				result += '<div class="appImg"><img src="'+window.conf.ROOT+'/'+img+'"></div>';
                    			});
                    			result += '<div style="clear: both;"></div></div>';
                    		}
                    		result += '</div>';
                    	});
                    	$('#best_appr').append(result);
                        // 每次数据加载完，必须重置
                        me.resetload();
                    }else{
                    	bestLoadEnd = true;
                    	// 锁定
                        me.lock();
                        // 无数据
                        $(".dropload-down").html('<div class="dropload-noData">暂无数据</div>');
                    }
            	});
            }else if(itemIndex == '3'){
                $.post(WST.U('home/goodsappraises/getByIdMob'), {goodsId:goodsInfo.id,type:'good',page:goodPage}, function(data){
            		var result = '';
                    goodPage++;

                    if(data!=-1){
                    	$.each(data, function(n,value){
                    		result += '<div class="goodsApp"><div class="app_one">';
                    		if(value.userPhoto){
                    			result += '<img src="'+window.conf.ROOT+'/'+value.userPhoto+'">';
                    		}else{
                    			result += '<img src="'+window.conf.ROOT+'/'+window.conf.USER_LOGO+'">';
                    		}
                    		result += '<span class="loginName"> '+value.loginName+'</span>';
                    		result += '<span class="rankName"> '+value.rankName+'</span></div><div class="app_two">';
                    		result += '<span class="creatTime"> '+value.createTime+'</span>';
                    		result += '<span class="goodsSpecNames"> '+value.goodsSpecNames+'</span></div>';
                    		result += '<div class="app_third">'+value.content+'</div>';
                    		if(value.images!=''){
                    			result += '<div class="app_fourth">';
                    			$.each(value.image,function(k,img){
                    				result += '<div class="appImg"><img src="'+window.conf.ROOT+'/'+img+'"></div>';
                    			});
                    			result += '<div style="clear: both;"></div></div>';
                    		}
                    		result += '</div>';
                    	});
                    	$('#good_appr').append(result);
                        // 每次数据加载完，必须重置
                        me.resetload();
                    }else{
                    	goodLoadEnd = true;
                    	// 锁定
                        me.lock();
                        // 无数据
                        $(".dropload-down").html('<div class="dropload-noData">暂无数据</div>');
                    }
            	});
            }else if(itemIndex == '4'){
                $.post(WST.U('home/goodsappraises/getByIdMob'), {goodsId:goodsInfo.id,type:'bad',page:badPage}, function(data){
            		var result = '';
                    badPage++;

                    if(data!=-1){
                    	$.each(data, function(n,value){
                    		result += '<div class="goodsApp"><div class="app_one">';
                    		if(value.userPhoto){
                    			result += '<img src="'+window.conf.ROOT+'/'+value.userPhoto+'">';
                    		}else{
                    			result += '<img src="'+window.conf.ROOT+'/'+window.conf.USER_LOGO+'">';
                    		}
                    		result += '<span class="loginName"> '+value.loginName+'</span>';
                    		result += '<span class="rankName"> '+value.rankName+'</span></div><div class="app_two">';
                    		result += '<span class="creatTime"> '+value.createTime+'</span>';
                    		result += '<span class="goodsSpecNames"> '+value.goodsSpecNames+'</span></div>';
                    		result += '<div class="app_third">'+value.content+'</div>';
                    		if(value.images!=''){
                    			result += '<div class="app_fourth">';
                    			$.each(value.image,function(k,img){
                    				result += '<div class="appImg"><img src="'+window.conf.ROOT+'/'+img+'"></div>';
                    			});
                    			result += '<div style="clear: both;"></div></div>';
                    		}
                    		result += '</div>';
                    	});
                    	$('#bad_appr').append(result);
                        // 每次数据加载完，必须重置
                        me.resetload();
                    }else{
                    	badLoadEnd = true;
                    	// 锁定
                        me.lock();
                        // 无数据
                        $(".dropload-down").html('<div class="dropload-noData">暂无数据</div>');
                    }
            	});
            }
        }
    }); 
});

