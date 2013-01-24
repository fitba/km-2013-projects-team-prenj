<?php 
    $data['title'] = 'Info page';
    $this->load->view('static/header.php', $data); 
?>

<div class="hero-unit">
<h4><?php if(isset($message)) echo $message; ?></h4>
</div>

<?php
    $this->load->view('static/footer.php', $data); 
?>