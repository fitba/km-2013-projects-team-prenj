/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
    $('.close').click(function() {
        $('.alert').hide();
    });
    
    $('.autosuggest').keyup(function(){
        /*var tags = $('#tags').val();
        $.post(CI_ROOT + '/index.php/ajax/getAutoCompleteTags/' + tags, { tags : tags }, function(data){
            $('.result').html(data);
            $('.result li').click(function(){
                var result_value = $(this).text();
                $('#tags').val(result_value);
                $('.result').html('');
            });
        });*/
    });
});

function like(id, path)
{
    $.post(CI_ROOT + path + id, { id : id }, function(data){
        if(isNaN(data) === false)
        {
            $('#tag' + id).text(' x ' + data);
        }
        else
        {
            alert(data);
        }
    });
}

function vote(id, path, vote)
{
    $.post(CI_ROOT + path + id + '/' + vote, { id : id, vote : vote }, function(data){
        if(isNaN(data) === false)
        {
            $('#numOfArticleVotes').text(data);
            $('#numOfQuestionVotes').text(data);
        }
        else
        {
            alert(data);
        }
    });
}

function voteAnswer(id, path, vote)
{
    $.post(CI_ROOT + path + id + '/' + vote, { id : id, vote : vote }, function(data){
        if(isNaN(data) === false)
        {
            $('#numOfAnswerVotes' + id).text(data);
        }
        else
        {
            alert(data);
        }
    });
}

function best(answer_id, path, question_id)
{
    $.post(CI_ROOT + path + answer_id + '/' + question_id, { answer_id : answer_id, question_id : question_id }, function(data){
        $('#answer' + answer_id).html(data);
        location.reload();
    });
}

function openComment(divOpens)
{
    $(divOpens).slideToggle();
}