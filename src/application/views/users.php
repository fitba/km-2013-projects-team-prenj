<?php 
    $data['title'] = 'Korisnici';
    $this->load->view('static/header.php', $data);
?>
<div class="row-fluid">
  <div class="span12">
    <h2>Korisnici</h2>
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
                        $nameOfFolder = 'pictures/' . $users[$iterate]['UserID'];
                        $baseLocation = str_replace('index.php/', '', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname(dirname($_SERVER['PHP_SELF']))).'/'.$nameOfFolder);
                        $locationOfPicutre = $baseLocation . '/' . $users[$iterate]['ProfilePicture'];
                        echo '<td><div class="formatPicture">';
                                if($users[$iterate]['ProfilePicture'] != NULL)
                                {
                                    echo '<a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'"><img src="'. $locationOfPicutre .'" height="61" width="60"/></a>';
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
                        echo '</div>
                              <div>
                                <a href="'.base_url('index.php/main/profile/' . $users[$iterate]['UserID']).'">' . $users[$iterate]['FirstName'] . ' ' . $users[$iterate]['LastName'] . '</a>
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
  </div>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>