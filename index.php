<?php

require_once('config.php');
$CampId = "1";

// Gather Camp Details
DB::query("SELECT * FROM camp WHERE Id = '".$CampId."'");

// Gather Donation Details

// 1. Count
$success_donate = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = '".$CampId."' AND Fit = '1'");
$failure_donate = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = '".$CampId."' AND Fit = '0'");

// 2. Blood Group Distribution
$bldgrp_results = DB::query("SELECT BloodGroup, COUNT(*) as Count FROM donor WHERE CampId = '".$CampId."' GROUP BY BloodGroup ORDER BY Count DESC");
$BldGrpData = "";
foreach ($bldgrp_results as $row) {
    $BldGrpData .= "['". $row['BloodGroup'] ."', ". $row['Count'] ."], ";
}

// 3. Year-Wise Distribution
// $year_results = DB::query("SELECT COUNT(*) FROM donor WHERE Id = '".$CampId."' GROUP BY _________");

// 4. Gender Distribution
$males = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = '".$CampId."' AND Fit = '1' AND Gender = 'M'");
$females = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = '".$CampId."' AND Fit = '1' AND Gender = 'F'");

// 5. Hall Distribution
$hall_results = DB::query("SELECT Hostel, COUNT(*) as Count FROM donor WHERE CampId = '".$CampId."' GROUP BY Hostel ORDER BY Count DESC LIMIT 3");
$HallTable = "<table class='table_lines'>\n<tr>\n<th>Rank</th><th>Hostel</th><th>Donations</th>\n</tr>\n";
$Rank = 0;
foreach ($hall_results as $row) {
    $Rank++;
    $HallTable .= "<tr>\n";
    $HallTable .= "<td>". $Rank."</td>";
    $HallTable .= "<td>". $row['Hostel']."</td>";
    $HallTable .= "<td>". $row['Count']."</td>";
    $HallTable .= "\n</tr>\n";
}
$HallTable .= "</table>";

?>
<html>
<head>
<title>&bull; Raktarpan &bull; Blood Donation Camp &bull; Live Statistics &bull;</title>
<link href="live.css" rel='stylesheet' type='text/css' />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Bloodgroup', 'Number of Students'],
          <?php echo $BldGrpData; ?>
        ]);

        var options = {
          title: "Blood Group Distribution",
          pieHole: 0.4,
          pieSliceTextStyle: {
            color: 'white',
          },
          is3D: true,
          pieSliceText: 'label',
          pieStartAngle: 50,
          slices: {  5: {offset: 0.1},
                    6: {offset: 0.2},
                    7: {offset: 0.2},
                    8: {offset: 0.2},
                    },
        };

        var chart = new google.visualization.PieChart(document.getElementById('stat-bldgrp'));
        chart.draw(data, options);
      }
    </script>
</head>
<body>
    <header>
      <center><img src="../rakt.png" class="header_image"></center>
    </header>

    <div id="content">

      <table>
        <tr>
          <td><img src="man.png"><span id="stat-gender"> <? echo $males;?></span></td>
          <td width="40%"><center><!-- h2>Successful Donations</center></h2 -->
              <div id="total"> 
                <? echo $success_donate; ?>
              </div><br>
              <center style="font-size: 24px">
                <!-- Total: --><b><? echo $success_donate+$failure_donate; ?></b>
              </center></td>
          <td><img src="woman.png"><span id="stat-gender"> <? echo $females;?></span></td>
      </tr>
    </table>

      <!-- <div id="stat-gender">
          <table>
            <tr>
              <td><img src="man.png">: <? echo $males;?></td>
              <td><img src="woman.png">: <? echo $females;?></td>
            </tr>
          </table>
          <small><? //echo "&bull; <b>Male</b>: " . $males . "<br> &bull; <b>Female</b>: " . $females; ?></small>
        </div> -->

      <div id="stats">

        <div id="stat-bldgrp" class="stats"></div>

        <!-- <div id="stat-year" class="stats"></div> -->

        <div id="stat-hall" class="stats">
            <? echo $HallTable; ?>
        </div>

      </div>
    </div>
</body>
</html>
