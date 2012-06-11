<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Comment
 *
 * @author User
 */
class Comment extends Element
{

    public $position;

    //public $content;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_comment';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->comment_type = $DomElement->getAttribute('type');
        $this->string_id = $DomElement->getAttribute('id');

        $this->caption = $this->getContent($DomElement->getElementsByTagName('caption')->item(0));

        $this->associates = array();
        $this->content = array();
        $this->subordinates = array();
        $this->indexauthors = array();


        $associates = $DomElement->getElementsByTagName('associate');

        foreach ($associates as $a)
        {
            $position = $position + 1;
            $associate = new Associate($this->xmlpath);
            $associate->loadFromXml($a, $position);
            $this->associates[] = $associate;
        }


        $commentbodys = $DomElement->getElementsByTagName('comment.body');
        $doc = new DOMDocument();

        foreach ($commentbodys as $c)
        {
            $position = $position + 1;


            $subordinates = $c->getElementsByTagName('subordinate');

            foreach ($subordinates as $s)
            {
                $hot = $s->getElementsByTagName('hot')->item(0);

                $position = $position + 1;
                $subordinate = new Subordinate($this->xmlpath);
                $subordinate->loadFromXml($s, $position);
                $this->subordinates[] = $subordinate;

                $s->parentNode->replaceChild($hot, $s);
            }

            $indexauthors = $c->getElementsByTagName('index.author');
            foreach ($indexauthors as $ia)
            {
                $position = $position + 1;
                $indexauthor = new MathIndex($this->xmlpath);
                $indexauthor->loadFromXml($ia, $position);
                $this->indexauthors[] = $indexauthor;

                $ia->parentNode->removeChild($ia);
            }

            $indexglossarys = $c->getElementsByTagName('index.glossary');
            foreach ($indexglossarys as $ig)
            {
                $position = $position + 1;
                $indexglossary = new MathIndex($this->xmlpath);
                $indexglossary->loadFromXml($ig, $position);
                $this->indexglossarys[] = $indexglossary;

                $ig->parentNode->removeChild($ig);
            }

            $indexsymbols = $c->getElementsByTagName('index.symbol');
            foreach ($indexsymbols as $is)
            {
                $position = $position + 1;
                $indexsymbol = new MathIndex($this->xmlpath);
                $indexsymbol->loadFromXml($is, $position);
                $this->indexsymbols[] = $indexsymbol;

                $is->parentNode->removeChild($is);
            }
            
            $element = $doc->importNode($c, true);
            $this->content[] = $doc->saveXML($element);
        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data = new stdClass();
        $data->comment_type = $this->comment_type;
        $data->string_id = $this->string_id;
        if (!empty($this->caption->content))
        {
            $data->caption = $this->caption->content;
        }

        if (!empty($this->content))
        {
            foreach ($this->content as $key => $content)
            {
                $data->comment_content = $content;

                $this->id = $DB->insert_record($this->tablename, $data);
            }
        }
        else
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }

//        foreach($this->associates as $key=>$associate)
//        {
//            $associate->saveIntoDb($associate->position);
//        }
//           foreach($this->subordinates as $key=>$subordinates)
//           {
//               foreach($subordinates as $subkey=>$subordinate)
//               {
//                   $subordinate->saveIntoDb($subordinate->position);
//               }
//           }
//        foreach($this->indexs as $key=>$index)
//        {
//            $index->saveIntoDb($index->position);
//        }
    }

}

?>
