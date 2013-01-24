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
                        <p><?php echo $question['Question'] ?></p>
                        <p><?php echo $question['Tags'] ?></p>
                    </div>
                    <div class="textRight">Pitanje postavio/la: <?php echo '<b>' . $question['FirstName'] . ' ' . $question['LastName'] . ' | '. $question['AskDate'] .'</b>'; ?></div>
                </td>
            </tr>
        </tbody>
    </table>
<h5>Komentari</h5>
<table class="table">
    <tbody>
        <tr>
            <td>
                <div class="comments">
                    <?php 
                    foreach($comments as $comment)
                    {
                    ?>
                    <div style="float: left">
                        <?php echo $comment['Ordinal']; ?>
                    </div>
                    <div style="margin-left: 30px">
                        <?php echo $comment['Comment'] . ' - ' . $comment['FirstName'] . ' ' . $comment['LastName']; ?>
                    </div>
                    <hr/>
                    <?php
                    }
                    if(isset($errorsComment))
                    {
                          echo '<div class="alert alert-error">
                                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                                  <h4>Upozorenje!</h4>
                                  '.$errorsComment.'
                                </div>';
                    }
                    if(isset($isOkComment))
                    {
                          echo '<div class="alert alert-success">
                                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                                  <h4>Informacija!</h4>
                                  '.$isOkComment.'
                                </div>';
                    }
                    if(isset($unexpectedErrorComment))
                    {
                          echo '<div class="alert alert-success">
                                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                                  <h4>Upozorenje!</h4>
                                  '.$unexpectedErrorComment.'
                                </div>';
                    }
                    ?>
                    <div style="margin-left: 30px">
                        <form action="<?php echo base_url('index.php/main/question/' . $question_id); ?>" method="post">
                            <p><textarea class="commentsSize" name="comment"></textarea></p>
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
      <h3>Odgovori</h3>
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
                        <p><?php echo $answer['Answer'] ?></p>
                    </div>
                    <div class="textRight">Odgovorio/la: <?php echo '<b>' . $answer['FirstName'] . ' ' . $answer['LastName'] . ' | '. $answer['AnswerDate'] .'</b>'; ?></div>
                </td>
            </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>
      <hr/>
      <?php 
      if(isset($errors))
      {
            echo '<div class="alert alert-error">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Upozorenje!</h4>
                    '.$errors.'
                  </div>';
      }
      if(isset($isOk))
      {
            echo '<div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Informacija!</h4>
                    '.$isOk.'
                  </div>';
      }
      if(isset($unexpectedError))
      {
            echo '<div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Upozorenje!</h4>
                    '.$unexpectedError.'
                  </div>';
      }
      ?>
      <form action="<?php echo base_url('index.php/main/question/' . $question_id); ?>" method="post">
          <p><textarea id="editor" name="answer"></textarea></p>
          <p><input type="submit" name="submitAnswer" value="Odgovori" class="btn btn-primary"/></p>
      </form>
  </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>
