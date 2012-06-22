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
        global $DB;

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
                                        $IDinDB = $DB->get_record('msm_comment', array('string_id' => $commentrefID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($commentrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
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
                                        else
                                        {
                                            $this->companion[] = $commentrefID;
                                        }
                                        //when db is set up, add code to check the db records first
                                        // then if there are no records with specified ID, then..
                                        // find the file with comment with specified ID
                                    }
                                    break;

                                case('definition.ref'):
                                    $definitionrefID = $grandchild->getAttribute('definitionID');

                                    if (!empty($definitionrefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_def', array('string_id' => $definitionrefID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($definitionrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
                                                $parser->load($filepath);

                                                // may need to change this code to load the entire file
                                                // containing the specified comment
                                                $defs = $parser->getElementsByTagName('def')->item(0);
                                                foreach ($defs as $d)
                                                {
                                                    $id = $d->getAttribute('id');

                                                    if ($id == $definitionrefID)
                                                    {
                                                        $position = $position + 1;
                                                        $def = new Definition($this->xmlpath);
                                                        $def->loadFromXml($d, $position);
                                                        $this->companion[] = $def;
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $definitionrefID;
                                        }
                                    }
                                    break;
//                                    
                                case('theorem.ref'):
                                    $theoremrefID = $grandchild->getAttribute('theoremID');

                                    if (!empty($theormerefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_theorem', array('string_id' => $theormerefID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($theoremrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
                                                $parser->load($filepath);

                                                // may need to change this code to load the entire file
                                                // containing the specified comment
                                                $theorems = $parser->getElementsByTagName('theorem')->item(0);
                                                foreach ($theorems as $t)
                                                {
                                                    $id = $t->getAttribute('id');

                                                    if ($id == $theormerefID)
                                                    {
                                                        $position = $position + 1;
                                                        $theorem = new Theorem($this->xmlpath);
                                                        $theorem->loadFromXml($t, $position);
                                                        $this->companion[] = $theorem;
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $theormerefID;
                                        }
                                    }
                                    break;
//                                    
                                case('showme.pack.ref'):
                                    $showmepackrefID = $grandchild->getAttribute('showmePackID');

                                    if (!empty($showmepackrefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_packs', array('string_id' => $showmepackrefID));

                                        if (!empty($IDinDB))
                                        {
                                            echo "showmeID";
                                            print_object($showmepackrefID);

                                            $filepath = $this->findFile($showmepackrefID, dirname($this->xmlpath));


                                            if (!empty($filepath))
                                            {
                                                echo "filepath";
                                                print_object($filepath);

                                                $parser->load($filepath);

                                                // may need to change this code to load the entire file
                                                // containing the specified comment
                                                $showmepacks = $parser->getElementsByTagName('showme.pack')->item(0);
                                                foreach ($showmepacks as $s)
                                                {
                                                    $id = $s->getAttribute('id');

                                                    if ($id == $showmepackrefID)
                                                    {
                                                        $position = $position + 1;
                                                        $showmepack = new Pack($this->xmlpath);
                                                        $showmepack->loadFromXml($s, $position);
                                                        $this->companion[] = $showmepack;
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $showmepackrefID;
                                        }
                                    }
                                    break;
//
//                                case('quiz.pack.ref'):
//                                    $quizpackID = $grandchild->getAttribute('quizPackID');
//
//                                    if (!empty($quizpackID))
//                                    {
//                                        $IDinDB = $DB->get_record('msm_packs', array('string_id' => $quizpackID));
//
//                                        if (!empty($IDinDB))
//                                        {
//                                            $filepath = $this->findFile($quizpackID);
//                                            $parser->load($filepath);
//
//                                            // may need to change this code to load the entire file
//                                            // containing the specified comment
//                                            $quizpacks = $parser->getElementsByTagName('quiz.pack')->item(0);
//                                            foreach ($quizpacks as $q)
//                                            {
//                                                $id = $q->getAttribute('id');
//
//                                                if ($id == $quizpackID)
//                                                {
//                                                    $position = $position + 1;
//                                                    $quizpack = new Pack($this->xmlpath);
//                                                    $quizpack->loadFromXml($q, $position);
//                                                    $this->companion[] = $quizpack;
//                                                }
//                                            }
//                                        }
//                                        else
//                                        {
//                                            $this->companion[] = $quizpackID;
//                                        }
//                                    }
//                                    break;
//
                                case('unit.ref'):
                                    $untiID = $grandchild->getAttribute('unitId');

                                    if (!empty($untiID))
                                    {
                                        $IDinDB = $DB->get_record('msm_unit', array('string_id' => $untiID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($untiID);
                                            if (!empty($filepath))
                                            {
                                                $parser->load($filepath);

                                                // may need to change this code to load the entire file
                                                // containing the specified comment
                                                $units = $parser->getElementsByTagName('unit')->item(0);
                                                foreach ($units as $u)
                                                {
                                                    $id = $u->getAttribute('id');

                                                    if ($id == $untiID)
                                                    {
                                                        $position = $position + 1;
                                                        $unit = new Unit($this->xmlpath);
                                                        $unit->loadFromXml($u, $position);
                                                        $this->companion[] = $unit;
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $untiID;
                                        }
                                    }
                                    break;
                            }
//               
                        }
                    }
                }
                if ($child->tagName == 'crossref')
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
                                        $IDinDB = $DB->get_record('msm_comment', array('string_id' => $commentrefID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($commentrefID, dirname($this->xmlpath));
                                            if (!empty($filepath))
                                            {
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
                                        else
                                        {
                                            $this->companion[] = $commentrefID;
                                        }
                                        //when db is set up, add code to check the db records first
                                        // then if there are no records with specified ID, then..
                                        // find the file with comment with specified ID
                                    }
                                    break;

                                case('definition.ref'):
                                    $definitionrefID = $grandchild->getAttribute('definitionID');

                                    if (!empty($definitionrefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_def', array('string_id' => $definitionrefID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($definitionrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
                                                $parser->load($filepath);

                                                // may need to change this code to load the entire file
                                                // containing the specified comment
                                                $defs = $parser->getElementsByTagName('def')->item(0);
                                                foreach ($defs as $d)
                                                {
                                                    $id = $d->getAttribute('id');

                                                    if ($id == $definitionrefID)
                                                    {
                                                        $position = $position + 1;
                                                        $def = new Definition($this->xmlpath);
                                                        $def->loadFromXml($d, $position);
                                                        $this->companion[] = $def;
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $definitionrefID;
                                        }
                                    }
                                    break;
//
                                case('theorem.ref'):
                                    $theoremrefID = $grandchild->getAttribute('theoremID');

                                    if (!empty($theormerefID))
                                    {
                                        $IDinDB = $DB->get_record('msm_theorem', array('string_id' => $theormerefID));

                                        if (!empty($IDinDB))
                                        {
                                            $filepath = $this->findFile($theoremrefID, dirname($this->xmlpath));

                                            if (!empty($filepath))
                                            {
                                                $parser->load($filepath);

                                                // may need to change this code to load the entire file
                                                // containing the specified comment
                                                $theorems = $parser->getElementsByTagName('theorem')->item(0);
                                                foreach ($theorems as $t)
                                                {
                                                    $id = $t->getAttribute('id');

                                                    if ($id == $theormerefID)
                                                    {
                                                        $position = $position + 1;
                                                        $theorem = new Theorem($this->xmlpath);
                                                        $theorem->loadFromXml($t, $position);
                                                        $this->companion[] = $theorem;
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $this->companion[] = $theormerefID;
                                        }
                                    }
                                    break;

//                                case('exercise.pack.ref'):
//                                    $exercisePackID = $grandchild->getAttribute('exercisePackID');
//
//                                    if (!empty($exercisePackID))
//                                    {
//                                        $IDinDB = $DB->get_record('msm_packs', array('string_id' => $exercisePackID));
//
//                                        if (!empty($IDinDB))
//                                        {
//                                            $filepath = $this->findFile($exercisePackID);
//                                            $parser->load($filepath);
//
//                                            // may need to change this code to load the entire file
//                                            // containing the specified comment
//                                            $exercisepacks = $parser->getElementsByTagName('exercise.pack')->item(0);
//                                            foreach ($exercisepacks as $ex)
//                                            {
//                                                $id = $ex->getAttribute('id');
//
//                                                if ($id == $exercisePackID)
//                                                {
//                                                    $position = $position + 1;
//                                                    $exercisepack = new Pack($this->xmlpath);
//                                                    $exercisePackID->loadFromXml($ex, $position);
//                                                    $this->companion[] = $exercisepack;
//                                                }
//                                            }
//                                        }
//                                        else
//                                        {
//                                            $this->companion[] = $exercisePackID;
//                                        }
//                                    }
//                                    break;
//
//                                case('example.pack.ref'):
//                                    $examplepackID = $grandchild->getAttribute('examplePackID');
//
//                                    if (!empty($examplepackID))
//                                    {
//                                        $IDinDB = $DB->get_record('msm_packs', array('string_id' => $examplepackID));
//
//                                        if (!empty($IDinDB))
//                                        {
//                                            $filepath = $this->findFile($examplepackID);
//                                            $parser->load($filepath);
//
//                                            // may need to change this code to load the entire file
//                                            // containing the specified comment
//                                            $examplepacks = $parser->getElementsByTagName('example.pack')->item(0);
//                                            foreach ($examplepacks as $emp)
//                                            {
//                                                $id = $emp->getAttribute('id');
//
//                                                if ($id == $examplepackID)
//                                                {
//                                                    $position = $position + 1;
//                                                    $examplepack = new Pack($this->xmlpath);
//                                                    $examplepack->loadFromXml($emp, $position);
//                                                    $this->companion[] = $examplepack;
//                                                }
//                                            }
//                                        }
//                                        else
//                                        {
//                                            $this->companion[] = $examplepackID;
//                                        }
//                                    }
//                                    break;
//
//                                case('unit.ref'):
//                                    $untiID = $grandchild->getAttribute('unitId');
//
//                                    if (!empty($untiID))
//                                    {
//                                        $IDinDB = $DB->get_record('msm_unit', array('string_id' => $untiID));
//
//                                        if (!empty($IDinDB))
//                                        {
//                                            $filepath = $this->findFile($untiID);
//                                            $parser->load($filepath);
//
//                                            // may need to change this code to load the entire file
//                                            // containing the specified comment
//                                            $units = $parser->getElementsByTagName('unit')->item(0);
//                                            foreach ($units as $u)
//                                            {
//                                                $id = $u->getAttribute('id');
//
//                                                if ($id == $untiID)
//                                                {
//                                                    $position = $position + 1;
//                                                    $unit = new Unit($this->xmlpath);
//                                                    $unit->loadFromXml($u, $position);
//                                                    $this->companion[] = $unit;
//                                                }
//                                            }
//                                        }
//                                        else
//                                        {
//                                            $this->companion[] = $untiID;
//                                        }
//                                    }
//                                    break;
//                                case('composition.ref'):
//                                    $compID = $grandchild->getAttribute('unitId');
//
//                                    if (!empty($compID))
//                                    {
//                                        $IDinDB = $DB->get_record('msm_unit', array('string_id' => $compID));
//
//                                        if (!empty($IDinDB))
//                                        {
//                                            $filepath = $this->findFile($compID);
//                                            $parser->load($filepath);
//
//                                            // may need to change this code to load the entire file
//                                            // containing the specified comment
//                                            $copmositors = $parser->getElementsByTagName('compositor')->item(0);
//                                            foreach ($copmositors as $comp)
//                                            {
//                                                $id = $comp->getAttribute('id');
//
//                                                if ($id == $compID)
//                                                {
//                                                    $position = $position + 1;
//                                                    $compositor = new Compositor($this->xmlpath);
//                                                    $compositor->loadFromXml($comp, $position);
//                                                    $this->companion[] = $compositor;
//                                                }
//                                            }
//                                        }
//                                        else
//                                        {
//                                            $this->companion[] = $compID;
//                                        }
//                                    }
//                                     break;
                            }
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
//        echo "subordinate save start";
//        $time = time();
//        print_object($time);

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

        foreach ($this->companion as $companion)
        {
            print_object($companion);
        }

        foreach ($this->cites as $cite)
        {
            $cite->saveIntoDb($cite->position);
        }
    }

    function findFile($elementID, $filepath)
    {
        echo "path";
        print_object($filepath);

        $dirOrFiles = scandir($filepath);

        echo "before loop";

        foreach ($dirOrFiles as $key => $file)
        {
          
            echo "in loop";
            // first two items in the array $dirOrFiles refers to the current and parent directories
            // which is not useful in this case
            if ($key > 1)
            {
                $ext = explode('.', $file);

                if (sizeof($ext) <= 1) // it's a directory
                {
                    echo "in directory";
                    print_object($filepath . '/' . $ext[0]);

                    $this->findFile($elementID, $filepath . '/' . $ext[0]);
                }
                else if ((sizeof($ext) > 1) && ($ext[1] == 'xml'))
                {
                    $Domparser = new DOMDocument();
                    @$Domparser->load($filepath . '/' . $file);

                    $comment = $Domparser->getElementsByTagName('comment')->item(0);
                    if (!empty($comment))
                    {
                        $commentID = $comment->getAttribute('id');

                        if ($commentID == $elementID)
                        {
                            $path = $filepath . '/' . $file;
                            return $path;
                        }
                    }

                    $definition = $Domparser->getElementsByTagName('definition')->item(0);
                    if (!empty($definition))
                    {
                        $defID = $def->getAttribute('id');

                        if ($defID == $elementID)
                        {
                            $path = $filepath . '/' . $file;
                            return $path;
                        }
                    }

                    $theorem = $Domparser->getElementsByTagName('theorem')->item(0);
                    if (!empty($theorem))
                    {
                        $theoremID = $theorem->getAttribute('id');

                        if ($theoremID == $elementID)
                        {
                            $path = $filepath . '/' . $file;
                            return $path;
                        }
                    }

                    $showmepack = $Domparser->getElementsByTagName('showme.pack')->item(0);

                    if (!empty($showmepack))
                    {
                        $showmepackID = $showmepack->getAttribute('id');

//                        echo "ID being compared";
//                        print_object($showmepackID);

                        if ($showmepackID == $elementID)
                        {
                            $path = $filepath . '/' . $file;

                            echo "finalpath";
                            print_object($path);
                            return $path;
                        }
                    }

                    $quizpack = $Domparser->getElementsByTagName('quiz.pack')->item(0);
                    if (!empty($quizpack))
                    {
                        $quizpackID = $quizpack->getAttribute('id');

                        if ($quizpackID == $elementID)
                        {
                            $path = $filepath . '/' . $file;
                            return $path;
                        }
                    }

                    $exercisepack = $Domparser->getElementsByTagName('exercise.pack')->item(0);
                    if (!empty($exercisepack))
                    {
                        $exercisepackID = $exercisepack->getAttribute('id');

                        if ($exercisepackID == $elementID)
                        {
                            $path = $filepath . '/' . $file;
                            return $path;
                        }
                    }

                    $examplepack = $Domparser->getElementsByTagName('example.pack')->item(0);
                    if (!empty($examplepack))
                    {
                        $examplepackID = $examplepack->getAttribute('id');

                        if ($examplepackID == $elementID)
                        {
                            $path = $filepath . '/' . $file;
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
                            $path = $filepath . '/' . $file;
                            return $path;
                        }
                    }
                }
            }
        }
    }

}

?>
