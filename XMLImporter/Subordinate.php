<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Subordinate
 *
 * @author User
 */
class Subordinate extends Element
{

    public $position;
    public $hot;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_subordinate';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $this->infos = array();
        $this->companion = array();
        $this->externalref = array();
        $this->crossref = array();

        $this->cites = array();
        $this->external_links = array();
        $this->subordinate_refs = array();

        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == 'hot')
                {
                    $this->hot = $this->getContent($child);
                }
                if ($child->tagName == 'info')
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($child, $position);
                    $this->infos[] = $info;
                }
                if ($child->tagName == 'companion')
                {
                    foreach ($child->childNodes as $grandchild)
                    {
                        if ($grandchild->nodeType == XML_ELEMENT_NODE)
                        {
                            $name = $grandchild->tagName;
                            $parser = new DOMDocument();

                            switch ($name)
                            {
                                case('comment.ref'):
                                    $commentrefID = $grandchild->getAttribute('commentID');

                                    if (!empty($commentrefID))
                                    {
                                        //when db is set up, add code to check the db records first
                                        // then if there are no records with specified ID, then..
                                        // find the file with comment with specified ID
                                        $filepath = $this->findFile($commentrefID);

                                        $parser->load($filepath);

                                        // may need to change this code to load the entire file
                                        // containing the specified comment
                                        $comments = $parser->getElementsByTagName('comment')->item(0);
                                        foreach ($comments as $c)
                                        {
                                            $id = $c->getAttribute('id');

                                            if ($id == $commentrefID)
                                            {
                                                $position = $position + 1;
                                                $comment = new Comment($this->xmlpath);
                                                $comment->loadFromXml($c, $position);
                                                $this->companion[] = $comment;
                                            }
                                        }
                                    }
                            }
//               
                        }
                    }
                }
                if ($child->tagName == 'external.link')
                {
                    $position = $position + 1;
                    $link = new ExternalLink($this->xmlpath);
                    $link->loadFromXml($child, $position);
                    $this->external_links[] = $link;
                }
                if ($child->tagName == 'cite')
                {
                    $position = $position + 1;
                    $cite = new Cite($this->xmlpath);
                    $cite->loadFromXml($child, $position);
                    $this->cites[] = $cite;
                }
            }
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position)
    {
        echo "subordinate save start";
        $time = time();
        print_object($time);
        
        global $DB;

        $data = new stdClass();
        $data->hot = $this->hot;

        $numOfRecords = $DB->count_records($this->tablename);
        if ($numOfRecords > 0)
        {
            // need the limit to be $numOfRecords+1 to process the last record
            for ($i = 1; $i < $numOfRecords + 1; $i++)
            {
                $string = $DB->get_field($this->tablename, 'hot', array('id' => $i));

                if ($string == $this->hot)
                {
                    $recordID = $i;
                    break;
                }
                else
                {
                    $recordID = false;
                }
            }
        }
        else
        {
            $recordID = false;
        }

        if (empty($recordID))
        {
            $this->id = $DB->insert_record($this->tablename, $data);
        }

        foreach ($this->infos as $key => $info)
        {
            $info->saveIntoDb($info->position);
        }

        foreach ($this->external_links as $external_link)
        {
            $external_link->saveIntoDb($external_link->position);
        }

        foreach ($this->cites as $cite)
        {
            $cite->saveIntoDb($cite->position);
        }
    }

    function findFile($elementID)
    {
        $path = $this->xmlpath;

        $dirOrFiles = scandir($path);

        foreach ($dirOrFiles as $key => $file)
        {
            // first two items in the array $dirOrFiles refers to the current and parent directories
            // which is not useful in this case
            if ($key > 1)
            {
                $ext = explode('.', $file);

                if ((sizeof($ext) > 1) && ($ext[1] == 'xml'))
                {
                    $DomParser = new DOMDocument();
                    @$DomParser->load($this->xmlpath . '/' . $file);

                    $comment = $Domparser->getElementsByTagName('comment')->item(0);
                    if (!empty($comment))
                    {
                        $commentID = $comment->getAttribute('id');

                        if ($commentID == $elementID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }

                    $definition = $Domparser->getElementsByTagName('definition')->item(0);
                    if (!empty($definition))
                    {
                        $defID = $def->getAttribute('id');

                        if ($defID == $elementID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }

                    $theorem = $Domparser->getElementsByTagName('theorem')->item(0);
                    if (!empty($theorem))
                    {
                        $theoremID = $theorem->getAttribute('id');

                        if ($theoremID == $elementID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }

                    $showmepack = $Domparser->getElementsByTagName('showme.pack')->item(0);
                    if (!empty($showmepack))
                    {
                        $showmepackID = $showmepack->getAttribute('id');

                        if ($showmepackID == $elementID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }

                    $quizpack = $Domparser->getElementsByTagName('quiz.pack')->item(0);
                    if (!empty($quizpack))
                    {
                        $quizpackID = $quizpacks->getAttribute('id');

                        if ($quizpackID == $elementID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }

                    // need to add code for scientist.ref...??

                    $unit = $Domparser->getElementsByTagName('unit')->item(0);
                    if (!empty($unit))
                    {
                        $unitID = $unit->getAttribute('unitId');

                        if ($unitID == $elementID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                }
            }
        }
    }

}

?>
