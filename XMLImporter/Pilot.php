<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pilot
 *
 * @author User
 */
class Pilot extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_pilot';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    function loadFromXml($DomElement, $position = '')
    {
        $doc = new DOMDocument();
        $pilot_step = $DomElement->getElementsByTagName('pilot.step')->item(0);

        if (!empty($pilot_step))
        {
            $element = $doc->importNode($pilot_step, true);
            $this->pilot_content .= $doc->saveXML($element);
        }

        $pilot_bodys = $DomElement->getElementsByTagName('pilot.body');


        if (!empty($pilot_bodys))
        {
            $doc = new DOMDocument();

            foreach ($pilot_bodys as $pib)
            {
                foreach ($this->processIndexAuthor($pib, $position) as $indexauthor)
                {
                    $this->indexauthors[] = $indexauthor;
                }

                foreach ($this->processIndexGlossary($pib, $position) as $indexglossary)
                {
                    $this->indexglossarys[] = $indexglossary;
                }

                foreach ($this->processIndexSymbols($pib, $position) as $indexsymbol)
                {
                    $this->indexsymbols[] = $indexsymbol;
                }
                foreach ($this->processSubordinate($pib, $position) as $subordinate)
                {
                    $this->subordinates[] = $subordinate;
                }

                foreach ($this->processMedia($pib, $position) as $media)
                {
                    $this->medias[] = $media;
                }

                foreach ($this->processContent($pib, $position) as $content)
                {
                    $this->pilot_content .= $content;
                }
            }
        }
    }

    function saveIntoDb($position)
    {
        global $DB;
        $data = new stdClass();
        $data->pilot_content = $this->pilot_content;

        $this->id = $DB->insert_record($this->tablename, $data);
    }

}

?>
