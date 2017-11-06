Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX.concat('/statusbar'));

Ext.require('Ext.ux.StatusBar');

Ext.onReady(function() {
	Ext.QuickTips.init();

	function trim(text) {
		return text.replace(/^\s+|\s+$/gm, '');
	}

	var vTempo;
	var vWarna = '';

	var txtTgl = Ext.create('Ext.toolbar.TextItem', {
		text: Ext.Date.format(new Date(), 'l, d F Y')
	});

	var txtJam = Ext.create('Ext.toolbar.TextItem', {
		text: Ext.Date.format(new Date(), 'H:i')
	});

	function buatMenu() {
		var grupTree = Ext.create('Ext.data.TreeStore', {
			autoLoad: true,
			proxy: {
				actionMethods: {
					read: 'POST'
			},
			reader: {
				type: 'json'
			},
				type: 'ajax',
				url: 'mainmenu/AmbilNodes'
			},
			root: {
				expanded: true
			}
		});
		
		var cmdButton = Ext.create('Ext.Button', {
			id: 'cmdWin',
			text: 'Open in new window',
			listeners : {
				click : function() {
					var aktifTab = tabPanel.getActiveTab();
					if (aktifTab !== null) {
						var win = window.open(aktifTab.itemId, '_blank');
						win.focus();
					}
				}
			}
		});
		
		var tabPanel = Ext.create('Ext.tab.Panel', {
			activeItem: 0,
			bodyStyle: 'background-color: '.concat(gBasePanel),
			border: false,
			defaults: {
				autoScroll: true
			},
			deferredRender: true,
			enableTabScroll: true,
			id: 'isipanel',
			margins: '2 5 5 0',
			layout: 'fit',
			layoutOnTabChange: true,
			plain: false,
			region: 'center',
			resizeTabs: true,
			xtype: 'tabpanel',
			items: []
		});
		
		var tbPanel = Ext.create('Ext.panel.Panel', {
			id: 'contentPanel',
			layout: 'border',
			region: 'center',
			items: [
				tabPanel
			],
			bbar: Ext.create('Ext.ux.StatusBar', {
				defaultText: 'Hak Cipta &copy; 2014 - '+ Ext.Date.format(new Date(), 'Y') +' ~ DokterPraktek Team',
				items: ['-',
					Ext.create('Ext.toolbar.TextItem', {
						text: vPetugas.toUpperCase()
					}), '-',
					txtTgl, '-', txtJam
				]
			})
		});
		
		var treePanel = Ext.create('Ext.tree.Panel', {
			autoScroll: true,
			border: false,
			collapsible: true,
			height: '93%',
			hideHeaders: true,
			id: 'drmenu',
			margins: '2 0 5 5',
			region: 'west',
			resizable: false,
			rootVisible: false,
			rowLines: true,
			scroll: true,
			store: grupTree,
			title: 'Menu Utama DokterPraktek',
			width: 250,
			bbar: Ext.create('Ext.ux.StatusBar', {
				defaultText: 'Tgl Jatuh Tempo : '.concat(Ext.Date.format(vTempo, 'd F Y')),
				iconCls: vWarna
			}),
			tbar: [{
				iconCls: 'icon-delete',
				text: 'Perluas',
				handler: function() {
					treePanel.expandAll();
				}
			},{
				iconCls: 'icon-add',
				text: 'Tutup',
				handler: function() {
					treePanel.collapseAll();
				}
			},{
				iconCls: 'icon-logout',
				text: 'Keluar',
				handler: fnLogout
			}],
			listeners: {
				itemclick: function(view, record, item, index, evt, eOpts) {
					// get the click tree node's text and ID attribute values.
					var xNodeText = record.get('text');
					var xIdTab = record.get('id');
					var xArrId = xIdTab.split('|');
					var xLeafTab = record.get('leaf');
					
					if (xLeafTab === false) {
						// Ext.MessageBox.alert('Tree Folder Clicked', 'You clicked on tree folder "' + xNodeText + '"');
						return;
					}
					
					// get a reference to the target Tab Panel.
					var tabPanel = Ext.ComponentQuery.query('viewport tabpanel')[0];
					
					// does the tab already exist?  Note the use of the '#' symbol to indicate
					// your looking for an object with the specified itemId.
					var tab = tabPanel.child('#' + xArrId[1]);
					
					// if tab not already present, create it and add to tab panel.
					if (tab === null && trim(xArrId[1]) !== '') {
						tab = Ext.create('Ext.Component', {
							autoEl: {
								src: xArrId[1],
								tag: 'iframe'
							},
							closable: true,
							height: '100%',
							itemId: trim(xArrId[1]),
							renderto: 'tabPanel',
							title: trim(xNodeText),
							xtype: 'component'
						});
						tabPanel.add(tab);
						tabPanel.doLayout();
					}
					tabPanel.setActiveTab(tab);
				}
			},
			viewConfig: {
				getRowClass: function() {
					return 'rowwrap';
				},
				stripeRows: true
			}
		});
		
		function fnLogout() {
			Ext.Ajax.request({
				method: 'POST',
				url: 'login/Logout',
				success: function() {
					window.location = 'login';
				}
			});
		}
		
		Ext.TaskManager.start({
			interval: 1000,
			run: function() {
				Ext.fly(txtTgl.getEl()).update(Ext.Date.format(new Date(), 'l, d F Y'));
				Ext.fly(txtJam.getEl()).update(Ext.Date.format(new Date(), 'H:i'));
			}
		});
		
		Ext.create('Ext.container.Viewport', {
			layout: 'border',
			renderTo: Ext.getBody(),
			items: [{
				height: 2,
				id: 'header',
				region: 'north',
				xtype: 'box'
			},
				treePanel, tbPanel
			]
		});
	}

	Ext.Ajax.request({
		method: 'POST',
		url: 'mainmenu/AmbilDefa',
		success: function(response, opts) {
			var xText = Ext.decode(response.responseText);
			
			if (xText.sukses === true) {
				vPetugas = xText.petugas;
				vTempo = new Date(xText.tgltempo);
				
				if (xText.tempo == 'KUNING') {
					vWarna = 'x-status-yellow';
				}
				else if (xText.tempo == 'GO') {
					vWarna = 'x-status-green';
				}
				else {
					vWarna = 'x-status-red';
				}
				
				buatMenu();
			}
		},
		failure: function(response, opts) {
			var xText = Ext.decode(response.responseText);
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.MessageBox.INFO,
				message: 'Tampil nilai default Gagal, Koneksi Gagal!!',
				title: 'DokterPraktek'
			});
		}
	});

	Ext.get('loading').destroy();
});