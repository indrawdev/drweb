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

	var grupDist = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_dist','fs_nm_dist','fs_alamat',
			'fs_kota','fs_tlp','fs_kontak',
			'fb_aktif','fs_status'
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
			url: 'setupdistributor/KodeDistributor'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_dist': Ext.getCmp('cboDist').getValue(),
					'fs_nm_dist': Ext.getCmp('cboDist').getValue()
				});
			}
		}
	});

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupDist,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupDist,
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
			{text: 'Kode Dist', dataIndex: 'fs_kd_dist', menuDisabled: true, width: 100},
			{text: 'Nama Dist', dataIndex: 'fs_nm_dist', menuDisabled: true, width: 300},
			{text: 'Alamat', dataIndex: 'fs_alamat', hidden: true},
			{text: 'Kota', dataIndex: 'fs_kota', hidden: true},
			{text: 'Telp', dataIndex: 'fs_tlp', hidden: true},
			{text: 'Contact Person', dataIndex: 'fs_kontak', hidden: true},
			{text: 'Aktif', dataIndex: 'fb_aktif', hidden: true},
			{text: 'Aktif', dataIndex: 'fs_status', menuDisabled: true, width: 80}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboDist').setValue(record.get('fs_kd_dist'));
				Ext.getCmp('cekAktif').setValue(record.get('fb_aktif'));
				Ext.getCmp('txtDist').setValue(record.get('fs_nm_dist'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				Ext.getCmp('txtKota').setValue(record.get('fs_kota'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				Ext.getCmp('txtKontak').setValue(record.get('fs_kontak'));
				
				winCari.hide();
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
				grupDist.load();
				vMask.show();
			}
		}
	});

	var cboDist = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldLabel: 'Kode Distributor',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboDist',
		maxLength: 25,
		name: 'cboDist',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtDist').setValue('');
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

	var txtDist = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldLabel: 'Nama Distributor',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtDist',
		maxLength: 50,
		name: 'txtDist',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtAlamat = {
		anchor: '100%',
		fieldLabel: 'Alamat',
		fieldStyle: 'text-transform: uppercase;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtAlamat',
		maxLength: 200,
		name: 'txtAlamat',
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtKota = {
		anchor: '100%',
		fieldLabel: 'Kota',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtKota',
		maxLength: 50,
		name: 'txtKota',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtTlp = {
		anchor: '100%',
		fieldLabel: 'Telp',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtTlp',
		maxLength: 50,
		name: 'txtTlp',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtKontak = {
		anchor: '100%',
		fieldLabel: 'Contact Person',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtKontak',
		maxLength: 50,
		name: 'txtKontak',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var cekAktif = {
		boxLabel: 'Aktif',
		checked: true,
		id: 'cekAktif',
		name: 'cekAktif',
		xtype: 'checkboxfield'
	};

	Ext.define('DataGrid', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_dist', type: 'string'},
			{name: 'fs_nm_dist', type: 'string'},
			{name: 'fs_alamat', type: 'string'},
			{name: 'fs_kota', type: 'string'},
			{name: 'fs_tlp', type: 'string'},
			{name: 'fs_kontak', type: 'string'},
			{name: 'fb_aktif', type: 'bool'}
		]
	});

	var grupGridDetil = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGrid',
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
			url: 'setupdistributor/GridDetil'
		}
	});

	var gridDetil = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		sortableColumns: false,
		store: grupGridDetil,
		columns: [{
			width: 45,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_dist',
			menuDisabled: true,
			text: 'Kode Dist',
			width: 100
		},{
			dataIndex: 'fs_nm_dist',
			menuDisabled: true,
			text: 'Nama Dist',
			width: 200
		},{
			dataIndex: 'fs_alamat',
			menuDisabled: true,
			text: 'Alamat',
			width: 100
		},{
			dataIndex: 'fs_kota',
			menuDisabled: true,
			text: 'Kota',
			width: 100
		},{
			dataIndex: 'fs_tlp',
			menuDisabled: true,
			text: 'Telp',
			width: 100
		},{
			dataIndex: 'fs_kontak',
			menuDisabled: true,
			text: 'Contact Person',
			width: 100
		},{
			align: 'center',
			dataIndex: 'fb_aktif',
			menuDisabled: true,
			stopSelection: false,
			text: 'Aktif',
			width: 55,
			xtype: 'checkcolumn'
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridDetil
		}),
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	function fnCekSimpan(){
		if (this.up('form').getForm().isValid()) {
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'setupdistributor/CekSimpan',
				params: {
					'fs_kd_dist': Ext.getCmp('cboDist').getValue()
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
			url: 'setupdistributor/Simpan',
			params: {
				'fs_kd_dist': Ext.getCmp('cboDist').getValue(),
				'fb_aktif': Ext.getCmp('cekAktif').getValue(),
				'fs_nm_dist': Ext.getCmp('txtDist').getValue(),
				'fs_alamat': Ext.getCmp('txtAlamat').getValue(),
				'fs_kota': Ext.getCmp('txtKota').getValue(),
				'fs_tlp': Ext.getCmp('txtTlp').getValue(),
				'fs_kontak': Ext.getCmp('txtKontak').getValue(),
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
				grupGridDetil.load();
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

	function fnCekHapus() {
		if (this.up('form').getForm().isValid()) {
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'setupdistributor/CekHapus',
				params: {
					'fs_kd_dist': Ext.getCmp('cboDist').getValue()
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
							fnHapus();
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
										fnHapus();
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

	function fnHapus() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'setupdistributor/Hapus',
			params: {
				'fs_kd_dist': Ext.getCmp('cboDist').getValue(),
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
				grupGridDetil.load();
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

	function fnReset() {
		Ext.getCmp('cboDist').setValue('');
		Ext.getCmp('cekAktif').setValue('1');
		Ext.getCmp('txtDist').setValue('');
		Ext.getCmp('txtAlamat').setValue('');
		Ext.getCmp('txtTlp').setValue('');
		Ext.getCmp('txtKota').setValue('');
		Ext.getCmp('txtKontak').setValue('');
		
		grupGridDetil.load();
	}

	var frmSetupDistributor = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Distributor',
		width: 550,
		items: [{
			activeTab: 0,
			bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
			border: false,
			plain: true,
			xtype: 'tabpanel',
			items: [{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Setup',
				xtype: 'form',
				items: [{
					fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 100,
					msgTarget: 'side'
					},
					style: 'padding: 5px;',
					xtype: 'fieldset',
					items: [{
						anchor: '90%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboDist
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekAktif
							]
						}]
					},
						txtDist,
						txtAlamat,
						txtKota,
						txtTlp,
						txtKontak
					]
				}],
				buttons: [{
					iconCls: 'icon-save',
					text: 'Simpan',
					handler: fnCekSimpan
				},{
					iconCls: 'icon-remove',
					text: 'Hapus',
					handler: fnCekHapus
				},{
					iconCls: 'icon-reset',
					text: 'Reset',
					handler: fnReset
				}]
			},{
				border: false,
				frame: false,
				title: 'Daftar Distributor',
				xtype: 'form',
				items: [
					gridDetil
				]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Silakan tunggu...',
		target: frmSetupDistributor
	});

	frmSetupDistributor.render(Ext.getBody());
	Ext.get('loading').destroy();
});