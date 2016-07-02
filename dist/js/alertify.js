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
    Plugin.prototype.init = function (option) {
        //$('.confirm-box-shadow').remove();
        var _self = this
        $('body').append(_self.createDialog(_self.settings))
        $('#'+_self.settings.id).on('click','.confirm-ok-button', function () {
            _self.settings.ok_callback(_self.element)
            _self.hideConfirmDialog();
        })
        $('#'+_self.settings.id).on('click','.confirm-cancel-button', function () {
            _self.settings.cancel_callback(_self.element)
            _self.hideConfirmDialog();
        })
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
        $('#'+_self.settings.id).css('display','block')
        $('#'+_self.settings.id).children('.confirm-dialog-plugin').addClass('bounceInDown').removeClass('bounceOutUp')

    }
    Plugin.prototype.hideConfirmDialog = function(){
        var _self = this;
        $('#'+_self.settings.id).children('.confirm-dialog-plugin').removeClass('bounceInDown').addClass('bounceOutUp')
        $('#'+_self.settings.id).children('.confirm-dialog-plugin').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (e) {
            if($(this).hasClass('bounceOutUp'))$(this).parents('div').css('display','none')
            console.log('animation end')
        })
    }
    Plugin.prototype.createDialog = function(option){
        var _self = this;
        var dialog = '<div class="confirm-box-shadow" id="'+_self.settings.id+'" style="display: none">' +
            '<div class="confirm-dialog-plugin animated bounceOutUp">' +
            '<div class="confirm-dialog-header">' +
            '<span><img src="/dist/img/warning.png"> WARNING!!</span>' +
            '</div>' +
            '<div class="confirm-dialog-body">' +option.message+
            '</div>' +
            '<div class="confirm-dialog-bottom">' +
            '<button class="confirm-ok-button">'+option.ok_button_text+'</button>' +
            '<button class="confirm-cancel-button">'+option.cancel_button_text+'</button>' +
            '</div>' +
            '</div></div>'
        return dialog;
    }
})(jQuery)