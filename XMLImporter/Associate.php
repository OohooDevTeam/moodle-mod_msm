<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Associate
 *
 * @author User
 */
class Associate extends Element
{

    public $position;
    public $subunit;
    public $ref;
    public $def;
    public $theorem;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->tablename = 'msm_associate';
    }

    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;
        $this->description = $DomElement->getAttribute('type');

        $this->infos = array();
//        $this->subunits = array();
//        $this->refs = array();
//        $this->defs = array();
//        $this->theorems = array();

        foreach ($DomElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                $name = $child->tagName;

                switch ($name)
                {
                    case('comment.ref'):
                        $position = $position + 1;
                        $commentID = $child->getAttribute('commentID');
                        $recID = $this->findRef($commentID, 'msm_comment', $position);
                        break;

                    case('showme.pack.ref'):
                        $position = $position + 1;
                        $showmepackID = $child->getAttribute('showmePackID');
                        $recID = $this->findRef($showmepackID, 'msm_packs', $position);
                        break;

                    case('quiz.pack.ref'):
                        $position = $position + 1;
                        $quizpackID = $child->getAttribute('quizPackID');
                        $recID = $this->findRef($quizpackID, 'msm_packs', $position);
                        break;

                    case('definition.ref'):
                        $position = $position + 1;
                        $definitionID = $child->getAttribute('definitionID');
                        $recID = $this->findRef($definitionID, 'msm_def', $position);
                        break;

                    case('theorem.ref'):
                        $position = $position + 1;
                        $theoremID = $child->getAttribute('theoremID');
                        $theorempartID = $child->getAttibute('theorempartID');
                        $recID = $this->findRef($theoremID, 'msm_theorem', $position);
                        break;

                    case('unit.ref'):
                        $position = $position + 1;
                        $unitID = $child->getAttribute('unitId');
                        $recID = $this->findRef($unitID, 'msm_unit', $position);
                        break;

                    case('info'):
                        $position = $position + 1;
                        $info = new MathInfo($this->xmlpath);
                        $info->loadFromXml($child, $position);
                        $this->infos[] = $info;
                        break;
                }
            }
        }
    }
    
    function saveIntoDb($position)
    {
        global $DB;
        
        $data = new stdClass();
        $data->description = $this->description;
        
        $this->id = $DB->insert_record($this->tablename, $data);
       
        foreach($this->infos as $key=>$info)
        {
            $info->saveIntoDb($info->position);
        }
        
//        foreach($this->subunits as $key=>$unit)
//        {
//            $unit->saveIntoDb($unit->position);
//        }
//        
//        foreach($this->theorems as $key=>$theorem)
//        {
//            $theorem->saveIntoDb($theorem->position);
//        }
//        
//        foreach($this->defs as $key=>$def)
//        {
//            $def->saveIntoDb($def->position);
//        }
        
//        foreach($this->refs as $key=>$ref)
//        {
//            $ref->saveIntoDb($ref->position);
//        }
    }

    /**
     * The findRef function is called from loadFromXml method to check if there is a record already in the specified database table
     * with the specified id that is referenced in the XML file.  If the record exists, then return the record ID, which would be inserted to 
     * the compositor table.  If the record does not exist, then it needs to call another method called findRefFile to find the 
     * XML file that contains the referenced ID.  Then insert this record to get the record ID to be inserted to the compositor table.
     * 
     * @global moodle database $DB
     * @param int $id
     * @param String $tablename
     * @param int $position 
     */
    function findRef($refid, $tablename, $position)
    {
        global $DB;

        $recordID = $DB->get_records($tablename, array('string_id' => $refid));

        if (!empty($recordID))
        {
            $id = $recordID;
        }
        else
        {
            $path = $this->findRefFile($refid);
            
            $refDoc = new DOMDocument();
            @$refDoc->load($path);

            $element = $refDoc->documentElement;

            if ($element->nodeType == XML_ELEMENT_NODE)
            {

                $tagname = $element->tagName;

                switch ($tagname)
                {
                    case('unit'):
                        $unit = new Unit(dirname($path));
                        $unit->loadFromXml($element, $position);
//                        $this->subunits[] = $unit;
                        $this->subunit = $unit;
                        break;

                    case('theorem'):
                        $theorem = new Theorem(dirname($path));
                        $theorem->loadFromXml($element, $position);
//                        $this->theorems[] = $theorem;
                        $this->theorem = $theorem;
                        break;

                    case('def'):
                        $def = new Definition(dirname($path));
                        $def->loadFromXml($element, $position);
//                        $this->defs[] = $def;
                        $this->def = $def;
                        break;

                    case('showme.pack'):
                        $pack = new Pack(dirname($path));
                        $pack->loadFromXml($element, $position);
//                        $this->refs[] = $pack;
                        $this->ref = $pack;
                        break;

                    case('example.pack'):
                        $pack = new Pack(dirname($path));
                        $pack->loadFromXml($element, $position);
//                        $this->refs[] = $pack;
                        $this->ref = $pack;
                        break;

                    case('exercise.pack'):
                        $pack = new Pack(dirname($path));
                        $pack->loadFromXml($element, $position);
//                        $this->refs[] = $pack;
                        $this->ref = $pack;
                        break;

                    case('quiz.pack'):
                        $pack = new Pack(dirname($path));
                        $pack->loadFromXml($element, $position);
//                        $this->refs[] = $pack;
                        $this->ref = $pack;
                        break;
                }
            }

            if(!empty($this->subunit))
            {
                $this->subunit->saveIntoDb($this->subunit->position);
                $id = $this->subunit->id;
            }
            else if(!empty($this->def))
            {
                $this->def->saveIntoDb($this->def->position);
                $id = $this->def->id;
            }            
            else if(!empty($this->theorem))
            {
                $this->theorem->saveIntoDb($this->theorem->position);
                $id = $this->theorem->id;
            }
            else if(!empty($this->ref))
            {
                $this->ref->saveIntoDb($this->ref->position);
                $id = $this->ref->id;
            }
        }

        return $id;
    }

    function findRefFile($refID)
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

                    $unit = $DomParser->getElementsByTagName("unit")->item(0);
                    $def = $DomParser->getElementsByTagName("def")->item(0);
                    $theorem = $DomParser->getElementsByTagName('theorem')->item(0);
                    $showmepack = $DomParser->getElementsByTagName('showme.pack')->item(0);
                    $examplepack = $DomParser->getElementsByTagName('example.pack')->item(0);
                    $exercisepack = $DomParser->getElementsByTagName('exercise.pack')->item(0);
                    $quizpack = $DomParser->getElementsByTagName('quiz.pack')->item(0);

                    if (!empty($unit))
                    {
                        $unitstringID = $unit->getAttribute('unitid');

                        if ($unitstringID == $refID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    else if (!empty($def))
                    {
                        $defID = $def->getAttribute('id');

                        if ($defID == $refID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    else if (!empty($theorem))
                    {
                        $theoremID = $theorem->getAttribute('id');

                        if ($theoremID == $refID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    else if (!empty($showmepack))
                    {
                        $showmepackID = $showmepack->getAttribute('id');

                        if ($showmepackID == $refID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    else if (!empty($examplepack))
                    {
                        $examplepackID = $examplepack->getAttribute('id');

                        if ($examplepackID == $refID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    else if (!empty($exercisepack))
                    {
                        $exercisepackID = $exercisepack->getAttribute('id');

                        if ($exercisepackID == $refID)
                        {
                            $path = $this->xmlpath . '/' . $file;
                            return $path;
                        }
                    }
                    else if (!empty($quizpack))
                    {
                        $quizpackID = $quizpack->getAttribute('id');

                        if ($quizpackID == $refID)
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
