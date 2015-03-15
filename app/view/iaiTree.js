/**
 * Główne okno testowego zadania dla IAI
 * Jedynym elementem okna jest drzewo   
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @extends Ext.Window
 * @copyright (c)2015 by jzbikowski@apis.com.pl
 * @package iaiTree
 */

Ext.define ('app.view.iaiTree', {

	extend		: 'Ext.Window',
	id			: 'iaiTreePanel',
	title		: 'Zadanie testowe',
	useQuickTips: true,
	maximizable	: true,
	closable	: false,
	width		: 450,
	height		: 400,
	layout		: 'fit',
	items		: [],
	listeners	: {
		show	: function (window,eOpts) {
			this.treeView.getRootNode ().expand ();
		}
	},
	initComponent	: function () {

		var me = this;

		me.callParent (arguments);

		me.controller = App.getController('iaiTree');
		me.treeView = new Ext.tree.Panel ({
			
			id		: 'iaiTreeView',
			xtype	: 'treepanel',
			store	: 'iaiTree',
			rootVisible	: true,
			
			// włączenie drag & drop
			viewConfig	: {
				plugins	: {
					ptype		: 'treeviewdragdrop',
					dragText	: '{0} wybranych elemetów'
				}
			},		
				
			// toolbar
			tbar		: [
				me.controller.itemAdd,
				me.controller.itemDel,
				'-',
				me.controller.itemRename,
				'->',
				me.controller.treeRefresh
			],
			
			// menu kontekstowe
			popUp	: new Ext.menu.Menu ({
				items   : [
					me.controller.itemAdd,
					me.controller.itemDel,
					'-',
					me.controller.itemRename,
					'-',
					me.controller.treeRefresh
				]
			}),
			
			getSelected: function () {
				var selected = this.getSelectionModel ().getSelection ();
				return selected.length?selected[0]:this.getRootNode ();
			},
			
			listeners   : {

				select  : function (model, rec, idx, opts) {
					me.controller.updateControls ();
				},
				
				itemdblclick	: function ( view, record, item, index, e, eOpts ) {
					me.controller.itemRename.execute ();
				},
				
				itemContextMenu : function (view, record, item, index, e, eOpts) {
					e.preventDefault ();
					if (this.popUp) this.popUp.showAt (e.getXY ());
				},

				itemmove  : function (node, oldParent, newParent, index, eOpts) {
					me.controller.rankRecalc (oldParent);
					if (oldParent != newParent)
						me.controller.rankRecalc (newParent);
					me.treeView.getStore ().sync ();
				},

				beforeitemmove  : function (node, oldParent, newParent, index, eOpts) {
					return true;
				}
			}
		});

		/* Jeśli transfer nie jest fatalny to maskowanie nie jest niezbędne 
		me.treeView.getStore ().on ('beforeload',function( store, operation, eOpts ) {
			me.treeView.setLoading (true);
		});

		me.treeView.getStore ().on ('load',function ( store, node, records, successful, eOpts ) {
			me.treeView.setLoading (false);
		});
		*/

		me.add (me.treeView);
	}

});
