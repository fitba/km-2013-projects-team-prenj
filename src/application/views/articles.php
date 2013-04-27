<?php 
    $data['title'] = 'Članak';
    $this->load->view('static/header.php', $data); 
?>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/jquery-ui.css'); ?>"/>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/jquery.tagit.css'); ?>"/>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/tagit.ui-zendesk.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/css/jRating.jquery.css'); ?>" type="text/css" />
<script type="text/javascript" src="<?php echo base_url("assets/javascript/jquery-ui.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/tag-it.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/javascript/tag-it.min.js"); ?>"></script>

<script type="text/javascript" src="<?php echo base_url("assets/javascript/jRating.jquery.js"); ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        
        $(function(){
            var article_id = '<?php echo $article_id; ?>';
            $.post(CI_ROOT + '/index.php/ajax/getEvaluate' + '/article/' + article_id, {  }, function(data){
                var jRateAverage = document.getElementById('jRatingAverage');
                
                if(data == 1)
                {
                    jRateAverage.style.width = '23px';
                }
                else if(data == 2)
                {
                    jRateAverage.style.width = '46px';
                }
                else if(data == 3)
                {
                    jRateAverage.style.width = '69px';
                }
                else if(data == 4)
                {
                    jRateAverage.style.width = '92px';
                }
                else if(data == 5)
                {
                    jRateAverage.style.width = '115px';
                }
                
            });
        });
        
        $("#tags").tagit();
        
        $('#openComment').click(function(){
            $('#commentOpens').slideToggle();
        });
        
        $(".basic").jRating({
	  step:true,
	  length : 5
	});
        
        var evaluate = '';
        $('.basic').mousemove(function(){
            var rate = document.getElementById("jRatingInfos").innerHTML;
            var explode = rate.split(" ");
            evaluate = explode[0];
        });
        
        $('.minus, .minusTest').click(function(){
            var article_id = '<?php echo $article_id; ?>';
           $.post(CI_ROOT + '/index.php/ajax/dismissEvaluate' + '/article/' + article_id, {  }, function(data1){
               if(data1 == 'true')
               {
                    $('.minus, .minusTest').hide();
                    $.post(CI_ROOT + '/index.php/ajax/averageEvaluate' + '/article/' + article_id, { }, function(data2){
                         $('.ocjena').text(data2 + ' / 5');
                     });
               }
               else
               {
                   
               }
           }); 
        });
        
        $('.basic').click(function(){
            var article_id = '<?php echo $article_id; ?>';
            $.post(CI_ROOT + '/index.php/ajax/evaluateArticle/' + article_id + '/' + evaluate, {  }, function(data){
                if(data === 'true')
                {
                    $.post(CI_ROOT + '/index.php/ajax/averageEvaluate' + '/article/' + article_id, { }, function(data){
                        $('.ocjena').text(data + ' / 5');
                    });
                    $('.minusTest').show();
                    $('#response').text('Uspješno ste ocijenili članak');
                    setTimeout(function() {
                        $('#response').fadeOut('fast');
                    }, 1000);
                }
                else
                {
                    $(function() {
                        $('#basic-modal-content').html(data);
                        $('#basic-modal-content').modal();
                    });
                }
            });
        });
    });
</script>



<div class="row-fluid">
<h3><?php echo $article['Title']; ?> <a style="float: right; font-size: 13px;"  href="<?php echo base_url('index.php/main/article/' . $article_id . '?editArticle=true'); ?>">[promijeni]</a></h3>
  <table class="table">
        <tbody>
            <tr>
                <td>
                    <div class="votes1">
                        <center>
                            <div><img class="showsTooltip" onmousemove="Tooltip.Text = 'Ovaj članak je jasan i koristan';" onclick="vote('<?php echo $article['ArticleID']; ?>', '/index.php/ajax/voteArticle/', '1');"  src="<?php echo base_url('assets/images/top_arrow.png'); ?>"/></div>
                            <strong id="numOfArticleVotes"><?php echo $resultOfVotes; ?></strong><br/> votes
                            <div><img class="showsTooltip" onmousemove="Tooltip.Text = 'Ovaj članak nije jasan niti koristan';" onclick="vote('<?php echo $article['ArticleID']; ?>', '/index.php/ajax/voteArticle/', '0');" src="<?php echo base_url('assets/images/bottom_arrow.png'); ?>"/></div>
                            <br/>
                            <p class="ocjena"><?php echo $averageEvaluate; ?> / 5</p>
                        </center>
                    </div>
                    <div class="questions">
                        <?php
                        if(isset($_GET['editArticle']) && $_GET['editArticle'] == 'true')
                        {
                            $tagsForEdit = '';
                            foreach ($tags as $tag)
                            {
                                $tagsForEdit .= $tag['Name']. ',';
                            }
                            echo '<form action="'.  base_url('index.php/main/article/' . $article_id) .'" method="post" onsubmit="return saveScrollPositions(this);">
                                    <p><input type="text" name="title" class="input-xxlarge" value="'.$article['Title'].'"/></p>
                                    <p><textarea id="editor" name="content">'.html_entity_decode($article['Content']).'</textarea></p>
                                    <p><input id="tags" type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge" value="'.$tagsForEdit.'"></p>
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
                            echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/search_c/index?pretraga=' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                        }
                        ?>
                        </p>
                        <?php
                        }
                        ?>
                    </div>
                    <br/><br/><br/>
                    <div class="exemple">
                        <em style="font-size: 18px;"><strong>Ocjenite članak</strong></em>
                        <img class="minusTest showsTooltip" onmousemove="Tooltip.Text = 'Poništite ocjenu';" src="<?php echo base_url('assets/images/minus.png'); ?>" width="40"/>
                        <?php
                        if(isset($user))
                        {
                            if($user > 0)
                            {
                            ?>
                            <img class="minus showsTooltip" onmousemove="Tooltip.Text = 'Poništite ocjenu';" src="<?php echo base_url('assets/images/minus.png'); ?>" width="40"/>
                            <?php 
                            }
                        }
                        ?>
                        <div id="mydiv" class="basic" data-average="12" data-id="1"></div>
                        <div id="response" style="color:green"></div>
                    </div>
                    <div class="textRight">
                        Članak postavio/la:<br/><?php echo  $this->formatdate->getFormatDate($article['PostDate']); ?><br/>
                            <?php     
                                echo '<div style="float:left">';
                                if($article['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $article['UserID']).'"><img src="'. $article['ProfilePicture'] .'" height="45" width="45"/></a>';
                                }
                                else
                                {
                                    if($article['Sex'] == 'm')
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $article['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                    }
                                    else
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $article['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                    }
                                }
                                
                                echo '<b>
                                         <a href="'. base_url('index.php/main/profile/' . $article['UserID']) .'">
                                            ' . $article['FirstName'] . ' ' . $article['LastName'] . '
                                         </a>'
                                    .'</b>'; 
                                echo '</div>';
                            ?>
                    </div>
                    
                    <?php 
                    foreach ($lastChangeArticle as $changedArticle)
                    {
                          echo '<div class="textRight">
                                Članak promijenio/la<br/>' . $this->formatdate->getFormatDate($changedArticle['LogDate']) . '
                                <div style="float:left">';
                                if($changedArticle['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $changedArticle['UserID']).'"><img src="'. $changedArticle['ProfilePicture'] .'" height="45" width="45"/></a>';
                                }
                                else
                                {
                                    if($changedArticle['Sex'] == 'm')
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $changedArticle['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="45" width="45"/></a>';
                                    }
                                    else
                                    {
                                        echo '<a href="'.base_url('index.php/main/profile/' . $changedArticle['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="45" width="45"/></a>';
                                    }
                                }
                                echo '<b><a href="'. base_url('index.php/main/profile/' . $changedArticle['UserID']) .'">
                                        '. $changedArticle['FirstName'] . ' ' . $changedArticle['LastName'] . '</a>
                                        </b>
                                  </div>
                               </div>';
                    }
                    ?>
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
                    <a href="#" style="margin-left: 30px" id="openComment" class="btn btn-mini">Otvorite komentar</a>
                    <br/><br/>
                    <div style="margin-left: 30px; display: none;" id="commentOpens">
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
