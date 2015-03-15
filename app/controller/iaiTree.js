/**
 * Kontroler testowego zadania dla IAI
 * Definiuje wykonywane akcje   
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2015 by jzbikowski@apis.com.pl
 * @package iaiTree
 */

Ext.define ('app.controller.iaiTree', {
	
	// Akcja dodająca nowy element do drzewa
	itemAdd 	: new Ext.Action ({
		iconCls	: 'add',
		text	: 'Dodaj',
		tooltip	: 'Dodaj nową pozycję',
		disabled: true,
		handler	: function (action, opts) {
			
			var tree = Ext.getCmp('iaiTreeView');
			var node = tree.getSelected ();
			var last = node.childNodes.length;
			var rank = last ? node.getChildAt (last-1).get ('rank')+1 : 1;
			var name = node.isRoot () ? 'element #' : node.get('text');

			Ext.Msg.prompt (node.get('text')+' - nowy potomek', 'Nazwa:', function (btn, name) {
				if (btn == 'ok') {
					node.set ('leaf',0);
					node.appendChild ({
						leaf	: 1,
						rank	: rank,
						text	: name
					});
					node.expand ();
					App.getStore ('iaiTree').sync ();
				}
			},this, false, name+rank);
		}
	}),
				
	// Akcja dodająca nowy element do drzewa
	itemRename 	: new Ext.Action ({
		iconCls	: 'edit',
		text	: 'Zmień',
		tooltip	: 'Zmień nazwę zaznaczonej pozycji',
		disabled: true,
		handler	: function (action, opts) {
			
			var tree = Ext.getCmp('iaiTreeView');
			var node = tree.getSelected ();
			var name = node.get('text');
			
			if (!node.get ('id'))
				return;
				
			Ext.Msg.prompt (name, 'Zmiana nazwy:', function (btn, name) {
				if (btn == 'ok') {
					node.set ('text',name);
					App.getStore ('iaiTree').sync ();
				}
			},this, false, name);
		}
	}),
				
	// Akcja usuwająca wybrany element z drzewa
	itemDel 	: new Ext.Action ({
		iconCls	: 'del',
		text	: 'Usuń',
		tooltip	: 'Usuń zaznaczoną pozycję',
		disabled: true,
		handler	: function (action, opts) {
			var tree = Ext.getCmp('iaiTreeView');
			var node = tree.getSelected ();
			Ext.Msg.confirm('Usuwanie','Na pewno usunąć '+node.get ('text'), function (btn) {
				if (btn == 'yes') {
					node.remove ();
					App.getStore ('iaiTree').sync ();
				}
			});
		}
	}),
	
	// Akcja przeładowująca drzewo
	treeRefresh 	: new Ext.Action ({
		iconCls	: 'refresh',
		text	: 'Odśwież',
		tooltip	: 'Ponownie wczytaj drzewo',
		disabled: false,
		handler	: function (action, opts) {
			App.getStore ('iaiTree').load ();
		}
	}),
	
	updateControls	: function () {
		
		var tree = Ext.getCmp('iaiTreeView');
		var node = tree.getSelected ();
		this.itemAdd.setDisabled (false);
		this.itemDel.setDisabled (node.get ('id')==0);
		this.itemRename.setDisabled (node.get ('id')==0);
	},

	// Funkcja przeliczająca kolejność elementów gałęziach
	// Wywoływana po przeniesieniu elementu	
	rankRecalc: function (node) {
		var rank = 0;
		for (var i=0; i<node.childNodes.length; i++) {
			var child = node.childNodes[i];
			rank++;							
			if (child.get ('rank')!=rank) {
				child.set ('rank',rank);
			}
		}
		App.getStore ('iaiTree').sync ();
	},

	initComponent	: function () {
		var me = this;
		me.callParent (arguments);
	}

});
