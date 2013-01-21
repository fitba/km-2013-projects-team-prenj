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
    if($ask)
    {
    ?>
    <h2>Postavite pitanje</h2>
    <input type="text" placeholder="Ovdje unesite naslov pitanja" class="input-xxlarge">
    <textarea id="editor" onload="createEditor();"></textarea>
    <?php 
    }
    ?>
  </div><!--/span-->
</div><!--/row-->
<?php 
    $this->load->view('static/footer.php'); 
?>