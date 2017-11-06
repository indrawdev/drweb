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
			'fs_kd_user','fs_nm_user','fs_password'
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
					'fs_kd_user': Ext.getCmp('cboPetugas').getValue(),
					'fs_nm_user': Ext.getCmp('cboPetugas').getValue()
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
			{text: 'Kode Petugas', dataIndex: 'fs_kd_user', menuDisabled: true, width: 150},
			{text: 'Nama Petugas', dataIndex: 'fs_nm_user', menuDisabled: true, width: 330}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboPetugas').setValue(record.get('fs_kd_user'));
				Ext.getCmp('txtPetugas').setValue(record.get('fs_nm_user'));
				
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

	var cboPetugas = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '70%',
		fieldLabel: 'Kode Petugas',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboPetugas',
		maxLength: 50,
		name: 'cboPetugas',
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

	var txtPetugas = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldLabel: 'Nama Petugas',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtPetugas',
		maxLength: 50,
		name: 'txtPetugas',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtLama = {	
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '80%',
		fieldLabel: 'Password Lama',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtLama',
		inputType: 'password',
		maxLength: 50,
		name: 'txtLama',
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
		anchor: '80%',
		fieldLabel: 'Password Baru',
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
		anchor: '80%',
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

	function fnCekSimpan(){
		if (this.up('form').getForm().isValid()) {
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'setupgantipass/CekSimpan',
				params: {
					'fs_kd_user': Ext.getCmp('cboPetugas').getValue(),
					'fs_lama': Ext.getCmp('txtLama').getValue(),
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
			url: 'setupgantipass/Simpan',
			params: {
				'fs_kd_user': Ext.getCmp('cboPetugas').getValue(),
				'fs_pass': Ext.getCmp('txtPass').getValue(),
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
			url: 'setupgantipass/Simpan2',
			params: {
				'fs_kd_user': Ext.getCmp('cboPetugas').getValue(),
				'fs_pass': Ext.getCmp('txtPass').getValue(),
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
		Ext.getCmp('txtPetugas').setValue('');
		Ext.getCmp('txtLama').setValue('');
		Ext.getCmp('txtPass').setValue('');
		Ext.getCmp('txtPass2').setValue('');
	}

	function fnReset() {
		Ext.getCmp('cboPetugas').setValue('');
		Ext.getCmp('txtPetugas').setValue('');
		Ext.getCmp('txtLama').setValue('');
		Ext.getCmp('txtPass').setValue('');
		Ext.getCmp('txtPass2').setValue('');
	}

	var frmSetupGantiPass = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Ganti Password',
		width: 550,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 120,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px 55px;',
			xtype: 'fieldset',
			items: [
				cboPetugas,
				txtPetugas,
				txtLama,
				txtPass,
				txtPass2
			]
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
		target: frmSetupGantiPass
	});

	frmSetupGantiPass.render(Ext.getBody());
	Ext.get('loading').destroy();
});