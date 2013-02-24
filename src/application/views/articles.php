<?php 
    $data['title'] = 'Članak';
    $this->load->view('static/header.php', $data); 
?>
<div class="row-fluid">
<h3><?php echo $article['Title']; ?> <a style="float: right; font-size: 13px;"  href="<?php echo base_url('index.php/main/article/' . $article_id . '?editArticle=true'); ?>">[promijeni]</a></h3>
  <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes">
                        <center>
                            <div><a href="<?php echo base_url('index.php/main/article/' . $article_id . '/' . 1); ?>"><img src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></a></div>
                            <?php echo $resultOfVotesForQuestion; ?><br/> votes
                            <div><a href="<?php echo base_url('index.php/main/article/' . $article_id . '/' . 0); ?>"><img src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></a></div>
                        </center>
                    </div>
                    <div class="questions">
                        <?php
                        if(isset($_GET['editArticle']) && $_GET['editArticle'] == 'true')
                        {
                            $tagsForEdit = '';
                            foreach ($tags as $tag)
                            {
                                $tagsForEdit .= $tag['Name']. ' ';
                            }
                            echo '<form action="'.  base_url('index.php/main/article/' . $article_id) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                    <p><input type="text" name="title" class="input-xxlarge" value="'.$article['Title'].'"/></p>
                                    <p><textarea id="editor" name="content">'.html_entity_decode($article['Content']).'</textarea></p>
                                    <p><input type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge" value="'.$tagsForEdit.'"></p>
                                    <p><input type="submit" name="submitEditArticle" value="Promijeni" class="btn btn-primary"/></p>
                                 </form>';
                        }
                        else
                        {
                            echo html_entity_decode($article['Content']);
                        ?>
                        <p>
                        <?php
                        foreach ($tags as $tag)
                        {
                            echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/tag_c/index/' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                        }
                        ?>
                        </p>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="textRight">
                        Članak postavio/la: <?php echo '<b><a href="'. base_url('index.php/main/profile/' . $article['UserID']) .'">' . $article['FirstName'] . ' ' . $article['LastName'] . '</a> | '. $this->formatdate->getFormatDate($article['PostDate']) .'</b>'; ?>
                        <br/>
                        <?php 
                        foreach ($lastChangeArticle as $changedArticle)
                        {
                            echo 'Članak promijenio/la <b><a href="'. base_url('index.php/main/profile/' . $changedArticle['UserID']) .'">' . $changedArticle['FirstName'] . ' ' . $changedArticle['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($changedArticle['LogDate']) .'</b><br/>';
                        }
                        ?>
                    </div>
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
                    foreach($commentsArticles as $comment)
                    {
                    ?>
                    <div style="float: left">
                        <?php echo $comment['Ordinal'];?>
                    </div>
                    <div style="margin-left: 30px">
                        <?php echo html_entity_decode($comment['Comment']) . ' - <b><a href="'. base_url('index.php/main/profile/' . $comment['CommentsUserID']) .'">' . $comment['FirstName'] . ' ' . $comment['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($comment['CommentDate']) . '</b>'; ?>
                    </div>
                    <hr/>
                    <?php
                    }
                    ?>
                    <div style="margin-left: 30px">
                        <form action="<?php echo base_url('index.php/main/article/' . $article_id); ?>" method="post" onsubmit="return saveScrollPositions(this);">
                            <?php $lastOrdinal = $this->general_m->selectMax('Ordinal', 'comments', 'ArticleID = ' . $article_id); ?>
                            <p><textarea class="commentsSize" name="comment"></textarea></p>
                            <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
                            <p><input type="hidden" name="articleid" value="<?php echo base64_encode($article_id); ?>"/></p>
                            <p><input type="hidden" name="commentDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
                            <p><input type="hidden" name="ordinal" value="<?php if($lastOrdinal['Last'] == null) echo 1; else echo $lastOrdinal['Last'] + 1; ?>"/></p>
                            
                            <input type="hidden" name="scrollx" id="scrollx" value="0" />
                            <input type="hidden" name="scrolly" id="scrolly" value="0" />
                            
                            <p><input type="submit" name="submitComment" value="Komentariši" class="btn btn-primary"/></p>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
<?php 
    $this->load->view('static/footer.php');
?>
