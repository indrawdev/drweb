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

	Ext.define('DataGrid', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_pesan', type: 'string'},
			{name: 'fs_kd_reg', type: 'string'},
			{name: 'fs_jam', type: 'string'},
			{name: 'fs_kd_mr', type: 'string'},
			{name: 'fs_nm_pasien', type: 'string'},
			{name: 'fs_alamat', type: 'string'},
			{name: 'fb_periksa', type: 'string'}
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
			url: 'infoantrian/GridDetil'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fd_tgl': Ext.Date.format(new Date(), 'Y-m-d')
				});
			}
		}
	});

	var gridDetil = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 457,
		sortableColumns: false,
		store: grupGridDetil,
		columns: [{
			width: 45,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_pesan',
			flex: 1,
			menuDisabled: true,
			text: 'Pendaftaran'
		},{
			dataIndex: 'fs_kd_reg',
			flex: 1,
			menuDisabled: true,
			text: 'Registrasi'
		},{
			dataIndex: 'fs_jam',
			flex: 0.7,
			menuDisabled: true,
			text: 'Jam'
		},{
			dataIndex: 'fs_kd_mr',
			flex: 1,
			menuDisabled: true,
			text: 'No.MR'
		},{
			dataIndex: 'fs_nm_pasien',
			flex: 2,
			menuDisabled: true,
			text: 'Nama Pasien'
		},{
			dataIndex: 'fs_alamat',
			flex: 3,
			menuDisabled: true,
			text: 'Alamat'
		},{
			dataIndex: 'fb_periksa',
			flex: 1,
			menuDisabled: true,
			text: 'Periksa'
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
		grupGridDetil.removeAll();
	}

	var frmInfo = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Informasi Antrian',
		width: 900,
		items: [
			gridDetil
		],
		buttons: [{
			iconCls: 'icon-reset',
			text: 'Refresh',
			handler: function() {
				if (this.up('form').getForm().isValid()) {
					grupGridDetil.load();
				}
			}
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Silakan tunggu...',
		target: frmInfo
	});

	frmInfo.render(Ext.getBody());
	Ext.get('loading').destroy();
});