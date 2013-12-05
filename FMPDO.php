<?php

/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */


/**
 * Bring in all child and related classes
 */
require_once(__DIR__ . '/FMPDO/dbclass.php');
require_once(__DIR__ . '/FMPDO/Record.php');
require_once(__DIR__ . '/FMPDO/Result.php');
require_once(__DIR__ . '/FMPDO/Edit.php');
require_once(__DIR__ . '/FMPDO/Error.php');
require_once(__DIR__ . '/FMPDO/Find.php');


/**
 * Base FMPDO Class
 *
 * @package FMPDO
 * handles db connection and spawning of command objects
 */
class FMPDO {

    var $fmpdo;
    var $findCmd;
    var $result = false;
    var $error = '';
    var $locale = 'en';  //TODO move config setting out to their own file

    /**
     * FMPDO Constructor
     * @param array $sql_config  an array of settings for the db connection
     *
     */
    function __construct($sql_config = array()) {

        // build the db connection from the included db class file

        if (!empty($sql_config)) {
            try {
                $this->db = new dbclass($sql_config);
            } catch (Exception $e) {
                $this->$error = 'Could not connect with database!' . $e;
            }

            $this->sql_config = $sql_config;
        } else {
            $this->error = 'Class instantiated without sql config array';
        }
    }


    function getAPIVersion(){

        return "0.0.0";
    }


    /**
     * Test for whether or not a variable is an FMPDO_Error object.
     *
     * @param mixed $variable
     * @return boolean.
     * @static
     *
     */
    function isError($variable)
    {
        return is_a($variable, 'FMPDO_Error');
    }

    /**
     * Returns the current value of $property
     *
     * @param string name of the property
     * @return boolean.
     *
     */
    function getProperty($property) {
        return isset($this->$property) ? $this->$property : null;
    }


    /**
     * Fetches a record from the database by its id column
     *
     * @param $table the name of the sql table
     * @param $id  the value of the id/primary key
     * @return FMPDO_Error|FMPDO_Record
     */
    public function getRecordByID($table, $id) {
        $query = $this->db->prepare("SELECT *  FROM " . $table . " WHERE id="."'$id' " ."LIMIT 1" );
        try {
            if (!$query) {
                return new FMPDO_Error($this->db->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new FMPDO_Error($e);
        }
        $rows=$query->fetchAll();
        return new FMPDO_Record($rows[0]);
    }


    /**
     * Instantiates a new FMPDO_Find object
     *
     * @param $table the sql table that the query will be performed on
     * @return FMPDO_Command_Find
     */
    function newFindCommand($table) {
        $findCommand = new FMPDO_Command_Find($this->sql_config, $table);
        return $findCommand;
    }


    /**
     * Instantiates a new FMPDO_Edit object
     *
     * @param $table  the sql table that the edit will be performed in
     * @param $id  // the primary key of the record that will be edited
     * @return FMPDO_Command_Edit
     */
    function newEditCommand($table, $id) {
        $editCmd = new FMPDO_Command_Edit($table, $id, $this->sql_config);
        return $editCmd;
    }

}