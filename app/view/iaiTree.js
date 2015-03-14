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
	initComponent	: function () {

		var me = this;
		var ctrl = App.getController('iaiTree');

		me.callParent (arguments);

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
				ctrl.itemAdd,
				ctrl.itemDel,
				'->',
				ctrl.treeRefresh
			],
			
			// menu kontekstowe
			popUp	: new Ext.menu.Menu ({
				items   : [
					ctrl.itemAdd,
					ctrl.itemDel,
					'-',
					ctrl.treeRefresh
				]
			}),
			
			getSelected: function () {
				var selected = this.getSelectionModel ().getSelection ();
				return selected.length?selected[0]:this.getRootNode ();
			},
			
			listeners   : {

				select  : function (model, rec, idx, opts) {
					ctrl.itemAdd.setDisabled (false);
					ctrl.itemDel.setDisabled (rec.get ('id')==0);
				},

				itemContextMenu : function (view, record, item, index, e, eOpts) {
					e.preventDefault ();
					if (this.popUp) this.popUp.showAt (e.getXY ());
				},

				itemmove  : function (node, oldParent, newParent, index, eOpts) {
					node.set ('pi',newParent.get ('id'));
					ctrl.rankRecalc (oldParent);
					if (oldParent != newParent)
						ctrl.rankRecalc (newParent);
					me.treeView.getStore ().sync ();
				},

				beforeitemmove  : function (node, oldParent, newParent, index, eOpts) {
					return true;
				},

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
