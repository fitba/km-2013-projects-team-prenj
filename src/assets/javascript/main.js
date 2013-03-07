/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    $('.close').click(function() {
        $('.alert').hide();
    });
});

function like(id, path)
{
    $.post(CI_ROOT + path + id, { id : id }, function(data){
        if(data == 'true')
        {
            alert('OK');
        }
    });
}

function vote(id, path, vote)
{
    $.post(CI_ROOT + path + id + '/' + vote, { id : id, vote : vote }, function(data){
        if(data == 'true')
        {
            alert('OK');
        }
    });
}

function best(answer_id, path, question_id)
{
    $.post(CI_ROOT + path + answer_id + '/' + question_id, { answer_id : answer_id, question_id : question_id }, function(data){

        alert(data);
        
    });
}

function openComment(divOpens)
{
    $(divOpens).slideToggle();
}

/*function getData(tag_id)
{
    $.post(CI_ROOT + '/index.php/qawiki_c/likeTag', { tag_id : tag_id }, function(data){
        if(data == 'success')
        {
            
        }
        else
        {
            alert(data);
        }
    });
}*/
