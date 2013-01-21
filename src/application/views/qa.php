<?php 
    $data['title'] = 'Home';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
    <a class="btn" href="<?php echo base_url('index.php/main/qa_wiki/qa/ask'); ?>">Ask Question</a> 
    <a class="btn">Question</a>
    <a class="btn">Tags</a> <a class="btn">Users</a>
    <a class="btn">Badges</a> <a class="btn">Unanswered</a>
</div>
<div class="row-fluid">
  <div class="span12">
    <?php 
    if(isset($ask))
    {
        if(isset($errors))
        {
            echo '<div class="alert alert-error">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Upozorenje!</h4>
                    '.$errors.'
                  </div>';
        }
        if(isset($isOk))
        {
            echo '<div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Informacija!</h4>
                    '.$isOk.'
                  </div>';
        }
        if(isset($unexpectedError))
        {
            echo '<div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Upozorenje!</h4>
                    '.$unexpectedError.'
                  </div>';
        }
    ?>
    <h2>Postavite pitanje</h2>
    <form action="<?php echo base_url('index.php/qawiki_c/askQuestion'); ?>" method="post">
        <p><input type="text" name="title" placeholder="Ovdje unesite naslov pitanja" class="input-xxlarge"></p>
        <p><textarea id="editor" name="editor"></textarea></p>
        <p><input type="text" name="tags" placeholder="Ovdje unesite tagove" class="input-xxlarge"></p>
        <p><input type="submit" name="askQuestion" class="btn" value="Submit"></p>
    </form>
    <?php 
    }
    ?>
  </div><!--/span-->
</div><!--/row-->
<?php 
    $this->load->view('static/footer.php'); 
?>