/**
 * Created by Arafat Hossain on 12/13/2015.
 */
(function ($) {
    var i=0;
    var pluginName = 'confirmDialog'
    $.fn[pluginName] = function (options) {
        this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                    new Plugin( this, options ));
            }
        })
    }
    function Plugin(element,option){
        var options = {
            message:'Are u sure',
            ok_button_text:'Confirm',
            cancel_button_text:'Cancel',
            id:'confirm-dialog-'+(i++),
            ok_callback: function (element) {
            },
            cancel_callback: function (element) {
            }
        }
        this.element = element;
        this.settings = $.extend({}, options, option);
        this.name = pluginName;
        this.init()
    }
    Plugin.prototype.registerListener = function () {
        var _self = this
        $('body').find('#'+_self.settings.id).on('click','.confirm-ok-button', function () {
            _self.settings.ok_callback(_self.element)
            _self.hideConfirmDialog();
        }).on('click','.confirm-cancel-button', function () {
            _self.settings.cancel_callback(_self.element)
            _self.hideConfirmDialog();
        })
    }
    Plugin.prototype.unRegisterListener = function () {
        var _self = this
        $('body').find('#'+_self.settings.id).off('click','.confirm-ok-button', function () {
            _self.settings.ok_callback(_self.element)
            _self.hideConfirmDialog();
        }).off('click','.confirm-cancel-button', function () {
            _self.settings.cancel_callback(_self.element)
            _self.hideConfirmDialog();
        })
    }
    Plugin.prototype.init = function (option) {
        //$('.confirm-box-shadow').remove();
        var _self = this
        $(_self.element).on('click', function (e) {
            //alert(_self.element.className)
            e.preventDefault()
            _self.showConfirmDialog()
        })
        return this;
    }
    Plugin.prototype.showConfirmDialog = function(){
        var _self = this;
        //alert(_self.settings.id)
        $('body').append(_self.createDialog(_self.settings))
        _self.registerListener();
        $('#'+_self.settings.id).find('.confirm-dialog-plugin').addClass('bounceInDown').removeClass('bounceOutUp')

    }
    Plugin.prototype.hideConfirmDialog = function(){
        var _self = this;
        $('#'+_self.settings.id).find('.confirm-dialog-plugin').removeClass('bounceInDown').addClass('bounceOutUp')
        $('#'+_self.settings.id).find('.confirm-dialog-plugin').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (e) {
            if($(this).hasClass('bounceOutUp')){
                _self.unRegisterListener();
                $("#"+_self.settings.id).remove();
            }
            console.log('animation end')
        })

    }
    Plugin.prototype.createDialog = function(option){
        var _self = this;
        var dialog = '<div class="confirm-box-shadow" id="'+_self.settings.id+'">' +
            '<div class="container" style="top: 100px;position: relative">' +
            '<div class="row">' +
            '<div class="col-md-4 col-sm-6 col-xs-10 col-centered">' +
            '<div class="confirm-dialog-plugin animated bounceOutUp">' +
            '<div class="confirm-dialog-header">' +
            '<span><i class="fa fa-warning"></i> WARNING!!</span>' +
            '</div>' +
            '<div class="confirm-dialog-body">' +option.message+
            '</div>' +
            '<div class="confirm-dialog-bottom">' +
            '<button class="btn btn-flat pull-right confirm-cancel-button">'+option.cancel_button_text+'</button>' +
            '<button class="btn btn-info pull-right confirm-ok-button">'+option.ok_button_text+'</button>' +
            '</div>' +
            '</div></div>' +
            '</div>' +
            '</div>' +
            '</div>'
        return dialog;
    }
})(jQuery)