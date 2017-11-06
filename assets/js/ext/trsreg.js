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

	function getAge(dateString) {
		var now = new Date();
		var today = new Date(now.getYear(),now.getMonth(),now.getDate());
		
		var yearNow = now.getYear();
		var monthNow = now.getMonth();
		var dateNow = now.getDate();
		
		var dob = new Date(dateString.substring(6,10),
				dateString.substring(0,2)-1,                   
				dateString.substring(3,5)                  
			);
		
		var yearDob = dob.getYear();
		var monthDob = dob.getMonth();
		var dateDob = dob.getDate();
		var age = {};
		var ageString = '';
		var yearString = '';
		var monthString = '';
		var dayString = '';
		var monthAge = 0;
		var dateAge = 0;
		
		yearAge = yearNow - yearDob;
		
		if (monthNow >= monthDob)
			monthAge = monthNow - monthDob;
		else {
			yearAge--;
			monthAge = 12 + monthNow -monthDob;
		}
		
		if (dateNow >= dateDob)
			dateAge = dateNow - dateDob;
		else {
			monthAge--;
			dateAge = 31 + dateNow - dateDob;
			
			if (monthAge < 0) {
				monthAge = 11;
				yearAge--;
			}
		}
		
		age = {
			years: yearAge,
			months: monthAge,
			days: dateAge
		};
		
		if ( age.years > 1 )
			yearString = ' years';
		else 
			yearString = ' year';
		if ( age.months> 1 )
			monthString = ' months';
		else 
			monthString = ' month';
		if ( age.days > 1 )
			dayString = ' days';
		else
			dayString = ' day';
		
		
		if ( (age.years > 0) && (age.months > 0) && (age.days > 0) )
			ageString = age.years + yearString + ', ' + age.months + monthString + ', and ' + age.days + dayString + ' old.';
		else if ( (age.years === 0) && (age.months === 0) && (age.days > 0) )
			ageString = 'Only ' + age.days + dayString + ' old!';
		else if ( (age.years > 0) && (age.months === 0) && (age.days === 0) )
			ageString = age.years + yearString + ' old. Happy Birthday!!';
		else if ( (age.years > 0) && (age.months > 0) && (age.days === 0) )
			ageString = age.years + yearString + ' and ' + age.months + monthString + ' old.';
		else if ( (age.years === 0) && (age.months > 0) && (age.days > 0) )
			ageString = age.months + monthString + ' and ' + age.days + dayString + ' old.';
		else if ( (age.years > 0) && (age.months === 0) && (age.days > 0) )
			ageString = age.years + yearString + ' and ' + age.days + dayString + ' old.';
		else if ( (age.years === 0) && (age.months > 0) && (age.days === 0) )
			ageString = age.months + monthString + ' old.';
		else ageString = 'Oops! Could not calculate age!';
		
		if ( (age.years > 0) || (age.months > 0) || (age.days > 0) ) {
			Ext.getCmp('txtUmurTh').setValue(age.years);
			Ext.getCmp('txtUmurBln').setValue(age.months);
			Ext.getCmp('txtUmurHr').setValue(age.days);
		}
		else {
			Ext.getCmp('txtUmurTh').setValue('0');
			Ext.getCmp('txtUmurBln').setValue('0');
			Ext.getCmp('txtUmurHr').setValue('0');
		}
		// return ageString;
	}

	var grupReg = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_reg','fs_kd_pesan','fd_tgl_masuk',
			'fs_kd_mr','fs_nm_pasien','fs_alamat',
			'fs_tlp','fs_kd_jk','fs_nm_jk',
			'fs_gol_darah','fd_tgl_lahir',
			'fn_tinggi','fn_berat',
			'fs_kd_agama','fs_nm_agama',
			'fs_kd_pendidikan','fs_nm_pendidikan',
			'fs_kd_pekerjaan','fs_nm_pekerjaan',
			'fs_nm_kerabat','fs_alm_kerabat','fs_hubungan',
			'fs_tlp_kerabat'
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
			url: 'trsreg/KodeReg'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_reg': Ext.getCmp('cboReg').getValue(),
					'fs_kd_mr': Ext.getCmp('cboReg').getValue(),
					'fs_nm_pasien': Ext.getCmp('cboReg').getValue(),
					'fs_alamat': Ext.getCmp('cboReg').getValue(),
					'fd_tgl_periksa': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Y-m-d')
				});
			}
		}
	});

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupReg,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupReg,
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
			{text: 'No.Reg', dataIndex: 'fs_kd_reg', menuDisabled: true, width: 100},
			{text: 'No.Pendaftaran', dataIndex: 'fs_kd_pesan', menuDisabled: true, width: 100},
			{text: 'Tgl Reg', dataIndex: 'fd_tgl_masuk', menuDisabled: true, width: 80},
			{text: 'No.Rekam Medis', dataIndex: 'fs_kd_mr', menuDisabled: true, width: 100},
			{text: 'Nama Pasien', dataIndex: 'fs_nm_pasien', menuDisabled: true, width: 200},
			{text: 'Alamat', dataIndex: 'fs_alamat', menuDisabled: true, width: 200},
			{text: 'Telp', dataIndex: 'fs_tlp', hidden: true},
			{text: 'Kode Jenis Kelamin', dataIndex: 'fs_kd_jk', hidden: true},
			{text: 'Nama Jenis Kelamin', dataIndex: 'fs_nm_jk', hidden: true},
			{text: 'Gol Darah', dataIndex: 'fs_gol_darah', hidden: true},
			{text: 'Tgl Lahir', dataIndex: 'fd_tgl_lahir', hidden: true},
			{text: 'Tinggi', dataIndex: 'fn_tinggi', hidden: true},
			{text: 'Berat', dataIndex: 'fn_berat', hidden: true},
			{text: 'Kode Agama', dataIndex: 'fs_kd_agama', hidden: true},
			{text: 'Nama Agama', dataIndex: 'fs_nm_agama', hidden: true},
			{text: 'Kode Pendidikan', dataIndex: 'fs_kd_pendidikan', hidden: true},
			{text: 'Nama Pendidikan', dataIndex: 'fs_nm_pendidikan', hidden: true},
			{text: 'Kode Pekerjaan', dataIndex: 'fs_kd_pekerjaan', hidden: true},
			{text: 'Nama Pekerjaan', dataIndex: 'fs_nm_pekerjaan', hidden: true},
			{text: 'Nama Kerabat', dataIndex: 'fs_nm_kerabat', hidden: true},
			{text: 'Alm Kerabat', dataIndex: 'fs_alm_kerabat', hidden: true},
			{text: 'Hubungan', dataIndex: 'fs_hubungan', hidden: true},
			{text: 'Telp Kerabat', dataIndex: 'fs_tlp_kerabat', hidden: true}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboReg').setValue(record.get('fs_kd_reg'));
				Ext.getCmp('cboPesan').setValue(record.get('fs_kd_pesan'));
				Ext.getCmp('txtTgl').setValue(record.get('fd_tgl_masuk'));
				Ext.getCmp('cboMR').setValue(record.get('fs_kd_mr'));
				Ext.getCmp('txtNama').setValue(record.get('fs_nm_pasien'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				Ext.getCmp('cboJK').setValue(record.get('fs_kd_jk'));
				Ext.getCmp('cboGol').setValue(record.get('fs_gol_darah'));
				Ext.getCmp('txtTglLahir').setValue(record.get('fd_tgl_lahir'));
				Ext.getCmp('txtTinggi').setValue(record.get('fn_tinggi'));
				Ext.getCmp('txtBerat').setValue(record.get('fn_berat'));
				Ext.getCmp('cboAgama').setValue(record.get('fs_kd_agama'));
				Ext.getCmp('cboPendidikan').setValue(record.get('fs_kd_pendidikan'));
				Ext.getCmp('txtPendidikan').setValue(record.get('fs_nm_pendidikan'));
				Ext.getCmp('cboPekerjaan').setValue(record.get('fs_kd_pekerjaan'));
				Ext.getCmp('txtPekerjaan').setValue(record.get('fs_nm_pekerjaan'));
				Ext.getCmp('txtNama2').setValue(record.get('fs_nm_kerabat'));
				Ext.getCmp('txtAlamat2').setValue(record.get('fs_alm_kerabat'));
				Ext.getCmp('txtHub').setValue(record.get('fs_hubungan'));
				Ext.getCmp('txtTlp2').setValue(record.get('fs_tlp_kerabat'));
				
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
				grupReg.load();
				vMask.show();
			}
		}
	});

	var cboReg = {
		anchor: '98%',
		emptyText: 'BARU',
		fieldLabel: 'No.Registrasi',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboReg',
		maxLength: 25,
		name: 'cboReg',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
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

	var txtReg = {
		anchor: '40%',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtReg',
		name: 'txtReg',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupPesan = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_pesan','fd_tgl_masuk',
			'fs_kd_mr','fs_nm_pasien','fs_alamat',
			'fs_tlp','fs_kd_jk','fs_nm_jk',
			'fs_gol_darah','fd_tgl_lahir',
			'fn_tinggi','fn_berat',
			'fs_kd_agama','fs_nm_agama',
			'fs_kd_pendidikan','fs_nm_pendidikan',
			'fs_kd_pekerjaan','fs_nm_pekerjaan',
			'fs_nm_kerabat','fs_alm_kerabat','fs_hubungan',
			'fs_tlp_kerabat'
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
			url: 'trsreg/KodePesan'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_pesan': Ext.getCmp('cboPesan').getValue(),
					'fs_nm_pasien': Ext.getCmp('cboPesan').getValue(),
					'fs_alamat': Ext.getCmp('cboPesan').getValue(),
					'fd_tgl_periksa': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Y-m-d')
				});
			}
		}
	});

	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupPesan,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupPesan,
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
			{text: 'No.Pendaftaran', dataIndex: 'fs_kd_pesan', menuDisabled: true, width: 100},
			{text: 'Tgl Reg', dataIndex: 'fd_tgl_masuk', menuDisabled: true, width: 80},
			{text: 'No.Rekam Medis', dataIndex: 'fs_kd_mr', menuDisabled: true, width: 100},
			{text: 'Nama Pasien', dataIndex: 'fs_nm_pasien', menuDisabled: true, width: 300},
			{text: 'Alamat', dataIndex: 'fs_alamat', menuDisabled: true, width: 300},
			{text: 'Telp', dataIndex: 'fs_tlp', hidden: true},
			{text: 'Kode Jenis Kelamin', dataIndex: 'fs_kd_jk', hidden: true},
			{text: 'Nama Jenis Kelamin', dataIndex: 'fs_nm_jk', hidden: true},
			{text: 'Gol Darah', dataIndex: 'fs_gol_darah', hidden: true},
			{text: 'Tgl Lahir', dataIndex: 'fd_tgl_lahir', hidden: true},
			{text: 'Tinggi', dataIndex: 'fn_tinggi', hidden: true},
			{text: 'Berat', dataIndex: 'fn_berat', hidden: true},
			{text: 'Kode Agama', dataIndex: 'fs_kd_agama', hidden: true},
			{text: 'Nama Agama', dataIndex: 'fs_nm_agama', hidden: true},
			{text: 'Kode Pendidikan', dataIndex: 'fs_kd_pendidikan', hidden: true},
			{text: 'Nama Pendidikan', dataIndex: 'fs_nm_pendidikan', hidden: true},
			{text: 'Kode Pekerjaan', dataIndex: 'fs_kd_pekerjaan', hidden: true},
			{text: 'Nama Pekerjaan', dataIndex: 'fs_nm_pekerjaan', hidden: true},
			{text: 'Nama Kerabat', dataIndex: 'fs_nm_kerabat', hidden: true},
			{text: 'Alm Kerabat', dataIndex: 'fs_alm_kerabat', hidden: true},
			{text: 'Hubungan', dataIndex: 'fs_hubungan', hidden: true},
			{text: 'Telp Kerabat', dataIndex: 'fs_tlp_kerabat', hidden: true}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboPesan').setValue(record.get('fs_kd_pesan'));
				Ext.getCmp('txtTgl').setValue(record.get('fd_tgl_masuk'));
				Ext.getCmp('cboMR').setValue(record.get('fs_kd_mr'));
				Ext.getCmp('txtNama').setValue(record.get('fs_nm_pasien'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				Ext.getCmp('cboJK').setValue(record.get('fs_kd_jk'));
				Ext.getCmp('cboGol').setValue(record.get('fs_gol_darah'));
				Ext.getCmp('txtTglLahir').setValue(record.get('fd_tgl_lahir'));
				Ext.getCmp('txtTinggi').setValue(record.get('fn_tinggi'));
				Ext.getCmp('txtBerat').setValue(record.get('fn_berat'));
				Ext.getCmp('cboAgama').setValue(record.get('fs_kd_agama'));
				Ext.getCmp('cboPendidikan').setValue(record.get('fs_kd_pendidikan'));
				Ext.getCmp('txtPendidikan').setValue(record.get('fs_nm_pendidikan'));
				Ext.getCmp('cboPekerjaan').setValue(record.get('fs_kd_pekerjaan'));
				Ext.getCmp('txtPekerjaan').setValue(record.get('fs_nm_pekerjaan'));
				Ext.getCmp('txtNama2').setValue(record.get('fs_nm_kerabat'));
				Ext.getCmp('txtAlamat2').setValue(record.get('fs_alm_kerabat'));
				Ext.getCmp('txtHub').setValue(record.get('fs_hubungan'));
				Ext.getCmp('txtTlp2').setValue(record.get('fs_tlp_kerabat'));
				
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
				grupPesan.load();
				vMask.show();
			}
		}
	});

	var cboPesan = {
		anchor: '98%',
		fieldLabel: 'No.Pendaftaran',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboPesan',
		maxLength: 25,
		name: 'cboPesan',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
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

	var txtTgl = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '72%',
		editable: true,
		fieldLabel: 'Tgl Registrasi',
		fieldStyle: 'background-color: #eee; background-image: none;',
		format: 'd-m-Y',
		id: 'txtTgl',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -10),
		name: 'txtTgl',
		readOnly: true,
		value: new Date(),
		xtype: 'datefield'
	};

	var grupMR = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_mr','fs_nm_pasien','fs_alamat',
			'fs_tlp','fs_kd_jk','fs_nm_jk',
			'fs_gol_darah','fd_tgl_lahir',
			'fn_tinggi','fn_berat',
			'fs_kd_agama','fs_nm_agama',
			'fs_kd_pendidikan','fs_nm_pendidikan',
			'fs_kd_pekerjaan','fs_nm_pekerjaan',
			'fs_nm_kerabat','fs_alm_kerabat','fs_hubungan',
			'fs_tlp_kerabat'
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
			url: 'trsreg/KodeMR'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_mr': Ext.getCmp('cboMR').getValue(),
					'fs_nm_pasien': Ext.getCmp('cboMR').getValue(),
					'fs_alamat': Ext.getCmp('cboMR').getValue()
				});
			}
		}
	});

	var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupMR,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupMR,
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
			{text: 'No.Rekam Medis', dataIndex: 'fs_kd_mr', menuDisabled: true, width: 100},
			{text: 'Nama Pasien', dataIndex: 'fs_nm_pasien', menuDisabled: true, width: 300},
			{text: 'Alamat', dataIndex: 'fs_alamat', menuDisabled: true, width: 300},
			{text: 'Telp', dataIndex: 'fs_tlp', hidden: true},
			{text: 'Kode Jenis Kelamin', dataIndex: 'fs_kd_jk', hidden: true},
			{text: 'Nama Jenis Kelamin', dataIndex: 'fs_nm_jk', hidden: true},
			{text: 'Gol Darah', dataIndex: 'fs_gol_darah', hidden: true},
			{text: 'Tgl Lahir', dataIndex: 'fd_tgl_lahir', hidden: true},
			{text: 'Tinggi', dataIndex: 'fn_tinggi', hidden: true},
			{text: 'Berat', dataIndex: 'fn_berat', hidden: true},
			{text: 'Kode Agama', dataIndex: 'fs_kd_agama', hidden: true},
			{text: 'Nama Agama', dataIndex: 'fs_nm_agama', hidden: true},
			{text: 'Kode Pendidikan', dataIndex: 'fs_kd_pendidikan', hidden: true},
			{text: 'Nama Pendidikan', dataIndex: 'fs_nm_pendidikan', hidden: true},
			{text: 'Kode Pekerjaan', dataIndex: 'fs_kd_pekerjaan', hidden: true},
			{text: 'Nama Pekerjaan', dataIndex: 'fs_nm_pekerjaan', hidden: true},
			{text: 'Nama Kerabat', dataIndex: 'fs_nm_kerabat', hidden: true},
			{text: 'Alm Kerabat', dataIndex: 'fs_alm_kerabat', hidden: true},
			{text: 'Hubungan', dataIndex: 'fs_hubungan', hidden: true},
			{text: 'Telp Kerabat', dataIndex: 'fs_tlp_kerabat', hidden: true}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboMR').setValue(record.get('fs_kd_mr'));
				Ext.getCmp('txtNama').setValue(record.get('fs_nm_pasien'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				Ext.getCmp('cboJK').setValue(record.get('fs_kd_jk'));
				Ext.getCmp('cboGol').setValue(record.get('fs_gol_darah'));
				Ext.getCmp('txtTglLahir').setValue(record.get('fd_tgl_lahir'));
				Ext.getCmp('txtTinggi').setValue(record.get('fn_tinggi'));
				Ext.getCmp('txtBerat').setValue(record.get('fn_berat'));
				Ext.getCmp('cboAgama').setValue(record.get('fs_kd_agama'));
				Ext.getCmp('cboPendidikan').setValue(record.get('fs_kd_pendidikan'));
				Ext.getCmp('txtPendidikan').setValue(record.get('fs_nm_pendidikan'));
				Ext.getCmp('cboPekerjaan').setValue(record.get('fs_kd_pekerjaan'));
				Ext.getCmp('txtPekerjaan').setValue(record.get('fs_nm_pekerjaan'));
				Ext.getCmp('txtNama2').setValue(record.get('fs_nm_kerabat'));
				Ext.getCmp('txtAlamat2').setValue(record.get('fs_alm_kerabat'));
				Ext.getCmp('txtHub').setValue(record.get('fs_hubungan'));
				Ext.getCmp('txtTlp2').setValue(record.get('fs_tlp_kerabat'));
				
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
				grupMR.load();
				vMask.show();
			}
		}
	});

	var cboMR = {
		anchor: '70%',
		emptyText: 'BARU',
		fieldLabel: 'No.Rekam Medis',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboMR',
		maxLength: 25,
		name: 'cboMR',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtNama').setValue('');
				Ext.getCmp('txtAlamat').setValue('');
				Ext.getCmp('txtTlp').setValue('');
				Ext.getCmp('cboJK').setValue('0');
				Ext.getCmp('txtTglLahir').setValue(new Date());
				Ext.getCmp('txtUmurTh').setValue('0');
				Ext.getCmp('txtUmurBln').setValue('0');
				Ext.getCmp('txtUmurHr').setValue('0');
				Ext.getCmp('txtTinggi').setValue('0');
				Ext.getCmp('txtBerat').setValue('0');
				Ext.getCmp('cboAgama').setValue('0');
				Ext.getCmp('cboPendidikan').setValue('');
				Ext.getCmp('txtPendidikan').setValue('');
				Ext.getCmp('cboPekerjaan').setValue('');
				Ext.getCmp('txtPekerjaan').setValue('');
				Ext.getCmp('cboGol').setValue('A');
				Ext.getCmp('txtNama2').setValue('');
				Ext.getCmp('txtHub').setValue('');
				Ext.getCmp('txtAlamat2').setValue('');
				Ext.getCmp('txtTlp2').setValue('');
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

	var txtNama = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '98%',
		fieldLabel: 'Nama',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtNama',
		maxLength: 100,
		name: 'txtNama',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtAlamat = {
		anchor: '98%',
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

	var txtTlp = {
		anchor: '98%',
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

	var grupJK = Ext.create('Ext.data.ArrayStore', {
		autoLoad: false,
		data: [['0', 'LAKI - LAKI'], ['1', 'PEREMPUAN']],
		fields: ['fs_kd_jk', 'fs_nm_jk']
	});

	var cboJK = {
		anchor: '100%',
		displayField: 'fs_nm_jk',
		editable: false,
		emptyText: 'Pilih Jenis Kelamin',
		fieldLabel: 'Jenis Kelamin',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboJK',
		name: 'cboJK',
		store: grupJK,
		value: '0',
		valueField: 'fs_kd_jk',
		xtype: 'combobox',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupGol = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_gol_darah'
		],
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'trsreg/KodeGolDarah'
		}
	});

	var cboGol = {
		anchor: '100%',
		displayField: 'fs_gol_darah',
		editable: false,
		emptyText: 'Pilih Gol.Darah',
		fieldLabel: 'Gol.Darah',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboGol',
		name: 'cboGol',
		store: grupGol,
		value: 'A',
		valueField: 'fs_gol_darah',
		xtype: 'combobox',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtTglLahir = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '43%',
		editable: true,
		fieldLabel: 'Tgl Lahir',
		format: 'd-m-Y',
		id: 'txtTglLahir',
		labelWidth: 80,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -100),
		name: 'txtTglLahir',
		value: new Date(),
		xtype: 'datefield',
		listeners: {
			change: function(value) {
				var tgl = Ext.Date.format(Ext.getCmp('txtTglLahir').getValue(), 'm/d/Y');
				var umur = getAge(tgl);
			}
		}
	};

	var txtUmurTh = {
		anchor: '100%',
		fieldLabel: 'Umur (Th)',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		id: 'txtUmurTh',
		labelWidth: 80,
		maskRe: /[0-9-]/,
		name: 'txtUmurTh',
		readOnly: true,
		value: '0',
		xtype: 'textfield',
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
			}
		}
	};

	var txtUmurBln = {
		anchor: '100%',
		fieldLabel: '(Bln)',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		id: 'txtUmurBln',
		labelWidth: 50,
		maskRe: /[0-9-]/,
		name: 'txtUmurBln',
		readOnly: true,
		value: '0',
		xtype: 'textfield',
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
			}
		}
	};

	var txtUmurHr = {
		anchor: '100%',
		fieldLabel: '(Hr)',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		id: 'txtUmurHr',
		labelWidth: 50,
		maskRe: /[0-9-]/,
		name: 'txtUmurHr',
		readOnly: true,
		value: '0',
		xtype: 'textfield',
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
			}
		}
	};

	var txtTinggi = {
		anchor: '100%',
		fieldLabel: 'Tinggi (Cm)',
		fieldStyle: 'text-align: right;',
		id: 'txtTinggi',
		labelWidth: 80,
		maskRe: /[0-9-]/,
		name: 'txtTinggi',
		value: '0',
		xtype: 'textfield',
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
			}
		}
	};

	var txtBerat = {
		anchor: '100%',
		fieldLabel: 'Berat (Kg)',
		fieldStyle: 'text-align: right;',
		id: 'txtBerat',
		labelWidth: 70,
		maskRe: /[0-9-]/,
		name: 'txtBerat',
		value: '0',
		xtype: 'textfield',
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
			}
		}
	};

	var grupAgama = Ext.create('Ext.data.Store', {
		autoLoad: true,
		fields: [
			'fs_kd_agama','fs_nm_agama'
		],
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'trsreg/KodeAgama'
		}
	});

	var cboAgama = {
		anchor: '43%',
		displayField: 'fs_nm_agama',
		editable: false,
		emptyText: 'Pilih Agama',
		fieldLabel: 'Agama',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboAgama',
		labelWidth: 80,
		name: 'cboAgama',
		store: grupAgama,
		value: '0',
		valueField: 'fs_kd_agama',
		xtype: 'combobox',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupPendidikan = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_pendidikan','fs_nm_pendidikan'
		],
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'trsreg/KodePendidikan'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_pendidikan': Ext.getCmp('cboPendidikan').getValue(),
					'fs_nm_pendidikan': Ext.getCmp('txtPendidikan').getValue()
				});
			}
		}
	});

	var winGrid4 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupPendidikan,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupPendidikan,
			items:[
				'-', {
				text: 'Keluar',
				handler: function() {
					winCari4.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: 'Kode Pendidikan', dataIndex: 'fs_kd_pendidikan', menuDisabled: true, width: 100},
			{text: 'Nama Pendidikan', dataIndex: 'fs_nm_pendidikan', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboPendidikan').setValue(record.get('fs_kd_pendidikan'));
				Ext.getCmp('txtPendidikan').setValue(record.get('fs_nm_pendidikan'));
				
				winCari4.hide();
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

	var winCari4 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Pencarian...',
		items: [
			winGrid4
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupPendidikan.load();
				vMask.show();
			}
		}
	});

	var cboPendidikan = {
		anchor: '98%',
		fieldLabel: 'Pendidikan',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboPendidikan',
		labelWidth: 80,
		maxLength: 25,
		name: 'cboPendidikan',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtPendidikan').setValue('');
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
					winCari4.show();
					winCari4.center();
				}
			}
		}
	};

	var txtPendidikan = {
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtPendidikan',
		name: 'txtPendidikan',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var grupPekerjaan = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_pekerjaan','fs_nm_pekerjaan'
		],
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'trsreg/KodePekerjaan'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_pekerjaan': Ext.getCmp('cboPekerjaan').getValue(),
					'fs_nm_pekerjaan': Ext.getCmp('txtPekerjaan').getValue()
				});
			}
		}
	});

	var winGrid5 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupPekerjaan,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupPekerjaan,
			items:[
				'-', {
				text: 'Keluar',
				handler: function() {
					winCari5.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: 'Kode Pekerjaan', dataIndex: 'fs_kd_pekerjaan', menuDisabled: true, width: 100},
			{text: 'Nama Pekerjaan', dataIndex: 'fs_nm_pekerjaan', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboPekerjaan').setValue(record.get('fs_kd_pekerjaan'));
				Ext.getCmp('txtPekerjaan').setValue(record.get('fs_nm_pekerjaan'));
				
				winCari5.hide();
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

	var winCari5 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Pencarian...',
		items: [
			winGrid5
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupPekerjaan.load();
				vMask.show();
			}
		}
	});

	var cboPekerjaan = {
		anchor: '98%',
		fieldLabel: 'Pekerjaan',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboPekerjaan',
		labelWidth: 80,
		maxLength: 25,
		name: 'cboPekerjaan',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtPekerjaan').setValue('');
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
					winCari5.show();
					winCari5.center();
				}
			}
		}
	};

	var txtPekerjaan = {
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtPekerjaan',
		name: 'txtPekerjaan',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtNama2 = {
		anchor: '98%',
		fieldLabel: 'Nama',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtNama2',
		maxLength: 100,
		name: 'txtNama2',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtAlamat2 = {
		anchor: '98%',
		fieldLabel: 'Alamat',
		fieldStyle: 'text-transform: uppercase;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtAlamat2',
		maxLength: 200,
		name: 'txtAlamat2',
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtHub = {
		anchor: '90%',
		fieldLabel: 'Hubungan',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtHub',
		labelWidth: 80,
		maxLength: 50,
		name: 'txtHub',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtTlp2 = {
		anchor: '90%',
		fieldLabel: 'Telp',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtTlp2',
		labelWidth: 80,
		maxLength: 50,
		name: 'txtTlp2',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	function fnCekSimpan(){
		if (this.up('form').getForm().isValid()) {
			var xKdPendidikan = Ext.getCmp('cboPendidikan').getValue();
			var xNmPendidikan = Ext.getCmp('txtPendidikan').getValue();
			var xKdPekerjaan = Ext.getCmp('cboPekerjaan').getValue();
			var xNmPekerjaan = Ext.getCmp('txtPekerjaan').getValue();
			
			if (trim(xKdPendidikan) !== '' && trim(xNmPendidikan) === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Pendidikan tidak ada dalam daftar!!',
					title: 'DokterPraktek'
				});
				return;
			}
			
			if (trim(xKdPekerjaan) !== '' && trim(xNmPekerjaan) === '') {
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Pekerjaan tidak ada dalam daftar!!',
					title: 'DokterPraktek'
				});
				return;
			}
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'trsreg/CekSimpan',
				params: {
					'fs_kd_reg': Ext.getCmp('cboReg').getValue(),
					'fs_kd_mr': Ext.getCmp('cboMR').getValue(),
					'fs_nm_pasien': Ext.getCmp('txtNama').getValue(),
					'fs_alamat': Ext.getCmp('txtAlamat').getValue()
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
			url: 'trsreg/Simpan',
			params: {
				'fs_kd_reg': Ext.getCmp('cboReg').getValue(),
				'fs_kd_pesan': Ext.getCmp('cboPesan').getValue(),
				'fd_tgl_masuk': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Y-m-d'),
				'fs_jam_masuk': Ext.Date.format(new Date(), 'H:i:s'),
				'fs_kd_mr': Ext.getCmp('cboMR').getValue(),
				'fs_nm_pasien': Ext.getCmp('txtNama').getValue(),
				'fs_alamat': Ext.getCmp('txtAlamat').getValue(),
				'fs_tlp': Ext.getCmp('txtTlp').getValue(),
				'fb_sex': Ext.getCmp('cboJK').getValue(),
				'fs_gol_darah': Ext.getCmp('cboGol').getValue(),
				'fd_tgl_lahir': Ext.Date.format(Ext.getCmp('txtTglLahir').getValue(), 'Y-m-d'),
				'fn_th': Ext.getCmp('txtUmurTh').getValue(),
				'fn_bl': Ext.getCmp('txtUmurBln').getValue(),
				'fn_hr': Ext.getCmp('txtUmurHr').getValue(),
				'fn_tinggi': Ext.getCmp('txtTinggi').getValue(),
				'fn_berat': Ext.getCmp('txtBerat').getValue(),
				'fs_kd_agama': Ext.getCmp('cboAgama').getValue(),
				'fs_kd_pendidikan': Ext.getCmp('cboPendidikan').getValue(),
				'fs_kd_pekerjaan': Ext.getCmp('cboPekerjaan').getValue(),
				'fs_nm_kerabat': Ext.getCmp('txtNama2').getValue(),
				'fs_alm_kerabat': Ext.getCmp('txtAlamat2').getValue(),
				'fs_hubungan': Ext.getCmp('txtHub').getValue(),
				'fs_tlp_kerabat': Ext.getCmp('txtTlp2').getValue(),
				'fd_prefix': Ext.Date.format(new Date(), 'ymd'),
				'fd_simpan': Ext.Date.format(new Date(), 'Y-m-d H:i:s')
			},
			success: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				
				if (xText.sukses === true) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: xText.hasil,
						title: 'DokterPraktek'
					});
					fnReset();
					Ext.getCmp('txtReg').setValue(trim(xText.kodereg));
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

	function fnCekHapus() {
		if (this.up('form').getForm().isValid()) {
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'trsreg/CekHapus',
				params: {
					'fs_kd_reg': Ext.getCmp('cboReg').getValue()
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
						message: 'Hapus Gagal, Koneksi Gagal!!',
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
			url: 'trsreg/Hapus',
			params: {
				'fs_kd_reg': Ext.getCmp('cboReg').getValue(),
				'fs_kd_pesan': Ext.getCmp('cboPesan').getValue()
			},
			success: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				
				if (xText.sukses === true) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: xText.hasil,
						title: 'DokterPraktek'
					});
					fnReset();
					Ext.getCmp('txtReg').setValue(trim(xText.kodereg));
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

	function fnReset() {
		Ext.getCmp('cboReg').setValue('');
		Ext.getCmp('txtReg').setValue('');
		Ext.getCmp('cboPesan').setValue('');
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('cboMR').setValue('');
		Ext.getCmp('txtNama').setValue('');
		Ext.getCmp('txtAlamat').setValue('');
		Ext.getCmp('txtTlp').setValue('');
		Ext.getCmp('cboJK').setValue('0');
		Ext.getCmp('txtTglLahir').setValue(new Date());
		Ext.getCmp('txtUmurTh').setValue('0');
		Ext.getCmp('txtUmurBln').setValue('0');
		Ext.getCmp('txtUmurHr').setValue('0');
		Ext.getCmp('txtTinggi').setValue('0');
		Ext.getCmp('txtBerat').setValue('0');
		Ext.getCmp('cboAgama').setValue('0');
		Ext.getCmp('cboPendidikan').setValue('');
		Ext.getCmp('txtPendidikan').setValue('');
		Ext.getCmp('cboPekerjaan').setValue('');
		Ext.getCmp('txtPekerjaan').setValue('');
		Ext.getCmp('cboGol').setValue('A');
		Ext.getCmp('txtNama2').setValue('');
		Ext.getCmp('txtHub').setValue('');
		Ext.getCmp('txtAlamat2').setValue('');
		Ext.getCmp('txtTlp2').setValue('');
	}

	var frmTrsReg = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Daftar Ulang / Registrasi Pasien',
		width: 900,
		items: [{
			fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 135,
			msgTarget: 'side'
			},
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 0.7,
					layout: 'anchor',
					xtype: 'container'
				},{
					flex: 1.2,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboReg,
						cboPesan,
						txtTgl
					]
				},{
					flex: 1.5,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtReg
					]
				}]
			}]
		},{
			fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 90,
			msgTarget: 'side'
			},
			style: 'padding: 5px;',
			title: 'Data Pasien',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboMR,
						txtNama,
						txtAlamat,
						txtTlp,
					{
						anchor: '80%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.4,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboJK
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboGol
							]
						}]
					}]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtTglLahir,
					{
						anchor: '80%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.41,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtUmurTh
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtUmurBln
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtUmurHr
							]
						}]
					},{
						anchor: '70%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.15,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtTinggi
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtBerat
							]
						}]
					},
						cboAgama,
					{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboPendidikan,
								cboPekerjaan
							]
						},{
							flex: 1.5,
							layout: 'anchor',
							xtype: 'container',
							items: [
								txtPendidikan,
								txtPekerjaan
							]
						}]
					}]
				}]
			}]
		},{
			fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 90,
			msgTarget: 'side'
			},
			style: 'padding: 5px;',
			title: 'Data Kerabat Pasien',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtNama2,
						txtAlamat2
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtHub,
						txtTlp2
					]
				}]
			}
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
	});

	Ext.TaskManager.start({
		run: function() {
			Ext.getCmp('txtTgl').setValue(Ext.Date.format(new Date(), 'd-m-Y'));
		},
		interval: 1000
	});

	var vMask = new Ext.LoadMask({
		msg: 'Silakan tunggu...',
		target: frmTrsReg
	});

	frmTrsReg.render(Ext.getBody());
	Ext.get('loading').destroy();
});