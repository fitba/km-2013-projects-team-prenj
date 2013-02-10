<?php 
    $data['title'] = 'Tagovi';
    $this->load->view('static/header.php', $data);
?>
<div class="row-fluid">
  <div class="span12">
    <h2>Tags</h2>
    <table class="table">
        <tbody>
            <?php
            $iterate = 0;
            for ($i = 0; $i < count($tags); $i++)
            {
                echo '<tr>';
                for ($j = 0; $j < 4; $j++)
                {
                    if($iterate != count($tags))
                    {
                        echo '<td>
                                <a href="#" class="hoverEffect" id="'.$iterate.'">
                                    <span class="label">'.$tags[$iterate]['Name']. '</span>
                                </a>
                                <p class="bubble" id="bubble'.$iterate.'">'.$tags[$iterate]['Description'].'</p>
                                <br/>' . $tags[$iterate]['Description'] . '
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
