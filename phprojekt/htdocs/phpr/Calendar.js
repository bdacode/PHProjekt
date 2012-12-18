define([
    'dojo/_base/declare',
    'dojo/_base/array',
    'dojo/dom-class',
    'dojo/date',
    'dojo/date/locale',
    'dojo/when',
    'dojo/date/stamp',
    'dijit/Calendar',
    'phpr/Api',
    'phpr/Timehelper'
], function(declare, array, clazz, ddate, locale, when, stamp, Calendar, phpr, timehelper) {
    var specialDayCache = {};

    function specialDays(month, fun) {
        var key = stamp.toISOString(month, {selector: 'date'});

        if (!specialDayCache.hasOwnProperty(key)) {
            specialDayCache[key] = phpr.getData(
                'index.php/Calendar2/index/jsonHolidays',
                {
                    query: {
                        'start': stamp.toISOString(month, {selector: 'date'}),
                        'end': stamp.toISOString(ddate.add(month, 'month', 1), {selector: 'date'})
                    }
                }
            ).then(
                function(data) {
                    specialDayCache[key] = data;
                    fun(data);
                }, function(error) {
                    debugger;
                }
            );
        } else {
            when(specialDayCache[month], fun);
        }
    }

    return declare([Calendar], {
        _populateGrid: function() {
            this.inherited(arguments);
            var node;
            var month = this._currentMonth();

            for (var timestamp in this._date2cell) {
                if (this._date2cell.hasOwnProperty(timestamp)) {
                    node = this._date2cell[timestamp];
                    var date = new this.dateClassObj(node.dijitDateValue);
                    if (locale.isWeekend(date) && date.getMonth() === month.getMonth()) {
                        clazz.add(node, 'weekend');
                    }

                    if (ddate.compare(new Date(), date, 'date') === 0) {
                        clazz.add(node, 'today');
                    }
                }
            }
            this._highlightSpecialDays();
        },

        _highlightSpecialDays: function() {
            specialDays(this._currentMonth(), dojo.hitch(this, function(data) {
                var byDate = {};
                array.forEach(data, function(specialDay) {
                    var d = timehelper.datetimeToJsDate(specialDay.date);
                    byDate[stamp.toISOString(d, {selector: 'date'})] = specialDay;
                });

                for (var timestamp in this._date2cell) {
                    if (this._date2cell.hasOwnProperty(timestamp)) {
                        var d = new Date(parseInt(timestamp, 10));
                        if (byDate.hasOwnProperty(stamp.toISOString(d, {selector: 'date'}))) {
                            clazz.add(this._date2cell[timestamp], 'specialDay');
                        }
                    }
                }
            }));
        },

        _currentMonth: function() {
            var month = new this.dateClassObj(this.currentFocus);
            month.setDate(1);
            return month;
        }
    });
});