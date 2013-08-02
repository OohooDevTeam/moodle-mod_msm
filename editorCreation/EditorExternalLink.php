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
 * EditorExternalLink class inherits from the EditorElement class and it represents the
 * external.link elements in the MSM editor.  The external.link element is similar to function of
 * anchor elements in HTML.  The external.link has subordinate element as a parent which contains
 * the hot-tagge word(anchor element).  The external.link element gives the anchored element the destination
 * url in href attribute which is like anchor element function in HTML but when the mouse is hovered instead of clicked,
 * a popup window using jquery dialog appears with extra information either about the tagged word or about the linked
 * webpage.
 *
 */
class EditorExternalLink extends EditorElement
{

    public $id;             // database ID associated with the external.link element in msm_external_link table
    public $compid;         // database ID associated with the external.link element in msm_compositor table
    public $href;           // external webpage URL
    public $type;           // type of document (value retained from XML structure but not implemented in MSM editor)        
    public $target;         // specifying where to open the link(basically same function as HTML)
    public $info;           // EditorInfo objects associated with the external.link element
    public $isRef;          // database ID associated with the referenced already-existing external.link element in msm_compositor table

    // constructor of this class

    public function __construct()
    {
        $this->tablename = "msm_external_link";
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorInfo) to process its
     * children's data.
     * 
     * @param string $idNumber             it contains string that consist of (parent_HTML_ID_number)-(current_HTML_ID_number) 
     * @return \EditorExternalLink
     */
    public function getFormData($idNumber)
    {
        $this->type = null;
        $this->target = "_blank";
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

        foreach ($allSubordinates as $index => $subordinate)
        {
            $idValuePair = explode("||", $subordinate);

            if (strpos($idValuePair[0], $idNumber) !== false)
            {
                if (strpos($idValuePair[0], 'url') !== false)
                {
                    if ($idValuePair[0] == 'msm_subordinate_url-' . $idNumber)
                    {
                        $this->href = $idValuePair[1];
                    }
                }
            }
        }

        $info = new EditorInfo();
        $info->getFormData($idNumber . "|sub");
        $this->info = $info;

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_external_link table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorInfo class.
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
            $data->type = $this->type;
            $data->href = $this->href;
            $data->target = $this->target;

            $this->id = $DB->insert_record($this->tablename, $data);
        }
        else
        {
            $childRecord = $DB->get_record("msm_compositor", array("parent_id" => $this->isRef));

            $childTable = $DB->get_record("msm_table_collection", array("id" => $childRecord->table_id));

            if ($childTable->tablename == "msm_info")
            {
                $info = new EditorInfo();
                $info->id = $childRecord->unit_id;
                $info->isRef = $this->isRef; // info and reference materials both have parent_id of subordinate
                $this->info = $info;
            }
        }

        $compdata = new stdClass();
        $compdata->unit_id = $this->id;
        $compdata->msm_id = $msmid;
        $compdata->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compdata->parent_id = $parentid;
        $compdata->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compdata);

        $sibling_id = 0;
        if (!empty($this->info))
        {
            $this->info->insertData($this->compid, $sibling_id, $msmid);
        }
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_external_link table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorInfo 
     * class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     */
    public function loadData($compid)
    {
        global $DB;

        $linkCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $this->compid = $compid;
        $this->id = $linkCompRecord->unit_id;

        $linkRecord = $DB->get_record($this->tablename, array("id" => $this->id));

        $this->href = $linkRecord->href;
        $this->type = $linkRecord->type;
        $this->target = $linkRecord->target;

        $childRecord = $DB->get_record("msm_compositor", array("parent_id" => $this->compid));

        $childTable = $DB->get_record("msm_table_collection", array("id" => $childRecord->table_id));

        if ($childTable->tablename == "msm_info")
        {
            $info = new EditorInfo();
            $info->loadData($childRecord->id);
            $this->info = $info;
        }
    }

    /* Unlike most of the other classes with EditorElement as a parent class, this class doesn't have its own
      display method but the values loaded from above method is used in displayData method in EditorSubordinate */

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.
     * 
     * @param string $id        String to be added to HTML ID of this external.link element and its components to make them unique
     * @return HTML string
     */
    public function displayPreview($id)
    {
        $previewHtml = '';

        if (!empty($this->info))
        {
            $previewHtml .= $this->info->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>
