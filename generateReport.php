<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("America/New_York");

// Ensure admin authentication
if ($_SESSION['access_level'] < 2) {
    header('Location: login.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UMW Alleviating Food Waste Volunteer Tracking | Attendance Reports</title>
    <!--<script src="js/data-filters.js" defer></script>-->
    <link href="css/base.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
    <?php require_once('header.php'); ?>
</head>
<body>
    <?php require_once('database/dbVolunteerActivity.php');?>
    <?php require_once('database/dbUsers.php');?>
    <?php require_once('database/dbOrganizations.php');?>

    <h2 style="margin-top: 1rem;">Generate Reports</h2>

    <main style="width: 50%; margin: auto;">
        <p style="margin-top: 1rem;text-align:center;">
            Use this tool to generate reports on volunteer activity. Reports are available in Excel or CSV format.
            Currently, you can export the full data stored in the database.
        </p>
        <div class="main-content-box" style="margin-bottom: 0;">
            <!--<div class="text-center">
                <p style="font-size: 18px; color: #c2c2c2ff; margin-top: 0.5rem; margin-bottom: 0.5rem;">Fiscal Year: <?= $fiscalYearStart ?> - <?= $fiscalYearEnd ?></p>
            </div>-->

            <form method="POST" action="processReport.php">
                <!-- Event ID -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="exportType" style="font-weight: 600;">Select the table you want to export</label>
                    <select name="exportType" id="exportType">
                            <option value='logs'>Volunteer logs</option>
                            <option value='users'>Users</option>
                            <option value='organizations'>Organizations</option>
                            <!--<option value='organizations'>Organizations</option>-->
                    </select>
                </div>

                <!-- Content Select -->

                    <h4 style="margin-top: 1rem; margin-bottom: 0.5rem; font-weight: 600;">Field Selector</h4>
                    <p style="font-size: 16px; color: #c2c2c2ff; margin-top: 0.5rem; margin-bottom: 0.5rem;">If this field is selected, the report will include all entries, including those that are archived.</p>
                    <div id="field-picker">
                            <div class="checkbox-grouping">
                                <label class="checkbox-label">
                                    <input type="checkbox" value="archived" name="archived" id="archived"> Archived</label>
                        </div>
                    </div>
                </section>

                <!-- Format -->
                <div style="margin-bottom: 1.5rem; margin-top: 1.5rem;">
                    <label for="format" style="font-weight: 600;">File Format</label>
                    <select name="format" id="format">
                        <option value="excel">Excel (.xls)</option>
                        <option value="csv">CSV (.csv)</option>
                    </select>
                </div>

                <div style="text-align: center; margin-top: 2rem;">
                    <input type="hidden" value="<?php echo $_SESSION['_id']; ?>" name="admin" id="admin">
                    <input type="hidden" value="<?php echo date("d-M-Y H:i:s e") ?>" name="time" id="time">
                    <input type="submit" value="Generate Report" class="button generate-btn" style="width: 40%;">
                </div>
            </form>

        <!-- Return Button -->
        </div>
        <div style="text-align: center;">
            <a href="index.php" class="button" style="display: inline-block; text-decoration: none; width: 41%;">Return to Dashboard</a>
        </div>

    </main>

    <script>
        function toggleDateFields() {
            const eventID = document.getElementById("eventID").value;
            // const monthField = document.getElementById("monthField");
            // monthField.style.display = reportType === "annually" ? "none" : "block";
        }
        document.addEventListener("DOMContentLoaded", toggleDateFields);
    </script>
</body>
</html>

