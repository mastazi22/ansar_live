(function ($) {
    var defaults = {
        sort_ASC: 'fa-sort-amount-asc',
        sort_DESC: 'fa-sort-amount-desc',
        exclude: -1
    }
    var pluginName = 'sortTable'
    $.fn[pluginName] = function (options) {
        this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                    new Plugin( this, options ));
            }
        })
    }
    function Plugin(element,options){
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this.name = pluginName;
        this.init()
    }
    Plugin.prototype.init = function(){
        var _self = this;
        $("tr", _self.element).eq(0).children().each(function (i) {
            if (i != _self.settings.exclude) {
                $(this).append(_self.getSortButton({col: i, sortDir: 1}))
            }
        });
    }
    Plugin.prototype.getSortButton = function(option) {
        var _self = this;
        var b = document.createElement("a");
        b.setAttribute('data-col-num', option.col);
        b.setAttribute('data-sort', option.sortDir);
        b.setAttribute('href', '#');
        b.style.color = "forestgreen";
        var i = document.createElement("i");
        $(i).addClass('fa ' + _self.settings.sort_ASC);
        $(b).append(i);
        $(b).on('click', function () {
            //$('tr:not(:eq(0))')
            var rows = $('tr', _self.element).not($(b).parents('tr')[0]);
            alert(rows.length);
            var i = $(this).attr('data-col-num')
            var s = $(this).attr('data-sort')
            rows.sort(function (c, d) {
                var p = $.trim($('td,th', c).filter(":eq(" + i + ")").text());
                var q = $.trim($('td,th', d).filter(":eq(" + i + ")").text());
                console.log({sss:p +" "+ q})
                console.log({ssss:s == 1 ? parseInt(p) > parseInt(q) : parseInt(p) < parseInt(q),s:s})
                if ($.isNumeric(p) && $.isNumeric(q)) return s == 1 ? parseInt(p) > parseInt(q) : parseInt(p) < parseInt(q)
                return s == 1 ? p > q : p < q;
            })
            rows.detach().appendTo(_self.element);

            $(this).attr('data-sort', s == 1 ? 0 : 1);
            if (s == 1) {
                $(this).children('i').addClass(_self.settings.sort_DESC).removeClass(_self.settings.sort_ASC)
            }
            else {
                $(this).children('i').addClass(_self.settings.sort_ASC).removeClass(_self.settings.sort_DESC)
            }
        });
        return b;
    }
})(jQuery)