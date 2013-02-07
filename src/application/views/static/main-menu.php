<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container-fluid">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="<?php echo base_url('index.php/main/index'); ?>">Knowledge Management System</a>
        <div class="nav-collapse collapse">
          <p class="navbar-text pull-right">
          <?php
          $sessionData = $this->login_m->isLoggedIn();
          if(!isset($sessionData))
          {
              $guest = 'Guest_';
              $guest .= rand(1, 9999999);
          ?>
            Welcome <a href="#" ><?php echo $guest; ?></a>
            <a href="<?php echo base_url('index.php/login_c/loginUser') ?>" >Login</a>
            <a href="<?php echo base_url('index.php/register_c/register') ?>" >Register</a>
          <?php 
          }
          else
          {
              echo 'Welcome <a href="#">'.$sessionData['FirstName'].' '.$sessionData['LastName'].'</a>';
              echo '<a href="'.base_url('index.php/login_c/logout').'">Logout</a>';
          }
          ?>
            &NegativeThickSpace;
          </p>
          <ul class="nav">
              <li><a href="<?php echo base_url('index.php/qawiki_c/askQuestion/qa'); ?>">Q/A section</a></li>
            <li><a href="<?php echo base_url('index.php/qawiki_c/askQuestion/wiki'); ?>">Wiki section</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
</div>
