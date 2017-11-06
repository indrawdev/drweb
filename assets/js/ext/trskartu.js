Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.form.NumericField',
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();

	var required = '<span style="color:red;font-weight:bold" data-qtip="Field ini wajib diisi">*</span>';

	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
	}

	function gridTooltipBarang(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Klik 2x pada item untuk edit',
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

	function fnTotal() {
		var xbiaya = Ext.getCmp('txtNBiaya').getValue();
		var xobat = Ext.getCmp('txtNObat').getValue();
		var xTotal = 0;
		
		xTotal = xbiaya + xobat;
		Ext.getCmp('txtNTotal').setValue(xTotal);
	}

	function fnCekReg() {
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'trskartu/CekKodeReg',
			params: {
				'fs_kd_reg': Ext.getCmp('cboReg').getValue()
			},
			success: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				
				if (xText.sukses === false) {
					if (trim(Ext.getCmp('txtMR').getValue()) !== '') {
						fnReset1();
					}
				}
			},
			failure: function(response, opts) {
				var xText = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					message: 'Cek Registrasi Gagal, Koneksi Gagal!!',
					title: 'DokterPraktek'
				});
				vMask.hide();
			}
		});
	}

	var grupReg = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_reg','fd_tgl_periksa','fs_kd_mr',
			'fs_nm_pasien','fs_alamat',
			'fd_tgl_lahir','fn_tinggi', 'fn_berat',
			'fs_anamnesa','fs_diagnosa','fs_tindakan',
			'fs_kd_icd','fs_nm_icd','fn_biaya','fn_obat'
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
			url: 'trskartu/KodeReg'
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
			{text: 'Tgl Reg', dataIndex: 'fd_tgl_periksa', menuDisabled: true, width: 80},
			{text: 'No.Rekam Medis', dataIndex: 'fs_kd_mr', menuDisabled: true, width: 100},
			{text: 'Nama Pasien', dataIndex: 'fs_nm_pasien', menuDisabled: true, width: 300},
			{text: 'Alamat', dataIndex: 'fs_alamat', menuDisabled: true, width: 300},
			{text: 'Tgl Lahir', dataIndex: 'fd_tgl_lahir', hidden: true},
			{text: 'Tinggi', dataIndex: 'fn_tinggi', hidden: true},
			{text: 'Berat', dataIndex: 'fn_berat', hidden: true},
			{text: 'Anamnesa', dataIndex: 'fs_anamnesa', hidden: true},
			{text: 'Diagnosa', dataIndex: 'fs_diagnosa', hidden: true},
			{text: 'Tindakan', dataIndex: 'fs_tindakan', hidden: true},
			{text: 'Kode ICD', dataIndex: 'fs_kd_icd', hidden: true},
			{text: 'Nama ICD', dataIndex: 'fs_nm_icd', hidden: true},
			{text: 'Biaya', dataIndex: 'fn_biaya', hidden: true},
			{text: 'Obat', dataIndex: 'fn_obat', hidden: true}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboReg').setValue(record.get('fs_kd_reg'));
				Ext.getCmp('txtTgl').setValue(record.get('fd_tgl_periksa'));
				Ext.getCmp('txtMR').setValue(record.get('fs_kd_mr'));
				Ext.getCmp('txtNama').setValue(record.get('fs_nm_pasien'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				Ext.getCmp('txtTglLahir').setValue(record.get('fd_tgl_lahir'));
				Ext.getCmp('txtTinggi').setValue(record.get('fn_tinggi'));
				Ext.getCmp('txtBerat').setValue(record.get('fn_berat'));
				Ext.getCmp('txtAnamnesa').setValue(record.get('fs_anamnesa'));
				Ext.getCmp('txtDiagnosa').setValue(record.get('fs_diagnosa'));
				Ext.getCmp('txtTindakan').setValue(record.get('fs_tindakan'));
				Ext.getCmp('cboICD').setValue(record.get('fs_kd_icd'));
				Ext.getCmp('txtICD').setValue(record.get('fs_nm_icd'));
				Ext.getCmp('txtNBiaya').setValue(record.get('fn_biaya'));
				Ext.getCmp('txtNObat').setValue(record.get('fn_obat'));
				
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
				grupGridBarang.load();
				grupGridHistori.load();
				vMask.hide();
			},
			beforeshow: function() {
				grupReg.load();
				vMask.show();
			}
		}
	});

	var cboReg = {
		anchor: '100%',
		fieldLabel: 'No.Registrasi',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboReg',
		maxLength: 25,
		name: 'cboReg',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				fnCekReg();
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

	var txtTgl = {
		anchor: '65%',
		fieldLabel: 'Tgl Periksa',
		fieldStyle: 'background-color: #eee; background-image: none;',
		format: 'd-m-Y',
		id: 'txtTgl',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -10),
		name: 'txtTgl',
		readOnly: false,
		value: new Date(),
		xtype: 'datefield'
	};

	var txtMR = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		fieldLabel: 'No.Rekam Medis',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtMR',
		name: 'txtMR',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtNama = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '90%',
		fieldLabel: 'Nama',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtNama',
		labelWidth: 70,
		name: 'txtNama',
		readOnly: true,
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtAlamat = {
		anchor: '90%',
		fieldLabel: 'Alamat',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtAlamat',
		labelWidth: 70,
		name: 'txtAlamat',
		readOnly: true,
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtTglLahir = {
		anchor: '35%',
		fieldLabel: 'Tgl Lahir',
		fieldStyle: 'background-color: #eee; background-image: none;',
		format: 'd-m-Y',
		hidden: true,
		id: 'txtTglLahir',
		labelWidth: 70,
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -100),
		name: 'txtTglLahir',
		readOnly: true,
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
		labelWidth: 70,
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
		labelWidth: 35,
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
		labelWidth: 30,
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
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		id: 'txtTinggi',
		labelWidth: 80,
		maskRe: /[0-9-]/,
		name: 'txtTinggi',
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

	var txtBerat = {
		anchor: '100%',
		fieldLabel: 'Berat (Kg)',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		id: 'txtBerat',
		labelWidth: 70,
		maskRe: /[0-9-]/,
		name: 'txtBerat',
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

	var txtAnamnesa = {
		anchor: '98%',
		fieldLabel: 'Anamnesa',
		fieldStyle: 'text-transform: uppercase;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtAnamnesa',
		labelAlign: 'top',
		maxLength: 200,
		name: 'txtAnamnesa',
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtDiagnosa = {
		anchor: '98%',
		fieldLabel: 'Diagnosa',
		fieldStyle: 'text-transform: uppercase;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtDiagnosa',
		labelAlign: 'top',
		maxLength: 200,
		name: 'txtDiagnosa',
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtTindakan = {
		anchor: '100%',
		fieldLabel: 'Tindakan / Terapi',
		fieldStyle: 'text-transform: uppercase;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtTindakan',
		labelAlign: 'top',
		maxLength: 200,
		name: 'txtTindakan',
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtCariICD = {
		anchor: '95%',
		emptyText: 'Minimal 4 karakter',
		fieldLabel: 'Masukkan Kata Kunci',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtCariICD',
		name: 'txtCariICD',
		xtype: 'textfield',
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function(field) {
					var xcari = field.getValue();
					
					if (xcari.length >= 4) {
						grupGridICD.load();
					}
				}
			}
		},
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	Ext.define('DataGridICD', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_icd', type: 'string'},
			{name: 'fs_nm_icd', type: 'string'}
		]
	});

	var grupGridICD = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridICD',
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
			url: 'trskartu/GridICD'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_cari': Ext.getCmp('txtCariICD').getValue()
				});
			}
		}
	});

	var gridICD = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 390,
		sortableColumns: false,
		store: grupGridICD,
		columns: [{
			width: 45,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_icd',
			flex: 1,
			menuDisabled: true,
			text: 'Kode ICD'
		},{
			dataIndex: 'fs_nm_icd',
			flex: 9,
			menuDisabled: true,
			text: 'Nama ICD'
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridICD
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboICD').setValue(record.get('fs_kd_icd'));
				Ext.getCmp('txtICD').setValue(record.get('fs_nm_icd'));
				
				vMask.hide();
				winICD.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipSearch
			},
			markDirty: false
		}
	});

	var winICD = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		height: 500,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Pencarian Kode ICD X',
		width: 700,
		items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 115,
					msgTarget: 'side'
				},
				items: [
					txtCariICD, {xtype: 'splitter'},
					gridICD
				]
			})
		],
		buttons: [{
			text: 'Keluar',
			handler: function() {
				vMask.hide();
				winICD.hide();
			}
		}]
	});

	Ext.define('Ext.ux.SearchICD', {
		alias: 'widget.searchICD',
		extend: 'Ext.form.field.Text',
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
					Ext.getCmp('txtCariICD').setValue('');
					grupGridICD.removeAll();
					vMask.show();
					winICD.show();
				}
			}
		}
	});

	var cboICD = {
		anchor: '98%',
		fieldLabel: 'ICD X',
		fieldStyle: 'text-transform: uppercase;',
		id: 'cboICD',
		labelWidth: 35,
		maxLength: 25,
		name: 'cboICD',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
				Ext.getCmp('txtICD').setValue('');
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
					Ext.getCmp('txtCariICD').setValue('');
					grupGridICD.removeAll();
					vMask.show();
					winICD.show();
				}
			}
		}
	};

	var txtICD = {
		anchor: '100%',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtICD',
		name: 'txtICD',
		readOnly: true,
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtResep = {
		fieldLabel: 'Resep',
		id: 'txtResep',
		labelSeparator: ':',
		labelWidth: 30,
		name: 'txtResep',
		value: '',
		xtype: 'displayfield'
	};

	var grupBarang = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_barang','fs_nm_barang'
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
			url: 'setupbarang/KodeBarangAktif'
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

	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
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
					winCari2.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: 'Kode', dataIndex: 'fs_kd_barang', menuDisabled: true, width: 100},
			{text: 'Nama Obat', dataIndex: 'fs_nm_barang', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboBarang').setValue(record.get('fs_kd_barang'));
				Ext.getCmp('txtBarang').setValue(record.get('fs_nm_barang'));
				
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
				grupBarang.load();
				vMask.show();
			}
		}
	});

	var grupGridBarang2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_barang','fs_nm_barang'
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
			url: 'setupbarang/KodeBarangAktif'
		},
		listeners: {
			beforeload: function(store, operation) {
				var recordgrid = gridBarang.getSelectionModel().getSelection()[0];
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_barang': recordgrid.get('fs_kd_barang'),
					'fs_nm_barang': recordgrid.get('fs_nm_barang')
				});
			}
		}
	});

	var winGrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupGridBarang2,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridBarang2,
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
			{text: 'Kode', dataIndex: 'fs_kd_barang', menuDisabled: true, width: 100},
			{text: 'Nama Obat', dataIndex: 'fs_nm_barang', menuDisabled: true, width: 380}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				var recordgrid = gridBarang.getSelectionModel().getSelection()[0];
				recordgrid.set('fs_kd_barang',record.get('fs_kd_barang'));
				recordgrid.set('fs_nm_barang',record.get('fs_nm_barang'));
				
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
				grupGridBarang2.load();
				vMask.show();
			}
		}
	});

	var cellEditingBarang = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2
	});

	Ext.define('DataGridBarang', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_barang', type: 'string'},
			{name: 'fs_nm_barang', type: 'string'},
			{name: 'fs_ket', type: 'string'}
		]
	});

	var grupGridBarang = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridBarang',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'trskartu/GridBarang'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_reg': Ext.getCmp('cboReg').getValue()
				});
			}
		}
	});

	var gridBarang = Ext.create('Ext.grid.Panel', {
		anchor: '98%',
		autoDestroy: true,
		height: 170,
		sortableColumns: false,
		store: grupGridBarang,
		columns: [{
			xtype: 'rownumberer'
		},{
			dataIndex: 'fs_kd_barang',
			menuDisabled: true,
			text: 'Kode',
			width: 100,
			editor: {
				editable: true,
				xtype: 'textfield',
				listeners: {
					change: function() {
						var recordgrid = gridBarang.getSelectionModel().getSelection()[0];
						recordgrid.set('fs_nm_barang','');
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
			}
		},{
			dataIndex: 'fs_nm_barang',
			menuDisabled: true,
			text: 'Nama Obat',
			width: 350
		},{
			dataIndex: 'fs_ket',
			menuDisabled: true,
			text: 'Keterangan',
			width: 350,
			editor: {
				fieldStyle: 'text-transform: uppercase;',
				maxLength: 200,
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						field.setValue(newValue.toUpperCase());
					}
				}
			}
		}],
		listeners: {
			'selectionchange': function(view, records) {
				gridBarang.down('#removeData').setDisabled(!records.length);
			}
		},
		plugins: [
			cellEditingBarang
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Pilih Obat',
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
							winCari2.show();
							winCari2.center();
						}
					}
				}
			},{
				anchor: '95%',
				emptyText: 'Masukkan Obat',
				fieldStyle: 'text-transform: uppercase;',
				hidden: true,
				id: 'txtBarang',
				name: 'txtBarang',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						field.setValue(newValue.toUpperCase());
					}
				}
			}]
		},{
			flex: 2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '95%',
				emptyText: 'Masukkan Keterangan',
				fieldStyle: 'text-transform: uppercase;',
				id: 'txtKet',
				maxLength: 200,
				name: 'txtKet',
				xtype: 'textfield',
				listeners: {
					change: function(field, newValue) {
						field.setValue(newValue.toUpperCase());
					}
				}
			}]
		},{
			xtype: 'buttongroup',
			columns: 2,
			defaults: {
				scale: 'small'
			},
			items: [{
				iconCls: 'icon-add',
				text: 'Tambah',
				handler : function() {
					var xTotal = grupGridBarang.getCount();
					var xKdBarang = Ext.getCmp('cboBarang').getValue();
					var xData = Ext.create('DataGridBarang', {
						fs_kd_barang: Ext.getCmp('cboBarang').getValue(),
						fs_nm_barang: Ext.getCmp('txtBarang').getValue(),
						fs_ket: Ext.getCmp('txtKet').getValue()
					});
					
					var store = gridBarang.getStore();
					var xLanjut = true;
					store.each(function(record, idx) {
						var xText = trim(record.get('fs_kd_barang'));
						
						if (xText == xKdBarang) {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.OK,
								closable: false,
								icon: Ext.MessageBox.INFO,
								message: 'Obat sudah ada, tambah obat batal!!',
								title: 'DokterPraktek'
							});
							xLanjut = false;
						}
						
					});
					if (xLanjut === false) {
						return;
					}
					
					if (trim(xKdBarang) === '') {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							message: 'Obat tidak ada dalam daftar!',
							title: 'DokterPraktek'
						});
						return;
					}
					
					grupGridBarang.insert(xTotal, xData);
					Ext.getCmp('cboBarang').setValue('');
					Ext.getCmp('txtBarang').setValue('');
					Ext.getCmp('txtKet').setValue('');
				}
			},{
				iconCls: 'icon-delete',
				itemId: 'removeData',
				text: 'Hapus',
				handler: function() {
					var sm = gridBarang.getSelectionModel();
					cellEditingBarang.cancelEdit();
					grupGridBarang.remove(sm.getSelection());
					gridBarang.getView().refresh();
					if (grupGridBarang.getCount() > 0) {
						sm.select(0);
					}
				},
				disabled: true
			}]
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipBarang
			},
			markDirty: false
		}
	});

	var txtJumlah = {
		fieldLabel: 'Jumlah Biaya',
		id: 'txtJumlah',
		labelSeparator: ':',
		labelWidth: 80,
		name: 'txtJumlah',
		value: '',
		xtype: 'displayfield'
	};

	var txtNBiaya = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: false,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Biaya',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtNBiaya',
		keyNavEnabled: false,
		labelWidth: 50,
		mouseWheelEnabled: false,
		name: 'txtNBiaya',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
				fnTotal();
			}
		}
	});

	var txtNObat = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: false,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Obat',
		fieldStyle: 'text-align: right;',
		hideTrigger: true,
		id: 'txtNObat',
		keyNavEnabled: false,
		labelWidth: 50,
		mouseWheelEnabled: false,
		name: 'txtNObat',
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
				fnTotal();
			}
		}
	});

	var txtNTotal = Ext.create('Ext.ux.form.NumericField', {
		alwaysDisplayDecimals: false,
		anchor: '100%',
		currencySymbol: null,
		decimalPrecision: 2,
		decimalSeparator: '.',
		fieldLabel: 'Total',
		fieldStyle: 'background-color: #eee; background-image: none; text-align: right;',
		hideTrigger: true,
		id: 'txtNTotal',
		keyNavEnabled: false,
		labelWidth: 50,
		mouseWheelEnabled: false,
		name: 'txtNTotal',
		readOnly: true,
		thousandSeparator: ',',
		useThousandSeparator: true,
		value: 0,
		listeners: {
			change: function(field, value) {
				if (Ext.isEmpty(field.getValue())) {
					field.setValue(0);
				}
			}
		}
	});

	Ext.define('DataGridHistori', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fd_tgl_periksa', type: 'string'},
			{name: 'fs_kd_reg', type: 'string'},
			{name: 'fs_anamnesa', type: 'string'},
			{name: 'fs_diagnosa', type: 'string'},
			{name: 'fs_icd', type: 'string'},
			{name: 'fs_tindakan', type: 'string'},
			{name: 'fs_ket', type: 'string'}
		]
	});

	var grupGridHistori = Ext.create('Ext.data.Store', {
		autoLoad: false,
		model: 'DataGridHistori',
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
			url: 'trskartu/GridHistori'
		},
		listeners: {
			beforeload: function(store, operation) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_mr': Ext.getCmp('txtMR').getValue()
				});
			}
		}
	});

	var gridHistori = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 304,
		sortableColumns: false,
		store: grupGridHistori,
		columns: [{
			width: 45,
			xtype: 'rownumberer'
		},{
			dataIndex: 'fd_tgl_periksa',
			menuDisabled: true,
			text: 'Tanggal',
			width: 70
		},{
			dataIndex: 'fs_kd_reg',
			menuDisabled: true,
			text: 'No.Registrasi',
			width: 80
		},{
			dataIndex: 'fs_anamnesa',
			menuDisabled: true,
			text: 'Anamnesa',
			width: 250
		},{
			dataIndex: 'fs_diagnosa',
			menuDisabled: true,
			text: 'Diagnosa',
			width: 250
		},{
			dataIndex: 'fs_icd',
			menuDisabled: true,
			text: 'Diagnosa (ICD)',
			width: 250
		},{
			dataIndex: 'fs_tindakan',
			menuDisabled: true,
			text: 'Terapi',
			width: 250
		},{
			dataIndex: 'fs_ket',
			menuDisabled: true,
			text: 'Obat',
			width: 250
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridHistori
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
				url: 'trskartu/CekSimpan',
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
		var xKdBarang = '';
		var xKet = '';
		var store = gridBarang.getStore();
		
		store.each(function(record, idx) {
			if (trim(record.get('fs_nm_barang')) !== '') {
				xKdBarang = xKdBarang +'|'+ record.get('fs_kd_barang');
				xKet = xKet +'|'+ record.get('fs_ket');
			}
		});
		
		Ext.Ajax.on('beforerequest', vMask.show, vMask);
		Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
		Ext.Ajax.on('requestexception', vMask.hide, vMask);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'trskartu/Simpan',
			params: {
				'fs_kd_reg': Ext.getCmp('cboReg').getValue(),
				'fd_tgl_periksa': Ext.Date.format(Ext.getCmp('txtTgl').getValue(), 'Y-m-d'),
				'fs_jam_periksa': Ext.Date.format(new Date(), 'H:i:s'),
				'fs_anamnesa': Ext.getCmp('txtAnamnesa').getValue(),
				'fs_diagnosa': Ext.getCmp('txtDiagnosa').getValue(),
				'fs_tindakan': Ext.getCmp('txtTindakan').getValue(),
				'fs_kd_icd': Ext.getCmp('cboICD').getValue(),
				'fs_nm_icd': Ext.getCmp('txtICD').getValue(),
				'fn_biaya': Ext.getCmp('txtNBiaya').getValue(),
				'fn_obat': Ext.getCmp('txtNObat').getValue(),
				'fn_total': Ext.getCmp('txtNTotal').getValue(),
				'fs_kd_barang': xKdBarang,
				'fs_ket': xKet,
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
					grupGridHistori.load();
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
				url: 'trskartu/CekHapus',
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
			url: 'trskartu/Hapus',
			params: {
				'fs_kd_reg': Ext.getCmp('cboReg').getValue()
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
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtMR').setValue('');
		Ext.getCmp('txtNama').setValue('');
		Ext.getCmp('txtAlamat').setValue('');
		Ext.getCmp('txtTglLahir').setValue(new Date());
		Ext.getCmp('txtUmurTh').setValue('0');
		Ext.getCmp('txtUmurBln').setValue('0');
		Ext.getCmp('txtUmurHr').setValue('0');
		Ext.getCmp('txtTinggi').setValue('0');
		Ext.getCmp('txtBerat').setValue('0');
		Ext.getCmp('txtAnamnesa').setValue('');
		Ext.getCmp('txtDiagnosa').setValue('');
		Ext.getCmp('txtTindakan').setValue('');
		Ext.getCmp('cboICD').setValue('');
		Ext.getCmp('txtICD').setValue('');
		Ext.getCmp('txtNBiaya').setValue('0');
		Ext.getCmp('txtNObat').setValue('0');
		Ext.getCmp('txtNObat').setValue('0');
		Ext.getCmp('txtNTotal').setValue('0');
		Ext.getCmp('txtCariICD').setValue('');
		grupGridBarang.removeAll();
		grupGridICD.removeAll();
	}

	function fnReset1() {
		Ext.getCmp('txtTgl').setValue(new Date());
		Ext.getCmp('txtMR').setValue('');
		Ext.getCmp('txtNama').setValue('');
		Ext.getCmp('txtAlamat').setValue('');
		Ext.getCmp('txtTglLahir').setValue(new Date());
		Ext.getCmp('txtUmurTh').setValue('0');
		Ext.getCmp('txtUmurBln').setValue('0');
		Ext.getCmp('txtUmurHr').setValue('0');
		Ext.getCmp('txtTinggi').setValue('0');
		Ext.getCmp('txtBerat').setValue('0');
		Ext.getCmp('txtAnamnesa').setValue('');
		Ext.getCmp('txtDiagnosa').setValue('');
		Ext.getCmp('txtTindakan').setValue('');
		Ext.getCmp('cboICD').setValue('');
		Ext.getCmp('txtICD').setValue('');
		Ext.getCmp('txtNBiaya').setValue('0');
		Ext.getCmp('txtNObat').setValue('0');
		Ext.getCmp('txtNTotal').setValue('0');
		Ext.getCmp('txtCariICD').setValue('');
		grupGridBarang.removeAll();
		grupGridICD.removeAll();
	}

	var frmTrsReg = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Kartu Pasien',
		width: 900,
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
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboReg,
						txtTgl,
						txtMR
					]
				},{
					flex: 2,
					layout: 'anchor',
					xtype: 'container',
					items: [
						txtNama,
						txtAlamat,
						txtTglLahir,
					{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.2,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.4,
									layout: 'anchor',
									xtype: 'container',
									items: [
										txtUmurTh
									]
								},{
									flex: 1.05,
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
							}]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [{
								anchor: '100%',
								layout: 'hbox',
								xtype: 'container',
								items: [{
									flex: 1.05,
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
							}]
						}]
					}]
				}]
			}]
		},{
			activeTab: 0,
			bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
			border: false,
			plain: true,
			xtype: 'tabpanel',
			items: [{
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 90,
					msgTarget: 'side'
				},
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Pemeriksaan',
				xtype: 'form',
				items: [{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1.02,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtAnamnesa
						]
					},{
						flex: 1.02,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtDiagnosa
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtTindakan
						]
					}]
				},{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							cboICD
						]
					},{
						flex: 6,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtICD
						]
					}]
				},{
					anchor: '100%',
					layout: 'hbox',
					xtype: 'container',
					items: [{
						flex: 2.5,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtResep,
							gridBarang
						]
					},{
						flex: 1,
						layout: 'anchor',
						xtype: 'container',
						items: [
							txtJumlah,
							txtNBiaya,
							txtNObat,
							txtNTotal
						]
					}]
				}]
			},{
				border: false,
				frame: false,
				title: 'Histori',
				xtype: 'form',
				items: [
					gridHistori
				]
			}]
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