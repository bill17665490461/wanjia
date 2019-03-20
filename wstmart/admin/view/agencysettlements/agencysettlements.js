var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/agencysettlements/pageQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '结算单号', name: 'settlementNo',Sort: false},
	        { display: '真实姓名', name: 'realName',Sort: false},
	        { display: '电话', name: 'telephone',Sort: false},
	        { display: '结算金额', name: 'settlementMoney',Sort: false,render: function (rowdata, rowindex, value){
	            return '¥'+value;
	        }},
	        { display: '结算佣金', name: 'commissionFee',Sort: false,render: function (rowdata, rowindex, value){
	            return '¥'+value;
	        }},
	        { display: '申请时间', name: 'createTime',Sort: false},
	        { display: '状态', name: 'settlementStatus',Sort: false,render: function (rowdata, rowindex, value){
	            return (rowdata['settlementStatus']==1)?"已结算":"未结算";
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:toView(" + rowdata['settlementId'] + ")'>查看</a>&nbsp;&nbsp;";
	            if(rowdata['settlementStatus']==0 && WST.GRANT.JSSQ_04)h += "<a href='javascript:toEdit(" + rowdata['settlementId'] + ")'>处理</a> ";
	            return h;
	        }}
        ]
    });
}
function toEdit(id){
	location.href=WST.U('admin/agencysettlements/toHandle','id='+id);
}
function toView(id){
	location.href=WST.U('admin/agencysettlements/toView','id='+id);
}
function loadGrid(){
	grid.set('url',WST.U('admin/agencysettlements/pageQuery','settlementNo='+$('#settlementNo').val()+"&settlementStatus="+$('#settlementStatus').val()+"&realName="+$('#realName').val()));
}

function save(){
	if(WST.confirm({content:'您确定提交该结算单吗？',yes:function(){
        var params = WST.getParams('.ipt');
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	    $.post(WST.U('admin/agencysettlements/handle'),params,function(data,textStatus){
	    	// console.log(data);return;
	    	layer.close(loading);
	    	var json = WST.toAdminJson(data);
	    	if(json.status=='1'){
	    		WST.msg("操作成功",{icon:1});
	    		location.href=WST.U('admin/agencysettlements/index');
	    	}else{
	    		WST.msg(json.msg,{icon:2});
	    	}
	    });
	}}));
}

function initGoodsGrid(id){
	$('#wst-tab-2').height(WST.pageHeight()-25);
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/agencysettlements/pageGoodsQuery','id='+id),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单号', name: 'orderNo',Sort: false},
	        { display: '商品名称', name: 'goodsName',Sort: false},
	        { display: '商品规格', name: 'goodsSpecNames',Sort: false,render: function (rowdata, rowindex, value){
	            if(WST.blank(value)!=''){
	            	value = value.split('@@_@@');
	                return value.join('，');
	            }
	        }},
	        { display: '商品价格', name: 'goodsPrice',Sort: false,render: function (rowdata, rowindex, value){
	            return '¥'+value;
	        }},
	        { display: '购买数量', name: 'goodsNum',Sort: false},
	        { display: '佣金比率', name: 'agencyRate',Sort: false}
        ]
    });
}
var flag = false;
function intView(id){
	var h = WST.pageHeight();
	$('.l-tab-content').height(h-30);
	$('.l-tab-content-item').height(h-30);
	$('.l-tab-content-item').css('overflow-y','auto');
	tab = $("#wst-tabs").ligerTab({
	         height: '99%',
	         changeHeightOnResize:true,
	         showSwitchInTab : false,
	         showSwitch: false,
	         onAfterSelectTabItem:function(n){
	           if(n=='wst-tab-2'){
	              if(!flag){
	                initGoodsGrid(id);
	                flag = true;
	              }
	           }
	         }
	});	
}
