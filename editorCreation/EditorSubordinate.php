<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * EditorSubordinate class inherits from the EditorElement class and it represents the
 * subordinate elements that are shown in the view as a popup jquery dialog boxes upon
 * mouse hover as a trigger.  This class is called by most of the classes with content components
 * associated and calls on EditorInfo class.
 */
class EditorSubordinate extends EditorElement
{

    public $id;                 // database ID associated with subordinate element in msm_subordinate table
    public $compid;             // database ID associated with subordinate element in msm_compositor table
    public $hot;                // anchor element text that triggers display of info element in view
    public $info;               // EditorInfo objects associated with the subordinate element
    public $external_link;      // EditorExternalLink objects associated with the subordinate element
    public $isRef;              // database ID associated with the referenced already-existing subordinate element in msm_compositor table

    // no errorArray necessary b/c null input has been checked already
    // when the subordinate was made

    // constructor for this class
    function __construct()
    {
        $this->tablename = 'msm_subordinate';
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorInfo/EditorExternalLink) to process its
     * children's data.
     * 
     * @param DOMElement $idNumber              anchor element from content
     * @return \EditorSubordinate
     */
    public function getFormData($idNumber)
    {
        $doc = new DOMDocument;

        $hotNode = $doc->importNode($idNumber, true);
        $hotNodeId = $hotNode->getAttribute("id");
        $hotNodeText = $hotNode->textContent;

        $this->hot = $hotNodeId . "||" . $hotNodeText;

        $id = $idNumber->getAttribute("id");

        $idInfo = explode("-", $id);
        $idEnding = '';

        for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
        {
            $idEnding .= $idInfo[$i] . "-";
        }
        $idEnding .= $idInfo[sizeof($idInfo) - 1];

        $allSubordinateValues = $_POST['msm_unit_subordinate_container'];
//
        $tempallSubordinates = explode("//|", $allSubordinateValues);

        // copying the array from string processing above (due to it ending in comma, the last
        // element is empty)
        $allSubordinates = array();

        for ($i = 0; $i < sizeof($tempallSubordinates); $i++)
        {
            $allSubordinates[] = $tempallSubordinates[$i];
        }

        $hasLink = false;
        foreach ($allSubordinates as $index => $subordinate)
        {
            $idValuePair = explode("||", $subordinate);

            if (strpos($idValuePair[0], $idEnding) !== false)
            {
                if (strpos($idValuePair[0], 'url') !== false)
                {
                    if ($idValuePair[0] == 'msm_subordinate_url-' . $idEnding)
                    {
                        $external_link = new EditorExternalLink();
                        $external_link->getFormData($idEnding);
                        $this->external_link = $external_link;
                        $hasLink = true;
                    }
                }
            }
        }

        if (!$hasLink)
        {
            $info = new EditorInfo();
            $info->getFormData($idEnding . "|sub");
            $this->info = $info;
        }

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_subordinate table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorInfo and/or EditorExternalLink classes.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         Database ID from msm_compositor of the parent element
     * @param integer $siblingid        Database ID from msm_compositor of the previous sibling element
     * @param integer $msmid            The instance ID of the MSM module.
     */
    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();

        if (empty($this->isRef))
        {
            $data->hot = $this->hot;

            $this->id = $DB->insert_record($this->tablename, $data);
        }
        else
        {
            $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->isRef));

            foreach ($childRecords as $child)
            {
                $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

                switch ($childTable->tablename)
                {
                    case "msm_external_link":
                        $extlink = new EditorExternalLink();
                        $extlink->id = $child->unit_id;
                        $extlink->isRef = $child->id;
                        $this->external_link = $extlink;
                        break;
                    case "msm_info":
                        $info = new EditorInfo();
                        $info->id = $child->unit_id;
                        $info->isRef = $this->isRef; // info and reference materials both have parent_id of subordinate
                        $this->info = $info;
                        break;
                }
            }
        }


        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->table_id = $DB->get_record("msm_table_collection", array('tablename' => $this->tablename))->id;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        if (!empty($this->external_link))
        {
            $this->external_link->insertData($this->compid, 0, $msmid);
        }

        if (!empty($this->info))
        {
            $this->info->insertData($this->compid, 0, $msmid);
        }
    }

    /**
     * This method has a purpose of displaying the data extracted from DB from loadData
     * method by outputting the HTML code.  This method calls displayData from the EditorInfo
     * and/or EditorExternalLink class.
     *  
     * @global moodle_database $DB
     * @param int $parentId                 database ID of parent element in msm_compositor table
     * @return string
     */
    public function displayData($parentId)
    {
        global $DB;

        $htmlContent = '';

        $hotIdInfo = explode("||", $this->hot);
        $idInfo = explode("-", $hotIdInfo[0]);

        if (sizeof($idInfo) == 2)
        {
            $idEnding = $idInfo[1];
        }
        else if (sizeof($idInfo) > 2)
        {
            $matchString = $idInfo[1];
            for ($i = 2; $i < sizeof($idInfo); $i++)
            {
                $matchString .= "-" . $idInfo[$i];
            }

            // swapping out arbitrary number given to parent element for id with database compositor id        
            $idEnding = preg_replace('/([A-Za-z]*?)\d+((?:-\d+)*)/', "$1", trim($matchString));
            $idEnding .= $parentId;
            $idEnding .= preg_replace('/([A-Za-z]*?)\d+((?:-\d+)*)/', "$2", trim($matchString));
        }
        else
        {
            $idEnding = $idInfo[0];
        }

        $htmlContent .= "<div id='msm_subordinate_result-$idEnding' class='msm_subordinate_results'>";
        $htmlContent .= "<div id='msm_subordinate_select-$idEnding'>";

        $compRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));
        $subChilds = $DB->get_records("msm_compositor", array("parent_id" => $this->compid));

        $selectType = '';
        foreach ($subChilds as $sub)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $sub->table_id))->tablename;

            if ($childTable == "msm_info")
            {
                $selectType = "Information";
            }
            else if ($childTable == "msm_external_link")
            {
                $selectType = "External Link";
                break;
            }
            else
            {
                if ($sub->msm_id == $compRecord->msm_id)
                {
                    $selectType = "Internal Reference";
                    break;
                }
                else
                {
                    $selectType = "External Reference";
                    break;
                }
            }
        }

        $htmlContent .= $selectType;

        $htmlContent .= "</div>";

        if (!empty($this->external_link))
        {
            $htmlContent .= "<div id='msm_subordinate_url-$idEnding'>";
            $htmlContent .= $this->external_link->href;
            $htmlContent .= "</div>";

            $htmlContent .= "<div id='msm_subordinate_hotword_match-$idEnding' class='msm_subordinate_hotword_matchs'>";
            $htmlContent .= $hotIdInfo[0];
            $htmlContent .= "</div>";

            $htmlContent .= $this->external_link->info->displayData($parentId, $idEnding);
        }
        else
        {
            $htmlContent .= "<div id='msm_subordinate_hotword_match-$idEnding' class='msm_subordinate_hotword_matchs'>";
            $htmlContent .= $hotIdInfo[0];
            $htmlContent .= "</div>";

            $htmlContent .= $this->info->displayData($parentId, $idEnding);
        }


        return $htmlContent;
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_subordinate table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the 
     * EditorExternalLink/EditorInfo classes.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorSubordinate
     */
    public function loadData($compid)
    {
        global $DB;

        $subordinateCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $subordinateCompRecord->unit_id;

        $subordinateRecord = $DB->get_record($this->tablename, array('id' => $this->id));
        $this->hot = $subordinateRecord->hot;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $this->compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            if ($childTable->tablename == 'msm_info')
            {
                $info = new EditorInfo();
                $info->loadData($child->id);
                $this->info = $info;
            }
            else if ($childTable->tablename == "msm_external_link")
            {
                $external_link = new EditorExternalLink();
                $external_link->loadData($child->id);
                $this->external_link = $external_link;
            }
        }
        return $this;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database. The contents associated with subordinate elements are shown when the user hovers over the
     * anchor element which triggers opening of jquery dialog box for information element.
     * 
     * @return HTML string
     */
    public function displayPreview()
    {
        $previewHtml = '';

        $idInfo = explode("||", $this->hot);
        $indexInfo = explode("-", $idInfo[0]);

        $id = '';
        for ($i = 1; $i < sizeof($indexInfo) - 1; $i++)
        {
            $id .= $indexInfo[$i] . "-";
        }
        $id .= $indexInfo[sizeof($indexInfo) - 1];

        if (!empty($this->external_link))
        {
            $previewHtml .= $this->external_link->displayPreview($id);
        }

        if (!empty($this->info))
        {
            $previewHtml .= $this->info->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>
