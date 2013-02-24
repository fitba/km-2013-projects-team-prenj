<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container-fluid">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
          <a class="brand" href="<?php echo base_url('index.php/main/index'); ?>"><img src="<?php echo base_url('assets/images/logo.png'); ?>"/></a>
        <div class="nav-collapse collapse">
          <ul class="nav pull-right">
              <li class="divider-vertical"></li>
          <?php
          $sessionData = $this->login_m->isLoggedIn();
          if(!isset($sessionData))
          {
          ?>
              <li><a href="<?php echo base_url('index.php/login_c/loginUser') ?>" >Login</a></li>
              <li><a href="<?php echo base_url('index.php/register_c/register') ?>" >Register</a></li>
          <?php 
          }
          else
          {
            echo '<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$sessionData['FirstName'].' '.$sessionData['LastName'].' <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="'.base_url('index.php/main/profile/' . $sessionData['UserID']).'">Profil</a></li>
                      <li><a href="'.base_url('index.php/login_c/logout').'">Logout</a></li>
                    </ul>
                  </li>';
          }
          ?>
          </ul>
          
          <ul class="nav pull-left">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Q/A Sekcija <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url('index.php/qawiki_c/qa/ask'); ?>">Postavite pitanje</a></li>
                  <li><a href="<?php echo base_url('index.php/qawiki_c/qa/questions'); ?>">Lista pitanja</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Wiki Sekcija <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url('index.php/qawiki_c/wiki/postArticles'); ?>">Postavite članak</a></li>
                  <li><a href="<?php echo base_url('index.php/qawiki_c/wiki/articles'); ?>">Lista članaka</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Ostalo <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url('index.php/qawiki_c/users'); ?>">Korisnici</a></li>
                  <li><a href="<?php echo base_url('index.php/qawiki_c/tags'); ?>">Tagovi</a></li>
                  <li><a href="<?php echo base_url('index.php/main/changes'); ?>">Promjene</a></li>
                </ul>
              </li>
          </ul>
            <form class="navbar-search pull-left" action="<?php echo base_url('index.php/search_c/index'); ?>" method="GET">
                <input type="text" name="pretraga" class="search-query span6" placeholder="Pretraga">
            </form>
        </div><!--/.nav-collapse -->
      </div>
    </div>
</div>

