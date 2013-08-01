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
 * EditorImage class is inherited from the abstract class EditorElement and contains 
 * these abstract methods: getFormData, insertData, and loadData.  This class represents
 * any image elements in content.  Any of the classes with content element associated
 * calls the EditorImage indirectly by calling processImage method.
 */
class EditorImage extends EditorElement {

    public $id;                     // database ID for the image element in msm_img table
    public $compid;                 // database ID for the image element in msm_compositor table
    public $string_id;              // save the alt attribute value for the image element
    public $src;                    // src attribute value that points to the image saved in moodle file repository
    public $description;            // description input associated with the image --> not implemented yet
    // (was going to add the input field in image mapping tinymce plugin)
    public $caption;                // title input associated with the image --> not implemented yet (image mapping plugin)
    public $height;                 // height attribute associated with the image element
    public $width;                  // width attribute associated with the image element
    public $fileoptions;            // moodle file options defined in authoringTool.php --> needed to retrieve data in moodle file repository
    public $isRef;                  // database ID associated with the referenced already-existing comment element in msm_compositor table

    // constructor for this class

    function __construct() {
        $this->tablename = 'msm_img';
    }

    // code only implements plain texts w/o maps...etc
    // idNumber == DOMElement with tag name of img
    /**
     * This method is an abstract method inherited from EditorElement.  This method is a bit different than other classes
     * because $idNumber in this method is a DOMElement and instead of retrieving information from POST object (other 
     * then the moodle file options), it retrieves data from the image element and changes the src value to contain
     * the moodle file serving script, pluginfile.php.
     * 
     * @TODO no code to deal with image mapping was done
     * 
     * @global moodle_database $DB
     * @global type $CFG
     * @param DOMElement $idNumber              image element in content
     * @return \EditorImage
     */
    public function getFormData($idNumber) {
        global $DB, $CFG;

        $doc = new DOMDocument();
        $imgNode = $doc->importNode($idNumber, true);
        $src = null;
        // processing the src value to add the wwwroot to the front of the string and remove the 
        // ../../ due to relative pathing

        $srcAttr = $imgNode->getAttribute("src");
        $wwwroot = "$CFG->wwwroot/";

        if (strstr(trim($srcAttr), trim($wwwroot))) {
            $src = $srcAttr;
        } else {
            $srcInfo = explode("/", $srcAttr);
            $src = $CFG->wwwroot;
            for ($i = 2; $i < sizeof($srcInfo); $i++) {
                $src .= "/" . $srcInfo[$i];
            }
        }
        $fileoptions = json_decode($_POST["msm_file_options"])->image;
        $childOrderInfo = explode(",", $_POST["msm_child_order"]);
        $msmid = trim($childOrderInfo[sizeof($childOrderInfo) - 1]);

        $msm = $DB->get_record('msm', array('id' => $msmid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        file_save_draft_area_files($fileoptions->itemid, $context->id, "mod_msm", $fileoptions->env, $msmid, null);

        $this->src = $src;

        $this->height = $imgNode->getAttribute("height");
        $this->width = $imgNode->getAttribute("width");
        $this->string_id = $imgNode->getAttribute("alt");
        $this->fileoptions = json_decode($_POST["msm_file_options"])->image;

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_imgS table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. 
     * 
     * @global moodle_database $DB
     * @param integer $parentid         The ID of the parent of this object(ie.media) in compositor table.
     * @param integer $siblingid        The ID of the previous sibling of this object in compositor table.
     * @param integer $msmid            The instance ID of the MSM module.
     */
    public function insertData($parentid, $siblingid, $msmid) {
        global $DB;

        $data = new stdClass();

        if (empty($this->isRef)) {
            $data->string_id = $this->string_id;
// $data->description = $this->description;
// $data->extended_caption = $this->caption;
            $data->src = $this->src . "||" . $msmid;
            $data->height = $this->height;
            $data->width = $this->width;

            $this->id = $DB->insert_record($this->tablename, $data);
        }

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);
    }

    /**
     * This method has a purpose of displaying the data extracted from DB from loadData
     * method by outputting the HTML code.  
     * 
     * @return string
     */
    public function displayData() {
        $htmlContent = '';
        if ((!empty($this->height)) && (!empty($this->width))) {
            $htmlContent .= "<img src='$this->src' height='$this->height' width='$this->width'/>";
        } else if ((empty($this->height)) && (!empty($this->width))) {
            $htmlContent .= "<img src='$this->src' width='$this->width'/>";
        } else if ((!empty($this->height)) && (empty($this->width))) {
            $htmlContent .= "<img src='$this->src' height='$this->height'/>";
        } else if ((empty($this->height)) && (empty($this->width))) {
            $htmlContent .= "<img src='$this->src'/>";
        }

        return $htmlContent;
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_img table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. 
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorImage
     */
    public function loadData($compid) {
        global $DB;

        $imgCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $this->compid = $imgCompRecord->id;
        $this->id = $imgCompRecord->unit_id;

        $imgRecord = $DB->get_record($this->tablename, array("id" => $this->id));

        $srcInfo = explode("||", $imgRecord->src);

        $this->src = $srcInfo[0];
        $this->height = $imgRecord->height;
        $this->width = $imgRecord->width;
        $this->string_id = $imgRecord->string_id;

        return $this;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * The method in this class is called by Media and is responsible for showing any image components in the content.
     * 
     * @return string
     */
    public function displayPreview() {
        $previewHtml = '';

        if ((!empty($this->height)) && (!empty($this->width))) {
            $htmlContent .= "<img src='$this->src' height='$this->height' width='$this->width'/>";
        } else if ((empty($this->height)) && (!empty($this->width))) {
            $htmlContent .= "<img src='$this->src' width='$this->width'/>";
        } else if ((!empty($this->height)) && (empty($this->width))) {
            $htmlContent .= "<img src='$this->src' height='$this->height'/>";
        } else if ((empty($this->height)) && (empty($this->width))) {
            $htmlContent .= "<img src='$this->src'/>";
        }

        return $previewHtml;
    }

}

?>
