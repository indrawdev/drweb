Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();

	var required = '<span style="color:red;font-weight:bold" data-qtip="Field ini wajib diisi">*</span>';

	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
	}

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Klik 2x pada record untuk memilih',
			target: view.el,
			trackMouse: true,
			listeners: {
				beforeshow: function (tip) {
					var tooltip = view.getRecord(tip.triggerElement).get('tooltip');
					if(tooltip){
						tip.update(tooltip);
					}
					else {
						tip.on('show', function() {
							Ext.defer(tip.hide, 5000, tip);
						}, tip, {single: true});
					}
				}
			}
		});
	}

	var grupUser = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_user','fs_nm_user','fs_password',
			'fs_kd_akses','fs_nm_akses'
		],
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'setuppetugas/KodePetugas'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_user': Ext.getCmp('cboUser').getValue(),
					'fs_nm_user': Ext.getCmp('cboUser').getValue()
				});
			}
		}
	});

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupUser,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupUser,
			items:[
				'-', {
				text: 'Keluar',
				handler: function() {
					winCari.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: 'Kode Petugas', dataIndex: 'fs_kd_user', menuDisabled: true, width: 110},
			{text: 'Nama Petugas', dataIndex: 'fs_nm_user', menuDisabled: true, width: 270},
			{text: 'Password', dataIndex: 'fs_password', hidden: true},
			{text: 'Kode Akses', dataIndex: 'fs_kd_akses', hidden: true},
			{text: 'Nama Akses', dataIndex: 'fs_nm_akses', menuDisabled: true, width: 100}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboUser').setValue(record.get('fs_kd_user'));
				Ext.getCmp('txtUser').setValue(record.get('fs_nm_user'));
				Ext.getCmp('txtPass').setValue(record.get('fs_password'));
				Ext.getCmp('txtPass2').setValue(record.get('fs_password'));
				Ext.getCmp('cboAkses').setValue(record.get('fs_kd_akses'));
				Ext.getCmp('txtAkses').setValue(record.get('fs_nm_akses'));
				
				winCari.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Pencarian...',
		items: [
			winGrid
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupUser.load();
				vMask.show();
			}
		}
	});

	var cboUser = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldLabel: 'Kode Petugas',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboUser',
		maxLength: 50,
		name: 'cboUser',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				fnReset1();
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari.show();
					winCari.center();
				}
			}
		}
	};

	var txtUser = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldLabel: 'Nama Petugas',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtUser',
		maxLength: 50,
		name: 'txtUser',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtPass = {	
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '63%',
		fieldLabel: 'Password',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtPass',
		inputType: 'password',
		maxLength: 50,
		name: 'txtPass',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtPass2 = {	
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '63%',
		fieldLabel: 'Konfirmasi Password',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtPass2',
		inputType: 'password',
		maxLength: 50,
		name: 'txtPass2',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupPaket = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_paket','fs_nm_paket'
		],
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'setuppetugas/KodePaket'
		}
	});

	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupPaket,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupPaket,
			items:[
				'-', {
				text: 'Keluar',
				handler: function() {
					winCari2.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: 'Kode Paket', dataIndex: 'fs_kd_paket', menuDisabled: true, width: 100},
			{text: 'Nama Paket', dataIndex: 'fs_nm_paket', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboAkses').setValue(record.get('fs_kd_paket'));
				Ext.getCmp('txtAkses').setValue(record.get('fs_nm_paket'));
				
				winCari2.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari2 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Pencarian...',
		items: [
			winGrid2
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupPaket.load();
				vMask.show();
			}
		}
	});

	var cboAkses = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		fieldLabel: 'Akses Petugas',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboAkses',
		maxLength: 50,
		name: 'cboAkses',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtAkses').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari2.show();
					winCari2.center();
				}
			}
		}
	};

	var txtAkses = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtAkses',
		name: 'txtAkses',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	function fnCekSimpan(){
		if (this.up('form').getForm().isValid()) {
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'setuppetugas/CekSimpan',
				params: {
					'fs_kd_user': Ext.getCmp('cboUser').getValue(),
					'fs_pass': Ext.getCmp('txtPass').getValue(),
					'fs_pass2': Ext.getCmp('txtPass2').getValue()
				},
				success: function(response, opts) {
					var xText = Ext.decode(response.responseText);
					
					if (xText.sukses === false) {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							message: xText.hasil,
							title: 'DokterPraktek'
						});
					}
					else {
						if (xText.sukses === true && xText.hasil == 'lanjut') {
							fnSimpan();
						}
						else {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.YESNO,
								closable: false,
								icon: Ext.MessageBox.QUESTION,
								message: xText.hasil,
								title: 'DokterPraktek',
								fn: function(btn) {
									if (btn == 'yes') {
										fnSimpan();
									}
								}
							});
						}
					}
				},
				failure: function(response, opts) {
					var xText = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: 'Simpan Gagal, Koneksi Gagal!!',
						title: 'DokterPraktek'
					});
					vMask.hide();
				}
			});
		}
	}

	function fnSimpan() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'setuppetugas/Simpan',
			params: {
				'fs_kd_user': Ext.getCmp('cboUser').getValue(),
				'fs_nm_user': Ext.getCmp('txtUser').getValue(),
				'fs_pass': Ext.getCmp('txtPass').getValue(),
				'fs_kd_akses': Ext.getCmp('cboAkses').getValue(),
				'fs_nm_akses': Ext.getCmp('txtAkses').getValue(),
				'fd_simpan': Ext.Date.format(new Date(), 'Y-m-d H:i:s')
			},
			success: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				
				if (xText.sukses === false) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: xText.hasil,
						title: 'DokterPraktek'
					});
				}
				else {
					fnSimpan2();
				}
			},
			failure: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Simpan Gagal, Koneksi Gagal!!',
					title: 'DokterPraktek'
				});
				vMask.hide();
			}
		});
	}

	function fnSimpan2() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'setuppetugas/Simpan2',
			params: {
				'fs_kd_user': Ext.getCmp('cboUser').getValue(),
				'fs_pass': Ext.getCmp('txtPass').getValue(),
				'fs_kd_akses': Ext.getCmp('cboAkses').getValue(),
				'fd_simpan': Ext.Date.format(new Date(), 'Y-m-d H:i:s')
			},
			success: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: xText.hasil,
					title: 'DokterPraktek'
				});
				fnReset();
			},
			failure: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Simpan Gagal, Koneksi Gagal!!',
					title: 'DokterPraktek'
				});
				vMask.hide();
			}
		});
	}

	function fnReset1() {
		Ext.getCmp('txtUser').setValue('');
		Ext.getCmp('txtPass').setValue('');
		Ext.getCmp('txtPass2').setValue('');
		Ext.getCmp('cboAkses').setValue('');
		Ext.getCmp('txtAkses').setValue('');
	}

	function fnReset() {
		Ext.getCmp('cboUser').setValue('');
		Ext.getCmp('txtUser').setValue('');
		Ext.getCmp('txtPass').setValue('');
		Ext.getCmp('txtPass2').setValue('');
		Ext.getCmp('cboAkses').setValue('');
		Ext.getCmp('txtAkses').setValue('');
	}

	var frmSetupPetugas = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Petugas',
		width: 550,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 120,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboUser
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container'
				}]
			},
				txtUser,
				txtPass,
				txtPass2,
			{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboAkses
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtAkses
					]
				}]
			}]
		}],
		buttons: [{
			iconCls: 'icon-save',
			text: 'Simpan',
			handler: fnCekSimpan
		},{
			iconCls: 'icon-reset',
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Silakan tunggu...',
		target: frmSetupPetugas
	});

	frmSetupPetugas.render(Ext.getBody());
	Ext.get('loading').destroy();
});