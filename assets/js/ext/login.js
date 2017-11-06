Ext.onReady(function() {
    Ext.QuickTips.init();

	var required = '<span style="color:red;font-weight:bold" data-qtip="Field ini wajib diisi">*</span>';

	var txtEmail = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '97.2%',
		fieldLabel: 'Email',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtEmail',
		maxLength: 50,
		name: 'txtEmail',
		value: '',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtPetugas = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '97.2%',
		fieldLabel: 'Kode Petugas',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtPetugas',
		name: 'txtPetugas',
		value: '',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtPassword = {	
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '97.2%',
		fieldLabel: 'Password',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtPassword',
		inputType: 'password',
		name: 'txtPassword',
		value: '',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	var txtTgl = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '70%',
		fieldLabel: 'Tanggal',
		format: 'dmY',
		hidden: true,
		id: 'txtTgl',
		maskRe: /[0-9-]/,
		minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -100),
		name: 'txtTgl',
		readOnly: true,
		value: new Date(),
		xtype: 'datefield'
	};

	var txtCaptcha = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '97.2%',
		fieldLabel: 'Ketik Captcha',
		fieldStyle: 'text-transform: uppercase;',
		id: 'txtCaptcha',
		maxLength: 25,
		name: 'txtCaptcha',
		value: '',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				field.setValue(newValue.toUpperCase());
			}
		}
	};

	Ext.define('Image', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'src', type: 'string'}
		]
	});

	var dataImg = Ext.create('Ext.data.Store', {
		autoLoad: true,
		id: 'imagesStore',
		model: 'Image',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'login/BuatCaptcha'
		}
	});

	var imageTpl = new Ext.XTemplate(
		'<tpl for=".">',
			'<div style="margin-bottom: 10px;" class="thumb-wrap">',
				'<img src="{src}" />',
				'<br/><span>{caption}</span>',
			'</div>',
		'</tpl>'
	);

	var gambar = Ext.create('Ext.view.View', {
		itemSelector: 'div.thumb-wrap',
		store: dataImg,
		tpl: imageTpl
	});

	var cmdRefresh = {
		iconCls: 'icon-refresh',
		id: 'cmdRefresh',
		text: 'Refresh Captcha',
		xtype: 'button',
		handler: function() {
			dataImg.load();
		}
	};

	function fnReset() {
		Ext.getCmp('txtEmail').setValue('');
		Ext.getCmp('txtPetugas').setValue('');
		Ext.getCmp('txtPassword').setValue('');
		Ext.getCmp('txtTgl').setValue(Ext.Date.format(new Date(), 'dmY'));
	}

	function fnCek() {
		if (this.up('form').getForm().isValid()) {
			
			Ext.Ajax.on('beforerequest', vMask.show, vMask);
			Ext.Ajax.on('requestcomplete', vMask.hide, vMask);
			Ext.Ajax.on('requestexception', vMask.hide, vMask);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'login/CekText',
				params: {
					'fs_email': Ext.getCmp('txtEmail').getValue(),
					'fs_text': Ext.getCmp('txtCaptcha').getValue()
				},
				success: function(response, opts) {
					var xText = Ext.decode(response.responseText);
					
					if (xText.sukses === false) {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							message: xText.hasil,
							title: 'iClinic'
						});
					}
					else {
						fnMasuk();
					}
				},
				failure: function(response, opts) {
					var xText = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: 'Cek Gagal, Koneksi Gagal!!',
						title: 'iClinic'
					});
					vMask.hide();
				}
			});
		}
	}

	function fnMasuk() {
		var xForm = Ext.getCmp('frmLogin').getForm();
		if (xForm.isValid()) {
			xForm.submit({
			waitTitle: 'Menghubungkan',
			waitmessage: 'Validasi Petugas dan Password...',
			success: function() {
					window.location = 'mainmenu';
				},
			failure: function(form, action) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						message: 'Masuk Failed, ' + action.response.responseText,
						title: 'iClinic'
					});
					Ext.getCmp('winLogin').body.unmask();
				}
			});
		}
	}

	var winLogin = Ext.create('Ext.window.Window', {
		closable: false,
		draggable: true,
		height: 300,
		id: 'winLogin',
		layout: 'border',
		name: 'winLogin',
		plain: true,
		resizable: false,
		title: 'Login',
		width: 360,
		items: [
			Ext.create('Ext.form.Panel', {
				bodyStyle: 'padding:15px 35px;',
				border: false,
				fieldDefaults: {
					labelAlign: 'right',
					labelSeparator: '',
					labelWidth: 90,
					msgTarget: 'side'
				},
				frame: false,
				id: 'frmLogin',
				method: 'POST',
				name: 'frmLogin',
				region: 'center',
				url: 'login/CekLogin',
				items:[
					txtEmail,
					txtPetugas,
					txtPassword,
					txtTgl,
					txtCaptcha,
					gambar,
					cmdRefresh
				],
				buttons: [{
					iconCls: 'icon-logout',
					text: 'Masuk',
					handler: fnCek
				},{
					iconCls: 'icon-reset',
					text: 'Reset',
					handler: fnReset
				}]
			})
		]
	});

	Ext.TaskManager.start({
		interval: 1000,
		run: function() {
			Ext.getCmp('txtTgl').setValue(Ext.Date.format(new Date(), 'dmY'));
		}
	});

	var vMask = new Ext.LoadMask(winLogin, {
		msg: 'Silakan tunggu...'
	});

	winLogin.show();
	Ext.get('loading').destroy();
});