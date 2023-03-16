<?php
  include 'database.php';
  
  //---------------------------------------- Condition to check that POST value is not empty.
  if (!empty($_POST)) {
    // keep track post values
    $id = $_POST['id'];
    
    $myObj = (object)array();
    
    //........................................ 
    $pdo = Database::connect();
    
    $sql = 'SELECT * FROM home WHERE id="' . $id . '"';
    foreach ($pdo->query($sql) as $row) {
      $date = date_create($row['date']);
      $dateFormat = date_format($date,"d-m-Y");
      
      $myObj->id = $row['id'];
      $myObj->suhu = $row['suhu'];
      $myObj->kelem = $row['kelem'];
      $myObj->gas = $row['gas'];
      $myObj->status_gas = $row['status_gas'];
      $myObj->tombol1 = $row['tombol1'];
      $myObj->tombol2 = $row['tombol2'];
      $myObj->ls_time = $row['time'];
      $myObj->ls_date = $dateFormat;
      
      $myJSON = json_encode($myObj);
      
      echo $myJSON;
    }
    Database::disconnect();
    //........................................ 
  }
  //---------------------------------------- 
?>