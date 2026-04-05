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

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/User.php');

function make_a_user($result_row) {
    //takes sql query results
    $theUser = new User(
    @$result_row['id'],
    @$result_row['start_date'],
    @$result_row['first_name'],
    @$result_row['last_name'],
    @$result_row['email'],
    @$result_row['password'],
    @$result_row['role'],
    @$result_row['semester'],
    @$result_row['archived']
);
    return $theUser;
}

function get_user_full_name_from_id($id) {
    $con=connect();
    $query = 'SELECT first_name,last_name,id FROM dbusers WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $result_row['first_name'] . " " . $result_row['last_name'];
}

  function get_semesters_in_users() {
    $con=connect();
    $query = "SELECT DISTINCT semester FROM dbusers" .
        " ORDER BY semester asc";
    $result = mysqli_query($con,$query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    return $rows;
 }

 function add_user($user) {
    $con = connect();
    $query = "SELECT * FROM dbusers WHERE id = '" . $user->get_id() . "'";
    $result = mysqli_query($con, $query);
    // if (!$user instanceof user) {
    //     die("Error: add_user type mismatch");
    // }



    // If the result is empty, it means the user doesn't exist, so we can add the user
    if (mysqli_num_rows($result) == 0) {
        // Prepare the insert query
        $insert_query = 'INSERT INTO dbusers (id, start_date, first_name, last_name, email, password, role, semester) 
            VALUES ("' .
            $user->get_id() . '","' .
            $user->get_start_date() . '","' .
            $user->get_first_name() . '","' .
            $user->get_last_name() . '","' .
            $user->get_email() . '","' .
            $user->get_password() . '","' .
            $user->get_role() . '","' .
            $user->get_semester() . '");';

        // Check if the query is properly built
        if (empty($insert_query)) {
            die("Error: insert query is empty");
        }

        // Perform the insert
        if (mysqli_query($con, $insert_query)) {
            mysqli_close($con);
            return true;
        } else {
            die("Error: " . mysqli_error($con)); // Debugging MySQL error
        }
    }

    mysqli_close($con);
    return false;
}

/*
 * @return a User from dbUsers table matching a particular id.
 * if not in table, return false
 */

function retrieve_user($id) { // (username! not id)
    $con=connect();
    $query = "SELECT * FROM dbusers WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    // var_dump($result_row);
    $theUser = make_a_user($result_row);
//    mysqli_close($con);
    return $theUser;
}

function retrieve_all() {
    $con=connect();
    $query = "SELECT distinct * FROM dbusers";
    //$query = "SELECT distinct * FROM dbusers WHERE role = 'Student'"; // used if we only want to get students for volunteer activity creation

    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return $result;
}

/*
used to find users in dbusers based on input search parameters
all selected search parameters must be true
*/
function search_users($name, $id, $semester, $role, $archival_statuses) {
    $where = 'select * from dbusers where 1=1';
    if (!($name || $id || $semester || $role || $archival_statuses)) {
        return [];
    }
    else {
        //make sure statuses are valid (0 or 1)
        $statuses_to_search = array_intersect($archival_statuses, ['0', '1']);
        if (empty($statuses_to_search)) {
            $archival_statuses = ['0', '1']; //if no valid status provided, search all
        }

        $bindings = []; $types= "";
        if ($name) {
            if (strpos($name, ' ')) {
                $name = explode(' ', $name, 2);
                $first = $name[0];
                $last = $name[1];
                $where .= " and first_name like ? and last_name like ?";
                $bindings[] = "%$first%";
                $bindings[] = "%$last%";
                $types .= "ss";
            } else {
                $where .= " and (first_name like ? or last_name like ?)";
                $bindings[] = "%$name%";
                $bindings[] = "%$name%"; //need this twice
                $types .= "ss";
            }
        }
        if ($id) {
            $where .= " and id like ?";
            $bindings[] = "%$id%";
            $types .= "s";
        }
        if ($semester) {
            $where .= " and semester like ?";
            $bindings[] = "%$semester%";
            $types .= "s";
        }
        if ($role) {
            $where .= " and role=?";
            $bindings[] = $role;
            $types .= "s";
        }
        if ($archival_statuses) {
            $placeholders = implode(',', array_fill(0, count($archival_statuses), '?'));
            $where .= " and archived in ($placeholders)";
            foreach ($archival_statuses as $status) {
                $bindings[] = $status;
                $types .= "s";
            }
        }
        

        $where .= " order by archived, last_name, first_name";

        $connection = connect();
        $query = $connection->prepare($where);

        //you have to do this to get a proper array to pass for dynamic bindings
        if ($bindings) {
            $refs = [];
            foreach ($bindings as $key => $value) {
                $refs[$key] = &$bindings[$key]; //passing by reference required
                //[ 0 => thing0, 1 => thing1, etc...]
            }
            array_unshift($refs, $types); //prepend $types to $refs
            call_user_func_array([$query, 'bind_param'], $refs);
            //does $query->bind_param("ssi...", $first, $last, $id, ...all of the values...);
            //but dynamically with any number of bindings
        }

        $query->execute();
        $result = $query->get_result();
        if (!$result) {
            mysqli_close($connection);
            return [];
        }
        $raw = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $users = [];
        foreach ($raw as $row) {
            if ($row['id'] == 'vmsroot') {
                continue;
            }
            $users []= make_a_user($row);
        }
        mysqli_close($connection);
        return $users;
    }
}

function update_role($id, $role) {
    $con=connect();
    $sql = 'UPDATE dbusers SET role = ? WHERE id = ?';
    $query = $con->prepare($sql);
    $query->bind_param("ss", $role, $id);

    $query->execute();
    $result = $query->get_result();
    mysqli_close($con);

    return $result;
}

//set archival status to 1 (archived) or 0 (active)
function update_user_archival_status($id, $archived) {
    if (!valueConstrainedTo($archived, ['1', '0'])) {
        return;
    }
    $con=connect();
    $sql = 'UPDATE dbusers SET archived = ? WHERE id = ?';
    $query = $con->prepare($sql);
    $query->bind_param("ss", $archived, $id);
    $query->execute();
    $result = $query->get_result();
    mysqli_close($con);

    return $result;
}

//deletes the given user from the database entirely
function remove_person($id) {
    $con=connect();
    $sql = 'SELECT * FROM dbusers WHERE id = ?';
    //bind and get result
    $query = $con->prepare($sql);
    $query->bind_param("s", $id);
    $query->execute();
    $result = $query->get_result();

    //if this user doesn't exist
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }

    //the user does exist
    try {
        $sql = 'DELETE FROM dbusers WHERE id = ?';
        $query = $con->prepare($sql);
        $query->bind_param("s", $id);
        $query->execute();
        $result = $query->get_result();
    } catch (Exception $e) {
        mysqli_close($con);
        return false; //failure
    }
    //success
    mysqli_close($con);
    return true;
}

// updates the required fields of a person's account
function update_user_required($id, $first_name, $last_name, $email, $semester) {
    $sql = "update dbusers set 
        first_name=?, last_name=?, 
        email=?, semester=?    
        where id=?";

    $con = connect();

    try {
        $query = $con->prepare($sql);
        $query->bind_param("sssss", $first_name, $last_name, $email, $semester, $id);
        $query->execute();

        if ($query->affected_rows === 0) {
            throw new Exception('no updates were made');
        }
    } catch (Exception $e) {
        mysqli_close($con);
        return false; //failure
    }

    mysqli_close($con);
    return True;
}

//aggregate data for a user
function get_all_aggregated_poundsOfFood_for_volunteers() {
    $con=connect();
    $query = "SELECT volunteerID,first_name,last_name,SUM(hours) as totalHours,SUM(poundsOfFood) as totalPoundsRescued
                FROM dbvolunteeractivity LEFT JOIN dbusers on dbusers.id = dbvolunteeractivity.volunteerID
                    GROUP BY volunteerID, last_name 
                        ORDER BY last_name";

    $result = mysqli_query($con,$query);
    mysqli_close($con);

    if ($result == null || mysqli_num_rows($result) == 0) {
        return false;
    }
    return $result;
}

function archive_users_by_semester($semester, $archived = '1') {
    $con=connect();
    $sql = 'UPDATE dbusers SET archived = ? WHERE semester = ?';
    $query = $con->prepare($sql);
    $query->bind_param("ss", $archived, $semester);
    $query->execute();
    return $query->affected_rows;
}

function change_password($id, $newPass) {
    $con=connect();
    $sql = 'UPDATE dbusers SET password = ? WHERE id = ?';
    $query = $con->prepare($sql);
    $query->bind_param("ss", $newPass, $id);
    $query->execute();
    $result = $query->get_result();
    mysqli_close($con);
    return $result;
}