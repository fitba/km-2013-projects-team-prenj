<?php 
    $data['title'] = 'Nedavne izmjene';
    $this->load->view('static/header.php', $data); 
?>
<div class="hero-unit">
  <h3>Izmjene</h3>
  <p></p>
  <p></p>
</div>
<div class="row-fluid">
  <div class="span12">
      <table class="table">
        <tr>
            <th></th>
            <th>Datum promjene</th>
            <th>Korisnik</th>
            <th>Novi naslov</th>
            <th>Novi sadržaj</th>
        </tr>
      <?php 
      foreach ($changes as $change)
      {
      ?>
        <tr>
            <td><a href="<?php echo base_url('index.php/main/changes/' . $change['LogID']); ?>">Prikaži</a></td>
            <td><?php echo $change['LogDate']; ?></td>
            <td><?php echo $change['FirstName'] . ' ' . $change['LastName']; ?></td>
            <td><?php echo $change['NewTitle']; ?></td>
            <td><?php echo '<textarea disabled="disabled" class="textareaFixed">' . $change['NewContent'] . '</textarea>'; ?></td>
        </tr>
      <?php
      }
      ?>
     </table>
  </div><!--/span-->
</div><!--/row-->
<?php 
    $this->load->view('static/footer.php'); 
?>