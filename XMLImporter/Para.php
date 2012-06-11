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
        
       foreach($this->processSubordinate($DomElement, $position)->subordinates as $subordinate)
       {
           $this->subordinates[] = $subordinate;
       }
       
       foreach($this->processSubordinate($DomElement, $position)->indexauthors as $indexauthor)
       {
           $this->indexauthors[] = $indexauthor;
       }
       
       foreach($this->processSubordinate($DomElement, $position)->indexglossarys as $indexglossary)
       {
           $this->indexglossarys[] = $subordinate;
       }
       
       foreach($this->processSubordinate($DomElement, $position)->indexsymbols as $indexsymbol)
       {
           $this->indexsymbols[] = $subordinate;
       }
       
       foreach($this->processSubordinate($DomElement, $position)->content as $content)
       {
           $this->content[] = $content;
       }

//        $doc = new DOMDocument();
//        
//        $position = $position + 1;
//
//        $subordinates = $DomElement->getElementsByTagName('subordinate');
//       
//        $length = $subordinates->length;
//
//        for ($i = 0; $i < $length; $i++)
//        {
//            $hot = $subordinates->item(0)->getElementsByTagName('hot')->item(0);
//
//            $position = $position + 1;
//            $subordinate = new Subordinate($this->xmlpath);
//            $subordinate->loadFromXml($subordinates->item(0), $position);
//            $this->subordinates[] = $subordinate;
//
//            $subordinates->item(0)->parentNode->replaceChild($hot, $subordinates->item(0));
//        }
//        
//
//        $indexauthors = $DomElement->getElementsByTagName('index.author');
//        $ialength = $indexauthors->length;
//        for ($i = 0; $i < $ialength; $i++)
//        {
//            $position = $position + 1;
//            $indexauthor = new MathIndex($this->xmlpath);
//            $indexauthor->loadFromXml($indexauthors->item(0), $position);
//            $this->indexauthors[] = $indexauthor;
//
//            $indexauthors->item(0)->parentNode->removeChild($indexauthors->item(0));
//        }
//
//        $indexglossarys = $DomElement->getElementsByTagName('index.glossary');
//        $iglength = $indexglossarys->length;
//        for ($i = 0; $i < $iglength; $i++)
//        {
//            $position = $position + 1;
//            $indexglossary = new MathIndex($this->xmlpath);
//            $indexglossary->loadFromXml($indexglossarys->item(0), $position);
//            $this->indexglossarys[] = $indexglossary;
//
//            $indexglossarys->item(0)->parentNode->removeChild($indexglossarys->item(0));
//        }
//       
//        $indexsymbols = $DomElement->getElementsByTagName('index.symbol');
//        $islength = $indexsymbols->length;
//        for ($i = 0; $i < $islength; $i++)
//        {
//            $position = $position + 1;
//            $indexsymbol = new MathIndex($this->xmlpath);
//            $indexsymbol->loadFromXml($indexsymbols->item(0), $position);
//            $this->indexsymbols[] = $indexsymbol;
//
//            $indexsymbols->item(0)->parentNode->removeChild($indexsymbols->item(0));
//        }
//
//        $element = $doc->importNode($DomElement, true);
//        $this->content[] = $doc->saveXML($element);
//        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data = new stdClass();
        $data->string_id = $this->string_id;
        $data->para_align = $this->align;
        $data->caption = $this->caption;
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
