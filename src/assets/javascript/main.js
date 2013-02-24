/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    $('.close').click(function() {
        $('.alert').hide();
    });
    
    $('#tags').keyup(function() {
        var str = $(this).val();
        if(str.length == 1 && str.match(' '))
        {
            str = str.substring(0, str.length - 1);
            $(this).val(str);
        }
        else if(str.match('  '))
        {
            str = str.substring(0, str.length - 1);
            $(this).val(str);
        }
        else if(str.match(' '))
        {
            //alert('OK');
        }
    });
    
    $('.hoverEffect').hover(function(){
        var id = $(this).attr('id');
        $('#bubble' + id).slideDown();
        $('#bubble' + id).mouseleave(function(){
            $(this).slideUp();
        })
    });
});
