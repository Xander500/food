<?php
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    include_once('domain/User.php');
    include_once('database/dbUsers.php');

    $person = Array();
    $person['id'] = 'vmsroot';
    $person['first_name'] = 'vmsroot';
    $person['last_name'] = '';
    $person['email'] = 'vmsroot';
    $person['password'] = password_hash('vmsroot', PASSWORD_BCRYPT);
    $person['role'] = 'Instructor';

    if (date("m") < 6) {
        $semester = "Spring " . date("Y");
    } else {
        $semester = "Fall " . date("Y");
    }
    $person['semester'] = $semester;
    $person['archived'] = '0';
    
    $PERSON = make_a_user($person);
    $result = add_user($PERSON);
    if ($result) {
        echo 'ROOT USER CREATION SUCCESS';
    } else {
        echo 'USER ALREADY EXISTS';
    }
?>