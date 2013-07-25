<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * This class represents all the theorem XML elements in the legacy document
 * (ie. files in the newXML) and the newly formed XML exported by the editor system
 * and it is called by Block/Associate/Companion/Crossref/Unit/MathCell/Subordinate/MathIndex classes.
 * Theorem class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 * 
 * @author Ga Young Kim
 */
class Theorem extends Element
{

    public $id;                         // database ID associated with the theorem element in msm_theorem table
    public $compid;                     // database ID associated with the theorem element in msm_compositor table
    public $position;                   // integer that keeps track of order of elements
    public $theorem_type;               // type associated with this theorem (eg. theorem, lemma, proposition..etc)
    public $string_id;                  // user-defined unique string(legacy material) or same as compid above to identify this theorem in XML
    public $caption;                    // title associatd with this theorem element
    public $textcaption;                // title associatd with this theorem element without any math elements 
    public $description;                // description of the theorem (used as a search parameter)
    public $statements = array();       // StatementTheorem objects associated with the theorem element
    public $associates = array();       // Associate objects associated with the theorem element
    public $proofs = array();           // Proof objects associated with the theorem element
    public $childs = array();           // StatementTheorem objects associated with the theorem element --> used in load/display

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_theorem';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (theorem element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        theorem elements
     * @param int $position                 integer that keeps track of order if elements
     * @param string $isRef                 indicates if the theorem in question is a reference material or main part of unit
     * @return \Theorem
     */
    function loadFromXml($DomElement, $position = '', $isRef = 'false')
    {
        $this->position = $position;

        $this->theorem_type = $DomElement->getAttribute('type');
        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));
        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        // if the current theorem is not a reference material, then parse the associates
        if ($isRef == 'false')
        {
            foreach ($DomElement->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'associate')
                    {
                        $position = $position + 1;
                        $associate = new Associate($this->xmlpath);
                        $associate->loadFromXml($child, $position);
                        $this->associates[] = $associate;
                    }
                    else if ($child->tagName == 'statement.theorem')
                    {
                        $position = $position + 1;
                        $statement = new StatementTheorem($this->xmlpath);
                        $statement->loadFromXml($child, $position);
                        $this->statements[] = $statement;
                    }
                    else if ($child->tagName == 'proof')
                    {
                        $position = $position + 1;
                        $proof = new Proof($this->xmlpath);
                        $proof->loadFromXml($child, $position);
                        $this->proofs[] = $proof;
                    }
                }
            }
        }
        else if ($isRef == 'true')  // if the current theorem is not a reference material, then do not parse the associates
        {
            foreach ($DomElement->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'statement.theorem')
                    {
                        $position = $position + 1;
                        $statement = new StatementTheorem($this->xmlpath);
                        $statement->loadFromXml($child, $position);
                        $this->statements[] = $statement;
                    }
                }
            }
        }
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of theorem element into
     * msm_theorem database table.  It calls saveInfoDb method for Associate/Subordinate/Table/
     * MathIndex/Media/MathArray classes.
     * 
     * @global moodle_databse $DB
     * @param int $position              integer that keeps track of order if elements
     * @param int $msmid                 MSM instance ID
     * @param int $parentid              ID of the parent element from msm_compositor
     * @param int $siblingid             ID of the previous sibling element from msm_compositor
     * @param int $theoremCompid         database ID of current theorem element in msm_compositor 
     *                                   (when the theorem record already exists by parsing the reference first)
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '', $theoremCompid = '')
    {
        global $DB;

        $sibling_id = $siblingid;

        if (empty($theoremCompid))
        {
            $data = new stdClass();
            $data->theorem_type = $this->theorem_type;
            $data->string_id = $this->string_id;
            $data->caption = $this->caption;
            $data->textcaption = $this->textcaption;
            $data->description = $this->description;

            $this->id = $DB->insert_record($this->tablename, $data, true, true);
            $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        }
        else
        {
            $this->compid = $theoremCompid;
            $this->id = $DB->get_record('msm_compositor', array('id' => $this->compid))->unit_id;
        }

        $elementPosition = array();

        if (!empty($this->statements))
        {
            foreach ($this->statements as $key => $statement)
            {
                $elementPosition['statement' . '-' . $key] = $statement->position;
            }
        }

        if (!empty($this->proofs))
        {
            foreach ($this->proofs as $key => $proof)
            {
                $elementPosition['proof' . '-' . $key] = $proof->position;
            }
        }

        asort($elementPosition);

        foreach ($elementPosition as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(statement.\d+)$/", $element) ? true : false):
                    $statementString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $statement = $this->statements[$statementString[1]];
                        $statement->saveIntoDb($statement->position, $msmid, $this->compid);
                        $sibling_id = $statement->compid;
                    }
                    else
                    {
                        $statement = $this->statements[$statementString[1]];
                        $statement->saveIntoDb($statement->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $statement->compid;
                    }
                    break;

                case(preg_match("/^(proof.\d+)$/", $element) ? true : false):
                    $proofString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $proof = $this->proofs[$proofString[1]];
                        $proof->saveIntoDb($proof->position, $msmid, $this->compid);
                        $sibling_id = $proof->compid;
                    }
                    else
                    {
                        $proof = $this->proofs[$proofString[1]];
                        $proof->saveIntoDb($proof->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $proof->compid;
                    }
                    break;
            }
        }

        $sibling_id = 0;

        foreach ($this->associates as $associate)
        {
            $associate->saveIntoDb($associate->position, $msmid, $this->compid, $sibling_id);
            $sibling_id = $associate->compid;
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the theorem element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Associate, Subordinate,
     * Media, MathArray and Table classes are also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current theorem element in msm_theorem table
     * @param int $compid                   database ID of the current theorem element in msm_compositor table
     * @param bool $indexref                flag to indicate that the MathIndex object called this function
     * @return \Theorem
     */
    function loadFromDb($id, $compid, $indexref = false)
    {
        global $DB;

        $theoremRecord = $DB->get_record('msm_theorem', array('id' => $id));

        if (!empty($theoremRecord))
        {
            $this->compid = $compid;
            $this->theorem_type = $theoremRecord->theorem_type;
            $this->caption = $theoremRecord->caption;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if (!$indexref)
            {
                if ($childtablename == 'msm_associate')
                {
                    $associate = new Associate();
                    $associate->loadFromDb($child->unit_id, $child->id);
                    $this->associates[] = $associate;
                }
                if ($childtablename == 'msm_statement_theorem')
                {
                    $statement = new StatementTheorem();
                    $statement->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $statement;
                }
                if ($childtablename == 'msm_proof')
                {
                    $proof = new Proof();
                    $proof->loadFromDb($child->unit_id, $child->id);
                    $this->proofs[] = $proof;
                }
            }
            else
            {
                if ($childtablename == 'msm_statement_theorem')
                {
                    $statement = new StatementTheorem();
                    $statement->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $statement;
                }
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in Associate, Subordinate, Media, MathArray and Table classes to
     * display the data from these classes.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';
        $content .= "<br />";
        $content .= "<div class='theorem'>";
        if (!empty($this->caption))
        {
            $content .= "<span class='theoremtitle'>" . $this->caption . "</span>";
        }

        if (!empty($this->theorem_type))
        {
            $content .= "<span class='theoremtype'>" . $this->theorem_type . "</span>";
        }
        $content .= "<br/>";

        $content .= "<div class='mathcontent'>";
        foreach ($this->childs as $child)
        {
            $content .= $child->displayhtml($isindex);
        }
        $content .= "</div>";

        $content .= "<br />";

        if (!$isindex)
        {
            if ((!empty($this->associates)) && (!empty($this->proofs)))
            {
                $content .= "<ul class='minibuttons'>";
                foreach ($this->associates as $associate)
                {
                    $content .= $associate->displayhtml();
                }

                foreach ($this->proofs as $proof)
                {
                    $content .= $proof->displayhtml();
                }

                $content .= "</ul>";
            }
            else if ((!empty($this->associates)) && (empty($this->proofs)))
            {
                $content .= "<ul class='minibuttons'>";
                foreach ($this->associates as $associate)
                {
                    $content .= $associate->displayhtml();
                }
                $content .= "</ul>";
            }
            else if ((empty($this->associates)) && (!empty($this->proofs)))
            {
                $content .= "<ul class='minibuttons'>";
                foreach ($this->proofs as $proof)
                {
                    $content .= $proof->displayhtml();
                }
                $content .= "</ul>";
            }
        }

        $content .= "</div>";
        $content .= "<br />";
//        print_object($content);

        return $content;
    }

}

?>
