<?php
/**
 * Encapsulated version of a dbs entry.
 */
class VolunteerActivity {
    private $id;
    private $date;
    private $volunteerID;
    private $volunteerName;
    private $hours;
    private $poundsOfFood;
    private $organizationID;
    private $organizationName;
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
        
        //! once we have organizations we should add the ability to get organizationName
        //! once we have users we should add the ability to get studentName
        //for display purposes
        
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
    
    function getVolunteerName() {
        return $this->volunteerName;
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
    
    function getOrganizationName() {
        return $this->organizationName;
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