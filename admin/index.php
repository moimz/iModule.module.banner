<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너모듈 관리자패널을 구성한다.
 * 
 * @file /modules/banner/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;
?>
<script>
Ext.onReady(function () { Ext.getCmp("iModuleAdminPanel").add(
	new Ext.TabPanel({
		id:"ModuleBanner",
		border:false,
		tabPosition:"bottom",
		activeTab:0,
		items:[
			new Ext.grid.Panel({
				id:"ModuleBannerItemList",
				title:Banner.getText("admin/item/title"),
				border:false,
				tbar:[
					new Ext.form.ComboBox({
						id:"ModuleBannerSelect",
						store:new Ext.data.JsonStore({
							proxy:{
								type:"ajax",
								url:ENV.getProcessUrl("banner","@getBanners"),
								extraParams:{is_all:"true"},
								reader:{type:"json"}
							},
							autoLoad:true,
							remoteSort:false,
							sorters:[{property:"sort",direction:"ASC"}],
							fields:["bid","title",{name:"sort",type:"int"}]
						}),
						width:140,
						editable:false,
						displayField:"title",
						valueField:"bid",
						value:"",
						listeners:{
							change:function(form,value) {
								Ext.getCmp("ModuleBannerItemList").getStore().getProxy().setExtraParam("bid",value);
								Ext.getCmp("ModuleBannerItemList").getStore().reload();
							}
						}
					}),
					new Ext.Button({
						iconCls:"fa fa-cog",
						handler:function() {
							Banner.manager.window();
						}
					}),
					"-",
					new Ext.Button({
						text:Banner.getText("admin/item/add"),
						iconCls:"xi xi-coupon",
						handler:function() {
							Banner.item.add();
						}
					}),
					new Ext.Button({
						text:"선택 배너삭제",
						iconCls:"mi mi-trash",
						handler:function() {
							Banner.item.delete();
						}
					})
				],
				store:new Ext.data.JsonStore({
					proxy:{
						type:"ajax",
						simpleSortMode:true,
						url:ENV.getProcessUrl("banner","@getItems"),
						extraParams:{gidx:"0"},
						reader:{type:"json"}
					},
					remoteSort:true,
					sorters:[{property:"reg_date",direction:"DESC"}],
					autoLoad:true,
					pageSize:50,
					fields:["idx","group_title","text","url","target","permission","sort","reg_date"],
					listeners:{
						load:function(store,records,success,e) {
							if (success == false) {
								if (e.getError()) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("LOAD_DATA_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
								}
							}
						}
					}
				}),
				columns:[{
					text:Banner.getText("admin/item/columns/bid"),
					width:150,
					dataIndex:"banner_title",
					sortable:true
				},{
					text:Banner.getText("admin/item/columns/title"),
					width:200,
					dataIndex:"text",
					sortable:true,
					renderer:function(value,p,record) {
						if (record.data.image != null) {
							p.style = "padding-left:90px; background:url("+record.data.image.thumbnail+") no-repeat 10px 50%; background-size:70px 20px;";
						}
						return value;
					}
				},{
					text:Banner.getText("admin/item/columns/text"),
					minWidth:200,
					flex:1,
					dataIndex:"text",
					sortable:true
				},{
					text:Banner.getText("admin/item/columns/url"),
					width:200,
					dataIndex:"url",
					sortable:true
				},{
					text:Banner.getText("admin/item/columns/target"),
					width:160,
					dataIndex:"target",
					align:"center",
					renderer:function(value) {
						return Banner.getText("target/"+value);
					}
				},{
					text:Banner.getText("admin/item/columns/sort"),
					width:80,
					dataIndex:"sort",
					hideable:false,
					sortable:true,
					align:"right"
				},{
					text:Banner.getText("admin/item/columns/permission"),
					width:200,
					hideable:false,
					dataIndex:"permission"
				},{
					text:Banner.getText("admin/item/columns/reg_date"),
					width:160,
					hideable:false,
					dataIndex:"reg_date",
					renderer:function(value) {
						return moment(value * 1000).format("YYYY-MM-DD HH:mm");
					}
				}],
				selModel:new Ext.selection.CheckboxModel(),
				bbar:new Ext.PagingToolbar({
					store:null,
					displayInfo:false,
					items:[
						"->",
						{xtype:"tbtext",text:Admin.getText("text/grid_help")}
					],
					listeners:{
						beforerender:function(tool) {
							tool.bindStore(Ext.getCmp("ModuleBannerItemList").getStore());
						}
					}
				}),
				listeners:{
					itemdblclick:function(grid,record) {
						Banner.item.add(record.data.idx);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.add('<div class="x-menu-title">'+record.data.text+'</div>');
						
						menu.add({
							iconCls:"xi xi-form",
							text:"대상 URL로 이동",
							handler:function() {
								window.open(record.data.url)
							}
						});
						
						menu.add("-");
						
						menu.add({
							iconCls:"xi xi-form",
							text:"배너수정",
							handler:function() {
								Banner.item.add(record.data.idx);
							}
						});
						
						menu.add({
							iconCls:"xi xi-form",
							text:"배너삭제",
							handler:function() {
								Banner.item.delete();
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			})
		]
	})
); });
</script>