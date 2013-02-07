/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    $('.close').click(function() {
        $('.alert').hide();
    });
    
    $('.hoverEffect').hover(function(){
        var id = $(this).attr('id');
        $('#bubble' + id).slideDown();
        $('#bubble' + id).mouseleave(function(){
            $(this).slideUp();
        })
    });
});
