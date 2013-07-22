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
 * This class represents all the associate XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Theorem classes.
 * Proof class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class Proof extends Element
{

    public $id;                         // database ID associated with the proof element in msm_proof table
    public $compid;                     // database ID associated with the proof element in msm_compositor table
    public $position;                   // integer that keeps track of order of elements
    public $string_id;                  // unique user-defined string to identify this proof element
    public $proof_type;                 // type of proof --> proof/idea/justification...etc
    public $proof_blocks = array();     // sections/parts of proof elements
    public $childs = array();           // same as proof_block but used in load/display of db data

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_proof';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (proof element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        proof elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Proof
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('proofid');

        $this->proof_type = $DomElement->getAttribute('type');

        $proof_blocks = $DomElement->getElementsByTagName('proof.block');

        foreach ($proof_blocks as $key => $pb)
        {
            $position = $position + 1;
            $proof_block = new ProofBlock($this->xmlpath);
            $proof_block->loadFromXml($pb, $position);
            $this->proof_blocks[] = $proof_block;
        }
        return $this;
    }

   /**
    * This method saves the extracted information from the XML files of proof element into
     * msm_proof database table.  It calls saveInfoDb method for ProofBlock classes.
     * 
     * @global moodle_databse $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
    */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        $sibling_id = $siblingid;

        $data->string_id = $this->string_id;
        $data->proof_type = $this->proof_type;

        $this->id = $DB->insert_record($this->tablename, $data);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);

        foreach ($this->proof_blocks as $proof_block)
        {
            if (empty($sibling_id))
            {
                $proof_block->saveIntoDb($proof_block->position, $msmid, $this->compid);
                $sibling_id = $proof_block->compid;
            }
            else
            {
                $proof_block->saveIntoDb($proof_block->position, $msmid, $this->compid, $sibling_id);
                $sibling_id = $proof_block->compid;
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the proof element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from ProofBlock class is also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current proof element in msm_proof table
     * @param int $compid                   database ID of the current proof element in msm_compositor table
     * @return \Proof
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $proofRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($proofRecord))
        {
            $this->compid = $compid;
            $this->proof_type = $proofRecord->proof_type;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childtablename == 'msm_proof_block')
            {
                $proofblock = new ProofBlock();
                $proofblock->loadFromDb($child->unit_id, $child->id);
                $this->childs[] = $proofblock;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in ProofBlock class to display the data.
     * 
     * @return string
     */
    function displayhtml()
    {
        $content = '';

        $content .= "<li class='proofminibutton' id='proofminibutton-" . $this->compid . "' onmouseover='infoopen(" . $this->compid . ")'>";
        $content .= "<span style='cursor:pointer'>";
        if (!empty($this->proof_type))
        {
            $content .= trim($this->proof_type);
        }
        else
        {
            $content .= "Proof";
        }
        $content .= "</span>";
        $content .= "</li>";

        $content .= "<div class='proof' id='proof-" . $this->compid . "' style='display:none;'>";

        foreach ($this->childs as $childComponent)
        {
            $content .= $childComponent->displayhtml();
        }
        $content .= "</div>";

        return $content;
    }

}

?>
