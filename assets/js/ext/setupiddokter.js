Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.onReady(function() {
    Ext.QuickTips.init();

	var required = '<span style="color:red;font-weight:bold" data-qtip="Field ini wajib diisi">*</span>';

	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
	}

	Ext.Ajax.request({
		method: 'POST',
		url: 'setupiddokter/KodeDokter',
		success: function(response, opts) {
			var xText = Ext.decode(response.responseText);
			
			if (xText.sukses === true) {
				Ext.getCmp('txtNama').setValue(xText.nmdokter);
				Ext.getCmp('txtAlamat').setValue(xText.alamat);
				Ext.getCmp('txtKota').setValue(xText.kota);
				Ext.getCmp('txtTlp').setValue(xText.tlp);
				Ext.getCmp('txtEmail').setValue(xText.email);
			}
		},
		failure: function(response, opts) {
			var xText = Ext.decode(response.responseText);
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.MessageBox.INFO,
				message: 'Tampil default Gagal, Koneksi Gagal!!',
				title: 'DokterPraktek'
			});
		}
	});

	var txtNama = {
		afterLabelTextTpl: required,
		allowBlank: false,
		fieldLabel: 'Nama Dokter',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtNama',
		name: 'txtNama',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtAlamat = {
		fieldLabel: 'Alamat',
		fieldStyle: 'text-transform: uppercase;',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtAlamat',
		name: 'txtAlamat',
		xtype: 'textareafield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtKota = {
		afterLabelTextTpl: required,
		allowBlank: false,
		fieldLabel: 'Kota',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtKota',
		name: 'txtKota',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtTlp = {
		afterLabelTextTpl: required,
		allowBlank: false,
		fieldLabel: 'Telp',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtTlp',
		name: 'txtTlp',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtEmail = {
		afterLabelTextTpl: required,
		allowBlank: false,
		fieldLabel: 'Email',
		fieldStyle: 'background-color: #eee; background-image: none; text-transform: uppercase',
		id: 'txtEmail',
		name: 'txtEmail',
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
				url: 'setupiddokter/CekSimpan',
				params: {
					'fs_nm_dokter': Ext.getCmp('txtNama').getValue(),
					'fs_alamat': Ext.getCmp('txtAlamat').getValue(),
					'fs_kota': Ext.getCmp('txtKota').getValue(),
					'fs_tlp': Ext.getCmp('txtTlp').getValue(),
					'fs_email': Ext.getCmp('txtEmail').getValue()
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
			url: 'setupiddokter/Simpan',
			params: {
				'fs_nm_dokter': Ext.getCmp('txtNama').getValue(),
				'fs_alamat': Ext.getCmp('txtAlamat').getValue(),
				'fs_kota': Ext.getCmp('txtKota').getValue(),
				'fs_tlp': Ext.getCmp('txtTlp').getValue(),
				'fs_email': Ext.getCmp('txtEmail').getValue(),
				'fd_simpan': Ext.Date.format(new Date(), 'Y-m-d H:i:s')
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
				fnSimpan2();
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
			url: 'setupiddokter/Simpan2',
			params: {
				'fs_nm_dokter': Ext.getCmp('txtNama').getValue(),
				'fs_alamat': Ext.getCmp('txtAlamat').getValue(),
				'fs_kota': Ext.getCmp('txtKota').getValue(),
				'fs_tlp': Ext.getCmp('txtTlp').getValue(),
				'fs_email': Ext.getCmp('txtEmail').getValue(),
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

	var frmSetupIdDokter = Ext.create('Ext.form.Panel', {
		border: false,
		floating: false,
		frame: true,
		region: 'center',
		title: 'Identitas Dokter',
		width: 550,
		items: [{
			fieldDefaults: {
				anchor: '100%',
				labelAlign: 'right',
				labelSeparator: '',
				labelWidth: 80,
				msgTarget: 'side'
			},
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [
				txtNama,
				txtAlamat,
				txtKota,
				txtTlp,
				txtEmail
			]
		}],
		buttons: [{
			iconCls: 'icon-save',
			text: 'Simpan',
			handler: fnCekSimpan
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Silakan tunggu...',
		target: frmSetupIdDokter
	});

	frmSetupIdDokter.render(Ext.getBody());
	Ext.get('loading').destroy();
});