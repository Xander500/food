<?php
session_start();

if ($_SESSION['access_level'] < 2) {
    header('Location: index.php');
    exit;
}

require_once('database/dbOrganizations.php');
require_once('include/output.php');

$deletedCount = 0;
$failedCount = 0;

if (isset($_POST['bulk_delete']) && isset($_POST['selected_orgs'])) {
    $selected = $_POST['selected_orgs'];

    if (is_array($selected)) {
        foreach ($selected as $id) {
            try {
                if (delete_organization($id)) {
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            } catch (mysqli_sql_exception $e) {
                $failedCount++;
            }
        }
    }
}

header('Location: organizationManagement.php?deleted=' . $deletedCount . '&failed=' . $failedCount);
exit;
?>