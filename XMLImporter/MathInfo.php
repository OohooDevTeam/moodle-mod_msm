<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Info
 *
 * @author User
 */
class MathInfo extends Element
{

    public $position;
    public $content;
    public $caption;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_info';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();


        foreach ($DomElement->childNodes as $infoChild)
        {
            if ($infoChild->nodeType == XML_ELEMENT_NODE)
            {
                if ($infoChild->tagName == 'info.caption')
                {
                    $this->caption = $this->getContent($infoChild);
                }
                else
                {
                    foreach ($this->processSubordinate($infoChild, $position)->subordinates as $subordinate)
                    {
                        $this->subordinates[] = $subordinate;
                    }

                    foreach ($this->processSubordinate($infoChild, $position)->indexauthors as $indexauthor)
                    {
                        $this->indexauthors[] = $indexauthor;
                    }

                    foreach ($this->processSubordinate($infoChild, $position)->indexglossarys as $indexglossary)
                    {
                        $this->indexglossarys[] = $subordinate;
                    }

                    foreach ($this->processSubordinate($infoChild, $position)->indexsymbols as $indexsymbol)
                    {
                        $this->indexsymbols[] = $subordinate;
                    }

                    foreach ($this->processSubordinate($infoChild, $position)->content as $content)
                    {
                        $this->content[] = $content;
                    }
                }
            }
        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data = new stdClass();
        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
        }
        if (!empty($this->content))
        {
            foreach ($this->content as $key => $content)
            {
                $data->info_content = $content;

                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }
    }

}

?>
