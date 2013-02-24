<?php 
    $data['title'] = 'Nedavne izmjene';
    $this->load->view('static/header.php', $data); 
?>
<div>
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
      if(isset($changes))
      {
        foreach ($changes as $change)
        {
        ?>
          <tr>
              <td><a href="<?php echo base_url('index.php/main/changes/' . $change['LogID']); ?>">Prikaži</a></td>
              <td><?php echo $change['LogDate']; ?></td>
              <td><?php echo $change['FirstName'] . ' ' . $change['LastName']; ?></td>
              <td>
              <?php 

              ?>
              </td>
          </tr>
        <?php
        }
      }
      ?>
     </table>
  </div><!--/span-->
</div><!--/row-->
<?php 
    $this->load->view('static/footer.php'); 
?>