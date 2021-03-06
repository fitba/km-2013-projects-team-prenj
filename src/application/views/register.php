<?php 
    $data['title'] = 'Register';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
  <h2 class="naslov">Register page</h2>
  <hr/>
  <form class="form-horizontal" action="<?php echo base_url('index.php/register_c/register'); ?>" method="post">
    <div class="control-group">
       <label class="control-label" for="firstName">First name:</label>
       <div class="controls">
           <div class="input-prepend">
               <span class="add-on"><i class="icon-font"></i></span>
               <input type="text" name="firstName" id="firstName" placeholder="First name">
           </div>
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="lastName">Last name:</label>
       <div class="controls">
           <div class="input-prepend">
               <span class="add-on"><i class="icon-bold"></i></span>
               <input type="text" name="lastName" id="lastName" placeholder="Last name">
           </div>
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="username">Username*:</label>
       <div class="controls">
           <div class="input-prepend">
               <span class="add-on"><i class="icon-user"></i></span>
               <input type="text" name="username" id="username" placeholder="Username">
           </div>
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="password">Password*:</label>
       <div class="controls">
           <div class="input-prepend">
               <span class="add-on"><i class="icon-qrcode"></i></span>
               <input type="password" name="password" id="password" placeholder="Password">
           </div>
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="passwordConfirm">Confirm password*:</label>
       <div class="controls">
           <div class="input-prepend">
               <span class="add-on"><i class="icon-qrcode"></i></span>
               <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirm password">
           </div>
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="email">Email*:</label>
       <div class="controls">
           <div class="input-prepend">
               <span class="add-on"><i class="icon-envelope"></i></span>
               <input type="text" name="email" id="email" placeholder="Email">
           </div>
       </div>
    </div>
    <div class="control-group">
       <label class="control-label" for="email">Pol*:</label>
       <div class="controls">
           <div class="input-prepend">
               <span class="add-on"><i class="icon-adjust"></i></span>
               <select name="sex">
                    <option value="0"></option>
                    <option value="m">Muško</option>
                    <option value="f">Žensko</option>
              </select>
           </div>
       </div>
    </div>
      <input type="hidden" name="registrationDate" value="<?php echo date("Y-m-d H:i:s"); ?>"/>
      <input type="hidden" name="key" value="<?php echo hash('sha256', uniqid()); ?>"/>
    <div class="form-actions">
        <button type="submit" name="registerUser" class="btn btn-primary"><i class="icon-ok icon-white"></i> Submit</button>
        <button type="button" class="btn btn-primary"><i class="icon-remove icon-white"></i> Cancel</button>
    </div>
   </form>
</div>
<?php 
    $this->load->view('static/footer.php'); 
?>