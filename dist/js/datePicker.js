/**
 * Created by Arafat Hossain on 1/1/2016.
 */
(function ($) {
    var constent = {
        month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        leapYear: [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
        normalYear: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
    }
    var i = 0;
    var pluginName = 'datePicker'
    $.fn[pluginName] = function (option) {
        this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                    new Plugin(this,option));
            }
        })
    }
    function Plugin(element,option) {
        this.element = element;
        this.name = pluginName;
        this.option = {
            currentView: 'week',
            weekDay: 0,
            currentMonth: 0,
            currentYear: 0,
            currentDay: 1,
            yearMonth: [],
            yearRange: [],
            element: "datepicker-"+(i),
            ppp:"date-picker-parent"+(i++),
            today:0,
            todayMonth:0,
            todayYear:0,
            defaultValue:moment(),
            dateFormat:'DD-MMM-YYYY'
        }
        $.extend(this.option,option);
        this.init()
    }

    Plugin.prototype.init = function () {
        var _self = this;
        var b = 'defaultValue'
        $('body').append(_self.getCalenderView());
        //$(this.element).attr('placeholder','YYYY-MM-DD')
        $(this.element).attr('data-target','#'+_self.option.element)
        $(this.element).attr('id',_self.option.ppp)
        _self.addEventListener();
        _self.initCalender()
    }
    Plugin.prototype.initCalender = function() {
        var date = new Date();
        var _self = this;
        //alert(_self.option.defaultValue)
        _self.option.today = date.getDate();
        _self.option.todayMonth = date.getMonth();
        _self.option.todayYear = date.getFullYear();
        var d = date.getFullYear()+"-"+(date.getMonth()+1<10?"0"+(date.getMonth()+1):date.getMonth()+1)+"-"+date.getDate();
        if(_self.option.defaultValue){
            //alert('asdadadads')
            if(moment(_self.option.defaultValue).isValid())$(this.element).val(moment(_self.option.defaultValue).format(_self.option.dateFormat)).trigger('input')
            else $(this.element).val('')
        }
        var d = _self.getCurrentDate();
        _self.option.currentDay = d.getDate();
        _self.option.currentMonth = d.getMonth();
        _self.option.currentYear = d.getFullYear();
        _self.option.weekDay = d.getDay();
        _self.option.currentView = 'week';
        $('#'+_self.option.element+' .month-year-view').text(constent.month[_self.option.currentMonth] + ", " + _self.option.currentYear)
        _self.setYearMonth(_self.option.currentYear)
        _self.makeCalender(_self.option.currentMonth)
        $('#'+_self.option.element+' .week-view').removeClass('zoomOut not-visible zoomIn')
        $('#'+_self.option.element+' .month-view').addClass('not-visible')
        $('#'+_self.option.element+' .year-view').addClass('not-visible')

    }
    Plugin.prototype.getCurrentDate = function() {
        var d = new Date();
        d.setDate(1);
        return d;
    }
    Plugin.prototype.getCurrentDate = function() {
        var d = new Date();
        d.setDate(1);
        return d;
    }
    Plugin.prototype.getModifiedDate = function(year, month, day) {
        var d = new Date();
        d.setFullYear(year, month, day)
        return d;
    }

    Plugin.prototype.getYearRange = function(year) {
        var _self = this;
        var s = year % 10 > 0 ? year - (year % 10) - 1 : year - 1
        var e = year + (10 - (year % 10))
        //alert(s)
        _self.option.yearRange = []
        for (var i = s; i <= e; i++) {
            _self.option.yearRange.push(i);
        }
        _self.option.yearRange.forEach(function (item, index) {
            $('#'+_self.option.element+' .year-view li').eq(index).text(item)
        })
    }

    Plugin.prototype.isLeapYear = function(year) {
        if (year % 4 == 0 || year % 400 == 0) {
            return true
        }
        else return false;
    }

    Plugin.prototype.setYearMonth = function(year) {
        var _self = this;
        _self.option.yearMonth = _self.isLeapYear(year) ? constent.leapYear : constent.normalYear;
    }

    Plugin.prototype.getCalenderView = function() {
        var cal = '<div class="date-picker not-visible" id="'+this.option.element+'">' +
            '<div class="date-header">' +
            '<a href="#" id="previous" class="pull-left">' +
            '<span class="fa fa-arrow-left"></span></a>' +

            '<div class="pull-left month-year-view">December,2015</div>' +
            '<a href="#" id="next" class="pull-right">' +
            '<span class="fa fa-arrow-right"></span></a>' +
            '</div>' +

            '<div class="date-picker-body">' +
            '<div class="week-view  animated">' +
            '<ul class="date-picker-row week-header">' +
            '<li class="date-col">SU</li>' +
            '<li class="date-col">MO</li>' +
            '<li class="date-col">TU</li>' +
            '<li class="date-col">WE</li>' +
            '<li class="date-col">TH</li>' +
            '<li class="date-col">FR</li>' +
            '<li class="date-col">SA</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="date-col">' +
            '1' +
            '</li>' +
            '<li class="date-col">' +
            '2' +
            '</li>' +
            '<li class="date-col">' +
            '3' +
            '</li>' +
            '<li class="date-col">' +
            '4' +
            '</li>' +
            '<li class="date-col">' +
            '5' +
            '</li>' +
            '<li class="date-col">' +
            '6' +
            '</li>' +
            '<li class="date-col">' +
            '7' +
            '</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="date-col">' +
            '8' +
            '</li>' +
            '<li class="date-col">' +
            '9' +
            '</li>' +
            '<li class="date-col">' +
            '10' +
            '</li>' +
            '<li class="date-col">' +
            '11' +
            '</li>' +
            '<li class="date-col">' +
            '12' +
            '</li>' +
            '<li class="date-col">' +
            '13' +
            '</li>' +
            '<li class="date-col">' +
            '14' +
            '</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="date-col">' +
            '15' +
            '</li>' +
            '<li class="date-col">' +
            '16' +
            '</li>' +
            '<li class="date-col">' +
            '17' +
            '</li>' +
            '<li class="date-col">' +
            '18' +
            '</li>' +
            '<li class="date-col">' +
            '19' +
            '</li>' +
            '<li class="date-col">' +
            '20' +
            '</li>' +
            '<li class="date-col">' +
            '21' +
            '</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="date-col">' +
            '22' +
            '</li>' +
            '<li class="date-col">' +
            '23' +
            '</li>' +
            '<li class="date-col">' +
            '24' +
            '</li>' +
            '<li class="date-col">' +
            '25' +
            '</li>' +
            '<li class="date-col">' +
            '26' +
            '</li>' +
            '<li class="date-col">' +
            '27' +
            '</li>' +
            '<li class="date-col">' +
            '28' +
            '</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="date-col">' +
            '29' +
            '</li>' +
            '<li class="date-col">' +
            '30' +
            '</li>' +
            '<li class="date-col">' +
            '31' +
            '</li>' +
            '<li class="date-col">' +
            '</li>' +
            '<li class="date-col">' +
            '</li>' +
            '<li class="date-col">' +
            '</li>' +
            '<li class="date-col"></li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="date-col">' +
            '29' +
            '</li>' +
            '<li class="date-col">' +
            '30' +
            '</li>' +
            '<li class="date-col">' +
            '31' +
            '</li>' +
            '<li class="date-col">' +
            '</li>' +
            '<li class="date-col">' +
            '</li>' +
            '<li class="date-col">' +
            '</li>' +
            '<li class="date-col"></li>' +
            '</ul>' +
            '</div>' +
            '<div class="month-view animated not-visible">' +
            '<ul class="date-picker-row">' +
            '<li class="month-col">Jan</li>' +
            '<li class="month-col">Feb</li>' +
            '<li class="month-col">Mar</li>' +
            '<li class="month-col">Apr</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="month-col">May</li>' +
            '<li class="month-col">Jun</li>' +
            '<li class="month-col">Jul</li>' +
            '<li class="month-col">Aug</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="month-col">Sep</li>' +
            '<li class="month-col">Oct</li>' +
            '<li class="month-col">Nov</li>' +
            '<li class="month-col">Dec</li>' +
            '</ul>' +
            '</div>' +
            '<div class="year-view animated not-visible">' +
            '<ul class="date-picker-row">' +
            '<li class="year-col">2009</li>' +
            '<li class="year-col">2010</li>' +
            '<li class="year-col">2011</li>' +
            '<li class="year-col">2012</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="year-col">2013</li>' +
            '<li class="year-col">2014</li>' +
            '<li class="year-col">2015</li>' +
            '<li class="year-col">2016</li>' +
            '</ul>' +
            '<ul class="date-picker-row">' +
            '<li class="year-col">2017</li>' +
            '<li class="year-col">2018</li>' +
            '<li class="year-col">2019</li>' +
            '<li class="year-col">2020</li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '</div>'
        return cal;
    }

    Plugin.prototype.executePrevious = function() {
        var _self = this;
        switch (_self.option.currentView) {
            case 'week':
                _self.option.currentMonth = _self.option.currentMonth - 1;
                if (_self.option.currentMonth < 0) {
                    _self.option.currentMonth = 11;
                    _self.option.currentYear = _self.option.currentYear - 1;
                }
                var d = _self.getModifiedDate(_self.option.currentYear, _self.option.currentMonth, 1)
                _self.option.weekDay = d.getDay();
                _self.makeCalender(_self.option.currentMonth)
                $('#'+_self.option.element+' .month-year-view').text(constent.month[_self.option.currentMonth] + ", " + _self.option.currentYear)
                break;
            case 'month':
                _self.option.currentYear = _self.option.currentYear - 1;
                var d = _self.getModifiedDate(_self.option.currentYear, _self.option.currentMonth, 1);
                _self.option.weekDay = d.getDay();
                _self.makeCalender(_self.option.currentMonth)
                $('#'+_self.option.element+' .month-year-view').text(_self.option.currentYear)
                break;
            case 'year':
                _self.getYearRange(_self.option.yearRange[1] - 10)
                $('#'+_self.option.element+' .month-year-view').text(_self.option.yearRange[1] + "-" + _self.option.yearRange[_self.option.yearRange.length - 2])
                break;
        }
    }

    Plugin.prototype.executeNext = function() {
        var _self = this;
        switch (_self.option.currentView) {
            case 'week':
                _self.option.currentMonth = _self.option.currentMonth + 1;
                if (_self.option.currentMonth > 11) {
                    _self.option.currentMonth = 0;
                    _self.option.currentYear = _self.option.currentYear + 1;
                }
                var d = _self.getModifiedDate(_self.option.currentYear, _self.option.currentMonth, 1);
                _self.option.weekDay = d.getDay();
                _self.makeCalender(_self.option.currentMonth)
                $( '#'+_self.option.element+' .month-year-view').text(constent.month[_self.option.currentMonth] + ", " + _self.option.currentYear)
                break;
            case 'month':
                _self.option.currentYear = _self.option.currentYear + 1;
                var d = _self.getModifiedDate(_self.option.currentYear, _self.option.currentMonth, 1);
                _self.option.weekDay = d.getDay();
                _self.makeCalender(_self.option.currentMonth)
                $('#'+_self.option.element+' .month-year-view').text(_self.option.currentYear)
                break;
            case 'year':
                _self.getYearRange(_self.option.yearRange[_self.option.yearRange.length - 1])
                $('#'+_self.option.element+' .month-year-view').text(_self.option.yearRange[1] + "-" + _self.option.yearRange[_self.option.yearRange.length - 2])
                break;

        }
    }

    Plugin.prototype.addEventListener = function() {
        var _self = this;
        $('#'+_self.option.element+' .month-year-view').on('click', function (e) {
            switch (_self.option.currentView) {
                case 'week':
                    $('#'+_self.option.element+' .month-view').removeClass('zoomOut not-visible').addClass('zoomIn')
                    $('.week-view').removeClass('zoomIn').addClass('zoomOut')
                    $('#'+_self.option.element+' .month-year-view').text(_self.option.currentYear)
                    _self.option.currentView = 'month';
                    break;
                case 'month':
                    $('#'+_self.option.element+' .year-view').removeClass('zoomOut not-visible').addClass('zoomIn')
                    $('#'+_self.option.element+' .month-view').removeClass('zoomIn').addClass('zoomOut')
                    _self.option.currentView = 'year';
                    _self.getYearRange(_self.option.currentYear);
                    $('#'+_self.option.element+' .month-year-view').text(_self.option.yearRange[1] + "-" + _self.option.yearRange[_self.option.yearRange.length - 2])
                    break;
            }
        })
        $('#'+_self.option.element+' .date-col,'+'#'+_self.option.element+' .month-col,'+'#'+_self.option.element+' .year-col').on('click', function (e) {
            switch (_self.option.currentView) {
                case 'month':
                    $('#'+_self.option.element+' .week-view').removeClass('zoomOut not-visible').addClass('zoomIn')
                    $('#'+_self.option.element+' .month-view').removeClass('zoomIn').addClass('zoomOut')
                    _self.option.currentMonth = $(this).parents('ul').index() * $(this).parents('ul').children().length + $(this).index()
                    var d = _self.getModifiedDate(_self.option.currentYear, _self.option.currentMonth, 1)
                    _self.option.weekDay = d.getDay()
                    _self.makeCalender(_self.option.currentMonth)
                    $('#'+_self.option.element+' .month-year-view').text(constent.month[_self.option.currentMonth] + ", " + _self.option.currentYear)
                    _self.option.currentView = 'week';
                    break;
                case 'year':
                    $('#'+_self.option.element+' .month-view').removeClass('zoomOut not-visible').addClass('zoomIn')
                    $('#'+_self.option.element+' .year-view').removeClass('zoomIn').addClass('zoomOut')
                    _self.option.currentView = 'month';
                    _self.option.currentYear = _self.option.yearRange[$(this).parents('ul').index() * $(this).parents('ul').children().length + $(this).index()]
                    _self.setYearMonth(_self.option.currentYear);
                    $('#'+_self.option.element+' .month-year-view').text(_self.option.currentYear)
                    break;
                case 'week':
                    if (!$(this).parents('ul').hasClass('week-header')) {
                        //alert($(this).text())
                        //$(_self.element).focus()
                        var date = _self.option.currentYear + "-" + (_self.option.currentMonth + 1 < 10 ? '0' + (_self.option.currentMonth + 1) : _self.option.currentMonth + 1) + "-" + (parseInt($(this).text().trim())<10?"0"+$(this).text():$(this).text());
                        //alert(date)
                        $(_self.element).val(moment(date).format(_self.option.dateFormat)).trigger('input')
                        $('#'+_self.option.element).addClass('not-visible')
                    }
                    break;
            }
        })
        $('#'+_self.option.element+' .week-view,'+'#'+_self.option.element+' .month-view,'+'#'+_self.option.element+' .year-view').on('webkitAnimationEnd oanimationend msAnimationEnd animationend', function () {
            //alert($(this).length)
            if ($(this).hasClass('zoomOut')) {
                $(this).addClass('not-visible')
            }
        })
        $('#'+_self.option.element+" #previous").on('click', function (e) {
            e.preventDefault();
            _self.executePrevious()
        })
        $('#'+_self.option.element+" #next").on('click', function (e) {
            e.preventDefault();
            _self.executeNext();
        })
        $('body').on('click focus','#'+_self.option.ppp, function (e) {
            e.stopPropagation()
            $(".date-picker").each(function () {
                $(this).addClass('not-visible')
            })
            //alert($(this).attr('data-target'))
            $($(this).attr('data-target')).removeClass('not-visible')
            //$('#'+_self.option.element).toggleClass('not-visible')

            $('#'+_self.option.element).css({
                top: ($(_self.element).offset().top + $(_self.element).outerHeight()) + "px",
                left: $(_self.element).offset().left + 'px',
                zIndex: '300000'
            })
            _self.initCalender()
        })
        //$('body').on('blur','#'+_self.option.ppp, function (e) {
        //    e.stopPropagation()
        //    $(".date-picker").each(function () {
        //        $(this).addClass('not-visible')
        //    })
        //})
        $('body').on('click', function (e) {
            //e.stopImmediatePropagation();
            //alert(e.target.id)
            if($(e.target).hasClass('date-picker')||$(e.target).parents('.date-picker').length>0|| e.target.id==_self.element.id);
            else{
                $('.date-picker').addClass('not-visible')
            }
        })
        $(_self.element).keypress(function (e) {
            var code = e.keyCode? e.keyCode: e.which;
            if((code>=48&& code<=57)|| code==45||code==9||code==37||code==38||code==39||code==40);
            else e.preventDefault();
        })
    }

    Plugin.prototype.makeCalender = function(month) {
        var _self = this;
        var beforeMonth = month - 1 < 0 ? _self.option.yearMonth[11] : _self.option.yearMonth[month - 1];
        for (var i = _self.option.weekDay - 1; i >= 0; i--) {
            $('#'+_self.option.element+' .week-view ul:not(:first-child) li').eq(_self.option.weekDay - 1 - i).text(beforeMonth - i).addClass('inactive')
        }
        for (var i = 0; i < _self.option.yearMonth[month]; i++) {
            $('#'+_self.option.element+' .week-view ul:not(:first-child) li').eq(_self.option.weekDay + i).text(i + 1).removeClass('inactive')
            //alert(_self.option.todayMonth+" "+_self.option.currentMonth+" "+_self.option.currentYear+" "+_self.option.todayYear)
            if(_self.option.todayMonth==_self.option.currentMonth&&_self.option.currentYear==_self.option.todayYear&&_self.option.today==i+1){
                //alert('jagaggjd')
                $('#'+_self.option.element+' .week-view ul:not(:first-child) li').eq(_self.option.weekDay + i).addClass('current-date')
            }
            else{
                $('#'+_self.option.element+' .week-view ul:not(:first-child) li').eq(_self.option.weekDay + i).removeClass('current-date')
            }
        }
        for (var i = 0; i < 42 - (_self.option.yearMonth[month] + _self.option.weekDay); i++) {
            $('#'+_self.option.element+' .week-view ul:not(:first-child) li').eq(_self.option.yearMonth[month] + _self.option.weekDay + i).text(i + 1).addClass('inactive')
        }
    }

    Plugin.prototype.addDropDownButton = function() {
        var button = '<span class="date-picker-dropdown glyphicon glyphicon-menu-down"></span>'
        return button;
    }
})(jQuery)