<?php 
    $data['title'] = 'Pretraga';
    $this->load->view('static/header.php', $data);
?>
<div class="hero-unit">
<table class="table">
        <tbody>
            <?php 
            if(isset($results))
            {
                if(count($results) == 0)
                {
                    echo '<tr><td><h4>Nema rezultata pretrage za termin \' <b><i><u>' . $_GET['pretraga'] . '</u></i></b> \'</h4></td></tr>';
                }
                else
                {
                    for ($i = 0; $i < count($results); $i++)
                    {
                        if($results[$i]->keyword == 'question')
                        {
                ?>
                <tr>
                    <td>
                        <p class="title"><?php echo '<a href="'. base_url('index.php/main/question/' . $results[$i]->myid) .'">' . $results[$i]->title . '</a>'; ?></p>
                        <p><?php echo $results[$i]->contents; ?></p>
                        <p>
                            <?php 
                                $tags = explode(' ', $results[$i]->tags);
                                foreach ($tags as $value) 
                                {
                                    if(!empty($value))
                                        echo '<span class="label"><a href="'.base_url('index.php/tag_c/index/' . $value).'" style="color:#FFF">' . $value . '</a></span> ';
                                }
                            ?>
                        </p>
                    </td>
                </tr>
                <?php
                        }
                        if($results[$i]->keyword == 'article')
                        {
                ?>
                <tr>
                    <td>
                        <p class="title"><?php echo '<a href="'. base_url('index.php/main/article/' . $results[$i]->myid) .'">' . $results[$i]->title . '</a>'; ?></p>
                        <p><?php echo $results[$i]->contents; ?></p>
                        <p>
                            <?php 
                                $tags = explode(' ', $results[$i]->tags);
                                foreach ($tags as $value) 
                                {
                                    if(!empty($value))
                                        echo '<span class="label"><a href="'.base_url('index.php/tag_c/index/' . $value).'" style="color:#FFF">' . $value . '</a></span> ';
                                }
                            ?>
                        </p>
                    </td>
                </tr>
                <?php   
                        }
                    }
                }
            }
            ?>
        </tbody>
    </table> 
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>