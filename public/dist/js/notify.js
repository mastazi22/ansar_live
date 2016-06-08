/**
 * Created by darksider on 12/13/2015.
 */
(function ($) {
    var options = {
        type:'success',
        message:'This is a default message'
    }
    $.fn.notifyDialog = function (option) {
        $.extend(options,option);
        $(this).children('.notify-success,.notify-error').remove()
        this.append(createNotificationDialog(options.type,options.message));
        $(this).on('click','#close-dialog', function () {
            $(this).parents('.notify-success,.notify-error').addClass('slideOutUp')
        })
        return this;
    }
    $.fn.showDialog = function () {
        $(this).children('.notify-success,.notify-error').addClass('slideInDown').removeClass('slideOutUp')
    }
    $.fn.hideDialog = function () {
        $('#close-dialog').trigger('click')
    }
    function createNotificationDialog(type,message){
        switch (type){
            case 'success':
                return successDialog(message)
            case 'error':
                return errorDialog(message)
        }
    }
    function successDialog(message){
        var mainDialog = '<div class="notify-success animated slideOutUp">' +
                '<div class="notify-body">' +
                '<button id="close-dialog" class="close"><i class="fa fa-close"></i></button>'+
                '<img src="/dist/img/success.png">'+
                '<p>'+message+'</p>'+
            '</div>'+
            '</div>'
        return mainDialog;
    }
    function errorDialog(message){
        var mainDialog = '<div class="notify-error animated">' +
            '<div class="notify-body">' +
            '<button id="close-dialog" class="close"><i class="fa fa-close"></i></button>'+
            '<img src="/dist/img/error.png">'+
            '<p>'+message+'</p>'+
            '</div>'+
            '</div>'
        return mainDialog;
    }
})(jQuery)