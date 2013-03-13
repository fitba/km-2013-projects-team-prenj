<?php 
    $data['title'] = 'Korisnici';
    $this->load->view('static/header.php', $data);
?>
<div class="row-fluid">
  <div class="span12">
    <h2>Korisnici</h2>
    <form action="<?php echo base_url('index.php/qawiki_c/users') ?>" method="GET" class="input-append">
        <input name="user_search" class="span4" type="text">
        <input class="btn" type="submit" value="Pretraga" />
    </form>
    <?php 
        if(!isset($_GET['user_search']))
        {   
    ?>
    <table class="table">
        <tbody>
            <?php
            $iterate = 0;
            for ($i = 0; $i < count($users); $i++)
            {
                echo '<tr>';
                for ($j = 0; $j < 4; $j++)
                {
                    if($iterate != count($users))
                    {
                        echo '<td><div class="formatPicture">';
                        if($users[$iterate]['ProfilePicture'] != NULL)
                        {
                            echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. $users[$iterate]['ProfilePicture'] .'" height="61" width="60"/></a>';
                        }
                        else
                        {
                            if($users[$iterate]['Sex'] == 'm')
                            {
                                echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="61" width="60"/></a>';
                            }
                            else
                            {
                                echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="61" width="60"/></a>';
                            }
                        }
                        $userTags = $this->qawiki_m->getTagsForUsers($users[$iterate]['UserID']);
                        echo '</div>
                              <div>
                                <a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'">' . $users[$iterate]['FirstName'] . ' ' . $users[$iterate]['LastName'] . '</a>
                                    <div>'.$users[$iterate]['Location'].'</div>
                                    <div>';
                                    foreach($userTags as $tag)
                                    {
                                        echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/search_c/index?pretraga=' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                                    }
                        echo       '</div>
                              </div>
                             </td>';
                        $iterate++;
                    }
                }
                $i = $iterate - 1;
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <?php 
        }
        else
        {
    ?>
     <table class="table">
        <tbody>
            <?php
            $iterate = 0;
            for ($i = 0; $i < count($users); $i++)
            {
                echo '<tr>';
                for ($j = 0; $j < 4; $j++)
                {
                    if($iterate != count($users))
                    {
                        echo '<td><div class="formatPicture">';
                        if($users[$iterate]['ProfilePicture'] != NULL)
                        {
                            echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. $users[$iterate]['ProfilePicture'] .'" height="61" width="60"/></a>';
                        }
                        else
                        {
                            if($users[$iterate]['Sex'] == 'm')
                            {
                                echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. base_url('pictures/default_male.gif') .'" height="61" width="60"/></a>';
                            }
                            else
                            {
                                echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. base_url('pictures/default_female.gif') .'" height="61" width="60"/></a>';
                            }
                        }
                        $userTags = $this->qawiki_m->getTagsForUsers($users[$iterate]['UserID']);
                        echo '</div>
                              <div>
                                <a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'">' . $users[$iterate]['FirstName'] . ' ' . $users[$iterate]['LastName'] . '</a>
                                    <div>'.$users[$iterate]['Location'].'</div>
                                    <div>';
                                    foreach($userTags as $tag)
                                    {
                                        echo '<span class="label"><a style="color:#FFF" href="'.base_url('index.php/search_c/index?pretraga=' . $tag['Name']).'">'.$tag['Name'].'</a></span>' . ' ';
                                    }
                        echo       '</div>
                              </div>
                             </td>';
                        $iterate++;
                    }
                }
                $i = $iterate - 1;
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <?php
        }
    ?>
  </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>