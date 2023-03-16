<!DOCTYPE HTML>
<html>
  <head>
    <title>HOME ASSISTANT SapuID</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/5973/5973800.png">
    <style>
      html {font-family: Arial; display: inline-block; text-align: center;}
      p {font-size: 1.2rem;}
      h4 {font-size: 0.8rem;}
      body {margin: 0;}
      /* ----------------------------------- TOPNAV STYLE */
      .topnav {overflow: hidden; background-color: #0c6980; color: white; font-size: 1.2rem;margin-left:auto;margin-right:auto;}
      /* ----------------------------------- */
      
      /* ----------------------------------- TABLE STYLE */
      .styled-table {
        border-collapse: collapse;
        margin-left: auto; 
        margin-right: auto;
        font-size: 0.9em;
        font-family: sans-serif;
        min-width: 400px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        border-radius: 0.5em;
        overflow: hidden;
        width: 90%;

      }

      .styled-table thead tr {
        background-color: #0c6980;
        color: #ffffff;
        text-align: left;
      }

      .styled-table th {
        padding: 12px 15px;
        text-align: left;
      }

      .styled-table td {
        padding: 12px 15px;
        text-align: left;
      }

      .styled-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
      }

      .styled-table tbody tr.active-row {
        font-weight: bold;
        color: #009879;
      }

      .bdr {
        border-right: 1px solid #e3e3e3;
        border-left: 1px solid #e3e3e3;
      }
      
      td:hover {background-color: rgba(12, 105, 128, 0.21);}
      tr:hover {background-color: rgba(12, 105, 128, 0.15);}
      .styled-table tbody tr:nth-of-type(even):hover {background-color: rgba(12, 105, 128, 0.15);}
      /* ----------------------------------- */
      
      /* ----------------------------------- BUTTON STYLE */
      .btn-group .button {
        background-color: #0c6980; /* Green */
        border: 1px solid #e3e3e3;
        color: white;
        padding: 5px 8px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        cursor: pointer;
        float: center;
      }

      .btn-group .button:not(:last-child) {
        border-right: none; /* Prevent double borders */
      }

      .btn-group .button:hover {
        background-color: #094c5d;
      }

      .btn-group .button:active {
        background-color: #0c6980;
        transform: translateY(1px);
      }

      .btn-group .button:disabled,
      .button.disabled{
        color:#fff;
        background-color: #a0a0a0; 
        cursor: not-allowed;
        pointer-events:none;
      }
      /* ----------------------------------- */
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  </head>
  
  <body>
    <div class="topnav col-12">
      <h3>HOME ASSISTANT sapuID</h3>
    </div>
    
    <br>
    
    <h3 style="color: #0c6980;">RECORD DATA</h3>

    <div class="wrap row text-center text-capitalize mb-3">
          <form action="" method="POST" class="row col-lg-7 col-sm-12">
                <div class="col-sm-12 col-md-12 col-lg-5 mt-3 ">
                  <td class="ps-2 ">dari tanggal</td>
                  <td class="ps-1 "><input type="date" name="dari_tgl" required="required"></td>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-5 mt-3 ">
                  <td class="ps-5">sampai tanggal</td>
                  <td class="ps-1"><input type="date" name="sampai_tgl" required="required"></td>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-1 mt-3 d-flex flex-start"><input type="submit" class="btn btn-primary" name="filter" value="Filter"></div>
          </form>
          <form action="" method="POST" class="col-lg-4 col-md-12 col-sm-12 d-flex flex-start">
            <input type="submit" class="btn btn-primary mx-1 col-sm-12 col-md-6 col-lg-3 mt-3 px-4" name="all" value="All">
          </form>
      </div>
    
    <table class="styled-table" id= "table_id">
      <thead>
        <tr>
          <th>NO</th>
          <th>ID</th>
          <th>BOARD</th>
          <th>TEMPERATURE (°C)</th>
          <th>HUMIDITY (%)</th>
          <th>GAS</th>
          <th>STATUS GAS SENSOR</th>
          <th>LED</th>
          <th>FAN</th>
          <th>TIME</th>
          <th>DATE (dd-mm-yyyy)</th>
        </tr>
      </thead>
      
          <!-- filter end -->

      <tbody id="tbody_table_record">
        <?php
          include 'database.php';
          $num = 0;
          //------------------------------------------------------------ The process for displaying a record table containing the DHT11 sensor data and the state of the LEDs.
          $pdo = Database::connect();
          // replace_with_your_table_name, on this project I use the table name 'esp32_table_dht11_leds_record'.
          // This table is used to store and record DHT11 sensor data updated by ESP32. 
          // This table is also used to store and record the state of the LEDs, the state of the LEDs is controlled from the "home.php" page. 
          // To store data, this table is operated with the "INSERT" command, so this table will contain many rows.
          $i = 1; 
            if(isset($_POST['filter'])){
              $dari_tgl = $_POST['dari_tgl'];
              $sampai_tgl = $_POST['sampai_tgl'];
              $sql = "SELECT * FROM tabel WHERE date BETWEEN '$dari_tgl' AND '$sampai_tgl' ORDER BY ident DESC";
            } else {
              $sql = "SELECT * FROM tabel ORDER BY ident DESC";
            } 
            if(isset($_POST['all'])){
              $sql = "SELECT * FROM tabel ORDER BY ident DESC";
            }
          
          foreach ($pdo->query($sql) as $row) {
            $date = date_create($row['date']);
            $dateFormat = date_format($date,"d-m-Y");
            $num++;
            echo '<tr>';
            echo '<td>'. $num . '</td>';
            echo '<td class="bdr">'. $row['id'] . '</td>';
            echo '<td class="bdr">'. $row['board'] . '</td>';
            echo '<td class="bdr">'. $row['suhu'] . '</td>';
            echo '<td class="bdr">'. $row['kelem'] . '</td>';
            echo '<td class="bdr">'. $row['gas'] . '</td>';
            echo '<td class="bdr">'. $row['status_gas'] . '</td>';
            echo '<td class="bdr">'. $row['tombol1'] . '</td>';
            echo '<td class="bdr">'. $row['tombol2'] . '</td>';
            echo '<td class="bdr">'. $row['time'] . '</td>';
            echo '<td>'. $dateFormat . '</td>';
            echo '</tr>';
          }
          Database::disconnect();
          //------------------------------------------------------------
        ?>
      </tbody>
    </table>
    
    <br>
    
    <div class="btn-group">
      <button class="button" id="btn_prev" onclick="prevPage()">Prev</button>
      <button class="button" id="btn_next" onclick="nextPage()">Next</button>
      <div style="display: inline-block; position:relative; border: 0px solid #e3e3e3; float: center; margin-left: 2px;;">
        <p style="position:relative; font-size: 14px;"> Table : <span id="page"></span></p>
      </div>
      <select name="number_of_rows" id="number_of_rows">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <button class="button" id="btn_apply" onclick="apply_Number_of_Rows()">Apply</button>
    </div>

    <br>
    
    <script>
      //------------------------------------------------------------
      var current_page = 1;
      var records_per_page = 10;
      var l = document.getElementById("table_id").rows.length
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function apply_Number_of_Rows() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function prevPage() {
        if (current_page > 1) {
            current_page--;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function nextPage() {
        if (current_page < numPages()) {
            current_page++;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function changePage(page) {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var listing_table = document.getElementById("table_id");
        var page_span = document.getElementById("page");
       
        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        [...listing_table.getElementsByTagName('tr')].forEach((tr)=>{
            tr.style.display='none'; // reset all to not display
        });
        listing_table.rows[0].style.display = ""; // display the title row

        for (var i = (page-1) * records_per_page + 1; i < (page * records_per_page) + 1; i++) {
          if (listing_table.rows[i]) {
            listing_table.rows[i].style.display = ""
          } else {
            continue;
          }
        }
          
        page_span.innerHTML = page + "/" + numPages() + " (Total Number of Rows = " + (l-1) + ") | Number of Rows : ";
        
        if (page == 0 && numPages() == 0) {
          btn_prev.disabled = true;
          btn_next.disabled = true;
          return;
        }

        if (page == 1) {
          btn_prev.disabled = true;
        } else {
          btn_prev.disabled = false;
        }

        if (page == numPages()) {
          btn_next.disabled = true;
        } else {
          btn_next.disabled = false;
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function numPages() {
        return Math.ceil((l - 1) / records_per_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      window.onload = function() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      };
      //------------------------------------------------------------
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
</html>