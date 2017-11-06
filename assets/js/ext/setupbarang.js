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

	var grupBarang = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_barang','fs_nm_barang','fs_kd_satuan',
			'fs_nm_satuan','fs_kd_dist','fs_nm_dist',
			'fs_ket','fb_aktif','fs_status'
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
			url: 'setupbarang/KodeBarang'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_barang': Ext.getCmp('cboBarang').getValue(),
					'fs_nm_barang': Ext.getCmp('cboBarang').getValue()
				});
			}
		}
	});

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupBarang,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupBarang,
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
			{text: 'Kode Barang', dataIndex: 'fs_kd_barang', menuDisabled: true, width: 100},
			{text: 'Nama Barang', dataIndex: 'fs_nm_barang', menuDisabled: true, width: 300},
			{text: 'Kode Satuan', dataIndex: 'fs_kd_satuan', hidden: true},
			{text: 'Nama Satuan', dataIndex: 'fs_nm_satuan', hidden: true},
			{text: 'Kode Dist', dataIndex: 'fs_kd_dist', hidden: true},
			{text: 'Nama Dist', dataIndex: 'fs_nm_dist', hidden: true},
			{text: 'Keterangan', dataIndex: 'fs_ket', hidden: true},
			{text: 'Aktif', dataIndex: 'fb_aktif', hidden: true},
			{text: 'Aktif', dataIndex: 'fs_status', menuDisabled: true, width: 80}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboBarang').setValue(record.get('fs_kd_barang'));
				Ext.getCmp('cekAktif').setValue(record.get('fb_aktif'));
				Ext.getCmp('txtBarang').setValue(record.get('fs_nm_barang'));
				Ext.getCmp('cboSatuan').setValue(record.get('fs_kd_satuan'));
				Ext.getCmp('txtSatuan').setValue(record.get('fs_nm_satuan'));
				Ext.getCmp('cboDist').setValue(record.get('fs_kd_dist'));
				Ext.getCmp('txtDist').setValue(record.get('fs_nm_dist'));
				Ext.getCmp('txtKet').setValue(record.get('fs_ket'));
				
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
				grupBarang.load();
				vMask.show();
			}
		}
	});

	var cboBarang = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldLabel: 'Kode Barang',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboBarang',
		maxLength: 25,
		name: 'cboBarang',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtBarang').setValue('');
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

	var txtBarang = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldLabel: 'Nama Barang',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtBarang',
		maxLength: 50,
		name: 'txtBarang',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupSatuan = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_satuan','fs_nm_satuan'
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
			url: 'setupsatuan/KodeSatuanAktif'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_satuan': Ext.getCmp('cboSatuan').getValue(),
					'fs_nm_satuan': Ext.getCmp('cboSatuan').getValue()
				});
			}
		}
	});

	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupSatuan,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupSatuan,
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
			{text: 'Kode Satuan', dataIndex: 'fs_kd_satuan', menuDisabled: true, width: 100},
			{text: 'Nama Satuan', dataIndex: 'fs_nm_satuan', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboSatuan').setValue(record.get('fs_kd_satuan'));
				Ext.getCmp('txtSatuan').setValue(record.get('fs_nm_satuan'));
				
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
				grupSatuan.load();
				vMask.show();
			}
		}
	});

	var cboSatuan = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		fieldLabel: 'Satuan',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboSatuan',
		maxLength: 25,
		name: 'cboSatuan',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtSatuan').setValue('');
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

	var txtSatuan = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtSatuan',
		name: 'txtSatuan',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupDist = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_dist','fs_nm_dist'
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
			url: 'setupdistributor/KodeDistributorAktif'
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

	var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
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
					winCari3.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: 'Kode Dist', dataIndex: 'fs_kd_dist', menuDisabled: true, width: 100},
			{text: 'Nama Dist', dataIndex: 'fs_nm_dist', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboDist').setValue(record.get('fs_kd_dist'));
				Ext.getCmp('txtDist').setValue(record.get('fs_nm_dist'));
				
				winCari3.hide();
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

	var winCari3 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Pencarian...',
		items: [
			winGrid3
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
		anchor: '98%',
		fieldLabel: 'Distributor',
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
					winCari3.show();
					winCari3.center();
				}
			}
		}
	};

	var txtDist = {
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtDist',
		name: 'txtDist',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtKet = {
		anchor: '100%',
		fieldLabel: 'Keterangan',
		fieldStyle: 'text-transform: uppercase;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtKet',
		maxLength: 100,
		name: 'txtKet',
		xtype: 'textareafield',
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
			{name: 'fs_kd_barang', type: 'string'},
			{name: 'fs_nm_barang', type: 'string'},
			{name: 'fs_nm_satuan', type: 'string'},
			{name: 'fs_nm_dist', type: 'string'},
			{name: 'fs_ket', type: 'string'},
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
			url: 'setupbarang/GridDetil'
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
			dataIndex: 'fs_kd_barang',
			menuDisabled: true,
			text: 'Kode Barang',
			width: 100
		},{
			dataIndex: 'fs_nm_barang',
			menuDisabled: true,
			text: 'Nama Barang',
			width: 250
		},{
			dataIndex: 'fs_nm_satuan',
			menuDisabled: true,
			text: 'Satuan',
			width: 100
		},{
			dataIndex: 'fs_nm_dist',
			menuDisabled: true,
			text: 'Distributor',
			width: 100
		},{
			dataIndex: 'fs_ket',
			menuDisabled: true,
			text: 'Keterangan',
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
			
			if (Ext.getCmp('cboDist').getValue() !== '' && Ext.getCmp('txtDist').getValue() === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Distributor tidak ada dalam daftar',
					title: 'DokterPraktek'
				});
				return;
			}
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'setupbarang/CekSimpan',
				params: {
					'fs_kd_barang': Ext.getCmp('cboBarang').getValue()
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
			url: 'setupbarang/Simpan',
			params: {
				'fs_kd_barang': Ext.getCmp('cboBarang').getValue(),
				'fb_aktif': Ext.getCmp('cekAktif').getValue(),
				'fs_nm_barang': Ext.getCmp('txtBarang').getValue(),
				'fs_kd_satuan': Ext.getCmp('cboSatuan').getValue(),
				'fs_kd_dist': Ext.getCmp('cboDist').getValue(),
				'fs_ket': Ext.getCmp('txtKet').getValue(),
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
				url: 'setupbarang/CekHapus',
				params: {
					'fs_kd_barang': Ext.getCmp('cboBarang').getValue()
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
			url: 'setupbarang/Hapus',
			params: {
				'fs_kd_barang': Ext.getCmp('cboBarang').getValue()
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
		Ext.getCmp('cboBarang').setValue('');
		Ext.getCmp('cekAktif').setValue('1');
		Ext.getCmp('txtBarang').setValue('');
		Ext.getCmp('cboSatuan').setValue('');
		Ext.getCmp('txtSatuan').setValue('');
		Ext.getCmp('cboDist').setValue('');
		Ext.getCmp('txtDist').setValue('');
		Ext.getCmp('txtKet').setValue('');
		
		grupGridDetil.load();
	}

	var frmSetupBarang = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Barang',
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
					labelWidth: 80,
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
								cboBarang
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
						txtBarang,
					{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboSatuan,
								cboDist
							]
						},{
							flex: 1.5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtSatuan,
								txtDist
							]
						}]
					},
						txtKet
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
				title: 'Daftar Barang',
				xtype: 'form',
				items: [
					gridDetil
				]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Silakan tunggu...',
		target: frmSetupBarang
	});

	frmSetupBarang.render(Ext.getBody());
	Ext.get('loading').destroy();
});