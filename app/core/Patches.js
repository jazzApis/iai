/**
 * Poprawki, fix'y, aktualizacje
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @copyright	 (c)2014 by jzbikowski@apis.com.pl
 * @package	webDesk
 */

Ext.define ('app.core.Patches', {

	constructor: function (config) {

		var me = this;

		// Skopiowany rekord nie miał ustawionej flagi phantom (nowy rekord) 
		Ext.override (Ext.data.Model, {
			copy : function (newId) {
				var iNewRecord = this.callParent (arguments);
				iNewRecord.phantom = true;
				return iNewRecord;    
			}
		});
		
		// Czyszczenie flagi dirty formularza
		Ext.override (Ext.form.BasicForm, {
			clearDirty: function () {
				var i, it = this.getFields ().items, l = it.length, c;
				for (i = 0; i < l; i++) {
					c = it[i];
					c.originalValue = c.getValue ();
				}
			}
		});
				
		//Translacja tekstu ładowania 
		Ext.override (Ext.LoadMask, {
			msg		: 'Proszę czekać...'
		});
				
		//Translacja tekstu ładowania 
		Ext.override (Ext.grid.View, {
			loadingText	: 'Proszę czekać...'
		});
				
		//Translacja tekstu ładowania 
		Ext.override (Ext.view.AbstractView, {
			loadingText	: 'Proszę czekać...'
		});
				
		//Translacja tekstu ładowania 
		Ext.override (Ext.form.field.Text, {
			blankText	: 'To pole jest wymagane',
			maxLengthText	: 'Maksymalna ilość znaków tego pola wynosi {0}'
		});
		
		//Translacja nazw przycisków 
		Ext.override (Ext.MessageBox, {
    		buttonText	: {
				ok	: 'Ok',
				yes	: 'Tak',
				no	: 'Nie',
				cancel	: 'Anuluj'
			},
			titleText	: {
				confirm	: 'Potwierdź',
				prompt	: 'Wprowadź',
				wait	: 'Czekaj...',
				alert	: 'Uwaga!'
			}
		});	
		
		String.prototype.format = function() {
			var formatted = this;
			for (var i = 0; i < arguments.length; i++) {
				var regexp = new RegExp('\\{'+i+'\\}', 'gi');
				formatted = formatted.replace(regexp, arguments[i]);
			}
			return formatted;
		};			
		
		decodeURL = function(str) {
			var obj =  {};
			if (str) {
				var arr = str.split ('&');
				for (var i = 0; i < arr.length; ++i) {
					var split = arr[i].split ('=');
					obj[decodeURIComponent (split[0])] = decodeURIComponent (split[1]);
				} 
			}
			return obj;
		};
		
		encodeURL = function(obj) {
			var str = [];
			for (var p in obj) {
				if (obj.hasOwnProperty (p)) {
					str.push (encodeURIComponent (p) + "=" + encodeURIComponent (obj[p]));
				}
			}
			return str.join ("&");        			
		};
		
	}
});
