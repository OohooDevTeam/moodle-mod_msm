<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Showme
 *
 * @author User
 */
class Showme extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_showme';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->caption = $this->getDomAttribute($DomElement->getElementsByTagName('caption'));

        $this->textcaption = $this->getDomAttribute($DomElement->getElementsByTagName('textcaption'));

        $this->statements = array();

        $statements = $DomElement->getElementsByTagName('statement.showme');

        foreach ($statements as $st)
        {
            $position = $position + 1;
            $content = new stdClass();
            $content = $this->getContent($st, $position, $this->xmlpath);
            $this->statements[] = $content->content;
        }

        $answer_showmes = $DomElement->getElementsByTagName('answer.showme');
        foreach ($answer_showmes as $a)
        {
            $position = $position + 1;
            $answer_showme = new AnswerShowme($this->xmlpath);
            $answer_showme->loadFromXml($a, $position);
            $this->answer_showmes[] = $answer_showme;
        }
    }

}

?>
