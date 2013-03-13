<?php 
    $data['title'] = 'Tagovi';
    $this->load->view('static/header.php', $data);
    
if(!isset($tag_id))
{
?>
    <h3>Tags</h3>
    <form action="<?php echo base_url('index.php/qawiki_c/tags') ?>" method="GET" class="input-append">
        <input name="tag_search" class="span4" type="text">
        <input class="btn" type="submit" value="Pretraga" />
    </form>
    <hr/>
    <?php
    if(!isset($_GET['tag_search']))
    {
        $iterate = 0;
        for ($i = 0; $i < count($tags); $i++)
        {
            echo '<div class="row-fluid">';
            for ($j = 0; $j < 3; $j++)
            {
                if($iterate != count($tags))
                {
                    $count = $this->general_m->countRows('follow_tags', 'UserID', "TagID = " . $tags[$iterate]['TagID']);
                    echo '<div class="span4">
                            <h4><a href="'.base_url('index.php/search_c/index?pretraga=' . $tags[$iterate]['Name']).'" class="btn btn-primary btn-small">'.$tags[$iterate]['Name']. '</a> <strong class="likeTags" id="tag'.$tags[$iterate]['TagID'].'"> x '.$count.'</strong></h4>';
                            if(strlen($tags[$iterate]['Description']) > 350)
                            {
                                echo '<p>'.substr(html_entity_decode($tags[$iterate]['Description']), 0, 350) . '...</p>';
                            }
                            else
                            {
                                echo '<p>'.html_entity_decode($tags[$iterate]['Description']).'</p>';
                            }
                    echo '<p><a class="btn btn-mini" href="'.base_url('index.php/qawiki_c/tags/' . $tags[$iterate]['TagID']).'">Detalji</a> <a class="btn btn-mini btn-primary" href="#" onclick="like('.$tags[$iterate]['TagID'].', \'/index.php/ajax/likeTag/\');"><i class="icon-white icon-thumbs-up"></i></a></p>
                          </div>';
                    $iterate++;
                }
            }
            $i = $iterate - 1;
            echo '</div><hr/>';
        }
    }
    else
    {
        $iterate = 0;
        for ($i = 0; $i < count($tags); $i++)
        {
            echo '<div class="row-fluid">';
            for ($j = 0; $j < 3; $j++)
            {
                if($iterate != count($tags))
                {
                    $count = $this->general_m->countRows('follow_tags', 'UserID', "TagID = " . $tags[$iterate]['TagID']);
                    echo '<div class="span4">
                            <h4><a href="'.base_url('index.php/search_c/index?pretraga=' . $tags[$iterate]['Name']).'" class="btn btn-primary btn-small">'.$tags[$iterate]['Name']. '</a> <strong class="likeTags" id="tag'.$tags[$iterate]['TagID'].'"> x '.$count.'</strong></h4>';
                            if(strlen($tags[$iterate]['Description']) > 350)
                            {
                                echo '<p>'.substr(html_entity_decode($tags[$iterate]['Description']), 0, 350) . '...</p>';
                            }
                            else
                            {
                                echo '<p>'.html_entity_decode($tags[$iterate]['Description']).'</p>';
                            }
                    echo '<p><a class="btn btn-mini" href="'.base_url('index.php/qawiki_c/tags/' . $tags[$iterate]['TagID']).'">Detalji</a> <a class="btn btn-mini btn-primary" href="#" onclick="like('.$tags[$iterate]['TagID'].', \'/index.php/ajax/likeTag/\');"><i class="icon-white icon-thumbs-up"></i></a></p>
                          </div>';
                    $iterate++;
                }
            }
            $i = $iterate - 1;
            echo '</div><hr/>';
        }
    }
}
else
{
    if(count($tag) > 0)
    {
?>
    <h3><?php echo $tag['Name']; ?> <a style="float: right; font-size: 13px;"  href="<?php echo base_url('index.php/qawiki_c/tags/' . $tag['TagID'] . '?editTag=true'); ?>">[promijeni]</a></h3>
    <hr/>
    <?php
    if(isset($_GET['editTag']) && $_GET['editTag'] == 'true')
    {
        
        if($tag['Description'] === NULL)
        {
            echo '<form action="'.  base_url('index.php/qawiki_c/tags/' . $tag['TagID']) .'" method="post" onsubmit="return saveScrollPositions(this);">
                    <p><textarea id="editor" name="description">Trenutno nemate nikakav opis za ovaj tag.</textarea></p>
                    <p><input type="submit" name="submitEditTag" value="Promijeni" class="btn btn-primary"/></p>
                 </form>';
        }
        else
        {
            echo '<form action="'.  base_url('index.php/qawiki_c/tags/' . $tag['TagID']) .'" method="post" onsubmit="return saveScrollPositions(this);">
                    <p><textarea id="editor" name="description">'.html_entity_decode($tag['Description']).'</textarea></p>
                    <p><input type="submit" name="submitEditTag" value="Promijeni" class="btn btn-primary"/></p>
                 </form>';
        }
    }
    else
    {
        if($tag['Description'] === NULL)
        {
            echo '<p>Trenutno nemate nikakav opis za ovaj tag.</p>';
        }
        else 
        {
            echo html_entity_decode($tag['Description']);
        }
    }
    ?>
<?php
    }
}
    $this->load->view('static/footer.php'); 
?>
