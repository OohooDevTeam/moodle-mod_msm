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
        
        foreach($this->processIndexAuthor($DomElement, $position) as $indexauthor)
        {
            $this->indexauthors[] = $indexauthor;
        }
        
        foreach($this->processIndexGlossary($DomElement, $position) as $indexglossary)
        {
            $this->indexglossarys[] = $indexglossary;
        }
        
        foreach($this->processIndexSymbols($DomElement, $position) as $indexsymbol)
        {
            $this->indexsymbols[] = $indexsymbol;
        }
        foreach($this->processSubordinate($DomElement, $position) as $subordinate)
        {
            $this->subordinates[] = $subordinate;
        }
        
        foreach($this->processContent($DomElement, $position) as $content)
        {
            $this->content[] = $content;
        }
        
//       foreach($this->processSubordinate($DomElement, $position)->subordinates as $subordinate)
//       {
//           $this->subordinates[] = $subordinate;
//       }
//       
//       foreach($this->processSubordinate($DomElement, $position)->indexauthors as $indexauthor)
//       {
//           echo "index author";
//           print_object($indexauthor);
//           $this->indexauthors[] = $indexauthor;
//       }
//       
//       foreach($this->processSubordinate($DomElement, $position)->indexglossarys as $indexglossary)
//       {
//           echo "index glossary";
//           print_object($indexglossary);
//           $this->indexglossarys[] = $indexglossary;
//       }
//       
//       foreach($this->processSubordinate($DomElement, $position)->indexsymbols as $indexsymbol)
//       {
//           echo "index symbol";
//           print_object($indexsymbol);
//           $this->indexsymbols[] = $indexsymbol;
//       }
//       
//       foreach($this->processSubordinate($DomElement, $position)->content as $content)
//       {
//           $this->content[] = $content;
//       }
       
//       echo "para content";
//       print_object($this);
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

        foreach ($this->indexglossarys as $key => $indexglossary)
        {
            $indexglossary->saveIntoDb($indexglossary->position);
        }

        foreach ($this->indexsymbols as $key => $indexsymbol)
        {
            $indexsymbol->saveIntoDb($indexsymbol->position);
        }

        foreach ($this->indexauthors as $key => $indexauthor)
        {
            $indexauthor->saveIntoDb($indexauthor->position);
        }
    }

}

?>
