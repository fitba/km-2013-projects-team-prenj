<?php 
    $data['title'] = 'Pitanje i odgovori';
    $this->load->view('static/header.php', $data); 
?>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/jquery-ui.css'); ?>"/>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/jquery.tagit.css'); ?>"/>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/tagit.ui-zendesk.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/css/jRating.jquery.css'); ?>" type="text/css" />
<script type="text/javascript" src="<?php echo base_url("assets/javascript/jquery-ui.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/tag-it.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/tag-it.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/jRating.jquery.js"); ?>"></script>


<script type="text/javascript">
    $(document).ready(function() {
        
        $(function(){
            var question_id = '<?php echo $question_id; ?>';
            $.post(CI_ROOT + '/index.php/ajax/getEvaluate' + '/question/' + question_id, {  }, function(data){
                var jRateAverage = document.getElementById('jRatingAverage');
                
                if(data == 1)
                {
                    jRateAverage.style.width = '23px';
                }
                else if(data == 2)
                {
                    jRateAverage.style.width = '46px';
                }
                else if(data == 3)
                {
                    jRateAverage.style.width = '69px';
                }
                else if(data == 4)
                {
                    jRateAverage.style.width = '92px';
                }
                else if(data == 5)
                {
                    jRateAverage.style.width = '115px';
                }
            });
        });
        
        
        $("#tags").tagit();
        
        $('#openComment').click(function(){
            $('#commentOpens').slideToggle();
        });
        
        $('#openAnswerForm').click(function(){
            $('#answerForm').slideToggle();
        });
        
        $(".basic").jRating({
	  step:true,
	  length : 5
	});
        
        $('.minus, .minusTest').click(function(){
           var question_id = '<?php echo $question_id; ?>';
           $.post(CI_ROOT + '/index.php/ajax/dismissEvaluate' + '/question/' + question_id, {  }, function(data1){
               if(data1 == 'true')
               {
                    $('.minus, .minusTest').hide();
                    $.post(CI_ROOT + '/index.php/ajax/averageEvaluate/' + '/question/' + question_id, { }, function(data2){
                         $('.ocjena').text(data2 + ' / 5');
                     });
               }
               else
               {
                   
               }
           }); 
        });
        
        var evaluate = '';
        $('.basic').mousemove(function(){
            var rate = document.getElementById("jRatingInfos").innerHTML;
            var explode = rate.split(" ");
            evaluate = explode[0];
        });
        
        $('.basic').click(function(){
            var question_id = '<?php echo $question_id; ?>';
            $.post(CI_ROOT + '/index.php/ajax/evaluateQuestion/' + question_id + '/' + evaluate, {  }, function(data){
                if(data === 'true')
                {
                    $.post(CI_ROOT + '/index.php/ajax/averageEvaluate' + '/question/' + question_id, { }, function(data){
                        $('.ocjena').text(data + ' / 5');
                    });
                    $('.minusTest').show();
                    $('#response').text('Uspješno ste ocijenili pitanje');
                    setTimeout(function() {
                        $('#response').fadeOut('fast');
                    }, 1000);
                }
                else
                {
                    $(function() {
                        $('#basic-modal-content').html(data);
                        $('#basic-modal-content').modal();
                    });
                }
            });
        });
    });
</script>
<div style="font-size: 16px;">
<h3><?php echo $question['Title']; ?> <a style="float: right; font-size: 13px;"  href="<?php echo base_url('index.php/main/question/' . $question_id . '?editQuestion=true'); ?>">[promijeni]</a></h3>
  <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            <div><img class="showsTooltip" onmousemove="Tooltip.Text = 'Ovo pitanje je jasno i korisno';" onclick="vote('<?php echo $question['QuestionID']; ?>', '/index.php/ajax/voteQuestion/', '1');" src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></div>
                            <strong id="numOfQuestionVotes"><?php echo $resultOfVotesForQuestion; ?></strong><br/> votes
                            <div><img class="showsTooltip" onmousemove="Tooltip.Text = 'Ovo pitanje nije jasno niti korisno';" onclick="vote('<?php echo $question['QuestionID']; ?>', '/index.php/ajax/voteQuestion/', '0');" src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></div>
                            <br/>
                            <p class="ocjena"><?php echo $averageEvaluate; ?> / 5</p>
                        </center>
                    </div>
                    <div class="questions">
                        <?php
                        if(isset($_GET['editQuestion']) && $_GET['editQuestion'] == 'true')
                        {
                            $tagsForEdit = '';
                            foreach ($tags as $tag)
                            {
                                $tagsForEdit .= $tag['Name']. ',';
                            }
                            echo '<form action="'.  base_url('index.php/main/question/' . $question_id) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                    <p><input type="text" name="title" class="input-xxlarge" value="'.$question['Title'].'"/></p>
                                    <p><textarea id="editor" name="question">'.html_entity_decode($question['Question']).'</textarea></p>
                                    <p><input id="tags" type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge" value="'.$tagsForEdit.'"></p>
                                    <p><input type="submit" name="submitEditQuestion" value="Promijeni" class="btn btn-primary"/></p>
                                 </form>';
                        }
                        else
                        {
                            echo html_entity_decode($question['Question']);
                        ?>
                        <p>
                        <?php
                        foreach ($tags as $tag)
                        {
                            echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/search_c/index?pretraga=' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                        }
                        ?>
                        </p>
                        <?php
                        }
                        ?>
                    </div>
                    <br/><br/><br/>
                    <div class="exemple">
                        <em style="font-size: 18px;"><strong>Ocjenite pitanje</strong></em>
                        <img class="minusTest showsTooltip" onmousemove="Tooltip.Text = 'Poništite ocjenu';" src="<?php echo base_url('assets/images/minus.png'); ?>" width="40"/>
                        <?php
                        if(isset($user))
                        {
                            if($user > 0)
                            {
                            ?>
                            <img class="minus showsTooltip" onmousemove="Tooltip.Text = 'Poništite ocjenu';" src="<?php echo base_url('assets/images/minus.png'); ?>" width="40"/>
                            <?php 
                            }
                        }
                        ?>
                        <div id="mydiv" class="basic" data-average="5" data-id="1"></div>
                        <div id="response" style="color:green"></div>
                    </div>
                    
                    <div class="textRight">
                        Pitanje postavio/la:<br/> <?php echo $this->formatdate->getFormatDate($question['AskDate']); ?><br/>
                            <?php 
                                echo '<div style="float:left">';
                                if($question['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $question['UserID']).'"><img src="'. $question['ProfilePicture'] .'" height="45" width="45"/></a>';
                                }
                                else
                                {
                                    if($question['Sex'] == 'm')
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $question['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                    }
                                    else
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $question['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                    }
                                }
                                echo '<b><a href="'. base_url('index.php/main/profile/' . $question['UserID']) .'">
                                ' . $question['FirstName'] . ' ' . $question['LastName'] . '</a></b></div>'; ?>
                        </div>
                    
                        <?php 
                        foreach($lastChangeQuestion as $changedQuestion)
                        {
                            echo '<div class="textRight">
                                    Pitanje promijenio/la<br/>' . $this->formatdate->getFormatDate($changedQuestion['LogDate']) .'
                                    <div style="float:left">';
                                    if($changedQuestion['ProfilePicture'] != NULL)
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $changedQuestion['UserID']).'"><img src="'. $changedQuestion['ProfilePicture'] .'" height="45" width="45"/></a>';
                                    }
                                    else
                                    {
                                        if($changedQuestion['Sex'] == 'm')
                                        {
                                            echo '<a href="'.base_url('index.php/main/profile/' . $changedQuestion['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                        }
                                        else
                                        {
                                            echo '<a href="'.base_url('index.php/main/profile/' . $changedQuestion['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                        }
                                    }
                            echo       '<b>
                                           <a href="'. base_url('index.php/main/profile/' . $changedQuestion['UserID']) .'">
                                            ' . $changedQuestion['FirstName'] . ' ' . $changedQuestion['LastName'] . '
                                           </a>
                                        </b>
                                    </div>
                                  </div>';
                        }
                        ?>
                </td>
            </tr>
        </tbody>
    </table>
<h5>Komentari (<?php echo count($commentsQuestion); ?>)</h5>
<table class="table">
    <tbody>
        <tr>
            <td>
                <div class="comments">
                    <?php 
                    foreach($commentsQuestion as $comment)
                    {
                    ?>
                    <div style="float: left">
                        <?php echo $comment['Ordinal'];?>
                    </div>
                    <div style="margin-left: 30px">
                        <?php echo html_entity_decode($comment['Comment']) . ' - <b><a href="'. base_url('index.php/main/profile/' . $comment['CommentsUserID']) .'">' . $comment['FirstName'] . ' ' . $comment['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($comment['CommentDate']) . '</b>'; ?>
                    </div>
                    <hr/>
                    <?php
                    }
                    ?>
                    <a href="#" style="margin-left: 30px" id="openComment" class="btn btn-mini">Otvorite komentar</a>
                    <br/><br/>
                    <div id="commentOpens" style="margin-left: 30px; display: none;">
                        <form action="<?php echo base_url('index.php/main/question/' . $question_id); ?>" method="post" onsubmit="return saveScrollPositions(this);">
                            <?php $lastOrdinal = $this->general_m->selectMax('Ordinal', 'comments', 'QuestionID = ' . $question_id); ?>
                            <p><textarea class="commentsSize" name="comment"></textarea></p>
                            <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
                            <p><input type="hidden" name="questionid" value="<?php echo base64_encode($question_id); ?>"/></p>
                            <p><input type="hidden" name="commentDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
                            <p><input type="hidden" name="ordinal" value="<?php if($lastOrdinal['Last'] == null) echo 1; else echo $lastOrdinal['Last'] + 1; ?>"/></p>
                            
                            <input type="hidden" name="scrollx" id="scrollx" value="0" />
                            <input type="hidden" name="scrolly" id="scrolly" value="0" />
                            
                            <p><input type="submit" name="submitComment" value="Komentariši" class="btn btn-primary"/></p>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
<div class="row-fluid">
  <div class="span12">
      <h3>Odgovori (<?php echo count($answers); ?>)</h3>
      <table class="table">
        <tbody>
            <?php 
            foreach($answers as $key => $answer)
            {
                $negativeAnswer = $this->general_m->countRows('votes', 'VoteID', "AnswerID = " . $answer['AnswerID'] . " AND Positive = '0'");
                $positiveAnswer = $this->general_m->countRows('votes', 'VoteID', "AnswerID = " . $answer['AnswerID']. " AND Positive = '1'");
                $resultOfVotesForAnswer = ($positiveAnswer - $negativeAnswer);
                
                $joinAnswer = array('answers' => 'answers.AnswerID = logs.AnswerID',
                                                   'users' => 'users.UserID = logs.UserID');
            
                $whereAnswer = 'logs.AnswerID = ' . $answer['AnswerID'];
                $lastChangeAnswer = $this->logs_m->getLogsBy('*', $joinAnswer, $whereAnswer);
            ?>
            <tr>
                <td>
                    <div style="margin-bottom: 30px;"><a name="ans<?php echo $answer['AnswerID']; ?>" href="#"></a></div>
                    <div class="votes">
                        <center>
                            <div><img class="showsTooltip" onmousemove="Tooltip.Text = 'Ovaj odgovor je jasan i koristan';" onclick="voteAnswer('<?php echo $answer['AnswerID']; ?>', '/index.php/ajax/voteAnswer/', '1');" src="<?php  echo base_url('assets/images/top_arrow.png'); ?>"/></div>
                            <strong id="numOfAnswerVotes<?php echo $answer['AnswerID']; ?>"><?php echo $resultOfVotesForAnswer; ?></strong><br/> votes
                            <div><img class="showsTooltip" onmousemove="Tooltip.Text = 'Ovaj odgovor nije jasan niti koristan';" onclick="voteAnswer('<?php echo $answer['AnswerID']; ?>', '/index.php/ajax/voteAnswer/', '0');" src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></div>
                        </center>
                        <center><a href="<?php echo base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID'] . '?editAnswer=true#ans' . $answer['AnswerID']); ?>">[promijeni]</a></center>
                        <center>
                            <?php
                            $best = $this->general_m->selectSomeById('Best', 'answers', 'AnswerID = ' . $answer['AnswerID']);
                            $countOfAnswers = count($answers);
                            $coutnOfRows = $this->general_m->countRows('answers', 'AnswerID', "QuestionID = " . $question_id . " AND Best = '0'");
                            if($sessionData['UserID'] === $question['UserID'])
                            {
                                if($best['Best'] === '1')
                                {
                                    echo '<input type="checkbox" class="showsTooltip" checked="checked"
                                                 onmousemove="Tooltip.Text = \'Ocijeni ovaj odogovor kao najbolji (kliknite opet da vratite na početno stanje)\';" 
                                                 onclick="best('.$answer['AnswerID'].', \'/index.php/ajax/bestAnswer/\', '.$question['QuestionID'].');"/><br/>
                                          <img class="showsTooltip" src="'.base_url('assets/images/star1.png').'"
                                               onmousemove="Tooltip.Text = \'Vlasnik pitanja je ocijenio ovaj odgovor kao najbolji\'" />';
                                }
                                else
                                {
                                    if(count($answers) == $coutnOfRows)
                                    {
                            ?>
                            <input type="checkbox" class="showsTooltip" 
                               onmousemove="Tooltip.Text = 'Ocijeni ovaj odogovor kao najbolji (kliknite opet da vratite na početno stanje)';" 
                               onclick="best('<?php echo $answer['AnswerID']; ?>', '/index.php/ajax/bestAnswer/', '<?php echo $question['QuestionID']; ?>');" />
                            <?php 
                                    }
                                }
                            }
                            else
                            {
                                if($best['Best'] === '1')
                                {
                                    echo '<img class="showsTooltip" src="'.base_url('assets/images/star1.png').'"
                                               onmousemove="Tooltip.Text = \'Vlasnik pitanja je ocijenio ovaj odgovor kao najbolji\'" />';
                                }
                            }
                            ?>
                            <div id="answer<?php echo $answer['AnswerID']; ?>"></div>
                        </center>
                    </div>
                    
                    <div class="questions">
                        <?php
                        if(isset($answer_id) && $answer_id == $answer['AnswerID'] && isset($_GET['editAnswer']) && $_GET['editAnswer'] == 'true')
                        {
                            echo '<form action="'.  base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID']) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                    <p><textarea id="editor" name="answer">'.html_entity_decode($answer['Answer']).'</textarea></p>
                                    <p><input type="submit" name="submitEditAnswer" value="Promijeni" class="btn btn-primary"/></p>
                                 </form>';
                        }
                        else
                        {
                            echo html_entity_decode($answer['Answer']);
                        }
                        ?>
                    </div>
                    
                    <div class="textRight">
                        Odgovorio/la: <?php echo $this->formatdate->getFormatDate($answer['AnswerDate']); ?>
                            <?php
                                echo '<div style="float:left">';
                                if($answer['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $answer['UserID']).'"><img src="'. $answer['ProfilePicture'] .'" height="45" width="45"/></a>';
                                }
                                else
                                {
                                    if($answer['Sex'] == 'm')
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $answer['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                    }
                                    else
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $answer['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                    }
                                }
                                echo '<b><a href="'. base_url('index.php/main/profile/' . $answer['AnswersUserID']) .'">
                                ' . $answer['FirstName'] . ' ' . $answer['LastName'] . '</a></b></div>'; ?>
                        </div>
                        <?php 
                        foreach($lastChangeAnswer as $changedAnswer)
                        {
                            echo '<div class="textRight">
                                    Promijenio/la ' . $this->formatdate->getFormatDate($changedAnswer['LogDate']) .'
                                    <div style="float:left">';
                                    if($changedAnswer['ProfilePicture'] != NULL)
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $changedAnswer['UserID']).'"><img src="'. $changedAnswer['ProfilePicture'] .'" height="45" width="45"/></a>';
                                    }
                                    else
                                    {
                                        if($changedAnswer['Sex'] == 'm')
                                        {
                                            echo '<a href="'.base_url('index.php/main/profile/' . $changedAnswer['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                        }
                                        else
                                        {
                                            echo '<a href="'.base_url('index.php/main/profile/' . $changedAnswer['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                        }
                                    }
                                echo   '<b>
                                           <a href="'. base_url('index.php/main/profile/' . $changedAnswer['UserID']) .'">
                                            ' . $changedAnswer['FirstName'] . ' ' . $changedAnswer['LastName'] . '
                                           </a>
                                        </b>
                                    </div>
                                  </div>';
                        }
                        ?>
                    <br/><br/><br/><br/><br/>
                </td>
            </tr>
            <tr>
                <td>
                    <h5>Komentari</h5>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="comments">
                                        <?php
                                        $commentsAnswer = $this->qawiki_m->getCommentsDataById(NULL, $answer['AnswerID']);
                                        foreach($commentsAnswer as $comment)
                                        {
                                        ?>
                                        <div style="float: left">
                                            <?php echo $comment['Ordinal'];?>
                                        </div>
                                        <div style="margin-left: 30px">
                                            <?php echo html_entity_decode($comment['Comment']) . ' - <b><a href="'. base_url('index.php/main/profile/' . $comment['CommentsUserID']) .'">' . $comment['FirstName'] . ' ' . $comment['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($comment['CommentDate']) . '</b>'; ?>
                                        </div>
                                        <hr/>
                                        <?php
                                        }
                                        $lastOrdinal = $this->general_m->selectMax('Ordinal', 'comments', 'AnswerID = ' . $answer['AnswerID']);
                                        ?>
                                        <a href="#comment<?php echo $answer['AnswerID']; ?>" onclick="openComment('#commentOpens<?php echo $answer['AnswerID']; ?>');" style="margin-left: 30px" id="openComment" class="btn btn-mini">Otvorite komentar</a>
                                        <br/><br/>
                                        <div name="comment<?php echo $answer['AnswerID']; ?>" style="margin-left: 30px; display: none;" id="commentOpens<?php echo $answer['AnswerID']; ?>">
                                            <form action="<?php echo base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID']); ?>" method="post" onsubmit="return saveScrollPositions(this);">
                                                <p><textarea class="commentsSize" name="comment"></textarea></p>
                                                <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
                                                <p><input type="hidden" name="answerid" value="<?php echo base64_encode($answer['AnswerID']); ?>"/></p>
                                                <p><input type="hidden" name="commentDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
                                                <p><input type="hidden" name="ordinal" value="<?php if($lastOrdinal['Last'] == null) echo 1; else echo $lastOrdinal['Last'] + 1; ?>"/></p>
                                                
                                                <input type="hidden" name="scrollx" id="scrollx" value="0" />
                                                <input type="hidden" name="scrolly" id="scrolly" value="0" />
                                                
                                                <p><input type="submit" name="submitCommentAnswer" value="Komentariši" class="btn btn-primary"/></p>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>
      <hr/>
      <?php 
      if(!(isset($_GET['editAnswer'])) || !(isset($_GET['editQuestion'])))
      {
      ?>
      <a href="#answerForm" id="openAnswerForm" class="btn btn-mini">Otvorite odgovor</a>
      <br/><br/>
      <form name="answerForm" id="answerForm" style="display: none;" action="<?php echo base_url('index.php/main/question/' . $question_id); ?>" method="post" onsubmit="return saveScrollPositions(this);">
          <p><textarea id="editor" name="answer"></textarea></p>
          <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
          <p><input type="hidden" name="questionid" value="<?php echo base64_encode($question_id); ?>"/></p>
          <p><input type="hidden" name="answerDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
          
          <input type="hidden" name="scrollx" id="scrollx" value="0" />
          <input type="hidden" name="scrolly" id="scrolly" value="0" />
          
          <p><input type="submit" name="submitAnswer" value="Odgovori" class="btn btn-primary"/></p>
      </form>
      <?php 
      }
      ?>
  </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>
