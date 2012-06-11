<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Definition
 *
 * @author User
 */
class Definition extends Element
{

    public $position;

    /**
     *
     * @param String $xmlpath 
     */
    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_def';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->type = $DomElement->getAttribute('type');

        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->associates = array();
        $this->indexs = array();
        $this->content = array();
        $this->subordinates = array();


        $associates = $DomElement->getElementsByTagName('associate');

        foreach ($associates as $a)
        {
            $position = $position + 1;
            $associate = new Associate($this->xmlpath);
            $associate->loadFromXml($a, $position);
            $this->associates[] = $associate;
        }

        $defbodys = $DomElement->getElementsByTagName('def.body');

        $doc = new DOMDocument();

        foreach ($defbodys as $d)
        {
            $position = $position + 1;
            $subordinates = $d->getElementsByTagName('subordinate');

            foreach ($subordinates as $s)
            {
                $hot = $s->getElementsByTagName('hot')->item(0);

                $position = $position + 1;
                $subordinate = new Subordinate($this->xmlpath);
                $subordinate->loadFromXml($s, $position);
                $this->subordinates[] = $subordinate;

                $s->parentNode->replaceChild($hot, $s);
            }

            $indexauthors = $d->getElementsByTagName('index.author');
            foreach ($indexauthors as $ia)
            {
                $position = $position + 1;
                $indexauthor = new MathIndex($this->xmlpath);
                $indexauthor->loadFromXml($ia, $position);
                $this->indexauthors[] = $indexauthor;

                $ia->parentNode->removeChild($ia);
            }

            $indexglossarys = $d->getElementsByTagName('index.glossary');
            foreach ($indexglossarys as $ig)
            {
                $position = $position + 1;
                $indexglossary = new MathIndex($this->xmlpath);
                $indexglossary->loadFromXml($ig, $position);
                $this->indexglossarys[] = $indexglossary;

                $ig->parentNode->removeChild($ig);
            }

            $indexsymbols = $d->getElementsByTagName('index.symbol');
            foreach ($indexsymbols as $is)
            {
                $position = $position + 1;
                $indexsymbol = new MathIndex($this->xmlpath);
                $indexsymbol->loadFromXml($is, $position);
                $this->indexsymbols[] = $indexsymbol;

                $is->parentNode->removeChild($is);
            }

            $element = $doc->importNode($d, true);
            $this->content[] = $doc->saveXML($element);
        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data->def_type = $this->type;
        $data->string_id = $this->string_id;
        if (!empty($this->caption))
        {
            $data->caption = $this->caption->content;
        }

        $data->description = $this->description;

        if (!empty($this->content))
        {
            foreach ($this->content as $key => $content)
            {
                $data->def_content = $content;
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else // has def.body as child of def
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }

        foreach ($this->associates as $key => $associate)
        {
            $associate->saveIntoDb($associate->position);
        }
    }

}

?>
