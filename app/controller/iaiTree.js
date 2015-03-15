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
			
			var ctrl = App.getController ('iaiTree');
			var tree = App.getTreeView ();
			var node = tree.getSelected ();
			var last = node.childNodes.length;
			var rank = last ? node.getChildAt (last-1).get ('rank')+1 : 1;
			var name = node.isRoot () ? 'element #' : node.get('text');

			Ext.Msg.prompt (node.get('text')+' - nowy potomek', 'Nazwa:', function (btn, text) {
				if (btn == 'ok' && ctrl.nameIsCorrect (text,'')) {
					node.set ('leaf',0);
					node.appendChild ({
						leaf	: 1,
						rank	: rank,
						text	: text
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
			
			var ctrl = App.getController ('iaiTree');
			var tree = App.getTreeView ();
			var node = tree.getSelected ();
			var name = node.get('text');
			
			if (!node.get ('id'))
				return;
				
			Ext.Msg.prompt (name, 'Zmiana nazwy:', function (btn, text) {
				if (btn == 'ok' && ctrl.nameIsCorrect (text,name)) {
					node.set ('text',text);
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
			
			var tree = App.getTreeView ();
			var node = tree.getSelected ();
			Ext.Msg.confirm('Usuwanie','Na pewno usunąć '+node.get ('text'), function (btn) {
				if (btn == 'yes') {
					node.remove ();
					App.getStore ('iaiTree').sync ();
				}
			});
		}
	}),
	
	// Przeładowująca drzewa 
	treeRefresh 	: new Ext.Action ({
		iconCls	: 'refresh',
		text	: 'Odśwież',
		tooltip	: 'Ponownie wczytaj drzewo',
		disabled: false,
		handler	: function (action, opts) {
			App.getStore ('iaiTree').load ();
		}
	}),

	// Sprawdzaniepoprawnosci i unikalnosci nazwy elementu
	nameIsCorrect	: function (name, oldName) {
		
		if (name=='') {
			Ext.MessageBox.alert ('Błąd','Elementy bez nazwy sa niedozwolone.');
		} else if (name == oldName) {
			// NOP - brak zmian 
		} else if (App.getStore ('iaiTree').getRootNode ().findChild ('text', name, true)) {
			Ext.MessageBox.alert ('Błąd','Element o nazwie "'+name+'" już istnieje.');
		} else {
			return true;
		}
		return false;
	},
		
	// Aktualizuje status elementów
	updateControls	: function () {
		
		var tree = Ext.getCmp('iaiTreeView');
		var node = tree.getSelected ();
		this.itemAdd.setDisabled (false);
		this.itemDel.setDisabled (node.get ('id')==0);
		this.itemRename.setDisabled (node.get ('id')==0);
	},

	// Funkcja przeliczająca kolejność elementów w gałęziach
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

	// Standardowa inicjacja komponentu
	initComponent	: function () {
		var me = this;
		me.callParent (arguments);
	}

});
