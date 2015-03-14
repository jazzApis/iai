/**
 * Model danych testowego zadania dla IAI
 * ======================================================================= 
 * @author	Jarosław Żbikowski 
 * @version $Id$
 * @extends Ext.data.Model
 * @copyright (c)2015 by jzbikowski@apis.com.pl
 * @package iaiTree
 */

Ext.define ('app.model.iaiTree', {
	extend: 'Ext.data.Model',
	fields: 
		[{ name: 'id'   , defaultValue: 0    , type: 'int' }
		,{ name: 'pi'   , defaultValue: 0    , type: 'int' }
		,{ name: 'rank' , defaultValue: 0    , type: 'int' }
		,{ name: 'text' }
	]
});
