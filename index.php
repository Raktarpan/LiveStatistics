<?php

$CampDir = ".";

require_once($CampDir . '/config.php');
require_once($CampDir . '/settings.php');

// Gather Camp Details
DB::query("SELECT * FROM camp WHERE Id = %i", $CampId);

// Gather Donation Details

// 1. Count
$success_donate = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = %i AND Fit = '1'", $CampId);
$failure_donate = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = %i AND Fit = '0'", $CampId);
$total_donate = $success_donate + $failure_donate;

// 2. Blood Group Distribution
$bldgrp_results = DB::query("SELECT BloodGroup, COUNT(*) as Count FROM donor WHERE CampId = %i AND Fit = '1' GROUP BY BloodGroup ORDER BY Count DESC", $CampId);
$BldGrpData = "";
foreach ($bldgrp_results as $row) {
    $BldGrpData .= "['". $row['BloodGroup'] ."', ". $row['Count'] ."], ";
}

// 3. Year-Wise Distribution
// $year_results = DB::query("SELECT COUNT(*) FROM donor WHERE Id = %i GROUP BY _________", $CampId);

// 4. Gender Distribution
$males = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = %i AND Fit = '1' AND Gender = 'M'", $CampId);
$females = DB::queryFirstField("SELECT COUNT(*) FROM donor WHERE CampId = %i AND Fit = '1' AND Gender = 'F'", $CampId);

// 5. Hall Distribution
$hall_results = DB::query("SELECT Hostel, COUNT(*) as Count FROM donor WHERE CampId = %i AND Fit = '1' GROUP BY Hostel ORDER BY Count DESC LIMIT 5", $CampId);
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
<link href="<? echo $CampDir; ?>/css/live.css" rel='stylesheet' type='text/css' />
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
          slices: {  5: {offset: 0.2},
                    6: {offset: 0.3},
                    7: {offset: 0.4},
                    8: {offset: 0.5},
                    },
        };

        var chart = new google.visualization.PieChart(document.getElementById('stat-bldgrp'));
        chart.draw(data, options);
      }
    </script>
     <!-- meta http-equiv="refresh" content="30" /-->
</head>
<body>
    <header>
    <center><img src="<? echo $CampDir; ?>/images/rakt.png" class="header_image"></center>
    </header>

    <div id="content">

      <table>
        <tr>
          <td><img src="<? echo $CampDir; ?>/images/man.png"><span id="stat-gender"> <? echo $males;?></span></td>
          <td width="40%"><center><h2>Complete</h2></center>
              <div id="total"> 
                <? echo $success_donate; ?>
              </div><br>
              <center style="font-size: 24px">
                <b><big>out of <? echo $total_donate; ?></big></b>
              </center></td>
          <td><img src="<? echo $CampDir; ?>/images/woman.png"><span id="stat-gender"> <? echo $females;?></span></td>
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
