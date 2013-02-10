<?php 
    $data['title'] = 'Q/A sekcija';
    $this->load->view('static/header.php', $data);
?>
<div class="hero-unit">
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/qa/ask'); ?>">Postavite pitanje</a>
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/qa/questions'); ?>">Pitanja</a>
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/tags'); ?>">Tagovi</a>
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/users'); ?>">Korisnici</a>
</div>
<div class="row-fluid">
  <div class="span12">
    <?php
    if(isset($key))
    {
        if($key == 'ask')
        {
    ?>
    <h2>Postavite pitanje</h2>
    <form action="<?php echo base_url('index.php/qawiki_c/qa/' . $key); ?>" method="post">
        <p><input type="text" name="title" placeholder="Ovdje unesite naslov pitanja" class="input-xxlarge"></p>
        <p><textarea id="editor" name="question"></textarea></p>
        <p><input type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge"> [<i>Tagove odvojte praznim poljem (razmakom)</i>]</p>
        <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>" /></p>
        <p><input type="hidden" name="askDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
        <p><input type="submit" name="askQuestion" class="btn" value="Submit"></p>
    </form>
    <?php
        }
        else if($key == 'questions')
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
                    $negative = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question['QuestionID'] . " AND Positive = '0'");
                    $positive = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $question['QuestionID'] . " AND Positive = '1'");
                    $answers = $this->general_m->countRows('answers', 'AnswerID', 'QuestionID = ' . $question['QuestionID']);
                    $user = $this->general_m->selectSomeById('*', 'users', 'UserID = ' . $question['UserID']);
                    $views = $this->general_m->countRows('views', 'ViewID', 'QuestionID = ' . $question['QuestionID']);
                    $tags = $this->qawiki_m->getTagsForQuestion($question['QuestionID']);
                    
                    $resultOfVotes = ($positive - $negative);
            ?>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            <?php echo '<b>' . $resultOfVotes . '</b>'; ?><br/> votes<br/>
                            <?php echo '<b>' . $answers . '</b>'; ?><br/> answers
                        </center>
                        <center><?php echo '<b>' .  $views  . '</b>';  ?> views</center>
                    </div>
                    <div class="questions">
                        <p class="title"><a href="<?php echo base_url('index.php/main/question/' . $question['QuestionID']); ?>"><?php echo $question['Title'] ?></a></p>
                        <p><?php echo $question['Question'] ?></p>
                        <p>
                        <?php
                        foreach ($tags as $tag)
                        {
                            echo '<span class="label">'.$tag['Name'].'</span>' . ' ';
                        }
                        ?>
                        </p>
                    </div>
                    <div class="textRight">Pitanje postavio/la: <?php echo '<b><a href="'. base_url('index.php/main/profile/' . $question['UserID']) .'">' . $user['FirstName'] . ' ' . $user['LastName'] . '</a> | '. $this->formatdate->getFormatDate($question['AskDate']) . '</b>'; ?></div>
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