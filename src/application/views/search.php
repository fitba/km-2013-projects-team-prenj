<?php 
    $data['title'] = 'Pretraga';
    $this->load->view('static/header.php', $data);
?>
<?php 
if(isset($results))
{
    if(count($results) == 0)
    {
        echo '<tr><td><h4>Nema rezultata pretrage za termin \' <b><i><u>' . $_GET['pretraga'] . '</u></i></b> \'</h4></td></tr>';
    }
    else
    {
?>
<div class="tabbable"> <!-- Only required for left/right tabs -->
    <ul class="nav nav-tabs">
        <?php
        $article = 0;
        $question = 0;
        foreach ($results as $value) 
        {
            if($value->keyword == 'question')
            {
                $question++;
            }
            
            if($value->keyword == 'article')
            {
                $article++;
            }
        }
        if($question > $article)
        {
            echo '<li class="active"><a href="#tab1" data-toggle="tab">Pitanja</a></li>
                  <li><a href="#tab2" data-toggle="tab">Članci</a></li>';
        }
        else if($question < $article)
        {
            echo '<li><a href="#tab1" data-toggle="tab">Pitanja</a></li>
                  <li class="active"><a href="#tab2" data-toggle="tab">Članci</a></li>';
        }
        else
        {
            echo '<li class="active"><a href="#tab1" data-toggle="tab">Pitanja</a></li>
                  <li><a href="#tab2" data-toggle="tab">Članci</a></li>';
        }
        ?>
        
    </ul>
    <div class="tab-content">
        <?php 
        if($question > $article)
        {
            echo '<div class="tab-pane active" id="tab1">';
        }
        else if($question < $article)
        {
            echo '<div class="tab-pane" id="tab1">';
        }
        else
        {
            echo '<div class="tab-pane active" id="tab1">';
        }
        ?>
            <table class="table">
                <tbody>
                    <?php 
                    for ($i = 0; $i < count($results); $i++)
                    {
                        if($results[$i]->keyword == 'question')
                        {
                            $negative = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $results[$i]->myid . " AND Positive = '0'");
                            $positive = $this->general_m->countRows('votes', 'VoteID', "QuestionID = " . $results[$i]->myid . " AND Positive = '1'");
                            $answers = $this->general_m->countRows('answers', 'AnswerID', 'QuestionID = ' . $results[$i]->myid);
                            //$user = $this->general_m->selectSomeById('*', 'users', 'UserID = ' . $question['UserID']);
                            $views = $this->general_m->countRows('views', 'ViewID', 'QuestionID = ' . $results[$i]->myid);
                            $tags = $this->qawiki_m->getTagsForQuestion($results[$i]->myid);

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
                                <p class="title"><?php echo '<a href="'. base_url('index.php/main/question/' . $results[$i]->myid) .'">' . $results[$i]->title . '</a>'; ?></p>
                                <p>
                                    <?php
                                    if(strlen(strip_tags($results[$i]->contents)) > 450)
                                    {
                                        echo substr(strip_tags(html_entity_decode($results[$i]->contents)), 0, 450) . '...';
                                    }
                                    else
                                    {
                                        echo html_entity_decode($results[$i]->contents);
                                    }
                                    ?>
                                </p>
                                <p>
                                    <?php 
                                        $tags = explode(' ', $results[$i]->tags);
                                        foreach ($tags as $value) 
                                        {
                                            if(!empty($value))
                                                echo '<span class="label"><a href="'.base_url('index.php/search_c/index?pretraga=' . $value).'" style="color:#FFF">' . $value . '</a></span> ';
                                        }
                                    ?>
                                </p>
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
        <?php 
        if($question > $article)
        {
            echo '<div class="tab-pane" id="tab2">';
        }
        else if($question < $article)
        {
            echo '<div class="tab-pane active" id="tab2">';
        }
        else
        {
            echo '<div class="tab-pane" id="tab2">';
        }
        ?>
            <table class="table">
                <tbody>
                    <?php
                    for ($i = 0; $i < count($results); $i++)
                    {
                        if($results[$i]->keyword == 'article')
                        {
                            $tags = $this->qawiki_m->getTagsForArticle($results[$i]->myid);
                            //$user = $this->general_m->selectSomeById('*', 'users', 'UserID = ' . $article['UserID']);
                            $negative = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $results[$i]->myid . " AND Positive = '0'");
                            $positive = $this->general_m->countRows('votes', 'VoteID', "ArticleID = " . $results[$i]->myid . " AND Positive = '1'");
                            $views = $this->general_m->countRows('views', 'ViewID', 'ArticleID = ' . $results[$i]->myid);

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
                                    <p class="title"><?php echo '<a href="'. base_url('index.php/main/article/' . $results[$i]->myid) .'">' . $results[$i]->title . '</a>'; ?></p>
                                    <p>
                                        <?php
                                        if(strlen(strip_tags($results[$i]->contents)) > 450)
                                        {
                                            echo substr(strip_tags(html_entity_decode($results[$i]->contents)), 0, 450) . '...';
                                        }
                                        else
                                        {
                                            echo html_entity_decode($results[$i]->contents);
                                        }
                                        ?>
                                    </p>
                                    <p>
                                        <?php 
                                            $tags = explode(' ', $results[$i]->tags);
                                            foreach ($tags as $value) 
                                            {
                                                if(!empty($value))
                                                    echo '<span class="label"><a href="'.base_url('index.php/search_c/index?pretraga=' . $value).'" style="color:#FFF">' . $value . '</a></span> ';
                                            }
                                        ?>
                                    </p>
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
    }
}
?>
                
<?php 
    $this->load->view('static/footer.php'); 
?>