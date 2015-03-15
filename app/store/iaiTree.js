/**
 * Tabela danych testowego zadania dla IAI
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @extends Ext.data.TreeStore
 * @copyright (c)2015 by jzbikowski@apis.com.pl
 * @package iaiTree
 */

Ext.define ('app.store.iaiTree', {

	extend	: 'Ext.data.TreeStore',
	model	: 'app.model.iaiTree',
	storeId	: 'iaiTree',
	autoLoad: false,
	autoSync: false,
	
	proxy	: {
		type	: 'ajax',
		api	: {
			read    : 'load',
			create  : 'save',
			update  : 'save',
			destroy : 'remove'
		}
	},
	
	root	: {
		id	: 0,
		rank	: 0,
		text	: 'Korzeń drzewa IAI',
		expanded: false
	},
	
	listeners	: {
		load	: function ( store, node, records, successful, eOpts ) {
			if (!successful)
				Ext.MessageBox.alert ('Problem','Błąd odczytu danych');
		}
	}
});
