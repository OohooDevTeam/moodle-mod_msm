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
 * This class represents all the proof.block XML elements in the legacy document
 * (ie. files in the newXML) and it is called by Proof classes.
 * ProofBlock class inherits from the abstract class Element and for all the methods
 * inherited, read documents for Element class.
 *
 * @author Ga Young Kim
 */
class ProofBlock extends Element
{

    public $id;                                     // database ID associated with the proof.block element in msm_proof_block table
    public $compid;                                 // database ID associated with the proof.block element in msm_compositor table
    public $position;                               // integer that keeps track of order of elements
    public $logic_types = array();                  // type of logic represented by the logic elements (eg. implies/equivalent to...etc)
    public $proof_logics = array();                 // logic elements associated with the proof.block element
    public $proof_block_bodys = array();            // content elements associated with the proof.block element
    public $captions = array();                     // caption elements (ie. titles) associated with each proof.block elements
    public $indexauthors = array();                 // MathIndex objects associated with the proof.block element--> info on authors
    public $indexglossarys = array();               // MathIndex objects associated with the proof.block element--> info on terms
    public $indexsymbols = array();                 // MathIndex objects associated with the proof.block element--> info on symbols
    public $subordinates = array();                 // Subordinate objects associated with the proof.block element
    public $matharrays = array();                   // MathArray objects associated with the proof.block element
    public $medias = array();                       // Media objects associated with the proof.block element
    public $tables = array();                       // Table objects associated with the proof.block element
    public $partrefs = array();                     // part.ref elements as child of logic elements in proof.block elements
    //      (part.ref elements are references to different part of proof.block that
    //       are related with logic given by its parent elmeent)
    public $caption;                                // title associated with the proof.block element in load/display functions
    public $proof_content;                          // content associated with the proof.block element in load/display functions
    public $proof_logic;                            // logic associated with the proof.block element in load/display functions

    /**
     * constructor for the class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_proof_block';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (proof.block element in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        proof.block elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \ProofBlock
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $doc = new DOMDocument();

        // index is used to group the appropriate logic/captiona and proof.block.body for the
        // specified proof.block
        // The index number is necessary becasue there are instances of proof.block that does not
        // have logic or caption but it always has a proof.block.body.
        $index = 0;

        // temporaray variable to collect all the proof.block.body of one proof.block
        // the values of sectionedContent will be copied to proof_block_bodys property of object of this class
        $sectionedContent = '';

        $newProofblockNode = $doc->createElementNS('Theorem', 'proof.block');
        // initial declaration of proofblockbodyNode
        $proofblockbodyNode = $doc->createElementNS('Theorem', 'proof.block.body');

        // because the transformed XML does not have the proper structure,
        // this part of the code restructures the XML to make the content processing easier     
        $appended = false; // checking if the new node has been appended to the document 
        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'logic')
                {
                    // append the proofblockbodyNode to new proof.block node
                    // when a new logic element is read
                    $newProofblockNode->appendChild($proofblockbodyNode);
                    $appended = true;
                    // need to create a new proofblockbodyNode
                    $proofblockbodyNode = $doc->createElementNS('Theorem', 'proof.block.body');
                    $childElement = $doc->importNode($child, true);
                    $newProofblockNode->appendChild($childElement);
                }
                else if ($child->tagName == 'caption')
                {
                    if (!$appended)
                    {
                        $newProofblockNode->appendChild($proofblockbodyNode);
                        $proofblockbodyNode = $doc->createElementNS('Theorem', 'proof.block.body');
                    }

                    $childElement = $doc->importNode($child, true);
                    $newProofblockNode->appendChild($childElement);
                }
                else
                {

                    $childElement = $doc->importNode($child, true);
                    $proofblockbodyNode->appendChild($childElement);
                    $appended = false;
                }
            }
        }
        // appending the last proffblockbodyNode (since there are no more logic elements to trigger appending) 
        $newProofblockNode->appendChild($proofblockbodyNode);
        $doc->appendChild($newProofblockNode);

        $rootElement = $doc->documentElement; // new proof.block element

        $logicExisted = false;
        $captionExisted = false;

        foreach ($rootElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'logic')
                {
                    $index++;
                    $logicExisted = true;

                    $this->proof_block_bodys[$index] = $sectionedContent;
                    $sectionedContent = '';
                    $element = $doc->importNode($child, true);
                    $this->logic_types[$index] = $element->getAttribute('type');
                    $this->proof_logics[$index] = $doc->saveXML($element);
                }
                else if ($child->tagName == 'caption')
                {
                    $captionExisted = true;
                    if (!$logicExisted)
                    {
                        $index++;
                    }

                    $captionElement = $doc->importNode($child, true);
                    $this->captions[$index] = $this->getContent($captionElement);
                }
                else if ($child->tagName == 'proof.block.body')
                {
                    $sectionedContent = $doc->saveXML($doc->importNode($child, true));

                    @$doc->loadXML($sectionedContent);
                    $element = $doc->importNode($doc->documentElement, true);

                    $proofblockcontent = '';

                    foreach ($this->processIndexAuthor($element, $position) as $indexauthor)
                    {
                        $this->indexauthors[$index][] = $indexauthor;
                    }

                    foreach ($this->processIndexGlossary($element, $position) as $indexglossary)
                    {
                        $this->indexglossarys[$index][] = $indexglossary;
                    }

                    foreach ($this->processIndexSymbols($element, $position) as $indexsymbol)
                    {
                        $this->indexsymbols[$index][] = $indexsymbol;
                    }
                    foreach ($this->processSubordinate($element, $position) as $subordinate)
                    {
                        $this->subordinates[$index][] = $subordinate;
                    }

                    foreach ($this->processMathArray($element, $position) as $matharray)
                    {
                        $this->matharrays[$index][] = $matharray;
                    }

                    foreach ($this->processMedia($element, $position) as $media)
                    {
                        $this->medias[$index][] = $media;
                    }

                    foreach ($this->processTable($element, $position) as $table)
                    {
                        $this->tables[$index][] = $table;
                    }

                    foreach ($this->processContent($element, $position) as $content)
                    {
                        $proofblockcontent .= $content;
                    }
                    $this->proof_block_bodys[$index] = $proofblockcontent;
                    // resetting the boolean flags to detect next logic/caption
                    $logicExisted = false;
                    $captionExisted = false;
                }
            }
        }

        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of proof element into
     * msm_proof_block database table.  It calls saveInfoDb method for MathIndex, Subordinate, Table,
     * MathArray, Media classes.
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

        foreach ($this->proof_block_bodys as $key => $content)
        {
            if (empty($this->proof_logics[$key]))
            {
                $data->proof_logic = null;
            }
            else
            {
                $data->proof_logic = $this->proof_logics[$key];
            }

            if (empty($this->captions[$key]))
            {
                $data->caption = null;
            }
            else
            {
                $data->caption = $this->captions[$key];
            }
            if (isset($this->logic_types[$key]))
            {
                $data->logic_type = $this->logic_types[$key];
            }
            else
            {
                $data->logic_type = null;
            }

            $data->proof_content = $content;

            $this->id = $DB->insert_record('msm_proof_block', $data);

            $compid = $this->insertToCompositor($this->id, 'msm_proof_block', $msmid, $parentid, $siblingid);
            $siblingid = $compid;

            $elementPositions = array();
            $sibling_id = null;

            if (!empty($this->subordinates[$key]))
            {
                foreach ($this->subordinates[$key] as $secondKey => $subordinate)
                {
                    $elementPositions['subordinate' . '-' . $secondKey] = $subordinate->position;
                }
            }

            if (!empty($this->indexauthors[$key]))
            {
                foreach ($this->indexauthors[$key] as $secondKey => $indexauthor)
                {
                    $elementPositions['indexauthor' . '-' . $secondKey] = $indexauthor->position;
                }
            }

            if (!empty($this->indexglossarys[$key]))
            {
                foreach ($this->indexglossarys[$key] as $secondKey => $indexglosary)
                {
                    $elementPositions['indexglossary' . '-' . $secondKey] = $indexglosary->position;
                }
            }

            if (!empty($this->indexsymbols[$key]))
            {
                foreach ($this->indexsymbols[$key] as $secondKey => $indexsymbol)
                {
                    $elementPositions['indexsymbol' . '-' . $secondKey] = $indexsymbol->position;
                }
            }

            if (!empty($this->medias[$key]))
            {
                foreach ($this->medias[$key] as $secondKey => $media)
                {
                    $elementPositions['media' . '-' . $secondKey] = $media->position;
                }
            }

            if (!empty($this->matharrays[$key]))
            {
                foreach ($this->matharrays[$key] as $secondKey => $matharray)
                {
                    $elementPositions['matharray' . '-' . $secondKey] = $matharray->position;
                }
            }

            if (!empty($this->tables[$key]))
            {
                foreach ($this->tables[$key] as $secondKey => $table)
                {
                    $elementPositions['table' . '-' . $secondKey] = $table->position;
                }
            }

            asort($elementPositions);

            foreach ($elementPositions as $element => $value)
            {
                switch ($element)
                {
                    case(preg_match("/^(subordinate.\d+)$/", $element) ? true : false):
                        $subordinateString = explode('-', $element);

                        if (empty($sibling_id))
                        {
                            $subordinate = $this->subordinates[$key][$subordinateString[1]];
                            $subordinate->saveIntoDb($subordinate->position, $msmid, $compid);
                            $sibling_id = $subordinate->compid;
                        }
                        else
                        {
                            $subordinate = $this->subordinates[$key][$subordinateString[1]];
                            $subordinate->saveIntoDb($subordinate->position, $msmid, $compid, $sibling_id);
                            $sibling_id = $subordinate->compid;
                        }
                        break;

                    case(preg_match("/^(indexauthor.\d+)$/", $element) ? true : false):
                        $indexauthorString = explode('-', $element);

                        if (empty($sibling_id))
                        {
                            $indexauthor = $this->indexauthors[$key][$indexauthorString[1]];
                            $indexauthor->saveIntoDb($indexauthor->position, $msmid, $compid);
                            $sibling_id = $indexauthor->compid;
                        }
                        else
                        {
                            $indexauthor = $this->indexauthors[$key][$indexauthorString[1]];
                            $indexauthor->saveIntoDb($indexauthor->position, $msmid, $compid, $sibling_id);
                            $sibling_id = $indexauthor->compid;
                        }
                        break;

                    case(preg_match("/^(indexsymbol.\d+)$/", $element) ? true : false):
                        $indexsymbolString = explode('-', $element);

                        if (empty($sibling_id))
                        {
                            $indexsymbol = $this->indexsymbols[$key][$indexsymbolString[1]];
                            $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $compid);
                            $sibling_id = $indexsymbol->compid;
                        }
                        else
                        {
                            $indexsymbol = $this->indexsymbols[$key][$indexsymbolString[1]];
                            $indexsymbol->saveIntoDb($indexsymbol->position, $msmid, $compid, $sibling_id);
                            $sibling_id = $indexsymbol->compid;
                        }
                        break;

                    case(preg_match("/^(indexglossary.\d+)$/", $element) ? true : false):
                        $indexglossaryString = explode('-', $element);

                        if (empty($sibling_id))
                        {
                            $indexglossary = $this->indexglossarys[$key][$indexglossaryString[1]];
                            $indexglossary->saveIntoDb($indexglossary->position, $msmid, $compid);
                            $sibling_id = $indexglossary->compid;
                        }
                        else
                        {
                            $indexglossary = $this->indexglossarys[$key][$indexglossaryString[1]];
                            $indexglossary->saveIntoDb($indexglossary->position, $msmid, $compid, $sibling_id);
                            $sibling_id = $indexglossary->compid;
                        }
                        break;

                    case(preg_match("/^(media.\d+)$/", $element) ? true : false):
                        $mediaString = explode('-', $element);

                        if (empty($sibling_id))
                        {
                            $media = $this->medias[$key][$mediaString[1]];
                            $media->saveIntoDb($media->position, $msmid, $compid);
                            $sibling_id = $media->compid;
                        }
                        else
                        {
                            $media = $this->medias[$key][$mediaString[1]];
                            $media->saveIntoDb($media->position, $msmid, $compid, $sibling_id);
                            $sibling_id = $media->compid;
                        }
                        break;

                    case(preg_match("/^(matharray.\d+)$/", $element) ? true : false):
                        $matharrayString = explode('-', $element);

                        if (empty($sibling_id))
                        {
                            $matharray = $this->matharrays[$key][$matharrayString[1]];
                            $matharray->saveIntoDb($matharray->position, $msmid, $compid);
                            $sibling_id = $matharray->compid;
                        }
                        else
                        {
                            $matharray = $this->matharrays[$key][$matharrayString[1]];
                            $matharray->saveIntoDb($matharray->position, $msmid, $compid, $sibling_id);
                            $sibling_id = $matharray->compid;
                        }
                        break;

                    case(preg_match("/^(table.\d+)$/", $element) ? true : false):
                        $tableString = explode('-', $element);

                        if (empty($sibling_id))
                        {
                            $table = $this->tables[$key][$tableString[1]];
                            $table->saveIntoDb($table->position, $msmid, $compid);
                            $sibling_id = $table->compid;
                        }
                        else
                        {
                            $table = $this->tables[$key][$tableString[1]];
                            $table->saveIntoDb($table->position, $msmid, $compid, $sibling_id);
                            $sibling_id = $table->compid;
                        }
                        break;
                }
            }
            $this->compid = $compid;

            // if there are media elements in the proof.block content, need to change the src to 
            // pluginfile.php format to serve the pictures.
            if (!empty($this->medias))
            {
                $newdata = new stdClass();
                $newdata->id = $this->id;
                if (!empty($this->proof_logics[$key]))
                {
                    $newdata->proof_logic = $this->proof_logics[$key];
                }

                if (!empty($this->captions[$key]))
                {
                    $newdata->caption = $this->captions[$key];
                }

                if (!empty($this->logic_types[$key]))
                {
                    $newdata->logic_type = $this->logic_types[$key];
                }

                $newdata->proof_content = $this->processDbContent($content, $this, $key);

                $DB->update_record('msm_proof_block', $newdata);
            }
        }
    }

    /**
     * This method is used to retrieve all relevant data linked with the proof.block element specified by the 
     * database IDs given by the parameter of the method.  LoadFromDb method from Subordinate, MathArray, Media,
     * Table classes is also called by this method.
     * 
     * @global moodle_database $DB
     * @param int $id                       database ID of the current proof.block element in msm_proof_block table
     * @param int $compid                   database ID of the current proof.block element in msm_compositor table
     * @return \ProofBlock
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $proofBlockRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($proofBlockRecord))
        {
            $this->compid = $compid;
            $this->id = $id;
            $this->proof_logic = $proofBlockRecord->proof_logic;
            $this->caption = $proofBlockRecord->caption;
            $this->proof_content = $proofBlockRecord->proof_content;
        }

        $proofid = $DB->get_record('msm_compositor', array('id' => $compid))->parent_id;
        $theoremid = $DB->get_record('msm_compositor', array('id' => $proofid))->parent_id;

        if ((!empty($this->proof_logic)) && (empty($this->caption)))
        {
            $doc = new DOMDocument();
            @$doc->loadXML($this->proof_logic);

            $partrefs = $doc->getElementsByTagName('part.ref');

            foreach ($partrefs as $partref)
            {
                $partrefcontent = $partref->nodeValue;

                $statementTheoremtableid = $DB->get_record('msm_table_collection', array('tablename' => 'msm_statement_theorem'))->id;

                //get all the statement.theorem elements that are children of the current theorem
                $currentTheoremChildren = $DB->get_records('msm_compositor', array('parent_id' => $theoremid, 'table_id' => $statementTheoremtableid), 'prev_sibling_id');

                foreach ($currentTheoremChildren as $statementTheorem)
                {
                    $partTheoremsComp = $DB->get_records('msm_compositor', array('parent_id' => $statementTheorem->id), 'prev_sibling_id');
                    foreach ($partTheoremsComp as $partTheorem)
                    {
                        $partTheorems = $DB->get_records('msm_part_theorem', array('id' => $partTheorem->unit_id, 'partid' => $partrefcontent));

                        if (sizeof($partTheorems) > 1)
                        {
                            $this->partrefs[] = array_pop($partTheorems);
                            break 2;
                        }
                        else if (sizeof($partTheorems) == 1)
                        {
                            $this->partrefs[] = array_pop($partTheorems);
                            break 2;
                        }
                    }
                }
            }
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_subordinate'):
                    $subordinate = new Subordinate();
                    $subordinate->loadFromDb($child->unit_id, $child->id);
                    $this->subordinates[] = $subordinate;
                    break;

                case('msm_math_array'):
                    $matharray = new MathArray();
                    $matharray->loadFromDb($child->unit_id, $child->id);
                    $this->matharrays[] = $matharray;
                    break;

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->medias[] = $media;
                    break;

                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->tables[] = $table;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method produces an HTML code to display the retrieved data from method above and
     * also calls the same method in Subordinate, Media, MathArray and Table classes to
     * display the data from these classes.
     * 
     * @param bool $isindex             flag variable to indicate if this method was called by MathIndex object
     * @return string
     */
    function displayhtml($isindex = false)
    {
        $content = '';

        $content .="<div class='proofblock'>";

        if (!empty($this->proof_logic))
        {
            $content .= "<ul>";
            $content .= "<li>";

            if (empty($this->caption))
            {
                if (count($this->partrefs) > 1)
                {
                    $content .= "<i>" . $this->partrefs[0]->part_content . "&" . $this->partrefs[1]->part_content . "</i>";
                }
                else if (count($this->partrefs) == 1)
                {
                    $content .= "<i>" . $this->partrefs[0]->part_content . "</i>";
                }

                $content = preg_replace("/<a id=(.+)>(.+)<\/a>/", '$2', $content);
                $content = preg_replace("/<a id=(.+)>/", '', $content);
                $content = str_replace('</a>', '', $content);

                $content .= "</li>";
            }
            else if (!empty($this->caption))
            {
                $content .= "<div class='proofblocktitle'>";
                $content .= $this->caption;
                $content .= "</div>";
                $content .= "</li>";
            }
        }
        else
        {
            $content .= "<div class='proofblocktitle'>";
            $content .= $this->caption;
            $content .= "</div>";
        }

        // this content needs proof.block.body tags to be added due to it lacking a root element
        // (without it, it will error out in displayContent due to loadXML method needing a root element)

        $content .= $this->displayContent($this, "<proof.block.body>$this->proof_content</proof.block.body>");

        if (!empty($this->proof_logic))
        {
            $content .= "</ul>";
        }

        $content .="</div>";


        return $content;
    }

}

?>
