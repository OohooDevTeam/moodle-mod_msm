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
                    $doc = new DOMDocument();
                    $position = $position + 1;                    

                    $subordinates = $infoChild->getElementsByTagName('subordinate');

                    foreach ($subordinates as $s)
                    {
                        $hot = $s->getElementsByTagName('hot')->item(0);

                        $position = $position + 1;
                        $subordinate = new Subordinate($this->xmlpath);
                        $subordinate->loadFromXml($s, $position);
                        $this->subordinates[] = $subordinate;

                        $s->parentNode->replaceChild($hot, $s);
                    }

                    $indexauthors = $infoChild->getElementsByTagName('index.author');
                    foreach ($indexauthors as $ia)
                    {
                        $position = $position + 1;
                        $indexauthor = new MathIndex($this->xmlpath);
                        $indexauthor->loadFromXml($ia, $position);
                        $this->indexauthors[] = $indexauthor;

                        $ia->parentNode->removeChild($ia);
                    }

                    $indexglossarys = $infoChild->getElementsByTagName('index.glossary');
                    foreach ($indexglossarys as $ig)
                    {
                        $position = $position + 1;
                        $indexglossary = new MathIndex($this->xmlpath);
                        $indexglossary->loadFromXml($ig, $position);
                        $this->indexglossarys[] = $indexglossary;
                        
                        $ig->parentNode->removeChild($ig);
                    }
                  
                    $indexsymbols = $infoChild->getElementsByTagName('index.symbol');
                    foreach ($indexsymbols as $is)
                    {
                        $position = $position + 1;
                        $indexsymbol = new MathIndex($this->xmlpath);
                        $indexsymbol->loadFromXml($is, $position);
                        $this->indexsymbols[] = $indexsymbol;

                        $is->parentNode->removeChild($is);
                    }

                    $element = $doc->importNode($infoChild, true);
                    $this->content[] = $doc->saveXML($element);
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
            $data->caption = $this->caption->content;
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
