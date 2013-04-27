<?php 
    $data['title'] = 'Wikipedia';
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
            if($key == 'postArticles')
            {
        ?>
        <h2>Postavite članak</h2>
        <hr/>
        <form action="<?php echo base_url('index.php/qawiki_c/wiki/' . $key); ?>" method="post">
            <p><input type="text" name="title" placeholder="Ovdje unesite naslov članka" class="input-xxlarge" /></p>
            <p><textarea id="editor" name="content"></textarea></p>
            <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>" /></p>
            <p><input type="hidden" name="postDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
            <p class="autosuggest">
                <input type="text" id="tags" name="tags" placeholder="Ovdje unesite tagove">
                <div class="autocomplete">
                    <ul class="result">
                    </ul>
                </div>
            </p>
            <p><input type="submit" name="postArticle" class="btn" value="Submit"></p>
        </form>
        <?php
            }
            else if($key == 'articles')
            {
        ?>
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
                                
                                $sum = $this->general_m->sum('evaluation', 'Evaluate', 'ArticleID = ' . $article['ArticleID']);
                                $count = $this->general_m->countRows('evaluation', 'Evaluate', 'ArticleID = ' . $article['ArticleID']);
                                
                                $averageEvaluate = 0;
                                if($count != 0)
                                    $averageEvaluate = number_format(($sum / $count), 1);
                        ?>
                                <tr>
                                    <td>
                                        <div class="votes">
                                            <center>
                                                <?php echo '<b>' . $resultOfVotes . '</b>'; ?><br/> votes<br/>
                                                <?php echo '<b>' .  $views  . '</b>';  ?><br/> views
                                                <br/><br/>
                                                <p class="ocjena"><?php echo $averageEvaluate; ?> / 5</p>
                                            </center>
                                        </div>
                                        <div class="questions">
                                            <p class="title"><a href="<?php echo base_url('index.php/main/article/' . $article['ArticleID']); ?>"><?php echo $article['Title'] ?></a></p>
                                            <p>
                                                <?php
                                                    if(strlen(strip_tags($article['Content'])) >= 450)
                                                    {
                                                        echo substr(strip_tags(html_entity_decode($article['Content'])), 0, 450) . '...';
                                                    }
                                                    else
                                                    {
                                                        echo html_entity_decode($article['Content']);
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
                                            Članak postavio/la: <?php echo $this->formatdate->getFormatDate($article['PostDate']); ?>
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
        <tbody>
        <?php
            }
        }
        ?>
    </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>