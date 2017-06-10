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
// DB::query("SELECT COUNT(*) FROM donor WHERE Id = '".$CampId."' GROUP BY ________");

?>
<html>
<head>
<title>&bull; Raktarpan &bull; Blood Donation Camp &bull; Live Statistics &bull;</title>
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
    <div id="content">
    <div id="total"> 
        <? echo "Total successful donations <b>". $success_donate ."</b>!<br>" ?>
        <small><? echo "&bull; <b>Male</b>: " . $males . " &bull; <b>Female</b>: " . $females . " &bull;"; ?></small>
    </div>
    <div id="stat-bldgrp">
    
    </div>
    <div id="stat-year"> 
    
    </div>
    <div id="stat-hall"> 
    
    </div>
    </div>
</body>
</html>
