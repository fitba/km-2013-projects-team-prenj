<?php 
    $data['title'] = 'Login';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
  <h2 class="naslov">Login page</h2>
  <hr/>
 
  <form class="form-inline" action="<?php echo base_url('index.php/login_c/loginUser'); ?>" method="post">
      <div class="input-prepend">
        <span class="add-on">@</span>
        <input type="text" name="email_username" placeholder="Email or username"/>
      </div>
      <div class="input-prepend">
        <span class="add-on">#</span>
        <input type="password" name="password" placeholder="Password"/>
      </div>
      
      <button type="submit" name="login" class="btn btn-primary"><i class="icon-ok icon-white"></i> Log in</button>
  </form>
</div>
<?php 
    $this->load->view('static/footer.php');
?>