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
require_once("Element.php");
require_once("Person.php");
require_once("ExtraInfo.php");
require_once("Intro.php");
require_once("Theorem.php");
require_once("StageDate.php");
require_once("Block.php");
require_once("InContent.php");
require_once("Para.php");
require_once("StatementTheorem.php");
require_once("PartTheorem.php");
require_once("MathIndex.php");
require_once("Pack.php");
require_once("Definition.php");
require_once("Associate.php");
require_once("MathInfo.php");
require_once("MathArray.php");
require_once("Table.php");
require_once("Showme.php");
require_once("Example.php");
require_once("Exercise.php");
require_once("Quiz.php");
require_once("PartExample.php");
require_once("AnswerShowme.php");
require_once("Proof.php");
require_once("Problem.php");
require_once("AnswerExercise.php");
require_once("Solution.php");
require_once("Approach.php");
require_once("MathComment.php");
require_once("AnswerExample.php");
require_once("PartExercise.php");
require_once("SolutionExt.php");
require_once("ApproachExt.php");
require_once("Step.php");
require_once("Pilot.php");
require_once("AnswerExt.php");
require_once("PartQuiz.php");
require_once("QuizChoice.php");
require_once("ProofBlock.php");
require_once("ExternalLink.php");
require_once("Cite.php");
require_once("Item.php");
require_once("MathRow.php");
require_once("MathCell.php");
require_once("ImgArea.php");
require_once("Companion.php");
require_once("Crossref.php");

/**
 * The Unit class is representing the unit XML element which is the "top-level" container for all the child elements.  
 * This class inherits from the abstract class, Element(more detail on this class in Element.php), which has an abstract method of
 * loadFromXml and saveIntoDb. This class contains five main functions: loadFromXML, saveIntoDb, loadFromDb, displayhtml and
 * findUnitfile to aid the processing of contents in unit.(more detailed information provided above method declaration)
 *
 * @author Ga Young Kim
 */
class Unit extends Element
{

    public $position; //needed to properly place the contents in differen tables in the compositor db table 
    public $creation;
    public $last_revision;
    public $intro;
    // compid contains the id of the record inserted into the compositor table to be used to 
    // define parent_id and prev_sibling_id fields in compositor table
    public $compid;
    public $string_id;
    public $standalone;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_unit';
        $this->comptablename = "msm_compositor";
    }

    /**
     * The loadFromXml method is one of the abstract methods inherited from the abstract class Element and it is the main XML parser that
     * parses the appropriate XML files to extract correct data.  It uses DOM to parse the XML in tree-like fashion and stores the data
     * into either as an array that is a property of the class.  These arrays are later used in saveIntoDb to store appropriate data to 
     * the database table
     * 
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '')
    {
        global $DB;

        $this->position = $position; // keeps track of order of contents

        $this->string_id = $DomElement->getAttribute('unitid');
        $this->standalone = $DomElement->getAttribute('standalone');

        $doc = new DOMDocument();

        if (!is_null($DomElement))
        {
            $element = $doc->importNode($DomElement, true);

            $this->subunits = array(); //stores all the subunits under legitimate.children element of XML file
            $this->authors = array();
            $this->block = array();
            $this->preface = array();
            $this->summary = array();
            $this->historical = array();
            $this->trailer = array();
            $this->exercisepacks = array();
            $this->examplepacks = array();
            $this->showmepacks = array();
            $this->quizpacks = array();

            // for exercise/examples listed as a studymaterial element
            $this->studyexercises = array();
            $this->studyexamples = array();
            $this->stagedates = array();

            foreach ($element->childNodes as $key => $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'description')
                    {
                        $this->description = $child;
                    }
                    else if ($child->tagName == 'titles')
                    {
                        $this->title = $this->getContent($child->getElementsByTagName('title')->item(0));
                        $this->plain_title = $this->getDomAttribute($child->getElementsByTagName('plain.title'));
                    }
                    else if ($child->tagName == 'authors')
                    {
                        $persons = $child->getElementsByTagName('person');

                        foreach ($persons as $p)
                        {
                            $position = $position + 1;
                            $person = new Person($this->xmlpath);
                            $person->loadFromXml($p, $position);
                            $this->authors[] = $person;
                        }
                    }
                    else if ($child->tagName == 'contributors')
                    {
                        $this->contributors = array();

                        $persons = $child->getElementsByTagName('person');

                        foreach ($persons as $p)
                        {
                            $position = $position + 1;
                            $person = new Person($this->xmlpath);
                            $person->loadFromXml($p, $position);
                            $this->contributors[] = $person;
                        }
                    }
                    else if ($child->tagName == 'acknowledgements')
                    {
                        $position = $position + 1;
                        $content = new stdClass();
                        $content = $this->getContent($acknowledgements->item(0), $position, $this->xmlpath);
                        $this->acknowledgements = $content->content;
                    }
                    else if ($child->tagName == 'dates')
                    {
                        foreach ($child->childNodes as $key => $grandChild)
                        {
                            if ($grandChild->nodeType == XML_ELEMENT_NODE)
                            {
                                if ($grandChild->tagName == 'creation')
                                {
                                    $date = $grandChild->getElementsByTagName('date');
                                    if (!empty($date))
                                    {
                                        $month = $this->getDomAttribute($date->item(0)->getElementsByTagName('month'));
                                        if ($month < 10) // to format the date as YYYYMMDD
                                        {
                                            $month = '0' . $month;
                                        }

                                        $day = $this->getDomAttribute($date->item(0)->getElementsByTagName('day'));
                                        if ($day < 10)// to format the date as YYYYMMDD
                                        {
                                            $day = '0' . $day;
                                        }

                                        $year = $this->getDomAttribute($date->item(0)->getElementsByTagName('year'));

                                        $this->creation = $year . $month . $day;
                                    }
                                }
                                else if ($grandChild->tagName == 'last.revision')
                                {
                                    $date = $grandChild->getElementsByTagName('date');
                                    if (!empty($date))
                                    {
                                        $month = $this->getDomAttribute($date->item(0)->getElementsByTagName('month'));
                                        if ($month < 10) // to format the date as YYYYMMDD
                                        {
                                            $month = '0' . $month;
                                        }

                                        $day = $this->getDomAttribute($date->item(0)->getElementsByTagName('day'));
                                        if ($day < 10)// to format the date as YYYYMMDD
                                        {
                                            $day = '0' . $day;
                                        }

                                        $year = $this->getDomAttribute($date->item(0)->getElementsByTagName('year'));

                                        $this->last_revision = $year . $month . $day;
                                    }
                                }
                                else if ($grandChild->tagName == 'stage')
                                {
                                    $position = $position + 1;
                                    $stageDate = new StageDate($this->xmlpath);
                                    $stageDate->loadFromXml($grandChild, $position);
                                    $this->stagedates[] = $stageDate;
                                } // end of dates if-else statments
                            }
                        }//end of dates foreach statement
                    }
                    else if ($child->tagName == 'preface')
                    {
                        $position = $position + 1;
                        $preface = new ExtraInfo($this->xmlpath);
                        $preface->loadFromXml($child, $position);
                        $this->preface = $preface;
                    }
                    else if ($child->tagName == 'intro')
                    {
                        $position = $position + 1;
                        $intro = new Intro($this->xmlpath);
                        $intro->loadFromXml($child, $position);
                        $this->intro = $intro;
                    }
                    else if ($child->tagName == 'xi:include')
                    {
                        $href = $child->getAttribute('href');

                        $xidoc = new DOMDocument();
                        @$xidoc->load($this->xmlpath . $href);

                        $element = $xidoc->documentElement;

                        if (trim($element->tagName) == 'intro')
                        {
                            $position = $position + 1;
                            $intro = new Intro(dirname($this->xmlpath . '/' . $href));
                            $intro->loadFromXml($element, $position);
                            $this->intro = $intro;
                        }
                        else if ($element->tagName == 'summary')
                        {
                            $position = $position + 1;
                            $summary = new ExtraInfo($this->xmlpath);
                            $summary->loadFromXml($element, $position);
                            $this->summary = $summary;
                        }
                        else if ($element->tagName == 'historical.notes')
                        {
                            $position = $position + 1;
                            $historical = new ExtraInfo($this->xmlpath);
                            $historical->loadFromXml($element, $position);
                            $this->historical = $historical;
                        }
                        else if ($element->tagName == 'trailer')
                        {
                            $position = $position + 1;
                            $trailer = new ExtraInfo($this->xmlpath);
                            $trailer->loadFromXml($element, $position);
                            $this->trailer = $trailer;
                        }
                        else
                        {
                            echo "another tag?";
                            print_object($element->tagName);
                        }
                    }
                    else if ($child->tagName == 'historical.notes')
                    {
                        $position = $position + 1;
                        $historical = new ExtraInfo($this->xmlpath);
                        $historical->loadFromXml($child, $position);
                        $this->historical = $historical;
                    }
                    else if ($child->tagName == 'summary')
                    {
                        $position = $position + 1;
                        $summary = new ExtraInfo($this->xmlpath);
                        $summary->loadFromXml($child, $position);
                        $this->summary = $summary;
                    }
                    else if ($child->tagName == 'trailer')
                    {
                        $position = $position + 1;
                        $trailer = new ExtraInfo($this->xmlpath);
                        $trailer->loadFromXml($child, $position);
                        $this->trailer = $trailer;
                    }
                    else if ($child->tagName == 'body')
                    {
                        foreach ($child->childNodes as $key => $grandChild)
                        {
                            if ($grandChild->nodeType == XML_ELEMENT_NODE)
                            {
                                if ($grandChild->tagName == 'block')
                                {
                                    $position = $position + 1;
                                    $block = new Block($this->xmlpath);
                                    $block->loadFromXml($grandChild, $position);
                                    $this->block[] = $block;
                                }
                            }
                        }
                    }
                    else if ($child->tagName == 'studymaterials')
                    {
                        foreach ($child->childNodes as $key => $grandChild)
                        {
                            if ($grandChild->nodeType == XML_ELEMENT_NODE)
                            {
                                if ($grandChild->tagName == 'exercise.pack.ref')
                                {
                                    $position = $position + 1;
                                    $exerciseID = $grandChild->getAttribute('exercisePackID');

                                    $filepath = $this->findUnitFile($exerciseID);

                                    $refdoc = new DOMDocument();
                                    @$refdoc->load($filepath);

                                    $exerciseElement = $refdoc->documentElement;

                                    if ($exerciseElement->tagName == 'exercise.pack')
                                    {
                                        $exercisepack = new Pack($this->xmlpath);
                                        $exercisepack->loadFromXml($exerciseElement, $position);
                                        $this->studyexercises[] = $exercisepack;
                                    }
                                }
                                if ($grandChild->tagName == 'example.pack.ref')
                                {
                                    $position = $position + 1;
                                    $exampleID = $grandChild->getAttribute('examplePackID');

                                    $filepath = $this->findUnitFile($exampleID);

                                    $refdoc = new DOMDocument();
                                    @$refdoc->load($filepath);

                                    $exampleElement = $refdoc->documentElement;

                                    if ($exampleElement->tagName == 'example.pack')
                                    {
                                        $examplePack = new Pack($this->xmlpath);
                                        $examplePack->loadFromXml($exampleElement, $position);
                                        $this->studyexamples[] = $examplePack;
                                    }
                                }
                            }
                        }
                    }
                    else if ($child->tagName == 'legitimate.children')
                    {
                        foreach ($child->childNodes as $key => $grandChild)
                        {
                            if ($grandChild->nodeType == XML_ELEMENT_NODE)
                            {
                                $grandChildName = $grandChild->tagName;

                                switch ($grandChildName)
                                {
                                    case('unit.choice'):
                                        $position = $position + 1;
                                        $unitrefID = $grandChild->getAttribute('unitId');

                                        $filepath = $this->findUnitFile($unitrefID);

                                        $refdoc = new DOMDocument();
                                        @$refdoc->load($filepath);

                                        $unitrefelement = $refdoc->documentElement;

                                        if ($unitrefelement->tagName == 'unit')
                                        {
                                            $unit = new Unit($this->xmlpath);
                                            $unit->loadFromXml($unitrefelement, $position);
                                            $this->subunits[] = $unit;
                                        }
                                        break;

                                    case('unit'):
                                        $position = $position + 1;
                                        $unit = new Unit($this->xmlpath);
                                        $unit->loadFromXml($grandChild, $position);
                                        $this->subunits[] = $unit;
                                        break;

                                    case('xi:include'):
                                        $position = $position + 1;

                                        $href = $grandChild->getAttribute('href');

                                        $childxi = new DOMDocument();
                                        @$childxi->load($this->xmlpath . $href);

                                        $element = $childxi->documentElement;

                                        if ($element->tagName == 'unit')
                                        {
                                            $position = $position + 1;
                                            $unit = new Unit(dirname($this->xmlpath . $href));
                                            $unit->loadFromXml($element, $position);
                                            $this->subunits[] = $unit;
                                        }
                                        if ($element->tagName == 'intro')
                                        {
                                            $position = $position + 1;
                                            $intro = new Intro(dirname($this->xmlpath . $href));
                                            $intro->loadFromXml($element, $position);
                                            $this->intro = $intro;
                                        }
                                        if ($element->tagName == 'showme.pack')
                                        {
                                            $position = $position + 1;
                                            $showmepack = new Pack(dirname($this->xmlpath . $href));
                                            $showmepack->loadFromXml($element, $position);
                                            $this->showmepacks[] = $showmepack;
                                        }
                                        if ($element->tagName == 'example.pack')
                                        {
                                            $position = $position + 1;
                                            $examplepack = new Pack(dirname($this->xmlpath . $href));
                                            $examplepack->loadFromXml($element, $position);
                                            $this->examplepacks[] = $examplepack;
                                        }
                                        if ($element->tagName == 'exercise.pack')
                                        {
                                            $position = $position + 1;
                                            $exercisepack = new Pack(dirname($this->xmlpath . $href));
                                            $exercisepack->loadFromXml($element, $position);
                                            $this->exercisepacks[] = $exercisepack;
                                        }
                                        if ($element->tagName == 'quiz.pack')
                                        {
                                            $position = $position + 1;
                                            $quizpack = new Pack(dirname($this->xmlpath . $href));
                                            $quizpack->loadFromXml($element, $position);
                                            $this->quizpacks[] = $quizpack;

                                            // there are exercise/showme/example/quiz that can be part of this
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $exercisepackRecordID = 0;
        $examplepackRecordID = 0;

        // to keep track of prev_sibling ids when going through all child elements
        $sibling_id = null;

        $data = new stdClass();
        $data->standalone = $this->standalone;
        $data->string_id = $this->string_id;
        $data->title = $this->title;
        $data->plain_title = $this->plain_title;
        $data->creationdate = $this->creation;
        $data->last_revision_date = $this->last_revision;

        if (!empty($this->acknowledgement))
        {
            $data->acknowledgement = $this->acknowledgement;
        }

        $this->id = $DB->insert_record($this->tablename, $data);

        // for inserting unit records in to compositor table

        if ($this->position == 1) // it's the root element, which means, no parent and no previous siblings
        {
            $compdata = new stdClass();
            $compdata->msm_id = $parentid;
            $compdata->unit_id = $this->id;
            $compdata->table_id = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;
            $compdata->parent_id = 0;
            $compdata->prev_sibling_id = 0;

            $this->compid = $DB->insert_record('msm_compositor', $compdata);
        }
        else // child element, therefore, has a parentid with possible previous sibling id
        {
            $compdata = new stdClass();
            $compdata->unit_id = $this->id;
            $compdata->table_id = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;
            $compdata->parent_id = $parentid;
            $compdata->prev_sibling_id = $siblingid;

            $this->compid = $DB->insert_record('msm_compositor', $compdata);
        }

        $elementPositions = array();

        if (!empty($this->intro))
        {
            $elementPositions['intro'] = $this->intro->position;
        }

        // ** may not be correct **//
        // it maybe the case where different authors may alternate in order with another element...
        // in this case, need to think of another method to determine the order of elements 
        if (!empty($this->authors))
        {
            foreach ($this->authors as $key => $author)
            {
                $elementPositions['author' . '-' . $key] = $author->position;
            }
        }
        if (!empty($this->contributors))
        {
            foreach ($this->contributors as $key => $contributor)
            {
                $elementPositions['contributors' . '-' . $key] = $contributor->position;
            }
        }
        if (!empty($this->preface))
        {
            $elementPositions['preface'] = $this->preface->position;
        }

        if (!empty($this->historical))
        {
            $elementPositions['historical'] = $this->historical->position;
        }

        if (!empty($this->trailer))
        {
            $elementPositions['trailer'] = $this->trailer->position;
        }

        if (!empty($this->summary))
        {
            $elementPositions['summary'] = $this->summary->position;
        }

        if (!empty($this->stagedates))
        {
            foreach ($this->stagedates as $key => $stagedate)
            {
                $elementPositions['stage' . '-' . $key] = $stagedate->position;
            }
        }

        if (!empty($this->subunits))
        {
            foreach ($this->subunits as $key => $subunit)
            {
                $elementPositions['subunit' . '-' . $key] = $subunit->position;
            }
        }

        if (!empty($this->block))
        {
            foreach ($this->block as $key => $block)
            {
                $elementPositions['block' . '-' . $key] = $block->position;
            }
        }

        if (!empty($this->exercisepacks))
        {
            foreach ($this->exercisepacks as $key => $exercisepack)
            {
                $elementPositions['exercisepack' . '-' . $key] = $exercisepack->position;
            }
        }

        if (!empty($this->examplepacks))
        {
            foreach ($this->examplepacks as $key => $examplepack)
            {
                $elementPositions['examplepack' . '-' . $key] = $examplepack->position;
            }
        }

        if (!empty($this->showmepacks))
        {
            foreach ($this->showmepacks as $key => $showmepack)
            {
                $elementPositions['showmepack' . '-' . $key] = $showmepack->position;
            }
        }

        if (!empty($this->quizpacks))
        {
            foreach ($this->quizpacks as $key => $quizpack)
            {
                $elementPositions['quizpack' . '-' . $key] = $quizpack->position;
            }
        }

        if (!empty($this->studyexercises))
        {
            foreach ($this->studyexercises as $key => $studyexercise)
            {
                $elementPositions['studyexercise' . '-' . $key] = $studyexercise->position;
            }
        }

        if (!empty($this->studyexamples))
        {
            foreach ($this->studyexamples as $key => $studyexample)
            {
                $elementPositions['studyexample' . '-' . $key] = $studyexample->position;
            }
        }

        // sorts the array according to the position of the element
        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                case(preg_match("/^(author.\d+)$/", $element) ? true : false):

                    $authorstring = split('-', $element);

                    if (empty($sibling_id))//  first author element which has no previous sibling
                    {
                        $author = $this->authors[$authorstring[1]];
                        $author->saveIntoDb($author->position, '', '', 'author');
                        $this->authors[$authorstring[1]]->compid = $author->insertToCompositor($author->id, $author->tablename, $this->compid);
                        $sibling_id = $this->authors[$authorstring[1]]->compid;
                    }
                    else // child has a previous sibling
                    {
                        $author = $this->authors[$authorstring[1]];
                        $author->saveIntoDb($author->position, '', '', 'author');
                        $this->authors[$authorstring[1]]->compid = $author->insertToCompositor($author->id, $author->tablename, $this->compid, $sibling_id);
                        $sibling_id = $this->authors[$authorstring[1]]->compid;
                    }
                    break;

                case(preg_match("/^(contributors.\d+)$/", $element) ? true : false):

                    $contributorstring = split('-', $element);

                    if (empty($sibling_id))//  first author element which has no previous sibling
                    {
                        $contributor = $this->contributors[$contributorstring[1]];
                        $contributor->saveIntoDb($contributor->position, '', '', 'contributor');
                        $this->contributors[$contributorstring[1]]->compid = $contributor->insertToCompositor($contributor->id, $contributor->tablename, $this->compid);
                        $sibling_id = $this->contributors[$contributorstring[1]]->compid;
                    }
                    else // child has a previous sibling
                    {
                        $contributor = $this->contributors[$contributorstring[1]];
                        $contributor->saveIntoDb($contributor->position, '', '', 'contributor');
                        $this->contributors[$contributorstring[1]]->compid = $contributor->insertToCompositor($contributor->id, $contributor->tablename, $this->compid, $sibling_id);
                        $sibling_id = $this->contributors[$contributorstring[1]]->compid;
                    }
                    break;

                case(preg_match("/^(stage.\d+)$/", $element) ? true : false):
                    $stageString = split('-', $element);

                    if (empty($sibling_id))//  first author element which has no previous sibling
                    {
                        $stagedate = $this->stagedates[$stageString[1]];
                        $stagedate->saveIntoDb($stagedate->position, 'contributor');
                        $this->stagedates[$stageString[1]]->compid = $stagedate->insertToCompositor($stagedate->id, $stagedate->tablename, $this->compid);
                        $sibling_id = $this->stagedates[$stageString[1]]->compid;
                    }
                    else // child has a previous sibling
                    {
                        $stagedate = $this->stagedates[$stageString[1]];
                        $stagedate->saveIntoDb($stagedate->position);
                        $this->stagedates[$stageString[1]]->compid = $stagedate->insertToCompositor($stagedate->id, $stagedate->tablename, $this->compid, $sibling_id);
                        $sibling_id = $this->stagedates[$stageString[1]]->compid;
                    }
                    break;

                case('intro'):
                    if (empty($sibling_id))
                    {
                        $this->intro->saveIntoDb($this->intro->position, $this->compid);
                        $sibling_id = $this->intro->compid;
                    }
                    else
                    {
                        $this->intro->saveIntoDb($this->intro->position, $this->compid, $sibling_id);
                        $sibling_id = $this->intro->compid;
                    }
                    break;

                case(preg_match("/^(block.\d+)$/", $element) ? true : false):
                    $blockString = split('-', $element);

                    if (empty($sibling_id))
                    {
                        $block = $this->block[$blockString[1]];
                        $block->saveIntoDb($block->position, $this->compid);
                        $sibling_id = $block->root;
                    }
                    else
                    {
                        $block = $this->block[$blockString[1]];
                        $block->saveIntoDb($block->position, $this->compid, $sibling_id);
                        if (!empty($block->root))
                        {
                            $sibling_id = $block->root;
                        }
                    }

                    break;

                case(preg_match("/^(subunit.\d+)$/", $element) ? true : false):
                    $subunitString = split('-', $element);
                    $subunitRecord = $this->checkForRecord($this->subunits[$subunitString[1]]);

                    if (empty($subunitRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $this->compid);
                            $sibling_id = $subunit->compid;
                        }
                        else
                        {
                            $subunit = $this->subunits[$subunitString[1]];
                            $subunit->saveIntoDb($subunit->position, $this->compid, $sibling_id);
                            $sibling_id = $subunit->compid;
                        }
                    }
                    else
                    {
                        $subunitID = $subunitRecord->id;
                        $unittableID = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;

                        $subunitCompRecords = $DB->get_records('msm_compositor', array('unit_id' => $subunitID, 'table_id' => $unittableID));
                        $subunitCompID = $this->insertToCompositor($subunitID, 'msm_unit', $this->compid, $sibling_id);
                        $sibling_id = $subunitCompID;

                        foreach ($subunitCompRecords as $unitCompRecord)
                        {
                            $this->grabSubunitChilds($unitCompRecord, $subunitCompID);
                        }
                    }
                    break;

                case(preg_match("/^(exercisepack.\d+)$/", $element) ? true : false):
                    $exercisepackString = split('-', $element);
                    $exercisepackRecord = $this->checkForRecord($this->exercisepacks[$exercisepackString[1]]);

                    if (empty($exercisepackRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $exercisepack = $this->exercisepacks[$exercisepackString[1]];
                            $exercisepack->saveIntoDb($exercisepack->position, $this->compid);
                            $sibling_id = $exercisepack->compid;
                        }
                        else
                        {
                            $exercisepack = $this->exercisepacks[$exercisepackString[1]];
                            $exercisepack->saveIntoDb($exercisepack->position, $this->compid, $sibling_id);
                            $sibling_id = $exercisepack->compid;
                        }
                    }
                    else
                    {
                        $exercisepackID = $exercisepackRecord->id;
                        $sibling_id = $this->insertToCompositor($exercisepackID, 'msm_packs', $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(examplepack.\d+)$/", $element) ? true : false):
                    $examplepackString = split('-', $element);
                    $examplepackRecord = $this->checkForRecord($this->examplepacks[$examplepackString[1]]);

                    if (empty($examplepackRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $examplepack = $this->examplepacks[$examplepackString[1]];
                            $examplepack->saveIntoDb($examplepack->position, $this->compid);
                            $sibling_id = $examplepack->compid;
                        }
                        else
                        {
                            $examplepack = $this->examplepacks[$examplepackString[1]];
                            $examplepack->saveIntoDb($examplepack->position, $this->compid, $sibling_id);
                            $sibling_id = $examplepack->compid;
                        }
                    }
                    else
                    {
                        $examplepackID = $examplepackRecord->id;
                        $examplepack = $this->examplepacks[$examplepackString[1]];
                        $examplepack->compid = $this->insertToCompositor($examplepackID, $examplepack->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(quizpack.\d+)$/", $element) ? true : false):

                    $quizpackString = split('-', $element);
                    $quizpackRecord = $this->checkForRecord($this->quizpacks[$quizpackString[1]]);

                    if (empty($quizpackRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $quizpack = $this->quizpacks[$quizpackString[1]];
                            $quizpack->saveIntoDb($quizpack->position, $this->compid);
                            $sibling_id = $quizpack->compid;
                        }
                        else
                        {
                            $quizpack = $this->quizpacks[$quizpackString[1]];
                            $quizpack->saveIntoDb($quizpack->position, $this->compid, $sibling_id);
                            $sibling_id = $quizpack->compid;
                        }
                    }
                    else
                    {
                        $quizpackID = $quizpackRecord->id;
                        $quizpack = $this->quizpacks[$quizpackString[1]];
                        $quizpack->compid = $this->insertToCompositor($quizpackID, $quizpack->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(showmepack.\d+)$/", $element) ? true : false):
                    $showmepackString = split('-', $element);
                    $showmepackRecord = $this->checkForRecord($this->showmepacks[$showmepackString[1]]);

                    if (empty($showmepackRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $showmepack = $this->showmepacks[$showmepackString[1]];
                            $showmepack->saveIntoDb($showmepack->position, $this->compid);
                            $sibling_id = $showmepack->compid;
                        }
                        else
                        {
                            $showmepack = $this->showmepacks[$showmepackString[1]];
                            $showmepack->saveIntoDb($showmepack->position, $this->compid, $sibling_id);
                            $sibling_id = $showmepack->compid;
                        }
                    }
                    else
                    {
                        $showmepackID = $showmepackRecord->id;
                        $showmepack = $this->showmepacks[$showmepackString[1]];
                        $showmepack->compid = $this->insertToCompositor($showmepackID, $showmepack->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(studyexercise.\d+)$/", $element) ? true : false):
                    $studyexerciseString = split('-', $element);

                    $studyexerciseRecord = $this->checkForRecord($this->studyexercises[$studyexerciseString[1]]);

                    if (empty($studyexerciseRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $studyexercise = $this->studyexercises[$studyexerciseString[1]];
                            $studyexercise->saveIntoDb($studyexercise->position, $this->compid);
                            $sibling_id = $studyexercise->compid;
                        }
                        else
                        {
                            $studyexercise = $this->studyexercises[$studyexerciseString[1]];
                            $studyexercise->saveIntoDb($studyexercise->position, $this->compid, $sibling_id);
                            $sibling_id = $studyexercise->compid;
                        }
                    }
                    else
                    {
                        $studyexerciseID = $studyexerciseRecord->id;
                        $studyexercise = $this->studyexercises[$studyexerciseString[1]];
                        $studyexercise->compid = $this->insertToCompositor($studyexerciseID, $studyexercise->tablename, $this->compid, $sibling_id);
                    }
                    break;

                case(preg_match("/^(studyexample.\d+)$/", $element) ? true : false):
                    $studyexampleString = split('-', $element);

                    $studyexampleRecord = $this->checkForRecord($this->studyexamples[$studyexampleString[1]]);

                    if (empty($studyexampleRecord))
                    {
                        if (empty($sibling_id))
                        {
                            $studyexample = $this->studyexamples[$studyexampleString[1]];
                            $studyexample->saveIntoDb($studyexample->position, $this->compid);
                            $sibling_id = $studyexample->compid;
                        }
                        else
                        {
                            $studyexample = $this->studyexamples[$studyexampleString[1]];
                            $studyexample->saveIntoDb($studyexample->position, $this->compid, $sibling_id);
                            $sibling_id = $studyexample->compid;
                        }
                    }
                    else
                    {
                        $studyexampleID = $studyexampleRecord->id;
                        $studyexample = $this->studyexamples[$studyexampleString[1]];
                        $studyexample->compid = $this->insertToCompositor($studyexampleID, $studyexample->tablename, $this->compid, $sibling_id);
                    }
                    break;
            }
        }
    }

    function loadFromDb($id, $compid)
    {
        global $DB;

        $unitrecord = $DB->get_record($this->tablename, array('id' => $id));

        $unitCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        if (!empty($unitrecord))
        {
            $this->compid = $compid;
            $this->id = $unitrecord->id;
            $this->title = $unitrecord->title;
            $this->creationdate = $unitrecord->creationdate;
            $this->last_revision_date = $unitrecord->last_revision_date;

//            $this->standalone = $unitrecord->standalone;
            $this->parent_id = $unitCompRecord->parent_id;
            $this->prev_sibling_id = $unitCompRecord->prev_sibling_id;

            if (!empty($unitrecord->acknowledgements))
            {
                $this->acknowledgement = $unitrecord->acknowledgements;
            }
        }

        //return child element records in ascending order of prev_sibling_id field
        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        $this->authors = array();
        $this->childs = array();

        foreach ($childElements as $child)
        {
            $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtablename)
            {
                case('msm_extra_info'):
                    $extrainfo = new ExtraInfo();
                    $extrainfo->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $extrainfo;
                    break;

                case('msm_person'):
                    $person = new Person();
                    $person->loadFromDb($child->unit_id);
                    $this->authors[] = $person;
                    break;

                case('msm_intro'):
                    $intro = new Intro();
                    $intro->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $intro;
                    break;
                case('msm_def'):
                    $def = new Definition();
                    $def->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $def;
                    break;

                case('msm_para'):
                    $para = new Para();
                    $para->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $para;
                    break;

                case('msm_content'):
                    $incontent = new InContent();
                    $incontent->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $incontent;
                    break;

                case('msm_theorem'):
                    $theorem = new Theorem();
                    $theorem->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $theorem;
                    break;

                case('msm_media'):
                    $media = new Media();
                    $media->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $media;
                    break;

                case('msm_math_array'):
                    $matharray = new MathArray();
                    $matharray->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $matharray;
                    break;

                case('msm_table'):
                    $table = new Table();
                    $table->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $table;
                    break;
//                   
                case('msm_comment'):
                    $comment = new MathComment();
                    $comment->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $comment;
                    break;
            }
        }

        return $this;
    }

    function displayhtml()
    {
        $content = '';
//        $content .= "<div class='unit' id='unit-" . $this->compid . "'>";
        // most likely a first page if creationdate and authors are there...
        if ((!empty($this->creationdate)) || (!empty($this->authors)))
        {
            $content .= "<div class='ridge'>";
            if (!empty($this->title))
            {
                $content .= "<div class='title'>";
                $content .= $this->title;
                $content .= "</div>";
            }

            if (!empty($this->creationdate))
            {
                $creationyear = substr($this->creationdate, 0, 4);
                $creationmonth = substr($this->creationdate, 4, -2);
                $creationdate = substr($this->creationdate, 6, 8);

                $revisionyear = substr($this->last_revision_date, 0, 4);
                $revisionmonth = substr($this->last_revision_date, 4, -2);
                $revisiondate = substr($this->last_revision_date, 6, 8);


                $content .= "<div class='date'>";
                $content .= "created on: ";
                $content .= $creationyear . "-" . $creationmonth . "-" . $creationdate . "<br />";
                $content .= "last revised on: ";
                $content .= $revisionyear . "-" . $revisionmonth . "-" . $revisiondate . "<br />";
                $content .= "</div>";

                foreach ($this->authors as $author)
                {
                    $content .= $author->displayhtml();
                }
            }

            $content .= "</div>"; //for closing the border div
        } // any units that is not a root element
        else
        {
            if (!empty($this->title))
            {
                $content .= "<div class='title'>";
                $content .= $this->title;
                $content .= "</div>";
            }
        }

        foreach ($this->childs as $child)
        {
            $content .= $child->displayhtml();
        }

//        $content .= "</div>";

        return $content;
    }

    /**
     * This function finds the file with the specified unit ID and returns the file path.
     * It is called by loadFromXml function whenever unit.choice element is encountered in XML.
     * 
     * @param type $DomElement the XML element that is being searched
     * @param type $unitID the ID of the unit element that is specified in unit.choice element
     * @return type $path path to the file containing the specified unit ID
     */
    function findUnitFile($unitID)
    {
        $path = $this->xmlpath;

        $dirOrFiles = scandir($path);

        foreach ($dirOrFiles as $key => $file)
        {
            // first two items in the array $dirOrFiles refers to the current and parent directories
            // which is not useful in this case
            if ($key > 1)
            {
                $ext = explode('.', $file);

                if ((sizeof($ext) > 1) && ($ext[1] == 'xml'))
                {
                    $DomParser = new DOMDocument();
                    @$DomParser->load($this->xmlpath . '/' . $file);

                    $unit = $DomParser->getElementsByTagName("unit")->item(0);
                    $exercise = $DomParser->getElementsByTagName('exercise.pack')->item(0);
                    $example = $DomParser->getElementsByTagName('example.pack')->item(0);

                    if (!empty($unit))
                    {
                        $unitstringID = $unit->getAttribute('unitid');

                        if ($unitstringID == $unitID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    if (!empty($exercise))
                    {
                        $exerciseID = $exercise->getAttribute('id');

                        if ($exerciseID == $unitID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    if (!empty($example))
                    {
                        $exampleID = $example->getAttribute('id');

                        if ($exampleID == $unitID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                }
            }
        }
    }

}

?>
