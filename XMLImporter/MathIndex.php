<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */

/**
 * Description of Index
 *
 * @author User
 */
class MathIndex extends Element
{

    public $position;
    public $term;
    public $symbol;
    public $symbol_type;
    public $sortstring;

    function __construct($xmlpath = '')
    {
        parent::__construct($xmlpath);
        $this->glossarytable = 'msm_index_glossary';
        $this->symboltable = 'msm_index_symbol';
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position 
     */
    public function loadFromXml($DomElement, $position = '')
    {
        $this->position = $position;

        $nameofElement = $DomElement->tagName;

        $this->infos = array();
        $this->names = array();

        switch ($nameofElement)
        {
            case('index.symbol'):
                $this->symbol = $this->getContent($DomElement->getElementsByTagName('symbol')->item(0));
                $this->symbol_type = $DomElement->getElementsByTagName('symbol')->item(0)->getAttribute('type');
                $this->sortstring = $this->getDomAttribute($DomElement->getElementsByTagName('sortstring'));

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;

            case('index.glossary'):
                $terms = $DomElement->getElementsByTagName('term');
                $string = '';

                foreach ($terms as $t)
                {
                    $term = $this->getContent($t);
                    $string = $string . $term . '/';
                    $this->term = $string;
                }

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;

            case('index.author'):

                $position = $position + 1;
                $name = new Person($this->xmlpath);
                $name->loadFromXml($DomElement, $position);
                $this->names[] = $name;

                $infos = $DomElement->getElementsByTagName('info');

                foreach ($infos as $i)
                {
                    $position = $position + 1;
                    $info = new MathInfo($this->xmlpath);
                    $info->loadFromXml($i, $position);
                    $this->infos[] = $info;
                }

                break;
        }
    }

    /**
     *
     * @global moodle_database $DB
     * @param int $position 
     */
    function saveIntoDb($position, $parentid = '', $siblingid = '')
    {
        global $DB;
        $data = new stdClass();
        $sibling_id = $siblingid;

        if (!empty($this->symbol))
        {
            $data->symbol_type = $this->symbol_type;
            $data->sortstring = $this->sortstring;
            $data->symbol = trim($this->symbol);

            $numOfRecords = $DB->count_records('msm_index_symbol');
            if ($numOfRecords > 0)
            {
                // need the limit to be $numOfRecords+1 to process the last record
                for ($i = 1; $i < $numOfRecords + 1; $i++)
                {
                    $string = $DB->get_field('msm_index_symbol', 'symbol', array('id' => $i));

                    if (trim($string) == trim($this->symbol))
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
                $this->id = $DB->insert_record($this->symboltable, $data);
                $this->compid = $this->insertToCompositor($this->id, $this->symboltable, $parentid, $sibling_id);
                $sibling_id = $this->compid;
            }
            else
            {
                $this->compid = $this->insertToCompositor($recordID, $this->symboltable, $parentid, $sibling_id);
                $sibling_id = $this->compid;
            }
        }

        if (!empty($this->names))
        {
            foreach ($this->names as $key => $name)
            {
                $firststring = $name->name["first"];
                $laststring = $name->name["last"];
                $middlestring = $name->name["middle"];

                $numOfRecords = $DB->count_records('msm_person');
                if ($numOfRecords > 0)
                {
                    // need the limit to be $numOfRecords+1 to process the last record
                    for ($i = 1; $i < $numOfRecords + 1; $i++)
                    {
                        $first = $DB->get_field('msm_person', 'firstname', array('id' => $i));
                        $last = $DB->get_field('msm_person', 'lastname', array('id' => $i));
                        $middle = $DB->get_field('msm_person', 'middlename', array('id' => $i));

                        if ((!empty($first)) && ($first == $firststring))
                        {
                            if ((!empty($last)) && ($last == $laststring))
                            {
                                if ((!empty($middle)) && ($middle == $middlestring))
                                {
                                    $recordID = $i;
                                    break;
                                }
                                else if ((empty($middle)) && (empty($middlestring))) //record exists where first and last name match but middle is empty
                                {
                                    $recordID = $i;
                                    break;
                                }
                                else
                                {
                                    $recordID = false;
                                }
                            }
                            else //first name matched but not lastname
                            {
                                $recordID = false;
                            }
                        }
                        else // first name did not match
                        {
                            $recordID = false;
                        }
                    }
                }
                else // the number of record in the table is less or equal to zero
                {
                    $recordID = false;
                }

                if (empty($recordID))
                {
                    $name->saveIntoDb($name->position, 'index');
                    $name->compid = $this->insertToCompositor($name->id, $name->tablename, $parentid, $siblingid);
                    $sibling_id = $name->compid;
                    $this->compid = $name->compid;
                }
                else
                {
                    $name->compid = $this->insertToCompositor($recordID, $name->tablename, $parentid, $siblingid);
                    $sibling_id = $name->compid;
                    $this->compid = $name->compid;
                }
            }
        }

        if (!empty($this->term))
        {
            $data->term = trim($this->term);

            // cannot compare the this->term and term field of a record due to XML nature of the two content
            $numOfRecords = $DB->count_records('msm_index_glossary');
            if ($numOfRecords > 0)
            {
                // need the limit to be $numOfRecords+1 to process the last record
                for ($i = 1; $i < $numOfRecords + 1; $i++)
                {
                    $string = $DB->get_field('msm_index_glossary', 'term', array('id' => $i));

                    if (trim($string) == trim($this->term))
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
                $this->id = $DB->insert_record($this->glossarytable, $data);
                $this->compid = $this->insertToCompositor($this->id, $this->glossarytable, $parentid, $siblingid);
                $sibling_id = $this->compid;
            }
            else
            {
                $this->compid = $this->insertToCompositor($recordID, $this->glossarytable, $parentid, $siblingid);
                $sibling_id = $this->compid;
            }
        }

        foreach ($this->infos as $info)
        {
            $numOfRecords = $DB->count_records('msm_info');
            if ($numOfRecords > 0)
            {
                // need the limit to be $numOfRecords+1 to process the last record
                for ($i = 1; $i < $numOfRecords + 1; $i++)
                {
                    $string = $DB->get_field('msm_info', 'info_content', array('id' => $i));

                    if (trim($string) == trim($info->content))
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
                $info->saveIntoDb($info->position, $this->compid);
            }
            else
            {
                $info->compid = $this->insertToCompositor($recordID, $info->tablename, $parentid, $sibling_id);
                $sibling_id = $info->compid;
            }
        }
    }

    /**
     * 
     * @global moodle_database $DB
     * @return \MathIndex
     */
//    function loadGlossaryFromDb()
//    {
//        global $DB;
//
//        // grabs all the records in index_glossary in alphabetical order
//        $sql = 'SELECT * FROM mdl_msm_index_glossary ORDER BY term ASC';
//
//        $allGlossaryRecords = $DB->get_records_sql($sql);
////
////        print_object($allGlossaryRecords);
////        die;
//
//        return $this;
//    }

    function loadSymbolFromDb($id, $compid)
    {
        global $DB;

        $symbolRecord = $DB->get_record($this->symboltable, array('id' => $id));

        $this->id = $symbolRecord->id;
        $this->symbol = $symbolRecord->symbol;
        $this->symbol_type = $symbolRecord->symbol_type;
        $this->sortstring = $symbolRecord->sortstring;

        $this->infos = array();
        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;

            if ($childTablename == 'msm_info')
            {
                $info = new MathInfo();
                $info->loadFromDb($child->unit_id, $child->id);
                $this->infos[] = $info;
            }
        }

        $this->parents = array();
        $currentRecord = $DB->get_record('msm_compositor', array('id' => $compid));
        $parentElements = $DB->get_records('msm_compositor', array('id' => $currentRecord->parent_id));

        foreach ($parentElements as $parent)
        {
            $parentTablename = $DB->get_record('msm_table_collection', array('id' => $parent->table_id))->tablename;

            if ($parentTablename == 'msm_def')
            {
                $def = new Definition();
                $def->loadFromDb($parent->unit_id, $parent->id);
                $this->parents[] = $def;
            }
            else if ($parentTablename == 'msm_theorem')
            {
                $theorem = new Theorem();
                $theorem->loadFromDb($parent->unit_id, $parent->id);
                $this->parents[] = $theorem;
            }
            else
            {
                $parentUnitElement = $DB->get_records('msm_compositor', array('id' => $parent->parent_id));
                $unitTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_unit'))->id;
                $theoremTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;
                $statementTheoremTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_statement_theorem'))->id;

                foreach ($parentUnitElement as $parentUnit)
                {
                    if ($parentUnit->table_id == $unitTable)
                    {
                        $unit = new Unit();
                        $unit->loadFromDb($parentUnit->unit_id, $parentUnit->id);
                        $this->parents[] = $unit;
                    }
                    else if ($parentUnit->table_id == $theoremTable)
                    {
                        $theorem = new Theorem();
                        $theorem->loadFromDb($parentUnit->unit_id, $parentUnit->id);
                        $this->parents[] = $theorem;
                    }
                    else if ($parentUnit->table_id == $statementTheoremTable)
                    {
                        $TheoremElement = $DB->get_record('msm_compositor', array('id' => $parentUnit->parent_id));

                        $theorem = new Theorem();
                        $theorem->loadFromDb($TheoremElement->unit_id, $TheoremElement->id);
                        $this->parents[] = $theorem;
                    }
                }
            }
        }

        return $this;
    }

    function displayGlossary()
    {
        global $CFG;

        $content = '';


        return $content;
    }

    function displaySymbol()
    {
        global $CFG;

        $content = '';

        if (sizeof($this->infos) != 0)
        {
            $content .= "<li><span>" . $this->symbol . "     </span><a id='symbolinfo-" . $this->infos[0]->compid . "' style='cursor:pointer;' onmouseover='infoopen(" . $this->infos[0]->compid . ")'><img src='$CFG->wwwroot/mod/msm/pix/info.png'/></a></li>";

            $content .= $this->infos[0]->displayhtml();
        }
        else
        {
            $content .= "<li><span>" . $this->symbol . "</span></li>";
        }

        $content .= "<div class='symbolrefcontent' id='symbolrefcontent-" . $this->infos[0]->compid . "' style='display:none;'>";
        foreach ($this->parents as $parent)
        {
            $content .= $parent->displayhtml();
        }
        $content .= "</div>";

        return $content;
    }

}

?>
