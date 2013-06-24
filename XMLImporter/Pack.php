<?php

/**
**************************************************************************
**                              MSM                                     **
**************************************************************************
* @package     mod                                                      **
* @subpackage  msm                                                      **
* @name        msm                                                      **
* @copyright   University of Alberta                                    **
* @link        http://ualberta.ca                                       **
* @author      Ga Young Kim                                             **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

/**
 * Description of Pack
 *
 * @author User
 */
class Pack extends Element
{

    public $position;
    public $type;
    public $title;
    public $caption;
    public $doclabel;
    public $texsupport;
    public $literature_db;
    public $string_id;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_packs';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $tagName = $DomElement->tagName;
        $this->type = $tagName;

        $this->string_id = $DomElement->getAttribute('id');
        $this->title = $this->getContent($DomElement->getElementsByTagName('title')->item(0));
        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));
        $this->doclabel = $this->getDomAttribute($DomElement->getElementsByTagName('doclabel'));
        $texsupport = $DomElement->getElementsByTagName('texsupport')->item(0);
        $literaturedb = $DomElement->getElementsByTagName('literature.db')->item(0);

        if (!empty($texsupport))
        {
            $this->texsupport = $texsupport->getAttribute('href');
        }

        if (!empty($literaturedb))
        {
            $this->literature_db = $literaturedb->getAttribute('href');
        }

        $showmes = $DomElement->getElementsByTagName('showme');
        $examples = $DomElement->getElementsByTagName('example');
        $exercises = $DomElement->getElementsByTagName('exercise');
        $quizs = $DomElement->getElementsByTagName('quiz');

        $xis = $DomElement->getElementsByTagNameNS('http://www.w3.org/2001/XInclude', '*');

        $this->showmes = array();
        $this->exercises = array();
        $this->quizs = array();
        $this->examples = array();

        if (!empty($showmes))
        {
            foreach ($showmes as $s)
            {
                $position = $position + 1;
                $showme = new Showme($this->xmlpath);
                $showme->loadFromXml($s, $position);
                $this->showmes[] = $showme;
            }
        }
        if (!empty($examples))
        {
            foreach ($examples as $empl)
            {
                $position = $position + 1;
                $example = new Example($this->xmlpath);
                $example->loadFromXml($empl, $position);
                $this->examples[] = $example;
            }
        }
        if (!empty($exercises))
        {
            foreach ($exercises as $excs)
            {
                $position = $position + 1;
                $exercise = new Exercise($this->xmlpath);
                $exercise->loadFromXml($excs, $position);
                $this->exercises[] = $exercise;
            }
        }
        if (!empty($quizs))
        {
            foreach ($quizs as $q)
            {
                $position = $position + 1;
                $quiz = new MathQuiz($this->xmlpath);
                $quiz->loadFromXml($q, $position);
                $this->quizs[] = $quiz;
            }
        }

        foreach ($xis as $x)
        {
            $href = $x->getAttribute('href');

            $xidoc = new DOMDocument();
            @$xidoc->load($this->xmlpath . '/' . $href);

            $element = $xidoc->documentElement;

            if ($element->tagName == 'showme')
            {
                $position = $position + 1;
                $showme = new Showme($this->xmlpath);
                $showme->loadFromXml($element, $position);
                $this->showmes[] = $showme;
            }
            if ($element->tagName == 'example')
            {
                $position = $position + 1;
                $example = new Example($this->xmlpath);
                $example->loadFromXml($element, $position);
                $this->examples[] = $example;
            }
            if ($element->tagName == 'exercise')
            {
                $position = $position + 1;
                $exercise = new Exercise($this->xmlpath);
                $exercise->loadFromXml($element, $position);
                $this->exercises[] = $exercise;
            }
            if ($element->tagName == 'quiz')
            {
                $position = $position + 1;
                $quiz = new MathQuiz($this->xmlpath);
                $quiz->loadFromXml($element, $position);
                $this->quizs[] = $quiz;
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();

        $data->string_id = $this->string_id;
        $data->caption = $this->caption;
        $data->title = $this->title;
        $data->doclabel = $this->doclabel;
        $data->texsupport = $this->texsupport;
        $data->literature_db = $this->literature_db;
        $data->type = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data, true, true);
        $this->compid = $this->insertToCompositor($this->id, $this->tablename, $msmid, $parentid, $siblingid);
        
        $elementPositions = array();
        $sibling_id = 0;
      
        if (!empty($this->quizs))
        {
            foreach ($this->quizs as $key => $quiz)
            {
                $elementPositions['quiz' . '-' . $key] = $quiz->position;
            }
        }

        if (!empty($this->examples))
        {
            foreach ($this->examples as $key => $example)
            {
                $elementPositions['example' . '-' . $key] = $example->position;
            }
        }

        if (!empty($this->exercises))
        {
            foreach ($this->exercises as $key => $exercise)
            {
                $elementPositions['exercise' . '-' . $key] = $exercise->position;
            }
        }

        if (!empty($this->showmes))
        {
            foreach ($this->showmes as $key => $showme)
            {
                $elementPositions['showme' . '-' . $key] = $showme->position;
            }
        }

        asort($elementPositions);

        foreach ($elementPositions as $element => $value)
        {
            switch ($element)
            {
                 case(preg_match("/^(quiz.\d+)$/", $element) ? true : false):
                    $quizString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $quiz = $this->quizs[$quizString[1]];
                        $quiz->saveIntoDb($quiz->position, $msmid, $this->compid);
                        $sibling_id = $quiz->compid;
                    }
                    else
                    {
                        $quiz = $this->quizs[$quizString[1]];
                        $quiz->saveIntoDb($quiz->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $quiz->compid;
                    }
                    break;
                    
                case(preg_match("/^(example.\d+)$/", $element) ? true : false):
                    $exampleString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $example = $this->examples[$exampleString[1]];
                        $example->saveIntoDb($example->position, $msmid, $this->compid);
                        $sibling_id = $example->compid;
                    }
                    else
                    {
                        $example = $this->examples[$exampleString[1]];
                        $example->saveIntoDb($example->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $example->compid;
                    }
                    break;

                case(preg_match("/^(exercise.\d+)$/", $element) ? true : false):
                    $exerciseString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $exercise = $this->exercises[$exerciseString[1]];
                        $exercise->saveIntoDb($exercise->position, $msmid, $this->compid);
                        $sibling_id = $exercise->compid;
                    }
                    else
                    {
                        $exercise = $this->exercises[$exerciseString[1]];
                        $exercise->saveIntoDb($exercise->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $exercise->compid;
                    }
                    break;

                case(preg_match("/^(showme.\d+)$/", $element) ? true : false):
                    $showmeString = explode('-', $element);

                    if (empty($sibling_id))
                    {
                        $showme = $this->showmes[$showmeString[1]];
                        $showme->saveIntoDb($showme->position, $msmid, $this->compid);
                        $sibling_id = $showme->compid;
                    }
                    else
                    {
                        $showme = $this->showmes[$showmeString[1]];
                        $showme->saveIntoDb($showme->position, $msmid, $this->compid, $sibling_id);
                        $sibling_id = $showme->compid;
                    }
                    break;
            }
        }
    }
    
    /**
     * 
     * @global moodle_database $DB
     * @param type $id
     * @param type $compid
     * @return \Pack
     */
    function loadFromDb($id, $compid)
    {
        global $DB;
        
        $packRecord = $DB->get_record($this->tablename, array('id'=>$id));
        
        if(!empty($packRecord))
        {
            $this->compid = $compid;
            $this->string_id = $packRecord->string_id;
            $this->title = $packRecord->title;
            $this->caption = $packRecord->caption;
            $this->doclabel = $packRecord->doclabel;
            $this->type = $packRecord->type;
        }
        
        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');
        
        $this->childs = array();
        
        foreach($childElements as $child)
        {
            $childtable = $DB->get_record('msm_table_collection', array('id'=>$child->table_id))->tablename;
            
            switch($childtable)
            {
                case('msm_showme'):
                    $showme = new Showme();
                    $showme->loadFromDb($child->unit_id, $child->id);
                    $this->childs[] = $showme;
                    break;
            }
        }
        
        return $this;
    }
    
    function displayhtml()
    {
        $content = '';
        
        $content .= "<div class='pack'>";

        $content .= "<div class='title'>";
        $content .= $this->title;
        $content .= "</div>";
        
        foreach ($this->childs as $childComponent)
        {
            $content .= $childComponent->displayhtml();
            $content .= "<br />";
        }
        $content .= "</div>";
        
        return $content;
    }

}

?>
