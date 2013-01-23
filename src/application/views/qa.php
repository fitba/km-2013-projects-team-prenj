<?php 
    $data['title'] = 'Q/A sekcija';
    $this->load->view('static/header.php', $data);
    
    if(isset($_SESSION['redirect']))
    {
        $this->redirectpage->unsetRedirectData();
    }
?>
<div class="hero-unit">
    <a class="btn" href="<?php echo base_url('index.php/main/qa_wiki/qa/ask'); ?>">Ask Question</a> 
    <a class="btn" href="<?php echo base_url('index.php/main/qa_wiki/qa/questions'); ?>">Questions</a>
    <a class="btn">Tags</a> <a class="btn">Users</a>
    <a class="btn">Badges</a> <a class="btn">Unanswered</a>
</div>
<div class="row-fluid">
  <div class="span12">
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
    if(isset($ask))
    {
        if($ask == 'ask')
        {
    ?>
    <h2>Postavite pitanje</h2>
    <form action="<?php echo base_url('index.php/qawiki_c/askQuestion'); ?>" method="post">
        <p><input type="text" name="title" placeholder="Ovdje unesite naslov pitanja" class="input-xxlarge"></p>
        <p><textarea id="editor" name="editor"></textarea></p>
        <p><input type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge"></p>
        <p><input type="submit" name="askQuestion" class="btn" value="Submit"></p>
    </form>
    <?php
        }
        else if($ask == 'questions')
        {
    ?>
    <h2>Lista pitanja</h2>
    <table class="table">
        <tbody>
            <?php 
            if(isset($questions))
            {
                foreach ($questions as $question)
                {
                    $votes = $this->general_m->countRows('votes', 'VoteID', 'QuestionID = ' . $question['QuestionID']);
                    $answers = $this->general_m->countRows('answers', 'AnswerID', 'QuestionID = ' . $question['QuestionID']);
                    $user = $this->general_m->selectSomeById('*', 'users', 'UserID', $question['UserID']);
            ?>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            <?php echo '<b>' . $votes . '</b>'; ?><br/> votes<br/>
                            <?php echo '<b>' . $answers . '</b>'; ?><br/> answers
                        </center>
                        <center><?php echo '<b>' . $question['Views'] . '</b>'; ?> views</center>
                    </div>
                    <div class="questions">
                        <p class="title"><a href="<?php echo base_url('index.php/main/question/' . $question['QuestionID']); ?>"><?php echo $question['Title'] ?></a></p>
                        <p><?php echo $question['Question'] ?></p>
                        <p><?php echo $question['Tags'] ?></p>
                    </div>
                    <div class="textRight">Pitanje postavio/la: <?php echo '<b>' . $user['FirstName'] . ' ' . $user['LastName'] . ' | '. $question['AskDate'] . '</b>'; ?></div>
                </td>
            </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
    <?php
        }
    }
    ?>
  </div><!--/span-->
</div><!--/row-->
<?php 
    $this->load->view('static/footer.php'); 
?>