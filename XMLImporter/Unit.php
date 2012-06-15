<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
require_once("Comment.php");
require_once("AnswerExample.php");
require_once("PartExercise.php");
require_once("SolutionExt.php");
require_once("ApproachExt.php");
require_once("Step.php");
require_once("Pilot.php");

/**
 * Description of Unit
 *
 * @author User
 */
class Unit extends Element
{

    public $position; //needed to properly place the contents in differen tables in the compositor db table 
    public $creation;
    public $last_revision;
    public $intro;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_unit';
        $this->comptablename = "msm_compositor";
    }

    /**
     *
     * @param DOMElement $DomElement 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position; // keeps track of order of contents

        $this->unitid = $DomElement->getAttribute('unitid');

        $doc = new DOMDocument();

        if (!is_null($DomElement))
        {
            $element = $doc->importNode($DomElement, true);

            $this->subunits = array();
            $this->exercises = array();
            $this->authors = array();
            $this->block = array();
            $this->preface = array();
            $this->summary = array();
            $this->historical = array();
            $this->trailer = array();
            $this->exercises = array();
            $this->examples = array();
            $this->showmes = array();
//            $this->theorem = array(); // theorems are all inside the block.body

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
                        $this->title = $this->getContent($DomElement->getElementsByTagName('title')->item(0));
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
                        $this->stagedates = array();

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

                        if ($element->tagName == 'intro')
                        {
                            $position = $position + 1;
                            $intro = new Intro(dirname($this->xmlpath . '/' . $href));
                            $intro->loadFromXml($element, $position);
                            $this->intro = $intro;
                        }
                        else
                        {
                            echo "another tag?";
                            print_object(element - tagName);
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
                                        $this->exercises[] = $exercisepack;
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
                                        $this->examples[] = $examplePack;
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
                                            $this->showmes[] = $showmepack;
                                        }
                                        if ($element->tagName == 'example.pack')
                                        {
                                            $position = $position + 1;
                                            $examplepack = new Pack(dirname($this->xmlpath . $href));
                                            $examplepack->loadFromXml($element, $position);
                                            $this->examples[] = $examplepack;
                                        }
                                        if ($element->tagName == 'exercise.pack')
                                        {
                                            $position = $position + 1;
                                            $exercisepack = new Pack(dirname($this->xmlpath . $href));
                                            $exercisepack->loadFromXml($element, $position);
                                            $this->exercises[] = $exercisepack;
                                        }
                                        // there are exercise/showme/example/quiz that can be part of this
                                        break;
                                    case('unit'):
                                        $unit = new Unit($this->xmlpath);
                                        $unit->loadFromXml($grandChild, $position);
                                        $this->subunits[] = $unit;
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data = new stdClass();
        $data->string_id = $this->unitid;
        $data->title = $this->title;
        $data->plain_title = $this->plain_title;
        $data->creationdate = $this->creation;
        $data->last_revision_date = $this->last_revision;

        if (!empty($this->acknowledgement))
        {
            $data->acknowledgement->$this->acknowledgement;
        }

        $this->id = $DB->insert_record($this->tablename, $data);

        foreach ($this->authors as $key => $author)
        {
            $author->saveIntoDb($author->position, "author");
        }

        if (!empty($this->contributors))
        {
            foreach ($this->contributors as $key => $contributor)
            {
                $contributor->saveIntoDb($contributor->position, "contributor");
            }
        }

        if (!empty($this->intro))
        {
            $this->intro->saveIntoDb($this->intro->position);
        }

        if (!empty($this->preface))
        {
            $this->preface->saveIntoDb($this->preface->position);
        }

        if (!empty($this->summary))
        {
            $this->summary->saveIntoDb($this->summary->position);
        }

        if (!empty($this->trailer))
        {
            $this->trailer->saveIntoDb($this->trailer->position);
        }

        if (!empty($this->historical))
        {
            $this->historical->saveIntoDb($this->historical->position);
        }

        if (!empty($this->subunits))
        {
            foreach ($this->subunits as $key => $subunit)
            {
                $subunit->saveIntoDb($subunit->position);
            }
        }

        if (!empty($this->block))
        {
            foreach ($this->block as $key => $block)
            {
                $block->saveIntoDb($block->position);
            }
        }

//        foreach ($this->exercises as $key => $exercise)
//        {
//            $exercise->saveIntoDb($exercise->position);
//        }

        foreach ($this->examples as $key => $example)
        {
            $example->saveIntoDb($example->position);
        }

        foreach ($this->showmes as $key => $showme)
        {
            $showme->saveIntoDb($showme->position);
        }

//        $compositorData = new stdClass();
//        $compositorData->unit_id = $this->id;
//
//        $tablename = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename));
//        $compositorData->table_id = $tablename;
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
