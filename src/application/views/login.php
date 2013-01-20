<?php 
    $data['title'] = 'Login';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
  <h2>Login page</h2>
  <hr/>
  <?php 
    if(isset($error))
    {
        echo '<div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4>Upozorenje!</h4>
                '.$error.'
              </div>';
    }
  ?> 
  <form class="form-inline" action="<?php echo base_url('index.php/login_c/loginUser'); ?>" method="post">
      <input type="text" name="email_username" placeholder="Email or username"/>
      <input type="password" name="password" placeholder="Password"/>
      <button type="submit" name="login" class="btn">Log in</button>
  </form>
</div>
<?php 
    $this->load->view('static/footer.php');
?>