<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Para
 *
 * @author User
 */
class Para extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_para';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->string_id = $DomElement->getAttribute('id');
        $this->align = $DomElement->getAttribute('align');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->description = $this->getDomAttribute($DomElement->getElementsByTagName('description'));

        $this->indexauthors = array();
        $this->indexglossarys = array();
        $this->indexsymbols = array();
        $this->subordinates = array();

        $this->content = array();

//        $parabodys = $DomElement->getElementsByTagName('para.body');
        $doc = new DOMDocument();
//
//        foreach ($parabodys as $parabody)
//        {
        $position = $position + 1;


        $subordinates = $DomElement->getElementsByTagName('subordinate');

        echo "length of sub in para";
        print_object($subordinates->length);

        foreach ($subordinates as $s)
        {
            $hot = $s->getElementsByTagName('hot')->item(0);

            $position = $position + 1;
            $subordinate = new Subordinate($this->xmlpath);
            $subordinate->loadFromXml($s, $position);
            $this->subordinates[] = $subordinate;

//                 echo "value of sub in para";
//                 print_object($hot->nodeValue);
//            print_object($s);
//
            $s->parentNode->replaceChild($hot, $s);
        }
//        foreach ($subordinates as $subor)
//        {
//            $hot = $subor->getElementsByTagName('hot')->item(0);
////            echo "value of sub in para";
////            print_object($hot->nodeValue);
////            print_object($key);
////            print_object($sub);
////            print_object($sub->parentNode->tagName);
//            $subor->parentNode->replaceChild($hot, $subor);
//        }

        $indexauthors = $DomElement->getElementsByTagName('index.author');
        foreach ($indexauthors as $inda)
        {
            $position = $position + 1;
            $indexauthor = new MathIndex($this->xmlpath);
            $indexauthor->loadFromXml($inda, $position);
            $this->indexauthors[] = $indexauthor;

            $inda->parentNode->removeChild($inda);
        }

        $indexglossarys = $DomElement->getElementsByTagName('index.glossary');
        foreach ($indexglossarys as $ig)
        {
            $position = $position + 1;
            $indexglossary = new MathIndex($this->xmlpath);
            $indexglossary->loadFromXml($ig, $position);
            $this->indexglossarys[] = $indexglossary;

            $ig->parentNode->removeChild($ig);
        }

        $indexsymbols = $DomElement->getElementsByTagName('index.symbol');
        foreach ($indexsymbols as $is)
        {
            $position = $position + 1;
            $indexsymbol = new MathIndex($this->xmlpath);
            $indexsymbol->loadFromXml($is, $position);
            $this->indexsymbols[] = $indexsymbol;

            $is->parentNode->removeChild($is);
        }

        $element = $doc->importNode($DomElement, true);
        $this->content[] = $doc->saveXML($element);
//        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->para_align = $this->align;
        $data->caption = $this->caption->content;
        $data->description = $this->description;

        if (!empty($this->content))
        {
//            echo "this content";
//            print_object($this->content);

            foreach ($this->content as $key => $content)
            {
                $data->para_content = $content;
                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }

        foreach ($this->subordinates as $key => $subordinate)
        {
            $subordinate->saveIntoDb($subordinate->position);
        }
    }

}

?>
