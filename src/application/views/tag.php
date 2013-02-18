<?php 
    $data['title'] = 'Tag';
    $this->load->view('static/header.php', $data);
?>
<div class="hero-unit">
<?php if(isset($tag)) echo $tag; ?>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>