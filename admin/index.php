<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너모듈 관리자패널을 구성한다.
 * 
 * @file /modules/banner/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.2.0
 * @modified 2019. 9. 24.
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
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("LOAD_DATA_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
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
						
						menu.addTitle(record.data.text);
						
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
			}),
			<?php if ($this->IM->getModule('member')->isAdmin() == true) { ?>
			new Ext.grid.Panel({
				id:"ModuleBannerAdminList",
				iconCls:"xi xi-crown",
				title:"관리자 관리",
				border:false,
				tbar:[
					new Ext.Button({
						id:"ModuleBannerAdminListAddButton",
						text:"관리자 추가",
						iconCls:"mi mi-plus",
						handler:function() {
							Member.search(function(member) {
								Ext.Msg.show({title:Admin.getText("alert/info"),msg:member.name+"님을 관리자로 추가하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
									if (button == "ok") {
										Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
										$.send(ENV.getProcessUrl("banner","@saveAdmin"),{midx:member.idx},function(result) {
											if (result.success == true) {
												Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
													Ext.getCmp("ModuleBannerAdminList").getStore().reload();
												}});
											} else {
												Ext.Msg.show({title:Admin.getText("alert/error"),msg:result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
											}
											return false;
										});
									}
								}});
							});
						}
					})
				],
				store:new Ext.data.JsonStore({
					proxy:{
						type:"ajax",
						simpleSortMode:true,
						url:ENV.getProcessUrl("banner","@getAdmins"),
						extraParams:{},
						reader:{type:"json"}
					},
					remoteSort:false,
					sorters:[{property:"name",direction:"ASC"}],
					autoLoad:true,
					pageSize:0,
					fields:[],
					listeners:{
						load:function(store,records,success,e) {
							if (success == false) {
								if (e.getError()) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("LOAD_DATA_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						}
					}
				}),
				columns:[{
					text:"이름",
					dataIndex:"name",
					sortable:true,
					width:100
				},{
					text:"이메일",
					dataIndex:"email",
					sortable:true,
					width:140,
					flex:1
				}],
				selModel:new Ext.selection.CheckboxModel(),
				bbar:[
					new Ext.Button({
						iconCls:"x-tbar-loading",
						handler:function() {
							Ext.getCmp("ModuleBannerAdminList").getStore().reload();
						}
					}),
					"->",
					{xtype:"tbtext",text:Admin.getText("text/grid_help")}
				],
				listeners:{
					itemdblclick:function(grid,record) {
						Banner.admin.add(record.data.midx);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.addTitle(record.data.name);
						
						menu.add({
							iconCls:"xi xi-trash",
							text:"관리자 삭제",
							handler:function() {
								Ext.Msg.show({title:Admin.getText("alert/info"),msg:"관리자를 삭제하시겠습니까?<br>해당 관리자는 더이상 관리할 수 없습니다.",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
									if (button == "ok") {
										Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
										$.send(ENV.getProcessUrl("banner","@deleteAdmin"),{midx:record.data.midx},function(result) {
											if (result.success == true) {
												Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
													Ext.getCmp("ModuleBannerAdminList").getStore().reload();
												}});
											} else {
												Ext.Msg.show({title:Admin.getText("alert/error"),msg:result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
											}
											return false;
										});
									}
								}});
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			}),
			<?php } ?>
			null
		]
	})
); });
</script>