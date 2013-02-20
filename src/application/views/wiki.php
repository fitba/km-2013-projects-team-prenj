<?php 
    $data['title'] = 'Wikipedia';
    $this->load->view('static/header.php', $data); 
?>
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
            <p><input type="text" id="tagsArticle" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge"> [<i>Tagove odvojte praznim poljem (razmakom)</i>]</p>
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
                                                echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/tag_c/index/' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                                            }
                                            ?>
                                            </p>
                                        </div>
                                        <div class="textRight">Članak postavio/la: <?php echo '<b><a href="'. base_url('index.php/main/profile/' . $article['UserID']) .'">' . $user['FirstName'] . ' ' . $user['LastName'] . '</a> | '. $this->formatdate->getFormatDate($article['PostDate']) . '</b>'; ?></div>
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