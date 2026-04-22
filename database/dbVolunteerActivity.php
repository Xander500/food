<?php
/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook,
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan,
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker.
 * This program is part of RMH Homebase, which is free software.  It comes with
 * absolutely no warranty. You can redistribute and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 *
 */

/**
 * @version March 1, 2012
 * @author Oliver Radwan and Allen Tucker
 */

/*
 * Created for Gwyneth's Gift in 2022 using original Homebase code as a guide
 */

/*
 * Created for Dr. Majid's Volunteer Impact Tracking System in 2026 using the Whiskey Valor code as a guide.
 * Removed all functions not related to our program, and added functions to support our program's features.
 */


include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/VolunteerActivity.php');
//Added to send emails to users when they are removed or signed up to an event.
include_once(dirname(__FILE__).'/../email.php');


function get_volunteerID_from_logID($id) {
    $con=connect();
    $sql = "SELECT volunteerID FROM dbvolunteeractivity WHERE id = ?";
    $query = $con->prepare($sql);
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    mysqli_close($con);

    if (mysqli_num_rows($result) !== 1) {
        return false;
    }

    $result_row = mysqli_fetch_assoc($result);
    return $result_row['volunteerID'];
}

function make_a_volunteer_activity($result_row) {
	/*
	 ($en, $v, $sd, $description, $ev))
	 */

    $theLog = new VolunteerActivity(
                    $result_row['id'],
                    $result_row['date'],
                    $result_row['volunteerID'],
                    $result_row['hours'],
                    $result_row['poundsOfFood'],
                    $result_row['organizationID'],
                    $result_row['location'],
                    $result_row['description'],
                    $result_row['archived']
                );
    return $theLog;
}

 function get_num_logs() {
    $con=connect();
    $query = "SELECT count(*) as num FROM dbvolunteeractivity";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_assoc($result);
    return $row['num'];
 }

 function get_num_logs_with_filters($filters_input, $want_archived = false) {
    $con=connect();
    $select_statement = "SELECT count(*) as num FROM dbvolunteeractivity AS va" .
            " JOIN dbusers AS u ON u.id = va.volunteerID" .
            " JOIN dborganizations AS o ON o.id = va.organizationID";
    $results = internal_apply_filters_to_select($con, $select_statement, '', $filters_input, $want_archived);
    $row = mysqli_fetch_assoc($results);
    return $row['num'];
 }

  function get_students_in_logs() {
    $con=connect();
    $query = "SELECT DISTINCT u.id, u.first_name, u.last_name FROM dbvolunteeractivity AS va JOIN dbusers AS u ON u.id = va.volunteerID" .
        " ORDER BY last_name asc, first_name asc, id asc";
    $result = mysqli_query($con,$query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    return $rows;
 }

   function get_organizations_in_logs() {
    $con=connect();
    $query = "SELECT DISTINCT o.id, o.name FROM dbvolunteeractivity AS va JOIN dborganizations AS o ON o.id = va.organizationID" .
        " ORDER BY name asc, id asc";
    $result = mysqli_query($con,$query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    return $rows;
 }

 function get_all_volunteer_activities_sorted_by_date() {
    $con=connect();
    $query = "SELECT va.id, va.date, va.volunteerID, va.hours, va.poundsOfFood," .
            " va.organizationID, va.location, va.description," .
            " u.first_name, u.last_name, o.name AS organization_name" .
            " FROM dbvolunteeractivity AS va" .
            " JOIN dbusers AS u ON u.id = va.volunteerID" .
            " JOIN dborganizations AS o ON o.id = va.organizationID" .
            " ORDER BY date desc, volunteerID asc, organizationID asc, id asc";
    $result = mysqli_query($con,$query);
    $theLogs = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theLog = make_a_volunteer_activity($result_row);
        $theLogs[] = $theLog;
    }
    mysqli_close($con);
    return $theLogs;
 }

 function extract_permitted_filters_on_logs($filters) {
        $valid_parameters = ['students', 'organizations', 'semesters', 'startdate', 'enddate',
            'maxhours', 'minhours','maxfood','minfood'];
        $accepted_filters = [];
        foreach ($valid_parameters as $p){
            if (isset($filters[$p]) && $filters[$p] !== '') {
                $accepted_filters[$p] = $filters[$p];
            }
        }
        return $accepted_filters;
 }

function get_all_volunteer_activities_custom_sort_pagination_with_filters($sortby_input, $order_input, $per_page, $offset, $filters_input, $want_archived = false) {
    $con=connect();
    //check valid options for sort
    $sortby = 'date'; $order = 'desc';
    if (in_array($sortby_input, ['last_name', 'date', 'organization_name', 'hours',
        'location', 'poundsOfFood', 'description'])){
        $sortby = $sortby_input;
    }
    if (in_array($order_input, ['asc', 'desc'])){
        $order = $order_input;
    }
    $per_page = (int) $per_page; $offset = (int) $offset; //sql injection

    //base sql query to get info from dbVA, dbusers, dbOrgs
    $select_statement = "SELECT va.id, va.date, va.volunteerID, va.hours, va.poundsOfFood," .
            " va.organizationID, va.location, va.description, va.archived," .
            " u.first_name, u.last_name, o.name, u.semester AS organization_name" .
            " FROM dbvolunteeractivity AS va" .
            " JOIN dbusers AS u ON u.id = va.volunteerID" .
            " JOIN dborganizations AS o ON o.id = va.organizationID" .
            " WHERE va.id=va.id";
    $order_statement = " ORDER BY $sortby $order, volunteerID asc, organizationID asc, id asc" .
            " LIMIT $per_page OFFSET $offset";
    $result = internal_apply_filters_to_select($con, $select_statement, $order_statement, $filters_input, $want_archived);
    $theLogs = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theLog = make_a_volunteer_activity($result_row);
        $theLogs[] = $theLog;
    }
    mysqli_close($con);
    return $theLogs;
  }

function internal_apply_filters_to_select($con, $select_statement, $order_statement, $filters_input, $want_archived) {
    $sql = $select_statement;
    //check for filter options
    $filters = extract_permitted_filters_on_logs($filters_input);

    //get values for filters
    //COALESCE means that if ? is null, it will use the other option
    //if nothing has been set for a particular filter, it will equal itself (va.hours=va.hours) and therefore be irrelevant
    $minhours = $filters['minhours'] ?? null;
    $maxhours = $filters['maxhours'] ?? null;
    $minfood = $filters['minfood'] ?? null;
    $maxfood = $filters['maxfood'] ?? null;
    $startdate = $filters['startdate'] ?? null;
    $enddate = $filters['enddate'] ?? null;
    $student = $filters['students'] ?? null;
    $organization  = $filters['organizations'] ?? null;
    $semester  = $filters['semesters'] ?? null;
    $archive = $want_archived ? 1 : 0;

    $sql .= " AND va.hours >= COALESCE(?, va.hours)" .
        " AND va.hours <= COALESCE(?, va.hours)" .
        " AND va.poundsOfFood >= COALESCE(?, va.poundsOfFood)" .
        " AND va.poundsOfFood <= COALESCE(?, va.poundsOfFood)" .
        " AND va.date >= COALESCE(?, va.date)" .
        " AND va.date <= COALESCE(?, va.date)" .
        " AND va.volunteerID = COALESCE(?, va.volunteerID)" .
        " AND va.organizationID = COALESCE(?, va.organizationID)" .
        " AND u.semester = COALESCE(?, u.semester)" .
        " AND va.archived = COALESCE(?, va.archived)";


    //sort and pagination section of sql
    $sql .= $order_statement;

    $query = $con->prepare($sql);
    $query->bind_param('ddddssssss',
        $minhours, $maxhours, $minfood, $maxfood, $startdate, $enddate,
        $student, $organization, $semester, $archive);
    $query->execute();
    return $query->get_result();
 }

 //get logs (no filters applied) for particular page and sort
  function get_all_volunteer_activities_custom_sort_pagination($sortby_input, $order_input, $per_page, $offset) {
    $con=connect();
    //check valid options
    $sortby = 'date'; $order = 'desc';
    if (in_array($sortby_input, ['last_name', 'date', 'organization_name', 'hours',
        'location', 'poundsOfFood', 'description'])){
        $sortby = $sortby_input;
    }

    if (in_array($order_input, ['asc', 'desc'])){
        $order = $order_input;
    }

    $query = $con->prepare("SELECT va.id, va.date, va.volunteerID, va.hours, va.poundsOfFood," .
            " va.organizationID, va.location, va.description, va.archived," .
            " u.first_name, u.last_name, o.name AS organization_name" .
            " FROM dbvolunteeractivity AS va" .
            " JOIN dbusers AS u ON u.id = va.volunteerID" .
            " JOIN dborganizations AS o ON o.id = va.organizationID" .
            " ORDER BY $sortby $order, volunteerID asc, organizationID asc, id asc" .
            " LIMIT ? OFFSET ?");
    $query->bind_param('ii', $per_page, $offset);
    $query->execute();
    $result = $query->get_result();
    $theLogs = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theLog = make_a_volunteer_activity($result_row);
        $theLogs[] = $theLog;
    }
    mysqli_close($con);
    return $theLogs;
 }

function get_all_logs_sorted_by_date($archived = '0') {
    $con=connect();
    $sql = 'SELECT * FROM dbvolunteeractivity WHERE (dbvolunteeractivity.archived = 0 OR ? = 1) ORDER BY date ASC';
    $query = $con->prepare($sql);
    $query->bind_param("s", $archived);
    $query->execute();
    $result = $query->get_result();

    if ($result == null || mysqli_num_rows($result) == 0) {
    mysqli_close($con);
        return false;
    }
    mysqli_close($con);
    return $result;
}

function fetch_volunteer_activity_by_id($id) {
    $connection = connect();
    $id = mysqli_real_escape_string($connection, $id);
    $query = $connection->prepare("select * from dbvolunteeractivity where id = ?");
    $query->bind_param("i", $id); //protect from sql injection
    $query->execute();

    $result = $query->get_result();
    $log = mysqli_fetch_assoc($result);
    if ($log) {
        require_once('include/output.php');
        $log = hsc($log);
        mysqli_close($connection);
        return $log;
    }
    mysqli_close($connection);
    return null;
}

//used instead of create_event
function create_activitylog($log, $volunteer = true) {
    $connection = connect();

    //safer checks
    $date           = $log["date"] ?? null;

    if ($volunteer)
        $volunteerID = $_SESSION["_id"] ?? null; // checks session, will fail if we remove the login session adding the username to _id
    else
        $volunteerID = $log["volunteerID"] ?? null; // log needs to have volunteerID added into log being passed to this function

    $hours          = $log["hours"] ?? null;
    $organizationID = $log["organizationID"] ?? null;
    $location       = $log["location"] ?? null;

    $latitude       = $log["latitude"] ?? null;
    $longitude      = $log["longitude"] ?? null;

    //optional
    $description    = $log["description"] ?? null;
    $poundsOfFood   = $log["poundsOfFood"] ?? null;

    //basic check of values
    if ($date === null || $hours === null || $location === null) {
        return false;
    }

    //check actually a user and if organization was selected
    if ($volunteerID === null || $organizationID === null) {
        return false;
    }

    //make injection safe with bindings
    $sql = "
        INSERT INTO dbvolunteeractivity 
            (date, volunteerID, hours, poundsOfFood, organizationID, location, description, latitude, longitude)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $query = $connection->prepare($sql);
    $query->bind_param("ssddissdd", 
            $date,
            $volunteerID,
            $hours,
            $poundsOfFood,
            $organizationID,
            $location,
            $description,
            $latitude,
            $longitude
        );
    $result = $query->execute();

    if ($result && $query->affected_rows > 0) {
        mysqli_close($connection);
        return true;
    }

    mysqli_close($connection);
    return false;
}


function update_volunteerLog($id, $logDetails) {
    $connection = connect();
    $volunteerID = $logDetails["volunteerID"];
    $organizationID = $logDetails["organizationID"];
    $hours = $logDetails["hours"];
    $poundsOfFood = $logDetails["poundsOfFood"];
    $date = $logDetails["date"];
    $location = $logDetails["location"];
    $description = $logDetails["description"];
    $archived = $logDetails["archived"];
    $latitude = $logDetails["latitude"];
    $longitude = $logDetails["longitude"];

    $query = "
        UPDATE dbvolunteeractivity
        SET volunteerID=?, organizationID=?, hours=?, poundsOfFood=?, date=?, location=?, description=?, archived=?
        WHERE id=?
    ";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssddsssss", $volunteerID, $organizationID, $hours, $poundsOfFood, $date, $location, $description, $archived, $id);

    if ($stmt->execute()) {
        mysqli_commit($connection);
        mysqli_close($connection);
        return true;
    }

    mysqli_close($connection);
    return false;
}

function delete_log($id) {
    $con=connect();
    $sql = 'DELETE FROM dbvolunteeractivity WHERE id = ?';
    $query = $con->prepare($sql);
    $query->bind_param("s", $id);

    if (!$query->execute()) {
        mysqli_close($con);
        return false;
    }

    $affected = $query->affected_rows;
    mysqli_close($con);
    return $affected > 0;
}

function get_impact_summary_by_volunteer($id) {
    $con = connect();
    $sql = 'SELECT SUM(hours) AS total_hours, SUM(poundsOfFood) AS total_pounds, COUNT(*) AS total_logs FROM dbvolunteeractivity WHERE volunteerID = ?';
    $query = $con->prepare($sql);
    $query->bind_param("s", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    mysqli_close($con);
    return $row;
}

function get_impact_summary_by_organization($id) {
    $con = connect();
    $sql = "SELECT dborganizations.name AS organization_name, SUM(hours) AS hours,
        SUM(poundsOfFood) AS pounds
        FROM dbvolunteeractivity
        JOIN dborganizations ON organizationID = dborganizations.id
        WHERE volunteerID = ?
        GROUP BY organizationID
        ORDER BY hours DESC";
    $query = $con->prepare($sql);
    $query->bind_param("s", $id);
    $query->execute();
    $result = $query->get_result();

    $rows = $result->fetch_all(MYSQLI_ASSOC);
    mysqli_close($con);
    return $rows;
}

function getTotalHours($sem = "All") {
    $con = connect();
    $query = "SELECT sum(hours) as h FROM dbvolunteeractivity";
    if ($sem != "All") {
        if (str_contains($sem, "Spring")) {
            $query .= " WHERE month(date) < 6 and year(date) = " . substr($sem, -4);
        } else {
            $query .= " WHERE month(date) >= 7 and year(date) = " . substr($sem, -4);
        }
    }
    $result = mysqli_query($con, $query);
    $result = mysqli_fetch_assoc($result);
    return $result['h'];
}

function getTotalPounds($sem = "All") {
    $con = connect();
    $query = "SELECT sum(poundsOfFood) as lb FROM dbvolunteeractivity";
    if ($sem != "All") {
        if (str_contains($sem, "Spring")) {
            $query .= " WHERE month(date) < 6 and year(date) = " . substr($sem, -4);
        } else {
            $query .= " WHERE month(date) >= 7 and year(date) = " . substr($sem, -4);
        }
    }
    $result = mysqli_query($con, $query);
    $result = mysqli_fetch_assoc($result);
    return $result['lb'];
}

function getImpactByStudent() {
    $con = connect();
    $query = "SELECT first_name, last_name, sum(hours), sum(poundsOfFood), count(*) FROM dbvolunteeractivity JOIN dbusers on volunteerID=dbusers.id GROUP BY volunteerID;";
    $result = mysqli_query($con, $query);
    $result = mysqli_fetch_all($result);
    return $result;
}

function getImpactByOrg() {
    $con = connect();
    $query = "SELECT dborganizations.name, sum(hours), sum(poundsOfFood), count(*) FROM dbvolunteeractivity JOIN dborganizations on organizationID=dborganizations.id GROUP BY organizationID;";
    $result = mysqli_query($con, $query);
    $result = mysqli_fetch_all($result);
    return $result;
}
function get_all_activity_locations_for_map($sem = "All") {
    $con = connect();
    $query = "SELECT id, location, latitude, longitude
              FROM dbvolunteeractivity
              WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
    if ($sem != "All") {
        if (str_contains($sem, "Spring")) {
            $query .= " AND month(date) < 6 and year(date) = " . substr($sem, -4);
        } else {
            $query .= " AND month(date) >= 7 and year(date) = " . substr($sem, -4);
        }
    }
    $result = mysqli_query($con, $query);

    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_close($con);
    return $rows;
}

function archive_logs_by_semester($semester, $archived = '1') {
    $con=connect();
    $sql = 'UPDATE dbvolunteeractivity as va ' .
        'JOIN dbusers as u ' .
        'ON u.id = va.volunteerID ' .
        'SET va.archived = ? ' .
        'WHERE semester = ?';
    $query = $con->prepare($sql);
    $query->bind_param("ss", $archived, $semester);
    $query->execute();
    return $query->affected_rows;
}

function find_logs_by_semester($semester) {
    $con=connect();
    $sql = 'SELECT va.id, va.date, va.volunteerID, va.hours, va.poundsOfFood, ' .
        'va.organizationID, va.location, va.description, va.archived ' .
        'FROM dbvolunteeractivity va ' .
        'JOIN dbusers AS u ' .
        'ON u.id = va.volunteerID ' .
        'WHERE semester = ?';
        
    $query = $con->prepare($sql);
    $query->bind_param("s", $semester);
    $query->execute();
    $result = $query->get_result();


    $theLogs = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theLog = make_a_volunteer_activity($result_row);
        $theLogs[] = $theLog;
    }
    mysqli_close($con);
    return $theLogs;
}

function get_monthly_hours($sem = "All") {
    $con = connect();
    $query = "SELECT month(date) as m, sum(hours) as v FROM dbvolunteeractivity WHERE ";
    if ($sem != "All") {
        $query .= "year(date) = " . substr($sem, -4) . " GROUP BY month(date)";
    } else {
        $query .= "year(date) = " . date("Y") . " GROUP BY month(date)";
    }
    $result = mysqli_query($con, $query);

    $months = array();
    while ($month = mysqli_fetch_assoc($result)) {
        $months[] = $month;
    }

    mysqli_close($con);
    return $months;
}

function get_monthly_pounds($sem = "All") {
    $con = connect();
    $query = "SELECT month(date) as m, sum(poundsOfFood) as v FROM dbvolunteeractivity WHERE ";
    if ($sem != "All") {
        $query .= "year(date) = " . substr($sem, -4) . " GROUP BY month(date)";
    } else {
        $query .= "year(date) = " . date("Y") . " GROUP BY month(date)";
    }
    $result = mysqli_query($con, $query);

    $months = array();
    while ($month = mysqli_fetch_assoc($result)) {
        $months[] = $month;
    }

    mysqli_close($con);
    return $months;
}

function get_years_logs() {
    $con = connect();
    $query = "SELECT DISTINCT year(date) as year FROM dbvolunteeractivity";
    $result = mysqli_query($con, $query);
    $result = mysqli_fetch_all($result);

    mysqli_close($con);
    return $result;
}