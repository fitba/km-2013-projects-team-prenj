<?php 
    $data['title'] = 'Register';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
  <h2>Register page</h2>
  <hr/>
  <?php 
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
  <form class="form-horizontal" action="<?php echo base_url('index.php/register_c/registerUser'); ?>" method="post">
    <div class="control-group">
       <label class="control-label" for="firstName">First name:</label>
       <div class="controls">
           <input type="text" name="firstName" id="firstName" placeholder="First name">
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="lastName">Last name:</label>
       <div class="controls">
           <input type="text" name="lastName" id="lastName" placeholder="Last name">
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="username">Username*:</label>
       <div class="controls">
           <input type="text" name="username" id="username" placeholder="Username">
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="password">Password*:</label>
       <div class="controls">
           <input type="password" name="password" id="password" placeholder="Password">
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="passwordConfirm">Confirm password*:</label>
       <div class="controls">
           <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirm password">
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="email">Email*:</label>
       <div class="controls">
           <input type="text" name="email" id="email" placeholder="Email">
       </div>
    </div>
    <div class="form-actions">
        <button type="submit" name="registerUser" class="btn btn-primary">Submit</button>
        <button type="button" class="btn">Cancel</button>
    </div>
   </form>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>