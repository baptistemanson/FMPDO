<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */
class FmpdoRecord
{
    var $_recordid;
    var $_fields = array();
    var $_relatedSets = array();

    function FmpdoRecord($pdo_row= array())
    {

        if(!empty($pdo_row) and !isset($pdo_row['id'])){
            return new FmpdoError("id column is required for FmpdoRecord Object");
        }
        if(!empty($pdo_row)){
            $this->_recordid = $pdo_row['id'];
            self::setFieldsFromPDOrow($pdo_row);
        }else{
            $this->_recordid = FALSE;
        }
    // a recordid of false indicates a new record that must be inserted upon commit
    // a non false ID indicates an existing record that must be updated upon commit

    }

    function setFieldsFromPDOrow($pdo_row)
    {
        if(isset($pdo_row) and is_array($pdo_row))
        {

            foreach($pdo_row as $k => $v){
             $this->_fields[$k] = Array($v);
            }
        }
    }

    function getField($field, $repetition= 0)
    {
        if(isset($this->_fields[$field][$repetition])){
            return $this->_fields[$field][$repetition];
        }else{
            return FALSE;
        }
    }


    function getRecordId()
    {
        if(isset($this->_recordid)){
            return $this->_recordid;
        }else{
            return FALSE;
        }
    }
}