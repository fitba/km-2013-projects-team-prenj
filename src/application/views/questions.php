<?php 
    $data['title'] = 'Pitanje i odgovori';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit" style="font-size: 16px;">
<h3><?php if(count($lastChangeQuestion) > 0) { echo $lastChangeQuestion['NewTitle']; } else { echo $question['Title']; }?> <a style="float: right; font-size: 13px;"  href="<?php echo base_url('index.php/main/question/' . $question_id . '?editQuestion=true'); ?>">[promijeni]</a></h3>
  <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes1">
                        <center>
                            <div><a href="<?php echo base_url('index.php/main/question/' . $question_id . '/' . 0 . '/' . 1); ?>"><img src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></a></div>
                            <?php echo $resultOfVotesForQuestion; ?><br/> votes
                            <div><a href="<?php echo base_url('index.php/main/question/' . $question_id . '/' . 0 . '/' . 0); ?>"><img src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></a></div>
                        </center>
                    </div>
                    <div class="questions">
                        <p>
                        <?php
                        if(isset($_GET['editQuestion']) && $_GET['editQuestion'] == 'true')
                        {
                            if(count($lastChangeQuestion) > 0)
                            {
                                echo '<form action="'.  base_url('index.php/main/question/' . $question_id) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                        <p><input type="text" name="newtitle" class="input-xxlarge" value="'.$lastChangeQuestion['NewTitle'].'"/></p>
                                        <p><textarea id="editor" name="newContent">'.$lastChangeQuestion['NewContent'].'</textarea></p>
                                        <p><input type="hidden" name="userid" value="'.base64_encode($sessionData['UserID']).'"/></p>
                                        <p><input type="hidden" name="questionid" value="'.base64_encode($question_id).'"/></p>
                                        <p><input type="hidden" name="logDate" value="'.date("Y-m-d H:i:s").'"/></p>
                                        <p><input type="submit" name="submitEditQuestion" value="Promijeni" class="btn btn-primary"/></p>
                                     </form>';
                            }
                            else
                            {
                                echo '<form action="'.  base_url('index.php/main/question/' . $question_id) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                        <p><input type="text" name="newtitle" class="input-xxlarge" value="'.$question['Title'].'"/></p>
                                        <p><textarea id="editor" name="newContent">'.$question['Question'].'</textarea></p>
                                        <p><input type="hidden" name="userid" value="'.base64_encode($sessionData['UserID']).'"/></p>
                                        <p><input type="hidden" name="questionid" value="'.base64_encode($question_id).'"/></p>
                                        <p><input type="hidden" name="logDate" value="'.date("Y-m-d H:i:s").'"/></p>
                                        <p><input type="submit" name="submitEditQuestion" value="Promijeni" class="btn btn-primary"/></p>
                                     </form>';
                            }
                        }
                        else
                        {
                            if(count($lastChangeQuestion) > 0)
                            {
                                echo $lastChangeQuestion['NewContent'];
                            }
                            else
                            {
                                echo $question['Question'];
                            }   
                        }
                        ?>
                        </p>
                    </div>
                    <div class="textRight">
                        Pitanje postavio/la: <?php echo '<b><a href="'. base_url('index.php/main/profile/' . $question['UserID']) .'">' . $question['FirstName'] . ' ' . $question['LastName'] . '</a> | '. $this->formatdate->getFormatDate($question['AskDate']) .'</b>'; ?>
                        <br/>
                        <?php 
                        if(count($lastChangeQuestion) > 0)
                        {
                            echo 'Pitanje promijenio/la <b><a href="'. base_url('index.php/main/profile/' . $lastChangeQuestion['UserID']) .'">' . $lastChangeQuestion['FirstName'] . ' ' . $lastChangeQuestion['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($lastChangeQuestion['LogDate']) .'</b>';
                        }
                        ?>
                    </div>
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
                        <?php echo $comment['Comment'] . ' - <b><a href="'. base_url('index.php/main/profile/' . $comment['CommentsUserID']) .'">' . $comment['FirstName'] . ' ' . $comment['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($comment['CommentDate']) . '</b>'; ?>
                    </div>
                    <hr/>
                    <?php
                    }
                    ?>
                    <div style="margin-left: 30px">
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
            
                $whereAnswer = 'logs.AnswerID = ' . $answer['AnswerID'] . ' AND logs.LogID = (SELECT MAX(LogID)
                                                                                              FROM logs
                                                                                              WHERE AnswerID = '.$answer['AnswerID'].')';
                $lastChangeAnswer = $this->logs_m->getLogsBy('*', $joinAnswer, $whereAnswer);
            ?>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            <div><a href="<?php echo base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID'] . '/' . 1); ?>"><img src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></a></div>
                            <?php echo $resultOfVotesForAnswer; ?><br/> votes
                            <div><a href="<?php echo base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID'] . '/' . 0); ?>"><img src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></a></div>
                        </center>
                        <div><a href="<?php echo base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID'] . '?editAnswer=true'); ?>">[promijeni]</a></div>
                    </div>
                    <div class="questions">
                        <p>
                        <?php
                        if(isset($answer_id) && $answer_id == $answer['AnswerID'] && isset($_GET['editAnswer']) && $_GET['editAnswer'] == 'true')
                        {
                            if(count($lastChangeAnswer) > 0)
                            {
                                echo '<form action="'.  base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID']) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                        <p><textarea id="editor" name="newContent">'.$lastChangeAnswer['NewContent'].'</textarea></p>
                                        <p><input type="hidden" name="userid" value="'.base64_encode($sessionData['UserID']).'"/></p>
                                        <p><input type="hidden" name="answerid" value="'.base64_encode($answer['AnswerID']).'"/></p>
                                        <p><input type="hidden" name="logDate" value="'.date("Y-m-d H:i:s").'"/></p>
                                        <p><input type="submit" name="submitEditAnswer" value="Promijeni" class="btn btn-primary"/></p>
                                     </form>';
                            }
                            else
                            {
                                echo '<form action="'.  base_url('index.php/main/question/' . $question_id . '/' . $answer['AnswerID']) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                        <p><textarea id="editor" name="newContent">'.$answer['Answer'].'</textarea></p>
                                        <p><input type="hidden" name="userid" value="'.base64_encode($sessionData['UserID']).'"/></p>
                                        <p><input type="hidden" name="answerid" value="'.base64_encode($answer['AnswerID']).'"/></p>
                                        <p><input type="hidden" name="logDate" value="'.date("Y-m-d H:i:s").'"/></p>
                                        <p><input type="submit" name="submitEditAnswer" value="Promijeni" class="btn btn-primary"/></p>
                                     </form>';
                            }
                        }
                        else
                        {
                            if(count($lastChangeAnswer) > 0)
                            {
                                echo $lastChangeAnswer['NewContent'];
                            }
                            else
                            {
                                echo $answer['Answer'];
                            }
                        }
                        ?>
                        </p>
                    </div>
                    <div class="textRight">
                        Odgovorio/la: <?php echo '<b><a href="'. base_url('index.php/main/profile/' . $answer['AnswersUserID']) .'">' . $answer['FirstName'] . ' ' . $answer['LastName'] . '</a> | '. $this->formatdate->getFormatDate($answer['AnswerDate']) .'</b>'; ?>
                        <br/>
                        <?php 
                        if(count($lastChangeAnswer) > 0 && $lastChangeAnswer != null)
                        {
                            echo 'Promijenio/la <b><a href="'. base_url('index.php/main/profile/' . $lastChangeAnswer['UserID']) .'">' . $lastChangeAnswer['FirstName'] . ' ' . $lastChangeAnswer['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($lastChangeAnswer['LogDate']) .'</b>';
                        }
                        ?>
                    </div>
                    
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
                                            <?php echo $comment['Comment'] . ' - <b><a href="'. base_url('index.php/main/profile/' . $comment['CommentsUserID']) .'">' . $comment['FirstName'] . ' ' . $comment['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($comment['CommentDate']) . '</b>'; ?>
                                        </div>
                                        <hr/>
                                        <?php
                                        }
                                        $lastOrdinal = $this->general_m->selectMax('Ordinal', 'comments', 'AnswerID = ' . $answer['AnswerID']);
                                        ?>
                                        <div style="margin-left: 30px">
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
      <form action="<?php echo base_url('index.php/main/question/' . $question_id); ?>" method="post" onsubmit="return saveScrollPositions(this);">
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
