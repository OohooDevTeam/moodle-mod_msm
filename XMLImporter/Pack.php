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
 * This class represents all the showme.pack/exercise.pack/example.pack/quiz.pack XML elements
 * in the legacy document(ie. files in the newXML) and it is called by Unit/Associate/Subordinate/Companion/Crossref class.
 * Pack class inherits from the abstract class Element and for all the methods inherited, read documents for Element class.
 * This class only displays the showmes in the showme.pack as a refernce material as everything else will be
 * replaced with moodle quiz_types.
 *
 * @author Ga Young Kim
 */
class Pack extends Element
{

    public $id;                         // Database ID associated with the pack elements in msm_packs table
    public $compid;                     // Database ID associated with the pack elements in msm_compositor table
    public $position;                   // integer that keeps track of order of elements
    public $type;                       // string indicating what type of pack it is in the msm_packs table(ie.showme/exercise/example/quiz)
    public $string_id;                  // unique ID given by the user to identify the pack elements  
    public $title;                      // title element associated with the pack elements
    public $caption;                    // caption element associated with the pack elements (title for material under the pack unit)
    public $doclabel;                   // element representing the label for browser tab
    public $texsupport;                 // LaTeX syntax definition file
    public $literature_db;              // XML flat database with all literature/author indices(for citation purposes)
    public $showmes = array();          // Showme objects associate with the pack elements    
    public $exercises = array();        // Exercise objects associate with the pack elements    
    public $quizs = array();            // MathQuiz objects associate with the pack elements  
    public $examples = array();         // Example objects associate with the pack elements  
    public $childs = array();           // Showme objects associated with pack for loadFromDb and display

    /**
     * constructor for the instace of this class
     * 
     * @param string $xmlpath         filepath to the parent dierectory of this XML file being parsed
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_packs';
    }

    /**
     * This is an abstract method inherited from Element class that is implemented by each of the classes 
     * in XMLImporter folder.  This method parses the given DOMElement (pack elements in this case) and extract
     * needed information to be inserted into the database.
     * 
     * @param DOMElement $DomElement        showme.pack/exercise.pack/example.pack/quiz.pack elements
     * @param int $position                 integer that keeps track of order if elements
     * @return \Pack
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
       
        // the pack element we are dealing with is a showme.pack
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
        // the pack element we are dealing with is a example.pack
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
        // the pack element we are dealing with is a exercise.pack
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
        // the pack element we are dealing with is a quiz.pack
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

        // the element is xi:include with link to the file
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
        return $this;
    }

    /**
     * This method saves the extracted information from the XML files of the pack elements into
     * msm_packs database table.  It calls saveInfoDb method for MathQuiz/Example/Showme/Exercise classes.
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

        $data->string_id = $this->string_id;
        $data->caption = $this->caption;
        $data->title = $this->title;
        $data->doclabel = $this->doclabel;
        $data->texsupport = $this->texsupport;
        $data->literature_db = $this->literature_db;
        $data->type = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data);
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
     * This method retrieves all the data related to the current packs element.  For displaying the information to the view.php
     * page, the only relevant data is the ones associated with showme.pack elements as other elements will be displayed as a question
     * type in moodle.  Therefore, this method only calls on loadFromDb from Showme class.
     * 
     * @global moodle_database $DB
     * @param int $id                   database ID associated with the showme.pack element in msm_packs table
     * @param int $compid               database ID associated with the showme.pack element in msm_compositor table
     * @return \Pack
     */
    function loadFromDb($id, $compid)
    {
        global $DB;

        $packRecord = $DB->get_record($this->tablename, array('id' => $id));

        if (!empty($packRecord))
        {
            $this->compid = $compid;
            $this->string_id = $packRecord->string_id;
            $this->title = $packRecord->title;
            $this->caption = $packRecord->caption;
            $this->doclabel = $packRecord->doclabel;
            $this->type = $packRecord->type;
        }

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childtable = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            switch ($childtable)
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

    /**
     * This method takes the data retrieved by the method above and creates a proper
     * HTML code to display the information in view.php.  It also calls the displayhtml
     * method from Showme class.
     * 
     * @return string
     */
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
