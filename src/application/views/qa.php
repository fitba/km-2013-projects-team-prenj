<?php 
    $data['title'] = 'Q/A sekcija';
    $this->load->view('static/header.php', $data);
?>
<div class="hero-unit">
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/askQuestion/qa/ask'); ?>">Ask Question</a> 
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/askQuestion/qa/questions'); ?>">Questions</a>
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/askQuestion/qa/tags'); ?>">Tags</a> 
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/askQuestion/qa/users'); ?>">Users</a>
    <a class="btn">Badges</a> 
</div>
<div class="row-fluid">
  <div class="span12">
    <?php
    if(isset($ask))
    {
        if($ask == 'ask')
        {
    ?>
    <h2>Postavite pitanje</h2>
    <form action="<?php echo base_url('index.php/qawiki_c/askQuestion/' . $key . '/' . $ask); ?>" method="post">
        <p><input type="text" name="title" placeholder="Ovdje unesite naslov pitanja" class="input-xxlarge"></p>
        <p><textarea id="editor" name="question"></textarea></p>
        <p><input type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge"></p>
        <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>" /></p>
        <p><input type="hidden" name="askDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
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
                        <center><?php echo $views;  ?> views</center>
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
        else if($ask == 'tags')
        {
    ?>
    <h2>Tags</h2>
    <table class="table">
        <tbody>
            <?php
            $iterate = 0;
            for ($i = 0; $i < count($tags); $i++)
            {
                echo '<tr>';
                for ($j = 0; $j < 4; $j++)
                {
                    if($iterate != count($tags))
                    {
                        echo '<td>
                                <a href="#" class="hoverEffect" id="'.$iterate.'">
                                    <span class="label">'.$tags[$iterate]['Name']. '</span>
                                </a>
                                <p class="bubble" id="bubble'.$iterate.'">'.$tags[$iterate]['Description'].'</p>
                                <br/>' . $tags[$iterate]['Description'] . '
                              </td>';
                        $iterate++;
                    }
                }
                $i = $iterate - 1;
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
    <?php
        }
        else if($ask == 'users')
        {
    ?>
            <h2>Korisnici</h2>
            <table class="table">
                <tbody>
                    <?php
                    $iterate = 0;
                    for ($i = 0; $i < count($users); $i++)
                    {
                        echo '<tr>';
                        for ($j = 0; $j < 4; $j++)
                        {
                            if($iterate != count($users))
                            {
                                $nameOfFolder = 'pictures/' . $users[$iterate]['UserID'];
                                $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname(dirname($_SERVER['PHP_SELF'])))).'/'.$nameOfFolder);
                                $locationOfPicutre = $baseLocation . '/' . $users[$iterate]['ProfilePicture'];
                                echo '<td><div class="formatPicture">';
                                        if($users[$iterate]['ProfilePicture'] != NULL)
                                        {
                                            echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. $locationOfPicutre .'" height="61" width="60"/></a>';
                                        }
                                        else
                                        {
                                            if($users[$iterate]['Sex'] == 'm')
                                            {
                                                echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="61" width="60"/></a>';
                                            }
                                            else
                                            {
                                                echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="61" width="60"/></a>';
                                            }
                                        }
                                echo '</div>
                                      <div>
                                        <a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'">' . $users[$iterate]['FirstName'] . ' ' . $users[$iterate]['LastName'] . '</a>
                                      </div>
                                     </td>';
                                $iterate++;
                            }
                        }
                        $i = $iterate - 1;
                        echo '</tr>';
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