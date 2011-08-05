/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/


if(!dojo._hasResource["dojox.grid.enhanced.plugins.Exporter"])dojo._hasResource["dojox.grid.enhanced.plugins.Exporter"]=!0,dojo.provide("dojox.grid.enhanced.plugins.Exporter"),dojo.require("dojox.grid.enhanced._Plugin"),dojo.require("dojox.grid._RowSelector"),dojo.declare("dojox.grid.enhanced.plugins.Exporter",dojox.grid.enhanced._Plugin,{name:"exporter",constructor:function(b,a){this.grid=b;this.formatter=a&&dojo.isObject(a)&&a.exportFormatter;this._mixinGrid()},_mixinGrid:function(){var b=this.grid;
b.exportTo=dojo.hitch(this,this.exportTo);b.exportGrid=dojo.hitch(this,this.exportGrid);b.exportSelected=dojo.hitch(this,this.exportSelected);b.setExportFormatter=dojo.hitch(this,this.setExportFormatter)},setExportFormatter:function(b){this.formatter=b},exportGrid:function(b,a,c){dojo.isFunction(a)&&(c=a,a={});if(dojo.isString(b)&&dojo.isFunction(c)){var a=a||{},d=this.grid,e=this,g=this._getExportWriter(b,a.writerArgs),a=a.fetchArgs&&dojo.isObject(a.fetchArgs)?a.fetchArgs:{},h=a.onComplete;if(d.store)a.onComplete=
function(a,b){h&&h(a,b);c(e._goThroughGridData(a,g))},a.sort=a.sort||d.getSortProps(),d._storeLayerFetch(a);else{for(var b=a.start||0,a=a.count||-1,i=[],f=b;f!=b+a&&f<d.rowCount;++f)i.push(d.getItem(f));c(this._goThroughGridData(i,g))}}},exportSelected:function(b,a){if(!dojo.isString(b))return"";var c=this._getExportWriter(b,a);return this._goThroughGridData(this.grid.selection.getSelected(),c)},_buildRow:function(b,a){var c=this;dojo.forEach(b._views,function(d,e){b.view=d;b.viewIdx=e;a.beforeView(b)&&
(dojo.forEach(d.structure.cells,function(d,e){b.subrow=d;b.subrowIdx=e;a.beforeSubrow(b)&&(dojo.forEach(d,function(d,e){b.isHeader&&c._isSpecialCol(d)&&b.spCols.push(d.index);b.cell=d;b.cellIdx=e;a.handleCell(b)}),a.afterSubrow(b))}),a.afterView(b))})},_goThroughGridData:function(b,a){var c=this.grid,d=dojo.filter(c.views.views,function(a){return!(a instanceof dojox.grid._RowSelector)}),e={grid:c,isHeader:!0,spCols:[],_views:d,colOffset:d.length<c.views.views.length?-1:0};a.beforeHeader(c)&&(this._buildRow(e,
a),a.afterHeader());e.isHeader=!1;a.beforeContent(b)&&(dojo.forEach(b,function(b,c){e.row=b;e.rowIdx=c;a.beforeContentRow(e)&&(this._buildRow(e,a),a.afterContentRow(e))},this),a.afterContent());return a.toString()},_isSpecialCol:function(b){return b.isRowSelector||b instanceof dojox.grid.cells.RowIndex},_getExportWriter:function(b,a){var c,d;c=dojox.grid.enhanced.plugins.Exporter;if(c.writerNames)if(c=c.writerNames[b.toLowerCase()],d=dojo.getObject(c))return c=new d(a),c.formatter=this.formatter,
c;else throw Error('Please make sure class "'+c+'" is required.');throw Error('The writer for "'+b+'" has not been registered.');}}),dojox.grid.enhanced.plugins.Exporter.registerWriter=function(b,a){var c=dojox.grid.enhanced.plugins.Exporter;c.writerNames=c.writerNames||{};c.writerNames[b]=a},dojox.grid.EnhancedGrid.registerPlugin(dojox.grid.enhanced.plugins.Exporter);