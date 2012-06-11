<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Table
 *
 * @author User
 */
class Table extends Element
{

    public $position;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_table';
    }

    //put your code here
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->string_id = $DomElement->getAttribute('id');
        $this->table_class = $DomElement->getAttribute('class');
        $this->table_summary = $DomElement->getAttribute('summary');
        $this->table_title = $DomElement->getAttribute('title');

        $position = $position + 1;
        $content = new stdClass();
        $content = $this->getContent($DomElement, $position, $this->xmlpath);
        $this->content = $content->content;
    }

}

?>
