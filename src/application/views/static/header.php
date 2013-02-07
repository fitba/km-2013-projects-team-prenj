<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('bootstrap/css/bootstrap.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('bootstrap/css/bootstrap-responsive.css'); ?>"/>
    <link rel="stylesheet" type="text/css"  href="<?php echo base_url('assets/css/myCssStyle.css'); ?>"/>
    
    <title>
        <?php 
	if(isset($title))
            echo $title;
	?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Knowledge Management System Project">
    <meta name="author" content="Zajim Kujović">

    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 20px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>
    <script type="text/javascript" src="<?php echo base_url("ckeditor/ckeditor.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/javascript/jquery.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/javascript/main.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("bootstrap/js/bootstrap.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("bootstrap/js/popover.js"); ?>"></script>
    
    <script type="text/javascript"> 
        var editor, html = '';
        function createEditor() 
        {
            if ( editor ) return;
            var config = {};
            config.entities = true;
            editor = CKEDITOR.replace( 'editor', config );
        }
        /* Skripta za pamćenje pozicije scroll-a. Ovo je mnogo korisno u sledećem slučaju: 
         * Kada pritisnemo dugme submit, a negdje smo na dno stranice, stranica se refrešuje 
         * i scroll se podesi na vrh stranice. Da se to ne bi dešavalo, sa ovom skriptom pamtimo scroll poziciju
         * i stavićemo tu poziciju da nam bude i posle refrešovanaj stranice.
         * */
        function saveScrollPositions(theForm) 
        {
            if(theForm) 
            {
                var scrolly = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;
                var scrollx = typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement.scrollLeft;
                theForm.scrollx.value = scrollx;
                theForm.scrolly.value = scrolly;
            }
        }
    </script>
  </head>
  <body onload="createEditor()">
<?php 
    $this->load->view('static/main-menu.php');
?>
<div class="container-fluid">
  <div class="row-fluid">
    <?php 
        $this->load->view('static/side-bar-nav.php');
    ?>
        <div class="span9">
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
   
   $phpSelfFile = $_SERVER['PHP_SELF'];

   $link = explode('/', $phpSelfFile);
   $linkForRedirect = '';
   
   if(!isset($_SESSION['nameOfFunction']))
   {
        foreach ($link as $key => $value) 
        {
            if($key != 0 && $key != 1 && $key != 2 && $key != 3)
            {
                if(($key + 1) == count($link))
                {
                    $linkForRedirect .= $value;
                }
                else
                {
                    $linkForRedirect .= $value . '/';
                }
            }
        }
        $_SESSION['redirect'] = $linkForRedirect;
   }
   else
   {
       if($link[count($link) - 1] != $_SESSION['nameOfFunction'])
       {
            foreach ($link as $key => $value) 
             {
                 if($key != 0 && $key != 1 && $key != 2 && $key != 3)
                 {
                     if(($key + 1) == count($link))
                     {
                         $linkForRedirect .= $value;
                     }
                     else
                     {
                         $linkForRedirect .= $value . '/';
                     }
                 }
             }
             $_SESSION['redirect'] = $linkForRedirect;
       }
   }
?> 