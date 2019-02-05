/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너모듈 관리자 UI 이벤트를 처리한다.
 * 
 * @file /modules/banner/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2019. 2. 5.
 */
var Banner = {
	/**
	 * 배너관리
	 */
	manager:{
		window:function() {
			new Ext.Window({
				id:"ModuleBannerWindow",
				title:Banner.getText("admin/manager/title"),
				width:600,
				height:500,
				modal:true,
				border:false,
				layout:"fit",
				items:[
					new Ext.grid.Panel({
						id:"ModuleBannerList",
						border:false,
						tbar:[
							new Ext.Button({
								text:Banner.getText("admin/manager/add"),
								iconCls:"mi mi-plus",
								handler:function() {
									Banner.manager.add();
								}
							}),
							new Ext.Button({
								text:"선택 배너삭제",
								iconCls:"mi mi-trash",
								handler:function() {
									Banner.manager.delete();
								}
							})
						],
						store:new Ext.data.JsonStore({
							proxy:{
								type:"ajax",
								simpleSortMode:true,
								url:ENV.getProcessUrl("banner","@getBanners"),
								reader:{type:"json"}
							},
							remoteSort:false,
							sorters:[{property:"title",direction:"ASC"}],
							autoLoad:true,
							pageSize:0,
							fields:["idx","title",{name:"item",type:"int"}],
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
							text:Banner.getText("admin/manager/columns/bid"),
							width:120,
							dataIndex:"bid"
						},{
							text:Banner.getText("admin/manager/columns/title"),
							minWidth:200,
							flex:1,
							dataIndex:"title"
						},{
							text:Banner.getText("admin/manager/columns/item"),
							width:100,
							dataIndex:"item",
							align:"right"
						}],
						selModel:new Ext.selection.CheckboxModel(),
						bbar:[
							new Ext.Button({
								iconCls:"x-tbar-loading",
								handler:function() {
									Ext.getCmp("ModuleBannerList").getStore().reload();
								}
							}),
							"->",
							{xtype:"tbtext",text:Admin.getText("text/grid_help")}
						],
						listeners:{
							itemdblclick:function(grid,record) {
								Banner.manager.add(record.data.bid);
							},
							itemcontextmenu:function(grid,record,item,index,e) {
								var menu = new Ext.menu.Menu();
								
								menu.add('<div class="x-menu-title">'+record.data.title+'</div>');
								
								menu.add({
									iconCls:"xi xi-form",
									text:Banner.getText("admin/manager/modify"),
									handler:function() {
										Banner.manager.add(record.data.bid);
									}
								});
								
								menu.add({
									iconCls:"mi mi-trash",
									text:Banner.getText("admin/manager/delete"),
									handler:function() {
										Banner.manager.delete();
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
		 * 배너추가 / 수정
		 *
		 * @param int idx 그룹고유번호 (없을 경우 추가)
		 */
		add:function(bid) {
			new Ext.Window({
				id:"ModuleBannerAddWindow",
				title:(bid ? Banner.getText("admin/manager/modify") : Banner.getText("admin/manager/add")),
				width:400,
				modal:true,
				autoScroll:true,
				border:false,
				items:[
					new Ext.form.Panel({
						id:"ModuleBannerAddForm",
						border:false,
						bodyPadding:"10 10 5 10",
						fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
						items:[
							new Ext.form.Hidden({
								name:"oBid",
								value:(bid ? bid : null)
							}),
							new Ext.form.TextField({
								fieldLabel:Banner.getText("admin/manager/form/bid"),
								name:"bid"
							}),
							new Ext.form.TextField({
								fieldLabel:Banner.getText("admin/manager/form/title"),
								name:"title"
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
										Ext.getCmp("ModuleBannerList").getStore().reload();
										Ext.getCmp("ModuleBannerSelect").getStore().reload();
										Ext.getCmp("ModuleBannerItemList").getStore().reload();
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
						if (bid) {
							Ext.getCmp("ModuleBannerAddForm").getForm().load({
								url:ENV.getProcessUrl("banner","@getBanner"),
								params:{bid:bid},
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
									Ext.getCmp("ModuleBannerAddWindow").close();
								}
							});
						}
					}
				}
			}).show();
		},
		delete:function() {
			var selected = Ext.getCmp("ModuleBannerList").getSelectionModel().getSelection();
			if (selected.length == 0) {
				Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 배너를 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
				return;
			}
			
			var bids = [];
			for (var i=0, loop=selected.length;i<loop;i++) {
				bids.push(selected[i].get("bid"));
			}
			
			Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택하신 배너를 정말 삭제하시겠습니까?<br>배너에 포함된 모든 항목이 함께 삭제됩니다.",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
				if (button == "ok") {
					Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
					$.send(ENV.getProcessUrl("board","@deleteBanner"),{bid:bids.join(",")},function(result) {
						if (result.success == true) {
							Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
								Ext.getCmp("ModuleBannerList").getStore().reload();
								Ext.getCmp("ModuleBannerSelect").getStore().reload();
								Ext.getCmp("ModuleBannerItemList").getStore().reload();
							}});
						}
					});
				}
			}});
		}
	},
	item:{
		/**
		 * 배너추가 / 수정
		 *
		 * @param int idx 배너고유번호 (없을경우 추가)
		 */
		add:function(idx) {
			new Ext.Window({
				id:"ModuleBannerItemAddWindow",
				title:(idx ? Banner.getText("admin/item/modify") : Banner.getText("admin/item/add")),
				width:500,
				modal:true,
				autoScroll:true,
				border:false,
				items:[
					new Ext.form.Panel({
						id:"ModuleBannerItemAddForm",
						border:false,
						bodyPadding:"10 10 10 10",
						fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
						items:[
							new Ext.form.Hidden({
								name:"idx"
							}),
							new Ext.form.ComboBox({
								fieldLabel:Banner.getText("admin/item/form/bid"),
								name:"bid",
								labelClsExtra:"required",
								store:new Ext.data.JsonStore({
									proxy:{
										type:"ajax",
										url:ENV.getProcessUrl("banner","@getBanners"),
										reader:{type:"json"}
									},
									autoLoad:true,
									remoteSort:false,
									sorters:[{property:"sort",direction:"ASC"}],
									fields:["bid","title"]
								}),
								editable:false,
								displayField:"title",
								valueField:"bid",
								listeners:{
									render:function(form) {
										if (Ext.getCmp("ModuleBannerSelect").getValue() != "") {
											form.setValue(Ext.getCmp("ModuleBannerSelect").getValue());
										}
									}
								}
							}),
							new Ext.form.TextField({
								fieldLabel:Banner.getText("admin/item/form/title"),
								name:"title",
								allowBlank:true
							}),
							new Ext.form.FieldContainer({
								fieldLabel:Banner.getText("admin/item/form/title_color"),
								labelClsExtra:"required",
								layout:"hbox",
								items:[
									new Ext.ux.ColorField({
										name:"title_color",
										maxLength:7,
										width:110,
										preview:false,
										style:{marginRight:"5px"},
										value:"#000000",
										disabled:true,
										validator:function(value) {
											return value.search(/^#[0-9a-fA-F]{6}/) === 0;
										},
										listeners:{
											change:function(form,value) {
												if (form.isValid() == true) {
													var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(value);
													$("#ModuleBannerItemAddTitleColor-inputEl").css("backgroundColor","rgba("+parseInt(result[1],16)+","+parseInt(result[2],16)+","+parseInt(result[3],16)+",0.2)");
													$("#ModuleBannerItemAddTitleColor-inputEl").css("color",value);
												}
											}
										}
									}),
									new Ext.form.DisplayField({
										id:"ModuleBannerItemAddTitleColor",
										fieldStyle:{border:"1px solid #ccc",textAlign:"center"},
										width:30,
										value:"T"
									}),
									new Ext.form.DisplayField({
										flex:1
									}),
									new Ext.form.Checkbox({
										name:"title_color_default",
										boxLabel:Banner.getText("admin/item/form/default_color"),
										checked:true,
										listeners:{
											change:function(form,checked) {
												form.getForm().findField("title_color").setDisabled(checked);
											}
										}
									})
								]
							}),
							new Ext.form.TextArea({
								fieldLabel:Banner.getText("admin/item/form/text"),
								name:"text",
								allowBlank:true
							}),
							new Ext.form.FieldContainer({
								fieldLabel:Banner.getText("admin/item/form/text_color"),
								labelClsExtra:"required",
								layout:"hbox",
								items:[
									new Ext.ux.ColorField({
										name:"text_color",
										maxLength:7,
										width:110,
										preview:false,
										style:{marginRight:"5px"},
										value:"#000000",
										disabled:true,
										validator:function(value) {
											return value.search(/^#[0-9a-fA-F]{6}/) === 0;
										},
										listeners:{
											change:function(form,value) {
												if (form.isValid() == true) {
													var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(value);
													$("#ModuleBannerItemAddTextColor-inputEl").css("backgroundColor","rgba("+parseInt(result[1],16)+","+parseInt(result[2],16)+","+parseInt(result[3],16)+",0.2)");
													$("#ModuleBannerItemAddTextColor-inputEl").css("color",value);
												}
											}
										}
									}),
									new Ext.form.DisplayField({
										id:"ModuleBannerItemAddTextColor",
										fieldStyle:{border:"1px solid #ccc",textAlign:"center"},
										width:30,
										value:"T"
									}),
									new Ext.form.DisplayField({
										flex:1
									}),
									new Ext.form.Checkbox({
										name:"text_color_default",
										boxLabel:Banner.getText("admin/item/form/default_color"),
										checked:true,
										listeners:{
											change:function(form,checked) {
												form.getForm().findField("text_color").setDisabled(checked);
											}
										}
									})
								]
							}),
							new Ext.form.FileUploadField({
								fieldLabel:Banner.getText("admin/item/form/image"),
								name:"image",
								buttonText:"이미지찾기",
								allowBlank:true,
								clearOnSubmit:false,
								accept:"image/*",
								emptyText:Banner.getText("admin/item/form/image_help"),
							}),
							new Ext.form.Checkbox({
								boxLabel:Banner.getText("admin/item/form/image_delete"),
								name:"image_delete",
								hidden:true,
								style:{marginTop:"-10px",paddingLeft:"105px"}
							}),
							new Ext.form.TextField({
								fieldLabel:Banner.getText("admin/item/form/url"),
								name:"url",
								allowBlank:true,
								afterBodyEl:'<div class="x-form-help">'+Banner.getText("admin/item/form/url_help")+'</div>'
							}),
							new Ext.form.ComboBox({
								fieldLabel:Banner.getText("admin/item/form/target"),
								name:"target",
								store:new Ext.data.ArrayStore({
									fields:["display","value"],
									data:[[Banner.getText("target/_blank"),"_blank"],[Banner.getText("target/_self"),"_self"],[Banner.getText("target/_top"),"_top"]]
								}),
								displayField:"display",
								valueField:"value",
								value:"_self"
							}),
							new Ext.form.FieldContainer({
								fieldLabel:Banner.getText("admin/item/form/sort"),
								layout:"hbox",
								items:[
									new Ext.form.NumberField({
										name:"sort",
										width:100,
										value:0
									}),
									new Ext.form.DisplayField({
										style:{marginLeft:"5px"},
										value:'<div class="x-form-help">('+Banner.getText("admin/item/form/sort_help")+')</div>'
									})
								]
							}),
							Admin.permissionField(Banner.getText("admin/item/form/permission"),"permission","true",true)
						]
					})
				],
				buttons:[
					new Ext.Button({
						text:Admin.getText("button/confirm"),
						handler:function() {
							Ext.getCmp("ModuleBannerItemAddForm").getForm().submit({
								url:ENV.getProcessUrl("banner","@saveItem"),
								submitEmptyText:false,
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/saving"),
								success:function(form,action) {
									Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
										Ext.getCmp("ModuleBannerItemList").getStore().loadPage(1);
										Ext.getCmp("ModuleBannerItemAddWindow").close();
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
							Ext.getCmp("ModuleBannerItemAddWindow").close();
						}
					})
				],
				listeners:{
					show:function() {
						if (idx) {
							Ext.getCmp("ModuleBannerItemAddForm").getForm().load({
								url:ENV.getProcessUrl("banner","@getItem"),
								params:{idx:idx},
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/loading"),
								success:function(form,action) {
									if (action.result.data.image != null) {
										Ext.getCmp("ModuleBannerItemAddForm").getForm().findField("image_delete").setBoxLabel(action.result.data.image.name+ "("+iModule.getFileSize(action.result.data.image.size)+") 삭제");
										Ext.getCmp("ModuleBannerItemAddForm").getForm().findField("image_delete").show();
									}
								},
								failure:function(form,action) {
									if (action.result && action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
									Ext.getCmp("ModuleBannerAddWindow").close();
								}
							});
						}
					}
				}
			}).show();
		},
		delete:function() {
			var selected = Ext.getCmp("ModuleBannerItemList").getSelectionModel().getSelection();
			if (selected.length == 0) {
				Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 배너항목을 먼저 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
				return;
			}
			
			var idxes = [];
			for (var i=0, loop=selected.length;i<loop;i++) {
				idxes.push(selected[i].get("idx"));
			}
			
			Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 배너항목을 정말 삭제하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
				if (button == "ok") {
					Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
					$.send(ENV.getProcessUrl("banner","@deleteItem"),{idx:idxes.join(",")},function(result) {
						if (result.success == true) {
							Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 배너항목를 성공적으로 삭제하였습니다.",buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
								Ext.getCmp("ModuleBannerItemList").getStore().loadPage(1);
							}});
						}
					});
				}
			}});
		}
	}
};