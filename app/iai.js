/**
 * Launcher (aplikacja?) testowego zadania dla IAI
 * Ładuje wymagane elementy i uruchamia główne okno iaiTree  
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @extends app.core.App
 * @copyright (c)2015 by jzbikowski@apis.com.pl
 * @package iaiTree
 */

Ext.define ('app.iai', {

	extend		: 'app.core.App',

	requires	: [
		'app.controller.iaiTree',
		'app.model.iaiTree',
		'app.store.iaiTree',
		'app.view.iaiTree'
	],

	init	: function (config) {

		var me = this;
		me.module = 'iai';
		me.callParent ();
		me.call ('check', {}, function (result) {
			if (result.success)
				me.getView('iaiTree').show ();
			else
				Ext.MessageBox.alert ('Błąd',result.msg);
		});
	},
	
	getTreeView	: function () {
		return Ext.getCmp ('iaiTreeView');
	}
});