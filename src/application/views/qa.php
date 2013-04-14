<?php 
    $data['title'] = 'Q/A sekcija';
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
    });
</script>
<div class="row-fluid">
  <div class="span12">
    <?php
    if(isset($key))
    {
        if($key == 'ask')
        {
    ?>
    <h2>Postavite pitanje</h2>
    <hr/>
    <form action="<?php echo base_url('index.php/qawiki_c/qa/' . $key); ?>" method="post">
        <p><input type="text" name="title" placeholder="Ovdje unesite naslov pitanja" class="input-xxlarge"></p>
        <p><textarea id="editor" name="question"></textarea></p>
        <p><input id="tags" type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge"/></p>
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
                    
                    $sum = $this->general_m->sum('evaluation', 'Evaluate', 'QuestionID = ' . $question['QuestionID']);
                    $count = $this->general_m->countRows('evaluation', 'Evaluate', 'QuestionID = ' . $question['QuestionID']);
                                
                    $averageEvaluate = number_format(($sum / $count), 1);
            ?>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            <?php echo '<b>' . $resultOfVotes . '</b>'; ?><br/> votes<br/>
                            <?php echo '<b>' . $answers . '</b>'; ?><br/> answers
                        </center>
                        <center>
                            <?php echo '<b>' .  $views  . '</b>';  ?> views
                            <br/><br/>
                            <p class="ocjena"><?php echo $averageEvaluate; ?> / 5</p>
                        </center>
                    </div>
                    <div class="questions">
                        <p class="title"><a href="<?php echo base_url('index.php/main/question/' . $question['QuestionID']); ?>"><?php echo $question['Title'] ?></a></p>
                        <p>
                            <?php
                                if(strlen($question['Question']) > 450)
                                {
                                    echo substr(html_entity_decode($question['Question']), 0, 450) . '...';
                                }
                                else
                                {
                                    echo html_entity_decode($question['Question']);
                                }
                            ?>
                        </p>
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
                                echo '<div style="float:left">';
                                if($user['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $user['UserID']).'"><img src="'. $user['ProfilePicture'] .'" height="45" width="45"/></a>';
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
    <?php
        }
    }
    ?>
  </div><!--/span-->
</div><!--/row-->
<?php 
    $this->load->view('static/footer.php'); 
?>