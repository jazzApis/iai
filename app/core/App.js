/**
 * Bazowa definicja modułu, z której powinny dziedziczyć pozostałe moduły
 * W przypadku jednomodułowej aplikacji zupełnie zbędne  
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @copyright (c)2015 by jzbikowski@apis.com.pl
 * @package iaiTree
 */

Ext.define ('app.core.App', {

	requires	: [
		'app.core.Patches'
	],

	mixins: {
		observable: 'Ext.util.Observable'
	},

	constructor: function (config) {

		Ext.create ('app.core.Patches');

		var me = this;

		me.mixins.observable.constructor.call (this, config);

		if (Ext.isReady) {
			Ext.Function.defer (me.init, 10, me);
		} else {
			Ext.onReady (me.init, me);
		}

		me.callParent (arguments);
	},

	init: function () {

		var me = this;

		Ext.QuickTips.init ();

		me.store = {};
		me.controller = {};
		me.view = {};

		for (var item in app.store) {
			me.store[item] = Ext.create ('app.store.'+item);
			me.store[item].proxy.url = me.callMethod(me.store[item].proxy.url);
			for (var action in me.store[item].proxy.api) {
				me.store[item].proxy.api[action] = me.callMethod (me.store[item].proxy.api[action]);
			}
		};

		for (var item in app.controller) {
			me.controller[item] = Ext.create ('app.controller.'+item);
		};

		for (var item in app.view) {
			me.view[item] = Ext.create ('app.view.'+item);
		};
	},

	getStore	: function (name) {
		return this.store[name];
	},

	getController	: function (name) {
		return this.controller[name];
	},

	getView	: function (name) {
		return this.view[name];
	},

	callMethod	: function (action) {
		return this.url+'php/index.php?module='+this.module+'&action='+action;
	}, 
	
	call : function (action, params, callback) {
		
		Ext.Ajax.request ({
			scope		: this,
			url			: this.callMethod(action),
			params		: params,
			method		: 'POST',
			timeout 	: 12000,
			callback	: function (opts, success, response) {
				var result = response && response.responseText && Ext.decode (response.responseText);
				callback (result);
			}
		});
	}
});