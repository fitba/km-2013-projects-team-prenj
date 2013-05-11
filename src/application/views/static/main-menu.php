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
              <li><a href="<?php echo base_url('index.php/login_c/loginUser') ?>" ><i class="icon-folder-open"></i> Login</a></li>
              <li><a href="<?php echo base_url('index.php/register_c/register') ?>" ><i class="icon-share-alt"></i> Register</a></li>
          <?php 
          }
          else
          {
            echo '<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$sessionData['FirstName'].' '.$sessionData['LastName'].' <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      <li><a href="'.base_url('index.php/main/profile/' . $sessionData['UserID']).'"><i class="icon-user"></i> Profil</a></li>
                      <li><a href="'.base_url('index.php/login_c/logout').'"><i class="icon-off"></i> Logout</a></li>
                    </ul>
                  </li>';
          }
          ?>
          </ul>
          
          <ul class="nav pull-left">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-question-sign"></i> Q/A Sekcija <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url('index.php/qawiki_c/qa/ask'); ?>"><i class="icon-plus-sign"></i> Postavite pitanje</a></li>
                  <li><a href="<?php echo base_url('index.php/qawiki_c/qa/questions'); ?>"><i class="icon-list"></i> Lista pitanja</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-globe"></i> Wiki Sekcija <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url('index.php/qawiki_c/wiki/postArticles'); ?>"><i class="icon-plus-sign"></i> Postavite članak</a></li>
                  <li><a href="<?php echo base_url('index.php/qawiki_c/wiki/articles'); ?>"><i class="icon-list"></i> Lista članaka</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-random"></i> Ostalo <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url('index.php/qawiki_c/users'); ?>"><i class="icon-user"></i> Korisnici</a></li>
                  <li><a href="<?php echo base_url('index.php/qawiki_c/tags'); ?>"><i class="icon-tags"></i> Tagovi</a></li>
                </ul>
              </li>
          </ul>
            <form class="navbar-search pull-left" action="<?php echo base_url('index.php/search_c/index'); ?>" method="GET">
                <input type="text" onmousemove="Tooltip.Text = 'Klikom na enter potvrdite pretragu';" name="pretraga" class="showsTooltip search-query span5" placeholder="Pretraga">
            </form>
        </div><!--/.nav-collapse -->
      </div>
    </div>
</div>

