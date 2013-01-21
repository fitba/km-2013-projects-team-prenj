<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container-fluid">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="#">Knowledge Management System</a>
        <div class="nav-collapse collapse">
          <p class="navbar-text pull-right">
          <?php 
          if(!isset($sessionData))
          {
          ?>
            <a href="<?php echo base_url('index.php/main/login') ?>" >Login &NegativeMediumSpace;</a>
            <a href="<?php echo base_url('index.php/main/register') ?>" >Register</a>
          <?php 
          }
          else
          {
              echo 'Welcome &NegativeMediumSpace;<a href="#">'.$sessionData['FirstName'].' '.$sessionData['LastName'].' &NegativeMediumSpace;</a>';
              echo '<a href="'.base_url('index.php/login_c/logout').'">Logout</a>';
          }
          ?>
            &NegativeThickSpace;
          </p>
          <ul class="nav">
              <li><a href="<?php echo base_url('index.php/main/qa_wiki/qa'); ?>">Q/A section</a></li>
            <li><a href="<?php echo base_url('index.php/main/qa_wiki/wiki'); ?>">Wiki section</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
</div>
