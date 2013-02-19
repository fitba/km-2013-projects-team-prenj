<?php 
    $data['title'] = 'Članak';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit" style="font-size: 16px;">
<h3><?php echo $article['Title']; ?> <a style="float: right; font-size: 13px;"  href="<?php echo base_url('index.php/main/article/' . $article_id . '?editArticle=true'); ?>">[promijeni]</a></h3>
  <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes1">
                        <center>
                            <div><a href="<?php echo base_url('index.php/main/article/' . $article_id . '/' . 0 . '/' . 1); ?>"><img src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></a></div>
                            <?php echo $resultOfVotesForQuestion; ?><br/> votes
                            <div><a href="<?php echo base_url('index.php/main/article/' . $article_id . '/' . 0 . '/' . 0); ?>"><img src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></a></div>
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
                                    <p><textarea id="editor" name="content">'.$article['Content'].'</textarea></p>
                                    <p><input type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge" value="'.$tagsForEdit.'"></p>
                                    <p><input type="submit" name="submitEditArticle" value="Promijeni" class="btn btn-primary"/></p>
                                 </form>';
                        }
                        else
                        {
                            echo $article['Content'];
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
<div class="row-fluid">
    <div class="span12">
    <?php 
    foreach ($subtitles as $key => $sub)
    {
        $joinSubtitle = array('subtitles' => 'subtitles.SubtitleID = logs.SubtitleID',
                                             'users' => 'users.UserID = logs.UserID');
            
        $whereSubtitle = 'logs.SubtitleID = ' . $sub['SubtitleID'];
        $lastChangeSubtitle = $this->logs_m->getLogsBy('*', $joinSubtitle, $whereSubtitle);
    ?>
        <h4><?php echo $sub['Subtitle']; ; ?> <a style="float: right; font-size: 13px;"  href="<?php echo base_url('index.php/main/article/' . $article_id . '/' . $sub['SubtitleID']); ?>">[promijeni]</a></h4>
        <hr style="margin: 0;"/>
        <?php
            if(isset($subtitle_id) && $subtitle_id == $sub['SubtitleID'])
            {
                echo '<form action="'.  base_url('index.php/main/article/' . $article_id . '/' . $sub['SubtitleID']) .'" method="post" onsubmit="return saveScrollPositions(this);">
                        <p><input type="text" name="subtitle" class="input-xxlarge" value="'.$sub['Subtitle'].'"/></p>
                        <p><textarea id="editor" name="subtitleContent">'.$sub['SubtitleContent'].'</textarea></p>
                        <p><input type="submit" name="submitEditSubtitle" value="Promijeni" class="btn btn-primary"/></p>
                     </form>';
            }
            else
            {
                echo $sub['SubtitleContent']; 
            }
            
            echo '<div class="textRight">';
            
            if($sub['AddDate'] != null && $sub['UserID'] != null)
            {
                echo 'Dodao/la <b><a href="'. base_url('index.php/main/profile/' . $sub['UserID']) .'">' . $sub['FirstName'] . ' ' . $sub['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($sub['AddDate']) .'</b>';
            }
            echo '<br/>';
            foreach($lastChangeSubtitle as $changedSubtitle)
            {
                echo 'Promijenio/la <b><a href="'. base_url('index.php/main/profile/' . $changedSubtitle['UserID']) .'">' . $changedSubtitle['FirstName'] . ' ' . $changedSubtitle['LastName'] . '</a> | ' . $this->formatdate->getFormatDate($changedSubtitle['LogDate']) .'</b>';
            }
            
            echo '</div>';
        ?>
        <br/><br/>
    <?php 
    }
        if(!(isset($_GET['editArticle'], $subtitle_id)))
        {
    ?>
       <form action="<?php echo base_url('index.php/main/article/' . $article_id); ?>" method="post" onsubmit="return saveScrollPositions(this);">
            <p><input type="text" name="subtitle" class="input-xxlarge" placeholder="Ovdje unesite naslov oblasti članka"/></p> 
            <p><textarea id="editor" name="subtitleContent"></textarea></p>
            <p><input type="hidden" name="articleid" value="<?php echo base64_encode($article['ArticleID']); ?>"/></p>
            <p><input type="hidden" name="addDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/></p>
            <p><input type="hidden" name="userid" value="<?php echo base64_encode($sessionData['UserID']); ?>"/></p>
            <p><input type="submit" name="submitAddNewSubtitle" value="Dodaj novu oblast" class="btn btn-primary"/></p>
       </form> 
       <?php
        }
       ?>
    </div>
</div>
<?php 
    $this->load->view('static/footer.php');
?>
