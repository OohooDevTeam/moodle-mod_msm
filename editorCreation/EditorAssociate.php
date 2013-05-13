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
 * EditorAssociate class represents the associate elements that will be created by 
 * the MSM editor which is responsible for creating the tools for the user to be able to
 * link associated materials to the specified definition/theorem/comments with/without 
 * reference materials either created new or existing ones eithe within or outside of the current
 * composition.  
 * 
 */
class EditorAssociate extends EditorElement
{

    public $id;
    public $compid;
    public $type; // in db--> description of the associate
    public $infos = array();

    // constructor for the class
    function __construct()
    {
        $this->tablename = 'msm_associate';
    }

    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorInfo) to process its
     * children's data.
     * 
     * @param string $idNumber          it contains string that consist of parent_HTML_ID_number-current_HTML_ID_number
     * @return \EditorAssociate
     */
    public function getFormData($idNumber)
    {
        $this->type = $_POST['msm_associate_dropdown-' . $idNumber];

        $infomatch = "/^msm_info_content-$idNumber.*$/";

        foreach ($_POST as $id => $value)
        {
            if (preg_match($infomatch, $id))
            {
                $idInfo = explode("-", $id);

                $newId = '';
                for ($i = 1; $i < sizeof($idInfo) - 1; $i++)
                {
                    $newId .= $idInfo[$i] . "-";
                }
                $newId .= $idInfo[sizeof($idInfo) - 1];

                $info = new EditorInfo();
                $info->getFormData($newId);
                $this->infos[] = $info;
            }
        }
        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_associate table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorInfo class.
     * 
     * @global moodle_database $DB
     * @param integer $parentid         The ID of the parent of this object(ie. Def/Theorem/Comment) in compositor table.
     * @param integer $siblingid        The ID of the previous sibling of this object(ie. anther associate) in compositor table.
     * @param integer $msmid            The instance ID of the MSM module.
     */
    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->description = $this->type;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $sibling_id = 0;
        foreach ($this->infos as $info)
        {
            $info->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $info->compid;
        }
    }

    /**
     * This method is an abstract method from EditorElement that has a purpose of displaying the 
     * data extracted from DB from loadData method by outputting the HTML code.  This method calls 
     * displayData from the EditorInfo class.
     * 
     * @global moodle_database $DB
     * @return HTML string
     */
    public function displayData()
    {
        global $DB;

        $currentAssociateRecord = $DB->get_record("msm_compositor", array("id" => $this->compid));

        $htmlContent = '';

        $htmlContent .= "<div id='msm_associate_childs-$currentAssociateRecord->parent_id-$this->compid' class='msm_associate_childs'>";
        $htmlContent .= "<div id='msm_associate_info_header-$this->compid' class='msm_associate_info_headers'>";
        $htmlContent .= "<b> ASSOCIATED INFORMATION </b>";
        $htmlContent .= "<span style='visibility: hidden;'>Drag here to move this element.</span>";
        $htmlContent .= "</div>";

        $htmlContent .= "<div class='msm_associate_optionarea'>";
        $htmlContent .= "<span class='msm_associate_option_label'>Type of information: </span>";
        $htmlContent .= "<select id='msm_associate_dropdown-$currentAssociateRecord->parent_id-$this->compid' class='msm_associated_dropdown' name='msm_associate_dropdown-$currentAssociateRecord->parent_id-$this->compid' disabled='disabled'>";
        switch ($this->type)
        {
            case "Comment":
                $htmlContent .= "<option value='Comment' selected='selected'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Explanation":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation' selected='selected'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Example":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example' selected='selected'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Illustration":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration' selected='selected'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Remark":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark' selected='selected'>Remark</option>";
                $htmlContent .= "<option value='Exploration'>Exploration</option>";
                break;
            case "Exploration":
                $htmlContent .= "<option value='Comment'>Comment</option>";
                $htmlContent .= "<option value='Explanation'>Explanation</option>";
                $htmlContent .= "<option value='Example'>Example</option>";
                $htmlContent .= "<option value='Illustration'>Illustration</option>";
                $htmlContent .= "<option value='Remark'>Remark</option>";
                $htmlContent .= "<option value='Exploration' selected='selected'>Exploration</option>";
                break;
        }
        $htmlContent .= "</select>";
        $htmlContent .= "</div>";

        foreach ($this->infos as $info)
        {
            $htmlContent .= $info->displayData("$currentAssociateRecord->parent_id-$this->compid", '');
        }

        $htmlContent .= "</div>";

        return $htmlContent;
    }

    /**
     * This abstract method from EditoElement extracts appropriate information from the 
     * msm_associate table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the EditorInfo 
     * class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorAssociate
     */
    public function loadData($compid)
    {
        global $DB;

        $associateCompRecord = $DB->get_record('msm_compositor', array('id' => $compid));

        $this->compid = $compid;
        $this->id = $associateCompRecord->unit_id;

        $associateRecord = $DB->get_record($this->tablename, array('id' => $this->id));

        $this->type = $associateRecord->description;

        $childElements = $DB->get_records('msm_compositor', array('parent_id' => $compid), 'prev_sibling_id');

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record('msm_table_collection', array('id' => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_info":
                    $info = new EditorInfo();
                    $info->loadData($child->id);
                    $this->infos[] = $info;
                    break;
            }
        }

        return $this;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * The method in this class is called by theorem/comment or definition and is responsible for showing the appropriate components
     * of the associate element.  This method, in turn, calls the displayPreview of the EditorInfo to display the appropriate UI dialog
     * windows.
     * 
     * @param string $prevClass         This string indicates which class triggered this method.  
     * @param string $id                This string is a pair of integers with '-' in middle to be inserted into HTML ID to make them unique
     * @return HTML string
     */
    public function displayPreview($prevClass = '', $id = '')
    {
        $previewHtml = '';
        if ($prevClass == 'def')
        {

            if (!empty($this->infos[0]->ref))
            {
                $previewHtml .= "<li class='defminibutton' id='defminibutton-$id' onmouseover='previewinfoopen(\"defminibutton-$id\", \"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
                $previewHtml .= "<div class='refcontent' id='refcontent-$id' style='display:none;'>";
                $previewHtml .= $this->infos[0]->ref->displayPreview($id);
//                print_object($this->infos[0]->ref);
                $previewHtml .= "</div>";
            }
            else
            {
                $previewHtml .= "<li class='defminibutton' id='defminibutton-" . $id . "' onmouseover='popup(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
            }

            $previewHtml .= $this->infos[0]->displayPreview($id);
        }

        if ($prevClass == 'theorem')
        {
            if (!empty($this->infos[0]->ref))
            {
                $previewHtml .= "<li class='minibutton' id='minibutton-$id' onmouseover='previewinfoopen(\"minibutton-$id\", \"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
                $previewHtml .= "<div class='refcontent' id='refcontent-$id' style='display:none;'>";
                $previewHtml .= $this->infos[0]->ref->displayPreview($id);
                $previewHtml .= "</div>";
            }
            else
            {
                $previewHtml .= "<li class='minibutton' id='minibutton-$id' onmouseover='popup(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
            }

            $previewHtml .= $this->infos[0]->displayPreview($id);
        }


        if ($prevClass == 'comment')
        {
            if (!empty($this->infos[0]->ref))
            {
                $previewHtml .= "<li class='commentminibutton' id='commentminibutton-$id' onmouseover='previewinfoopen(\"commentminibutton-$id\", \"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
                $previewHtml .= "<div class='refcontent' id='refcontent-$id' style='display:none;'>";
                $previewHtml .= $this->infos[0]->ref->displayPreview($id);
                $previewHtml .= "</div>";
            }
            else
            {
                $previewHtml .= "<li class='commentminibutton' id='commentminibutton-$id' onmouseover='popup(\"$id\")'>";
                $previewHtml .= "<span style='cursor:pointer'>";
                $previewHtml .= $this->type;
                $previewHtml .= "</span>";
                $previewHtml .= "</li>";
            }

            $previewHtml .= $this->infos[0]->displayPreview($id);
        }

        return $previewHtml;
    }

}

?>