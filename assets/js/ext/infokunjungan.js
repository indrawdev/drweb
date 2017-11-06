Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
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
			{name: 'fs_nm_pasien', type: 'string'},
			{name: 'fs_kd_reg', type: 'string'},
			{name: 'fs_umur', type: 'string'},
			{name: 'fs_anamnesa', type: 'string'},
			{name: 'fs_diagnosa', type: 'string'},
			{name: 'fs_tindakan', type: 'string'},
			{name: 'fs_ket', type: 'string'}
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
			url: 'infokunjungan/GridDetil'
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
			dataIndex: 'fs_nm_pasien',
			flex: 2,
			menuDisabled: true,
			text: 'Nama'
		},{
			dataIndex: 'fs_kd_reg',
			flex: 1.25,
			menuDisabled: true,
			text: 'No.Reg / RM'
		},{
			dataIndex: 'fs_umur',
			flex: 1.25,
			menuDisabled: true,
			text: 'Umur / Sex'
		},{
			dataIndex: 'fs_anamnesa',
			flex: 2,
			menuDisabled: true,
			text: 'Anamnesa'
		},{
			dataIndex: 'fs_diagnosa',
			flex: 2,
			menuDisabled: true,
			text: 'Diagnosa'
		},{
			dataIndex: 'fs_tindakan',
			flex: 2,
			menuDisabled: true,
			text: 'Terapi'
		},{
			dataIndex: 'fs_ket',
			flex: 2,
			menuDisabled: true,
			text: 'Obat'
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

	function fnReset() {
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtTgl2').setValue(new Date());
		grupGridDetil.removeAll();
	}

	var frmInfo = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Informasi Kunjungan',
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