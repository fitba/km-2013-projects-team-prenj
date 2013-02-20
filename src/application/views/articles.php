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
                    <div class="votes1">
                        <center>
                            <div><a href="<?php echo base_url('index.php/main/article/' . $article_id . '/' . 1); ?>"><img src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></a></div>
                            <?php echo $resultOfVotesForQuestion; ?><br/> votes
                            <div><a href="<?php echo base_url('index.php/main/article/' . $article_id . '/' . 0); ?>"><img src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></a></div>
                        </center>
                    </div>
                    <div class="questions">
                        <p>
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
                        </p>
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
</div>
<?php 
    $this->load->view('static/footer.php');
?>
