<?php 
    $data['title'] = 'Wikipedia';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/wiki/postArticles'); ?>">Postavite članak</a>
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/wiki/articles'); ?>">Članci</a>
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/tags'); ?>">Tagovi</a>
    <a class="btn" href="<?php echo base_url('index.php/qawiki_c/users'); ?>">Korisnici</a>
</div>
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
            <p><input type="text" name="title" placeholder="Ovdje unesite naslov članka" class="input-xxlarge" value="<?php if(isset($_SESSION['titleArticle'])) echo $_SESSION['titleArticle'] ?>"></p>
            <p><textarea id="editor" name="article"><?php if(isset($_SESSION['article'])) echo $_SESSION['article']; ?></textarea></p>
            <p><input type="text" id="tagsArticle" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge" value="<?php if(isset($_SESSION['tagsArticle'])) echo $_SESSION['tagsArticle'] ?>"> [<i>Tagove odvojte praznim poljem (razmakom)</i>]</p>
            Ovdje odredite koliko želite pod naslova da navedete za vaš članak. Za svaki pod naslov morate napisati sadržaj.
            Ako ne želite da navedete nijedan pod naslov samo kliknite na polje unosa članka.
            <input style="height: 17px;" type="number" name="numberOfSubtitles" class="input-small"/>
            <input style="margin-bottom: 10px; height: 27px;" type="submit" name="subtitleSubmit" class="btn" value="Potvrdi">
            <?php 
            if(isset($subtitlesTags))
            {
                echo $subtitlesTags; 
            }
            ?>
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
                                $tags = $this->qawiki_m->getTagsForQuestion($article['ArticleID']);
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
                                            <p><?php echo $article['Content'] ?></p>
                                            <p>
                                            <?php
                                            foreach ($tags as $tag)
                                            {
                                                echo '<span class="label">'.$tag['Name'].'</span>' . ' ';
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