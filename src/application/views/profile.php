<?php 
    $data['title'] = 'Profile';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
    <h3><?php echo $userData['FirstName'] . ' ' . $userData['LastName']; ?></h3>
    <hr/>
    <p>Email: <?php echo $userData['Email']; ?></p>
    <p>Pravo ime: <?php echo $userData['FirstName'] . ' ' . $userData['LastName']; ?></p>
    <p>Korisničko ime: <?php echo $userData['Username']; ?></p>
    <p>Datum registracije: <?php echo $this->formatdate->getFormatDate($userData['RegistrationDate']); ?></p>
</div>
<?php 
    $this->load->view('static/footer.php');
?>