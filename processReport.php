<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_cache_expire(30);
session_start();

if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 2) {
    header('Location: login.php');
    die();
}

require_once('database/dbusers.php');
require_once('database/dbVolunteerActivity.php');
require_once('database/dbOrganizations.php');

// Get user input
$exportType = $_POST['exportType'] ?? '';
$format = $_POST['format'] ?? 'csv';

///////////////////// USERS

if ($exportType == 'users') {

    // Fetch Data
    $reportData = get_all_aggregated_poundsOfFood_for_volunteers();

    $eventID = "volunteer data";
    $eventName = "test";

    if ($format === 'csv') {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=volunteer_report.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');

        //title row
        fputcsv($output, ["Volunteer Report"]);

        //column headers
        fputcsv($output, [
            "Username",
            "LastName",
            "FirstName",
            "TotalHoursVolunteered",
            "TotalPoundsOfFoodRescued",
        ]);

        //data rows
        while ($log = $reportData->fetch_assoc()) {
            fputcsv($output, [
                $log["volunteerID"],
                $log["last_name"],
                $log["first_name"],
                $log["totalHours"],
                $log["totalPoundsRescued"]
            ]);
        }

        fclose($output);
        exit();
    }

    // EXCEL EXPORT
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=attendance_report_{$eventID}_{$eventName}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<table border='1' style='border-collapse: collapse; font-family: Arial, sans-serif; text-align: center;'>";

    // Report Title
    //echo "<tr><th colspan='5' style='font-size: 18px; background-color: #004488; color: white; padding: 10px;'>User Report - " . $eventID . ": {$eventName}</th></tr>";
    echo "<tr><th colspan='5' style='font-size: 18px; background-color: #004488; color: white; padding: 10px;'>User Report" . ": </th></tr>";

    // Column Headers
    echo "<tr>
        <th style='background-color: #88CCEE; padding: 5px;'>Username</th>
        <th style='background-color: #AA4499; padding: 5px;'>Last Name</th>
        <th style='background-color: #DDCC77; padding: 5px;'>First Name</th>
        <th style='background-color: #155724; padding: 5px;'>Total Hours Volunteered</th>
        <th style='background-color: #86b7fe; padding: 5px;'>Total Pounds of Food Rescued</th>
      </tr>";

    // Data Rows
    while ($log = $reportData->fetch_assoc()) {
        echo "<tr>
            <td style='background-color: #EAEAEA; padding: 5px; text-align: center;'>{$log["volunteerID"]}</td>
            <td style='padding: 5px;'>{$log["last_name"]}</td>
            <td style='padding: 5px;'>{$log["first_name"]}</td>
            <td style='padding: 5px;'>{$log["totalHours"]}</td>
            <td style='padding: 5px;'>{$log["totalPoundsRescued"]}</td>
          </tr>";
    }

    echo "</table>";
    echo "</body></html>";
    exit();
} ///////////////////// LOGS

else if ($exportType == 'logs') {

    // Fetch Data
    $reportData = get_all_logs_sorted_by_date();

    $eventID = "volunteer data";
    $eventName = "test";

    if ($format === 'csv') {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=volunteer_report.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');

        //title row
        fputcsv($output, ["Volunteer Report"]);

        //column headers
        fputcsv($output, [
            "ID",
            "date",
            "volunteerID",
            "hoursVolunteered",
            "poundsOfFoodRescued",
            "organizationID",
            "location",
            "description",
        ]);

        //data rows
        while ($log = $reportData->fetch_assoc()) {
            fputcsv($output, [
                $log["id"],
                $log["date"],
                $log["volunteerID"],
                $log["hours"],
                $log["poundsOfFood"],
                $log["organizationID"],
                $log["location"],
                $log["description"]
            ]);
        }

        fclose($output);
        exit();
    }

    // EXCEL EXPORT
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=attendance_report_{$eventID}_{$eventName}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<table border='1' style='border-collapse: collapse; font-family: Arial, sans-serif; text-align: center;'>";

    //report title
    //echo "<tr><th colspan='5' style='font-size: 18px; background-color: #004488; color: white; padding: 10px;'>User Report - " . $eventID . ": {$eventName}</th></tr>";
    echo "<tr><th colspan='8' style='font-size: 18px; background-color: #004488; color: white; padding: 10px;'>Log Report" . ": </th></tr>";

    // Fetch the first row to get headers
    $firstRow = $reportData->fetch_assoc();
    array_splice($firstRow, -3); // removes the last 3 keys which are long and lat

    // Get keys from the first row
    $headers = array_keys($firstRow);

    // Output header row
    echo "<tr>";
    foreach ($headers as $header) {
        // Optional: make header more readable
        $prettyHeader = ucwords(str_replace(['_', 'ID'], [' ', ' ID'], $header));
        echo "<th style='background-color: #88CCEE; padding: 5px;'>{$prettyHeader}</th>";
    }
    echo "</tr>";

    // Output the first row
    echo "<tr>";
    foreach ($headers as $key) {
        $value = htmlspecialchars($firstRow[$key] ?? '');
        echo "<td style='padding: 5px; text-align: center;'>{$value}</td>";
    }
    echo "</tr>";

    // Output the remaining rows
    while ($row = $reportData->fetch_assoc()) {
        array_splice($row, -3); // removes the last 3 keys which are long and lat
        echo "<tr>";
        foreach ($headers as $key) {
            $value = htmlspecialchars($row[$key] ?? '');
            echo "<td style='padding: 5px; text-align: center;'>{$value}</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo "</body></html>";
    exit();
}

else if ($exportType == 'organizations') {

    // Fetch Data
    $reportData = fetch_organizations();
    $impactData = getImpactByOrg();

    $eventID = "volunteer data";
    $eventName = "test";

    if ($format === 'csv') {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=volunteer_report.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen('php://output', 'w');

        //title row
        fputcsv($output, ["Volunteer Report"]);

        //column headers
        fputcsv($output, [
            "ID",
            "date",
            "volunteerID",
            "hoursVolunteered",
            "poundsOfFoodRescued",
            "organizationID",
            "location",
            "description",
        ]);

        //data rows
        $i = 0;
        while ($log = $reportData->fetch_assoc()) {
            $logimpact = $impactData[$i];
            fputcsv($output, [
                $log["id"],
                $log["name"],
                $log["email"],
                $log["location"],
                $log["description"],
                $logimpact[1],
                $logimpact[2],
            ]);
            $i++;
        }

        fclose($output);
        exit();
    }

    // EXCEL EXPORT
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=attendance_report_{$eventID}_{$eventName}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<table border='1' style='border-collapse: collapse; font-family: Arial, sans-serif; text-align: center;'>";

    //report title
    //echo "<tr><th colspan='5' style='font-size: 18px; background-color: #004488; color: white; padding: 10px;'>User Report - " . $eventID . ": {$eventName}</th></tr>";
    echo "<tr><th colspan='8' style='font-size: 18px; background-color: #004488; color: white; padding: 10px;'>Log Report" . ": </th></tr>";

    // Fetch the first row to get headers
    $firstRow = $reportData->fetch_assoc();
    $firstRowImpact = $impactData[0];

    // Get keys from the first row
    $headers = array_keys($firstRow);

    // Output header row
    echo "<tr>";
    foreach ($headers as $header) {
        // Optional: make header more readable
        $prettyHeader = ucwords(str_replace(['_', 'ID'], [' ', ' ID'], $header));
        echo "<th style='background-color: #88CCEE; padding: 5px;'>{$prettyHeader}</th>";
    }
        echo "<th style='background-color: #88CCEE; padding: 5px;'>Pounds Rescued</th>";
        echo "<th style='background-color: #88CCEE; padding: 5px;'>Total Food Rescued</th>";
    echo "</tr>";

    // Output the first row
    echo "<tr>";
    foreach ($headers as $key) {
        $value = htmlspecialchars($firstRow[$key] ?? '');
        echo "<td style='padding: 5px; text-align: center;'>{$value}</td>";
    }
        echo "<td style='padding: 5px; text-align: center;'>{$firstRowImpact[1]}</td>";
        echo "<td style='padding: 5px; text-align: center;'>{$firstRowImpact[2]}</td>";
    echo "</tr>";

    // Output the remaining rows
    $i = 1;
    while ($row = $reportData->fetch_assoc()) {
        $nextRow = $row; 
        $nextRowImpact = $impactData[$i];
        echo "<tr>";
        foreach ($headers as $key) {
            $value = htmlspecialchars($nextRow[$key] ?? '');
            echo "<td style='padding: 5px; text-align: center;'>{$value}</td>";
        }
            // rounds
            echo "<td style='padding: 5px; text-align: center;'>" . number_format($nextRowImpact[1] ?? 0, 2) . "</td>";
            echo "<td style='padding: 5px; text-align: center;'>" . number_format($nextRowImpact[2] ?? 0, 2) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "</body></html>";
    exit();
}
?>
