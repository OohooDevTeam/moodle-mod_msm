<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pack
 *
 * @author User
 */
class Pack extends Element
{

    public $position;

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
        $this->title = $this->getDomAttribute($DomElement->getElementsByTagName('title'));
        $this->caption = $this->getDomAttribute($DomElement->getElementsByTagName('caption'));
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
        
        $this->showmes = array();
        $this->exercises = array();
        $this->quizs = array();
        $this->examples = array();

        if (!empty($showmes))
        {
            foreach ($showmes as $s)
            {
                $position = $position+1;
                $showme = new Showme($this->xmlpath);
                $showme->loadFromXml($s, $position);
                $this->showmes[] = $showme;
            }
        }
        if (!empty($examples))
        {
            foreach ($examples as $empl)
            {
                $position = $position+1;
                $example = new Example($this->xmlpath);
                $example->loadFromXml($empl, $position);
                $this->examples[] = $example;
            }
        }
        if (!empty($exercises))
        {
            foreach ($exercises as $excs)
            {
                $position = $position+1;
                $exercise = new Exercise($this->xmlpath);
                $exercise->loadFromXml($excs, $position);
                $this->exercises[] = $exercise;
            }
        }
        if (!empty($quizs))
        {
            foreach ($quizs as $q)
            {
                $position = $position+1;
                $quiz = new Quiz($this->xmlpath);
                $quiz->loadFromXml($q, $position);
                $this->quizs[] = $quiz;
            }
        }
    }

}

?>
