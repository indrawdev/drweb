Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.form.NumericField',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();

	var required = '<span style="color:red;font-weight:bold" data-qtip="Field ini wajib diisi">*</span>';

	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
	}

	var txtTgl = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 'Tanggal',
		format: 'd-m-Y',
		id: 'txtTgl',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -10),
		name: 'txtTgl',
		value: new Date(),
		xtype: 'datefield'
	};

	var txtTgl2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		editable: true,
		fieldLabel: 's/d',
		format: 'd-m-Y',
		id: 'txtTgl2',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -10),
		name: 'txtTgl2',
		value: new Date(),
		xtype: 'datefield'
	};

	Ext.define('DataGrid', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_reg', type: 'string'},
			{name: 'fd_tgl_periksa', type: 'string'},
			{name: 'fs_nm_pasien', type: 'string'},
			{name: 'fs_alamat', type: 'string'},
			{name: 'fn_biaya', type: 'string'},
			{name: 'fn_obat', type: 'string'},
			{name: 'fn_total', type: 'string'}
		]
	});

	var grupGridDetil = Ext.create('Ext.data.Store', {
		autoLoad: false,
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
			url: 'infopendapatan/GridDetil'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Y-m-d'),
					'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Y-m-d')
				});
			}
		}
	});

	var gridDetil = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 408,
		sortableColumns: false,
		store: grupGridDetil,
		columns: [{
			width: 45,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_reg',
			flex: 0.9,
			menuDisabled: true,
			text: 'Registrasi'
		},{
			dataIndex: 'fd_tgl_periksa',
			flex: 0.8,
			menuDisabled: true,
			text: 'Tanggal'
		},{
			dataIndex: 'fs_nm_pasien',
			flex: 2,
			menuDisabled: true,
			text: 'Nama'
		},{
			dataIndex: 'fs_alamat',
			flex: 2,
			menuDisabled: true,
			text: 'Alamat'
		},{
			align: 'right',
			dataIndex: 'fn_biaya',
			flex: 1,
			format: '0,000',
			menuDisabled: true,
			text: 'Biaya',
			xtype: 'numbercolumn'
		},{
			align: 'right',
			dataIndex: 'fn_obat',
			flex: 1,
			format: '0,000',
			menuDisabled: true,
			text: 'Obat',
			xtype: 'numbercolumn'
		},{
			align: 'right',
			dataIndex: 'fn_total',
			flex: 1,
			format: '0,000',
			menuDisabled: true,
			text: 'Total',
			xtype: 'numbercolumn'
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

	function fnPrint() {
		// Ext.Ajax.on('beforerequest', vMask.show, vMask);
		// Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		// Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'infopendapatan/PrintInfo',
			params: {
				'fd_tgl': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Y-m-d'),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtTgl2').getValue(), 'Y-m-d')
			},
			success: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				/*
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: xText.hasil,
					title: 'DokterPraktek'
				});*/
				
				vMask.show();
				
				if (xText.sukses === true) {
					var xfile = trim(xText.nmfile);
					var xfilexls = trim(xText.nmfilexls);
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: true,
						height: 500,
						layout: 'fit',
						title: 'Info Pendapatan',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							anchor: '95%',
							href: xfilexls,
							hrefTarget: '_blank',
							iconCls: 'icon-save',
							text: 'Download Excel',
							xtype: 'button'
						},{
							text: 'Keluar',
							handler: function() {
								vMask.hide();
								winpdf.hide();
							}
						}]
					}).show();
				}
				else {
					vMask.hide();
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: xText.hasil,
						title: 'DokterPraktek'
					});				
				}
			},
			failure: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Print Gagal, Koneksi Gagal!!',
					title: 'DokterPraktek'
				});
			}
		});
	}

	function fnReset() {
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
		grupGridDetil.removeAll();
	}

	var frmInfo = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Informasi Pendapatan',
		width: 900,
		items: [{
			fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 50,
			msgTarget: 'side'
			},
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 2,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTgl
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTgl2
					]
				},{
					flex: 2,
					layout: 'anchor',
					xtype: 'container'
				}]
			}]
		},
			gridDetil
		],
		buttons: [{
			iconCls: 'icon-preview',
			text: 'Proses',
			handler: function() {
				if (this.up('form').getForm().isValid()) {
					grupGridDetil.load();
				}
			}
		},{
			iconCls: 'icon-print',
			text: 'Print',
			handler: function() {
				if (this.up('form').getForm().isValid()) {
					grupGridDetil.load();
					vMask.show();
					fnPrint();
				}
			}
		},{
			iconCls: 'icon-reset',
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Silakan tunggu...',
		target: frmInfo
	});

	frmInfo.render(Ext.getBody());
	Ext.get('loading').destroy();
});