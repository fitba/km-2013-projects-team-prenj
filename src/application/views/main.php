<?php 
    $data['title'] = 'Home';
    $this->load->view('static/header.php', $data); 
?>
<div class="tabbable"> <!-- Only required for left/right tabs -->
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">Pitanja</a></li>
    <li><a href="#tab2" data-toggle="tab">Članci</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab1">
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
                        <p><?php echo html_entity_decode($question['Question']) ?></p>
                        <p>
                        <?php
                        foreach ($tags as $tag)
                        {
                            echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/search_c/index?pretraga=' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                        }
                        ?>
                        </p>
                    </div>
                    <div class="textRight">
                        Pitanje postavio/la: <?php echo $this->formatdate->getFormatDate($question['AskDate']); ?>
                            <?php
                                $nameOfFolder = 'pictures/' . $user['UserID'];
                                $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname($_SERVER['PHP_SELF']))).'/'.$nameOfFolder);
                                $locationOfPicutre = $baseLocation . '/' . $user['ProfilePicture'];
                                
                                echo '<div style="float:left">';
                                if($user['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $user['UserID']).'"><img src="'. $locationOfPicutre .'" height="45" width="45"/></a>';
                                }
                                else
                                {
                                    if($user['Sex'] == 'm')
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $user['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                    }
                                    else
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $user['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                    }
                                }
                                echo '<b><a href="'. base_url('index.php/main/profile/' . $question['UserID']) .'">
                                ' . $user['FirstName'] . ' ' . $user['LastName'] . '</a></b></div>'; ?>
                    </div>
                </td>
            </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table> 
    </div>
    <div class="tab-pane" id="tab2">
      <h2>Lista članaka</h2>
                <table class="table">
                    <tbody>
                        <?php 
                        if(isset($articles))
                        {
                            foreach ($articles as $article) 
                            {
                                $tags = $this->qawiki_m->getTagsForArticle($article['ArticleID']);
                                $user = $this->general_m->selectSomeById('*', 'users', 'UserID = ' . $article['UserID']);
                                $negative = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article['ArticleID'] . " AND Positive = '0'");
                                $positive = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $article['ArticleID'] . " AND Positive = '1'");
                                $views = $this->general_m->countRows('views', 'ViewID', 'ArticleID = ' . $article['ArticleID']);
                                
                                $resultOfVotes = ($positive - $negative);
                        ?>
                                <tr>
                                    <td>
                                        <div class="votes">
                                            <center>
                                                <?php echo '<b>' . $resultOfVotes . '</b>'; ?><br/> votes<br/>
                                                <?php echo '<b>' .  $views  . '</b>';  ?><br/> views
                                            </center>
                                        </div>
                                        <div class="questions">
                                            <p class="title"><a href="<?php echo base_url('index.php/main/article/' . $article['ArticleID']); ?>"><?php echo $article['Title'] ?></a></p>
                                            <p><?php echo html_entity_decode($article['Content']) ?></p>
                                            <p>
                                            <?php
                                            foreach ($tags as $tag)
                                            {
                                                echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/search_c/index?pretraga=' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                                            }
                                            ?>
                                            </p>
                                        </div>
                                        <div class="textRight">
                                            Članak postavio/la: <?php echo $this->formatdate->getFormatDate($article['PostDate']); ?>
                                                <?php
                                                    $nameOfFolder = 'pictures/' . $user['UserID'];
                                                    $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname($_SERVER['PHP_SELF']))).'/'.$nameOfFolder);
                                                    $locationOfPicutre = $baseLocation . '/' . $user['ProfilePicture'];

                                                    echo '<div style="float:left">';
                                                    if($user['ProfilePicture'] != NULL)
                                                    {
                                                        echo '<a href="'.base_url('index.php/main/profile/' . $user['UserID']).'"><img src="'. $locationOfPicutre .'" height="45" width="45"/></a>';
                                                    }
                                                    else
                                                    {
                                                        if($user['Sex'] == 'm')
                                                        {
                                                            echo '<a href="'.base_url('index.php/main/profile/' . $user['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                                        }
                                                        else
                                                        {
                                                            echo '<a href="'.base_url('index.php/main/profile/' . $user['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                                        }
                                                    }
                                                    echo '<b><a href="'. base_url('index.php/main/profile/' . $article['UserID']) .'">
                                                    ' . $user['FirstName'] . ' ' . $user['LastName'] . '</a></b></div>'; 
                                                ?>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
    </div>
  </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>