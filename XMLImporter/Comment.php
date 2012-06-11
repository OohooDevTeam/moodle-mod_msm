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
            foreach ($this->processSubordinate($c, $position)->subordinates as $subordinate)
            {
                $this->subordinates[] = $subordinate;
            }

            foreach ($this->processSubordinate($c, $position)->indexauthors as $indexauthor)
            {
                $this->indexauthors[] = $indexauthor;
            }

            foreach ($this->processSubordinate($c, $position)->indexglossarys as $indexglossary)
            {
                $this->indexglossarys[] = $subordinate;
            }

            foreach ($this->processSubordinate($c, $position)->indexsymbols as $indexsymbol)
            {
                $this->indexsymbols[] = $subordinate;
            }

            foreach ($this->processSubordinate($c, $position)->content as $content)
            {
                $this->content[] = $content;
            }
        }
    }

    function saveIntoDb($position)
    {
        global $DB;

        $data = new stdClass();
        $data->comment_type = $this->comment_type;
        $data->string_id = $this->string_id;
        if (!empty($this->caption))
        {
            $data->caption = $this->caption;
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
