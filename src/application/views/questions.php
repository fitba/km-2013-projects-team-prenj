<?php 
    $data['title'] = 'Pitanje i odgovori';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit" style="font-size: 16px;">
<h3>Pitanje</h3>
  <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes1">
                        <center>
                            0<br/> votes<br/>
                            0<br/> answers
                        </center>
                        <center>0 views</center>
                    </div>
                    <div class="questions">
                        <p><?php echo $question['Question']; $userid = $question['UserID']; ?></p>
                        <p><?php  ?></p>
                    </div>
                    <div class="textRight">Pitanje postavio/la: <?php echo '<b><a href="'. base_url('index.php/main/profile/' . $question['UserID']) .'">' . $question['FirstName'] . ' ' . $question['LastName'] . '</a> | '. $this->formatdate->getFormatDate($question['AskDate']) .'</b>'; ?></div>
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
                        <?php echo $comment['Ordinal']; $comuserid = $comment['CommentsUserID']; ?>
                    </div>
                    <div style="margin-left: 30px">
                        <?php echo $comment['Comment'] . ' - <b><a href="'. base_url('index.php/main/profile/' . $comment['CommentsUserID']) .'">' . $comment['FirstName'] . ' ' . $comment['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($comment['CommentDate']) . '</b>'; ?>
                    </div>
                    <hr/>
                    <?php
                    }
                    ?>
                    <div style="margin-left: 30px">
                        <form action="<?php echo base_url('index.php/main/question/' . $question_id); ?>" method="post">
                            <?php $lastOrdinal = $this->general_m->selectMax('Ordinal', 'comments', 'QuestionID = ' . $question_id); ?>
                            <p><textarea class="commentsSize" name="comment"></textarea></p>
                            <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
                            <p><input type="hidden" name="questionid" value="<?php echo base64_encode($question_id); ?>"/></p>
                            <p><input type="hidden" name="commentDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
                            <p><input type="hidden" name="ordinal" value="<?php if($lastOrdinal['Last'] == null) echo 1; else echo $lastOrdinal['Last'] + 1; ?>"/></p>
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
            foreach($answers as $answer)
            {
            ?>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            0<br/> votes<br/>
                            0<br/> answers
                        </center>
                        <center>0 views</center>
                    </div>
                    <div class="questions">
                        <p><?php echo $answer['Answer']; $ansuserid = $answer['UserID']; ?></p>
                    </div>
                    <div class="textRight">Odgovorio/la: <?php echo '<b><a href="'. base_url('index.php/main/profile/' . $answer['AnswersUserID']) .'">' . $answer['FirstName'] . ' ' . $answer['LastName'] . '</a> | '. $this->formatdate->getFormatDate($answer['AnswerDate']) .'</b>'; ?></div>
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
                                            <?php echo $comment['Ordinal']; $commmuserid = $comment['UserID']; ?>
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
                                            <form action="<?php echo base_url('index.php/qawiki_c/postCommentOnAnswer/' . $question_id . '/' . $answer['AnswerID']); ?>" method="post">
                                                <p><textarea class="commentsSize" name="comment"></textarea></p>
                                                <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
                                                <p><input type="hidden" name="answerid" value="<?php echo base64_encode($answer['AnswerID']); ?>"/></p>
                                                <p><input type="hidden" name="commentDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
                                                <p><input type="hidden" name="ordinal" value="<?php if($lastOrdinal['Last'] == null) echo 1; else echo $lastOrdinal['Last'] + 1; ?>"/></p>
                                                <p><input type="submit" name="submitComment" value="Komentariši" class="btn btn-primary"/></p>
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
      
      <form action="<?php echo base_url('index.php/main/question/' . $question_id); ?>" method="post">
          <p><textarea id="editor" name="answer"></textarea></p>
          <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
          <p><input type="hidden" name="questionid" value="<?php echo base64_encode($question_id); ?>"/></p>
          <p><input type="hidden" name="answerDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
          <p><input type="submit" name="submitAnswer" value="Odgovori" class="btn btn-primary"/></p>
      </form>
  </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>
