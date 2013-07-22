<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */

/**
 * This class represents all the person XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by MathIndex/Unit classes.  Person class inherits from the
 * abstract class Element and for all the methods inherited, read documents for Element class.
 * 
 * @TODO need to implement functions to get user information from moodle user database for newly
 * created materials
 * 
 * @author Ga Young Kim
 */
class Person extends Element
{

    public $id;                         // database ID associated with the person element in msm_person table
    public $position;                   // integer that keeps track of order of elements
    public $type;                       // type of person (eg. author/contributor/part of index.author)
    public $name = array();             // associative array containing person's first/initial/middle/last names
    public $contactdata = array();      // associative array containing the contact info about the said person

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = "msm_person";
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (person element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        person elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Person
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
        return $this;
    }

    /**
     * This method inserts each instance of authors as a record in the msm_person table.
     * The parameter type is used to specify the class this method was called from. (as this
     * method can be called from either author class or contributor class)
     * 
     * @global moodle_database $DB
     * @param int $position             integer that keeps track of order if elements
     * @param int $msmid                not used in ths method but listed in abstract method in Element class
     * @param int $parentid             not used in ths method but listed in abstract method in Element class
     * @param int $siblingid            not used in ths method but listed in abstract method in Element class
     * @param String $type              type of person element (ie. author/contributor/part of index.author)
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '', $type = '')
    {
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

    /**
     * This method is used to retrieve all relevant data linked with the person element specified by the 
     * database IDs given by the parameter of the method.  
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current person element in msm_person table
     * @return \Person
     */
    function loadFromDb($id)
    {
        global $DB;

        $authorrecord = $DB->get_record($this->tablename, array('id' => $id));

        $this->name["first"] = $authorrecord->firstname;
        $this->name["middle"] = $authorrecord->middlename;
        $this->name["last"] = $authorrecord->lastname;
        $this->name["initials"] = $authorrecord->initials;

        $this->contactdata["email"] = $authorrecord->email;
        $this->contactdata["webpage"] = $authorrecord->webpage;
        $this->contactdata["phone"] = $authorrecord->phone;
        $this->contactdata["address"] = $authorrecord->address;

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above.
     * 
     * @return string
     */
    function displayhtml()
    {
        $content = '';
        if (!empty($this->name["first"]))
        {
            $firstname = $this->name["first"];
        }
        if (!empty($this->name["last"]))
        {
            $lastname = $this->name["last"];
        }

        if (!empty($this->name["middle"]))
        {
            $middlename = $this->name["middle"];
        }

        if (!empty($this->name["initials"]))
        {
            $initials = $this->name["initials"];
        }

        $content .= "<div class='author'>";
        $content .= "written by: ";

        if ((!empty($firstname)) && (!empty($lastname)) && (!empty($middlename)))  //initals missing or present
        {
            $content .= $firstname . " " . $middlename . ", " . $lastname;
        }
        else if ((!empty($firstname)) && (!empty($lastname)) && (!empty($initials))) // no middlename
        {
            $content .= $firstname . ", " . $lastname . "(" . $initials . ")";
        }
        else if ((!empty($initials)) && (!empty($middlename)) && (!empty($lastname))) // no firstname
        {
            $content .= "(" . $initials . "), " . $lastname;
        }
        else if ((!empty($initials)) && (!empty($middlename)) && (!empty($firstname))) // no lastname
        {
            $content .= $firstname . " " . $middlename . "(" . $initials . ")";
        }
        else if ((!empty($firstname)) && (!empty($lastname)))
        {
            $content .= $firstname . ", " . $lastname;
        }
        else if (!empty($initials))
        {
            $content .= $initials;
        }
        else if (!empty($lastname))
        {
            $content .= $lastname;
        }
        else if (!empty($firstname))
        {
            $content .= $firstname;
        }
        else if (!empty($middlename))
        {
            $content .= $middlename;
        }

        $content .= "</div>";

        return $content;
    }

}

?>
