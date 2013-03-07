<?php 
    $data['title'] = 'Pitanje i odgovori';
    $this->load->view('static/header.php', $data); 
?>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/jquery-ui.css'); ?>"/>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/jquery.tagit.css'); ?>"/>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/tagit.ui-zendesk.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/jquery-ui.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/tag-it.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/tag-it.min.js"); ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $("#tags").tagit();
        
        $('#openComment').click(function(){
            $('#commentOpens').slideToggle();
        });
        
        $('#openAnswerForm').click(function(){
            $('#answerForm').slideToggle();
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
                            <div><a class="showsTooltip" onmousemove="Tooltip.Text = 'Ovo pitanje je jasno i korisno';" onclick="vote('<?php echo $question['QuestionID']; ?>', '/index.php/ajax/voteQuestion/', '1');" href="#"><img src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></a></div>
                            <?php echo $resultOfVotesForQuestion; ?><br/> votes
                            <div><a class="showsTooltip" onmousemove="Tooltip.Text = 'Ovo pitanje nije jasno niti korisno';" onclick="vote('<?php echo $question['QuestionID']; ?>', '/index.php/ajax/voteQuestion/', '0');" href="#"><img src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></a></div>
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
                    <div class="textRight">
                        Pitanje postavio/la:<br/> <?php echo $this->formatdate->getFormatDate($question['AskDate']); ?><br/>
                            <?php 
                                $nameOfFolder = 'pictures/' . $question['UserID'];
                                $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname($_SERVER['PHP_SELF']))).'/'.$nameOfFolder);
                                $locationOfPicutre = $baseLocation . '/' . $question['ProfilePicture'];
                                
                                echo '<div style="float:left">';
                                if($question['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $question['UserID']).'"><img src="'. $locationOfPicutre .'" height="45" width="45"/></a>';
                                }
                                else
                                {
                                    if($user['Sex'] == 'm')
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
                            $nameOfFolder = 'pictures/' . $changedQuestion['UserID'];
                            $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname($_SERVER['PHP_SELF']))).'/'.$nameOfFolder);
                            $locationOfPicutre = $baseLocation . '/' . $changedQuestion['ProfilePicture'];
                            
                            echo '<div class="textRight">
                                    Pitanje promijenio/la<br/>' . $this->formatdate->getFormatDate($changedQuestion['LogDate']) .'
                                    <div style="float:left">';
                                    if($changedQuestion['ProfilePicture'] != NULL)
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $changedQuestion['UserID']).'"><img src="'. $locationOfPicutre .'" height="45" width="45"/></a>';
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
                    <div class="votes">
                        <center>
                            <div><a class="showsTooltip" onmousemove="Tooltip.Text = 'Ovaj odgovor je jasan i koristan';" onclick="vote('<?php echo $answer['AnswerID']; ?>', '/index.php/ajax/voteAnswer/', '1');" href="#"><img src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></a></div>
                            <?php echo $resultOfVotesForAnswer; ?><br/> votes
                            <div><a class="showsTooltip" onmousemove="Tooltip.Text = 'Ovaj odgovor nije jasan niti koristan';" onclick="vote('<?php echo $answer['AnswerID']; ?>', '/index.php/ajax/voteAnswer/', '0');" href="#"><img src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></a></div>
                        </center>
                        <center><a href="<?php echo base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID'] . '?editAnswer=true#editAnswer' . $answer['AnswerID']); ?>">[promijeni]</a></center>
                        <center><a class="showsTooltip" onmousemove="Tooltip.Text = 'Ovo je najbolji odgovor (kliknite opet da vratite na početno stanje)';" onclick="best('<?php echo $answer['AnswerID']; ?>', '/index.php/ajax/bestAnswer/', '<?php echo $question['QuestionID']; ?>');" href="#"><img src="<?php echo base_url('assets/images/star1.png'); ?>" alt="Ocjenite odgovor kao najbolji"/></a></center>
                    </div>
                    <a name="editAnswer<?php echo $answer['AnswerID']; ?>" href="#"></a>
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
                                $nameOfFolder = 'pictures/' . $answer['UserID'];
                                $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname($_SERVER['PHP_SELF']))).'/'.$nameOfFolder);
                                $locationOfPicutre = $baseLocation . '/' . $answer['ProfilePicture'];
                                
                                echo '<div style="float:left">';
                                if($answer['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $answer['UserID']).'"><img src="'. $locationOfPicutre .'" height="45" width="45"/></a>';
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
                            $nameOfFolder = 'pictures/' . $changedAnswer['UserID'];
                            $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname($_SERVER['PHP_SELF']))).'/'.$nameOfFolder);
                            $locationOfPicutre = $baseLocation . '/' . $changedAnswer['ProfilePicture'];
                            
                            echo '<div class="textRight">
                                    Promijenio/la ' . $this->formatdate->getFormatDate($changedAnswer['LogDate']) .'
                                    <div style="float:left">';
                                    if($changedAnswer['ProfilePicture'] != NULL)
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $changedAnswer['UserID']).'"><img src="'. $locationOfPicutre .'" height="45" width="45"/></a>';
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
