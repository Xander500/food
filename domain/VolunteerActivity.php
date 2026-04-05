<?php
/**
 * Encapsulated version of a dbs entry.
 */
class VolunteerActivity {
    private $id;
    private $date;
    private $volunteerID;
    private $hours;
    private $poundsOfFood;
    private $organizationID;
    private $location;
    private $description;
    private $archived;

    function __construct($id, $date, $volunteerID, $hours, $poundsOfFood, $organizationID, $location, $description, $archived='0') {
        $this->id = $id;
        $this->date = $date;
        $this->volunteerID = $volunteerID;
        $this->hours = $hours;
        $this->poundsOfFood = $poundsOfFood;
        $this->organizationID = $organizationID;
        $this->location = $location;
        $this->description = $description;
        $this->archived = $archived; //default not archived
    }

    function getID() {
        return $this->id;
    }

    function getDate() {
        return $this->date;
    }

    function getVolunteerID() {
        return $this->volunteerID;
    }


    function getHours() {
        return $this->hours;
    }
    
    function getPoundsOfFood() {
        return $this->poundsOfFood;
    }

    function getOrganizationID() {
        return $this->organizationID;
    }
    
    function getLocation() {
        return $this->location;
    }
    
    function getDescription() {
        return $this->description;
    }

    function is_archived() {
        return $this->archived;
    }

}