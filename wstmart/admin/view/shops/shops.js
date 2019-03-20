var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/shops/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '店铺编号', name: 'shopSn',isSort: false},
	        { display: '店铺名称', name: 'shopName',isSort: false},
	        { display: '店主姓名', name: 'shopkeeper',isSort: false},
	        { display: '店主联系电话', name: 'telephone',isSort: false},
	        { display: '店主店铺地址', name: 'shopAddress',isSort: false},
	        { display: '店铺余额', name: 'shopMoney',isSort: false},
	        { display: '营业状态', name: 'shopAtive',isSort: false,render: function (rowdata, rowindex, value){
	        	return (rowdata['shopAtive']==1)?"营业中":"休息中";
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            if(WST.GRANT.DPGL_02)h += "<a href='javascript:toEdit(" + rowdata['shopId'] + ")'>修改</a> ";
	            if(WST.GRANT.DPGL_03)h += "<a href='javascript:toDel(" + rowdata['shopId'] + ")'>删除</a> ";
	            if(WST.GRANT.DPGL_04)h += "<a href='javascript:getForRecharge(" + rowdata['shopId'] + ")'>充值</a> ";
	            return h;
	        }}
        ]
    });
}
function initStopGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/shops/pageStopQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '店铺编号', name: 'shopSn',isSort: false},
	        { display: '店铺名称', name: 'shopName',isSort: false},
	        { display: '店主姓名', name: 'shopkeeper',isSort: false},
	        { display: '店主联系电话', name: 'telephone',isSort: false},
	        { display: '店主店铺地址', name: 'shopAddress',isSort: false},
	        { display: '所属公司', name: 'shopCompany',isSort: false},
	        { display: '营业状态', name: 'shopAtive',isSort: false,render: function (rowdata, rowindex, value){
	        	return (rowdata['shopAtive']==1)?"营业中":"休息中";
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:toEdit(" + rowdata['shopId'] + ")'>修改</a> ";
	            h += "<a href='javascript:toDel(" + rowdata['shopId'] + ")'>删除</a> "; 
	            return h;
	        }}
        ]
    });
}

function initStopTrial(){
    grid = $("#maingrid").ligerGrid({
        url:WST.U('admin/shops/pageStopTrial'),
        pageSize:WST.pageSize,
        pageSizeOptions:WST.pageSizeOptions,
        height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '店铺编号', name: 'shopSn',isSort: false},
            { display: '果园名称', name: 'shopName',isSort: false},
            { display: '果园主姓名', name: 'shopkeeper',isSort: false},
            { display: '联系电话', name: 'telephone',isSort: false},
            { display: '果园详细地址', name: 'shopAddress',isSort: false},
            
            { display: '审核状态', name: 'shopAtive',isSort: false,render: function (rowdata, rowindex, value){
                    return (rowdata['shopAtive']==1)?"待审核":"休息中";
                }},
            { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
                    var h = "";
                    h += "<a href='javascript:isTrial(" + rowdata['shopId'] + ")'>查看</a> ";
                    h += "<a href='javascript:toAdopt(" + rowdata['shopId'] + ")'>通过</a> ";
                    h += "<a href='javascript:toDel(" + rowdata['shopId'] + ")'>删除</a> ";
                    return h;
                }}
        ]
    });
}

function isTrial(id) {
    location.href=WST.U('admin/shops/isTrial','id='+id);
}

function toAdopt(id){
    var box = WST.confirm({content:"您确定要让该店铺申请通过吗?",yes:function(){
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/shops/toAdopt'),{id:id},function(data,textStatus){
                layer.close(loading);
                var json = WST.toAdminJson(data);
                if(json.status=='1'){
                    WST.msg("操作成功",{icon:1});
                    layer.close(box);
                    grid.reload();
                }else{
                    WST.msg(json.msg,{icon:2});
                }
            });
        }});
}
function initEdit(opts){
	WST.upload({
	  	  pick:'#shopImgPicker',
	  	  formData: {dir:'shops'},
	  	  accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
	  	  callback:function(f){
	  		  var json = WST.toAdminJson(f);
	  		  if(json.status==1){
	  			$('#uploadMsg').empty().hide();
	            $('#preview').attr('src',WST.conf.ROOT+"/"+json.savePath+json.thumb);
	            $('#shopImg').val(json.savePath+json.name);
	            $('#editFrom').validator('hideMsg', '#shopImg');
	  		  }
		  },
		  progress:function(rate){
		      $('#uploadMsg').show().html('已上传'+rate+"%");
		  }
	});
	initTime('#serviceStartTime',opts.serviceStartTime);
	initTime('#serviceEndTime',opts.serviceEndTime);
	if($('#shopId').val()>0){
		var areaIdPath = opts.areaIdPath.split("_");
    	$('#area_0').val(areaIdPath[0]);
    	var aopts = {id:'area_0',val:areaIdPath[0],childIds:areaIdPath,className:'j-areas',isRequire:true}
		WST.ITSetAreas(aopts);
		if(opts.bankAreaIdPath!=''){
			var areaIdPath = opts.bankAreaIdPath.split("_");
    	    $('#barea_0').val(areaIdPath[0]);
    	    var aopts = {id:'barea_0',val:areaIdPath[0],childIds:areaIdPath,className:'j-bareas',isRequire:true}
		    WST.ITSetAreas(aopts);
		}
		
	}
	
}
function toEdit(id){
	location.href=WST.U('admin/shops/toEdit','id='+id);
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该店铺吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/shops/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           		            grid.reload();
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function getForRecharge(id){
    var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/shops/get'),{id:id},function(data,textStatus){
	    layer.close(loading);
	    var json = WST.toAdminJson(data);
	    if(json.shopId){
	        $('#shopId').val(json.shopId);
	        $('#account').html(json.shopName);
	        toRecharge(json.shopId);
	    }else{
	       	WST.msg(json.msg,{icon:2});
	    }
    });
}
function toRecharge(id){
    var box = WST.open({title:'编辑',type:1,content:$('#rechargeBox'),area: ['400px', '200px'],btn:['确定','取消'],yes:function(){
        var params = {};
        params.shopId = id;
        params.money = $.trim($('#money').val());
        params.payType = 0;
        if(params.money==''){
          WST.msg('无效的充值金额',{icon:2});
          return;
        }
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('admin/shops/recharge'), params, function(data,textStatus){
          // console.log(data);return;
            layer.close(loading);
            var json = WST.toAdminJson(data);
            if(json.status=='1'){
              WST.msg("操作成功",{icon:1});
              $('#rechargeForm')[0].reset();
              layer.close(box);
              grid.reload();
            }else{
              WST.msg(json.msg,{icon:2});
            }
        });
    },cancel:function(){$('#rechargeForm')[0].reset();},end:function(){$('#rechargeForm')[0].reset();}});
}
function checkLoginKey(obj){
	if($.trim(obj.value)=='')return;
	var params = {key:obj.value,userId:0};
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/users/checkLoginKey'),params,function(data,textStatus){
    	layer.close(loading);
    	var json = WST.toAdminJson(data);
    	if(json.status!='1'){
    		WST.msg(json.msg,{icon:2});
    		obj.value = '';
    	}
    });
}
function save(){
	$('#editFrom').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			params.areaId = WST.ITGetAreaVal('j-areas');
			params.bankAreaId = WST.ITGetAreaVal('j-bareas');
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('admin/shops/'+((params.shopId==0)?"add":"edit")),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toAdminJson(data);
		    	if(json.status=='1'){
		    		WST.msg("操作成功",{icon:1});
		    		location.href=WST.U('admin/shops/index');
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}
function initTime($id,val){
	var html = [],t0,t1;
	var str = val.split(':');
	for(var i=0;i<24;i++){
		t0 = (val.indexOf(':00')>-1 && (parseInt(str[0],10)==i))?'selected':'';
		t1 = (val.indexOf(':30')>-1 && (parseInt(str[0],10)==i))?'selected':'';
		html.push('<option value="'+i+':00" '+t0+'>'+i+':00</option>');
		html.push('<option value="'+i+':30" '+t1+'>'+i+':30</option>');
	}
	$($id).append(html.join(''));
}
