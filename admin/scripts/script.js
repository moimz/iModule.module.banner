/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너모듈 관리자 UI 이벤트를 처리한다.
 * 
 * @file /modules/banner/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2018. 11. 22.
 */
var Banner = {
	/**
	 * 배너 그룹관리
	 */
	group:{
		/**
		 * 그룹관리 윈도우
		 */
		manager:function() {
			new Ext.Window({
				id:"ModuleBannerGroupWindow",
				title:Banner.getText("admin/group/title"),
				width:600,
				height:500,
				modal:true,
				border:false,
				layout:"fit",
				items:[
					new Ext.grid.Panel({
						id:"ModuleBannerGroupList",
						border:false,
						tbar:[
							new Ext.Button({
								text:Banner.getText("admin/group/add"),
								iconCls:"mi mi-add",
								handler:function() {
									Banner.group.add();
								}
							})
						],
						store:new Ext.data.JsonStore({
							proxy:{
								type:"ajax",
								simpleSortMode:true,
								url:ENV.getProcessUrl("banner","@getGroups"),
								reader:{type:"json"}
							},
							remoteSort:false,
							sorters:[{property:"title",direction:"ASC"}],
							autoLoad:true,
							pageSize:0,
							fields:["idx","title",{name:"banner",type:"int"}],
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
						width:"100%",
						columns:[{
							text:Banner.getText("admin/group/columns/idx"),
							width:60,
							align:"right",
							dataIndex:"idx"
						},{
							text:Banner.getText("admin/group/columns/title"),
							minWidth:200,
							flex:1,
							dataIndex:"title"
						},{
							text:Banner.getText("admin/group/columns/type"),
							width:100,
							dataIndex:"type",
							renderer:function(value) {
								return Banner.getText("type/"+value);
							}
						},{
							text:Banner.getText("admin/group/columns/banner"),
							width:100,
							dataIndex:"banner",
							align:"right",
							summaryType:"sum",
							renderer:function(value) {
								return Ext.util.Format.number(value,"0,000");
							}
						}],
						selModel:new Ext.selection.CheckboxModel(),
						bbar:[
							new Ext.Button({
								iconCls:"x-tbar-loading",
								handler:function() {
									Ext.getCmp("ModuleBannerGroupList").getStore().reload();
								}
							}),
							"->",
							{xtype:"tbtext",text:Admin.getText("text/grid_help")}
						],
						listeners:{
							itemdblclick:function(grid,record) {
								Banner.group.add(record.data.idx);
							},
							itemcontextmenu:function(grid,record,item,index,e) {
								var menu = new Ext.menu.Menu();
								
								menu.add('<div class="x-menu-title">'+record.data.title+'</div>');
								
								menu.add({
									iconCls:"fa fa-sitemmap",
									text:"그룹수정",
									handler:function() {
										Banner.group.add(record.data.type,record.data.idx);
									}
								});
								
								menu.add({
									iconCls:"fa fa-trash",
									text:"그룹삭제",
									handler:function() {
										Banner.group.delete(record.data.idx);
									}
								});
								
								e.stopEvent();
								menu.showAt(e.getXY());
							}
						}
					})
				]
			}).show();
		},
		/**
		 * 그룹추가 / 수정
		 *
		 * @param int idx 그룹고유번호 (없을 경우 추가)
		 */
		add:function(idx) {
			new Ext.Window({
				id:"ModuleBannerGroupAddWindow",
				title:(idx ? Banner.getText("admin/group/modify") : Banner.getText("admin/group/add")),
				width:400,
				modal:true,
				autoScroll:true,
				border:false,
				items:[
					new Ext.form.Panel({
						id:"ModuleBannerGroupAddForm",
						border:false,
						bodyPadding:"10 10 10 10",
						fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
						items:[
							new Ext.form.Hidden({
								name:"idx"
							}),
							new Ext.form.FieldSet({
								title:Banner.getText("admin/group/form/default_setting"),
								items:[
									new Ext.form.TextField({
										fieldLabel:Banner.getText("admin/group/form/title"),
										name:"title"
									})
								]
							}),
							new Ext.form.FieldSet({
								title:Banner.getText("admin/group/form/banner_setting"),
								items:[
									new Ext.form.ComboBox({
										fieldLabel:Banner.getText("admin/group/form/type"),
										name:"type",
										store:new Ext.data.ArrayStore({
											fields:["display","value"],
											data:[[Banner.getText("type/TEXT"),"TEXT"],[Banner.getText("type/TITLETEXT"),"TITLETEXT"],[Banner.getText("type/IMAGE"),"IMAGE"],[Banner.getText("type/IMAGETEXT"),"IMAGETEXT"]]
										}),
										displayField:"display",
										valueField:"value",
										value:"TEXT"
									})
								]
							})
						]
					})
				],
				buttons:[
					new Ext.Button({
						text:Admin.getText("button/confirm"),
						handler:function() {
							Ext.getCmp("ModuleBannerGroupAddForm").getForm().submit({
								url:ENV.getProcessUrl("banner","@saveGroup"),
								submitEmptyText:false,
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/saving"),
								success:function(form,action) {
									Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
										Ext.getCmp("ModuleBannerGroupList").getStore().reload();
										Ext.getCmp("ModuleBannerGroupSelect").getStore().reload();
										Ext.getCmp("ModuleBannerGroupAddWindow").close();
									}});
								},
								failure:function(form,action) {
									if (action.result) {
										if (action.result.message) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										} else {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_SAVE_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										}
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("INVALID_FORM_DATA"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
								}
							});
						}
					}),
					new Ext.Button({
						text:Admin.getText("button/cancel"),
						handler:function() {
							Ext.getCmp("ModuleBannerGroupAddWindow").close();
						}
					})
				],
				listeners:{
					show:function() {
						if (idx) {
							Ext.getCmp("ModuleBannerGroupAddForm").getForm().load({
								url:ENV.getProcessUrl("banner","@getGroup"),
								params:{idx:idx},
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/loading"),
								success:function(form,action) {
									
								},
								failure:function(form,action) {
									if (action.result && action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
									Ext.getCmp("ModuleBannerGroupAddWindow").close();
								}
							});
						}
					}
				}
			}).show();
		}
	},
	/**
	 * 배너추가 / 수정
	 *
	 * @param int idx 배너고유번호 (없을경우 추가)
	 */
	add:function(idx) {
		new Ext.Window({
			id:"ModuleBannerAddWindow",
			title:(idx ? Banner.getText("admin/banner/modify") : Banner.getText("admin/banner/add")),
			width:500,
			modal:true,
			autoScroll:true,
			border:false,
			items:[
				new Ext.form.Panel({
					id:"ModuleBannerAddForm",
					border:false,
					bodyPadding:"10 10 10 10",
					fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
					items:[
						new Ext.form.Hidden({
							name:"idx"
						}),
						new Ext.form.FieldSet({
							title:Banner.getText("admin/banner/form/default_setting"),
							items:[
								new Ext.form.ComboBox({
									fieldLabel:Banner.getText("admin/banner/form/group"),
									name:"gidx",
									store:new Ext.data.JsonStore({
										proxy:{
											type:"ajax",
											url:ENV.getProcessUrl("banner","@getGroups"),
											reader:{type:"json"}
										},
										autoLoad:true,
										remoteSort:false,
										sorters:[{property:"sort",direction:"ASC"}],
										fields:["idx","title","type",{name:"sort",type:"int"}]
									}),
									editable:false,
									displayField:"title",
									valueField:"idx",
									listeners:{
										render:function(form) {
											if (Ext.getCmp("ModuleBannerGroupSelect").getValue() != 0) {
												form.setValue(Ext.getCmp("ModuleBannerGroupSelect").getValue());
											}
										},
										change:function(form,value) {
											var type = form.getSelection().data.type;
											
											if (type.indexOf("TITLE") == -1) {
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("title").disable();
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("title").hide();
											} else {
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("title").enable();
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("title").show();
											}
											
											if (type.indexOf("TEXT") == -1) {
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").disable();
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").hide();
											} else {
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").enable();
												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").show();
											}
											
											if (type.indexOf("IMAGE") == -1) {
//												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").disable();
//												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").hide();
											} else {
//												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").enable();
//												Ext.getCmp("ModuleBannerAddForm").getForm().findField("text").show();
											}
										}
									}
								}),
								new Ext.form.FieldContainer({
									fieldLabel:"노출순서",
									layout:"hbox",
									items:[
										new Ext.form.NumberField({
											name:"sort",
											width:100,
											value:0
										}),
										new Ext.form.DisplayField({
											style:{marginLeft:"5px"},
											value:"(노출순서가 낮을수록 먼저 노출됩니다.)"
										})
									]
								}),
								Admin.permissionField("노출권한","permission","true",true)
							]
						}),
						new Ext.form.FieldSet({
							title:Banner.getText("admin/banner/form/banner_setting"),
							items:[
								new Ext.form.TextField({
									fieldLabel:"대상 URL",
									name:"url",
									afterBodyEl:'<div class="x-form-help">배너틀 클릭하였을 때 이동할 주소를 입력하세요.</div>'
								}),
								new Ext.form.ComboBox({
									fieldLabel:Banner.getText("admin/banner/form/target"),
									name:"target",
									store:new Ext.data.ArrayStore({
										fields:["display","value"],
										data:[[Banner.getText("target/_blank"),"_blank"],[Banner.getText("target/_self"),"_self"],[Banner.getText("target/_top"),"_top"]]
									}),
									displayField:"display",
									valueField:"value",
									value:"_self"
								}),
								new Ext.form.TextField({
									fieldLabel:"배너제목",
									name:"title",
									hidden:true,
									disabled:true
								}),
								new Ext.form.TextField({
									fieldLabel:"배너내용",
									name:"text",
									hidden:true,
									disabled:true
								})
							]
						})
					]
				})
			],
			buttons:[
				new Ext.Button({
					text:Admin.getText("button/confirm"),
					handler:function() {
						Ext.getCmp("ModuleBannerAddForm").getForm().submit({
							url:ENV.getProcessUrl("banner","@saveBanner"),
							submitEmptyText:false,
							waitTitle:Admin.getText("action/wait"),
							waitMsg:Admin.getText("action/saving"),
							success:function(form,action) {
								Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
									Ext.getCmp("ModuleBannerList").getStore().loadPage(1);
									Ext.getCmp("ModuleBannerAddWindow").close();
								}});
							},
							failure:function(form,action) {
								if (action.result) {
									if (action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_SAVE_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("INVALID_FORM_DATA"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						});
					}
				}),
				new Ext.Button({
					text:Admin.getText("button/cancel"),
					handler:function() {
						Ext.getCmp("ModuleBannerAddWindow").close();
					}
				})
			],
			listeners:{
				show:function() {
					if (idx) {
						Ext.getCmp("ModuleBannerAddForm").getForm().load({
							url:ENV.getProcessUrl("banner","@getBanner"),
							params:{idx:idx},
							waitTitle:Admin.getText("action/wait"),
							waitMsg:Admin.getText("action/loading"),
							success:function(form,action) {
								
							},
							failure:function(form,action) {
								if (action.result && action.result.message) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
								Ext.getCmp("ModuleBannerGroupAddWindow").close();
							}
						});
					}
				}
			}
		}).show();
	},
	delete:function() {
		var select = Ext.getCmp("ModuleBannerList").getSelectionModel().getSelection();
		if (select.length == 0) {
			Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 배너를 먼저 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
			return;
		}
		
		for (var i=0, loop=select.length;i<loop;i++) {
			select[i] = select[i].get("idx");
		}
		
		Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 배너 "+loop+"개를 정말 삭제하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
			if (button == "ok") {
				$.send(ENV.getProcessUrl("banner","@deleteBanner"),{idx:JSON.stringify(select)},function(result) {
					if (result.success == true) {
						Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 배너를 성공적으로 삭제하였습니다.",buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
							Ext.getCmp("ModuleBannerList").getStore().loadPage(1);
						}});
					}
				});
			}
		}});
	}
};