<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Person
 *
 * @author User
 */
class Person extends Element
{

    public $position;
    public $type;

    //need both contactdata and name db tables
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = "msm_person";
    }

    /**
     *
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        if ($DomElement->parentNode->tagName == 'authors')
        {
            $this->type = 'author';
        }
        else if ($DomElement->parentNode->tagName == 'contributors')
        {
            $this->type = 'contributor';
        }
        else
        {
            $this->type = 'index';
        }

        $this->name = array();
        $this->contactdata = array();

        $names = $DomElement->getElementsByTagName("name");

        foreach ($names as $n)
        {
            $this->name["first"] = $this->getDomAttribute($n->getElementsByTagName("first"));
            $this->name["last"] = $this->getDomAttribute($n->getElementsByTagName("last"));
            $this->name["middle"] = $this->getDomAttribute($n->getElementsByTagName("middle"));
            $this->name["initials"] = $this->getDomAttribute($n->getElementsByTagName("initials"));
        }

        $contactdatas = $DomElement->getElementsByTagName("contactdata");

        foreach ($contactdatas as $contactdata)
        {
            $this->contactdata["email"] = $this->getDomAttribute($contactdata->getElementsByTagName("e-mail"));
            $this->contactdata["webpage"] = $this->getDomAttribute($contactdata->getElementsByTagName("webpage"));
            $this->contactdata["phone"] = $this->getDomAttribute($contactdata->getElementsByTagName("phone"));
            $this->contactdata["address"] = $this->getDomAttribute($contactdata->getElementsByTagName("address"));
        }
    }

    /**
     * This method inserts each instance of authors as a record in the msm_person table.
     * The parameter type is used to specify the class this method was called from. (as this
     * method can be called from either author class or contributor class)
     * 
     * @global moodledatabase $DB
     * @param int $position
     * @param String $type 
     */
    function saveIntoDb($position, $type)
    {
        echo "person save start";
        $time = time();
        print_object($time);
        
        global $DB;

        $data = new stdClass();
        $data->firstname = $this->name["first"];
        $data->middlename = $this->name["middle"];
        $data->lastname = $this->name["last"];
        $data->initials = $this->name["initials"];

        if (!empty($this->contactdata))
        {
            $data->email = $this->contactdata["email"];
            $data->webpage = $this->contactdata["webpage"];
            $data->phone = $this->contactdata["phone"];
            $data->address = $this->contactdata["address"];
        }
        
        $data->type = $type;

        $this->id = $DB->insert_record($this->tablename, $data);
    }

}

?>
