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
 * EditorMedia class represents the wrapper div tag surrounding the image elements.
 * This class was mostly created to accomodate for the database structure created from
 * legacy XML files.  This class calls on EditorImage class and is called by most classes
 * that have content components.
 */
class EditorMedia extends EditorElement
{

    public $id;                 // database ID associated with the media element in msm_media table
    public $compid;             // database ID associated with the media element in msm_compositor table
    public $active;             // indicating if there are image maps attached to the image (not implemented)
    public $inline;             // indicating if the image is centered or inline with content (not implemented
    // b/c in tinymce it's wrapped in a <p> that has centred alignment)
    public $media_type;         // type of element that this div is wrapping around (currently, only can have images) 
    public $image;              // EditorImage object that is associate with the media element
    public $isRef;              // database ID associated with the referenced already-existing media element in msm_compositor table

    // constructor for this class

    function __construct()
    {
        $this->tablename = "msm_media";
    }

    // idNumber is img domElement
    /**
     * This method is an abstract method inherited from EditorElement.  It finds the needed information for database table
     * from the POST object(from editor form submission).  It calls the same method from another class(EditorImage) to process its
     * children's data.
     * 
     * @param DOMElement $idNumber          img element from the content
     * @return \EditorMedia
     */
    public function getFormData($idNumber)
    {
        $this->active = 0;              //default setup for now until image.mapping plugin is developed
        $this->inline = 0;              //default setup for now until image.mapping plugin is developed
        $this->media_type = "images";   //default setup for now until image.mapping plugin is developed

        $image = new EditorImage();
        $image->getFormData($idNumber);
        $this->image = $image;

        return $this;
    }

    /**
     * This method is an abstract method inherited from EditorElement.  Its main purpose is to
     * insert the data obtained from the POST object via method above to the msm_media table and to 
     * insert structural data (its parent/sibling...etc) to the compositor table. This method also calls 
     * insertData method from EditorImage class.
     * 
     * @global moodle_database $DB
     * @param int $parentid             Database ID from msm_compositor of the parent element
     * @param integer $siblingid        Database ID from msm_compositor of the previous sibling element
     * @param integer $msmid            The instance ID of the MSM module.
     */
    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();

        if (empty($this->isRef))
        {
            $data->active = $this->active;
            $data->inline = $this->inline;
            $data->media_type = $this->media_type;

            $this->id = $DB->insert_record($this->tablename, $data);
        }
        else
        {
            $childRecords = $DB->get_record("msm_compositor", array("parent_id" => $this->isRef));
            $childTable = $DB->get_record("msm_table_collection", array("id" => $childRecords->table_id));

            if ($childTable->tablename == "msm_img")
            {
                $img = new EditorImage();
                $img->id = $childRecords->unit_id;
                $img->isRef = $childRecords->id;
                $this->image = $img;
            }
        }

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);

        $this->image->insertData($this->compid, 0, $msmid);
    }

    /**
     * This abstract method from EditorElement extracts appropriate information from the 
     * msm_media table and also triggers extraction of data from its children using the 
     * data given by the msm_compositor table. It calls the loadData method from the
     * EditorImage class.
     * 
     * @global moodle_database $DB
     * @param integer $compid           The database ID from the msm_compositor table
     * @return \EditorMedia
     */
    public function loadData($compid)
    {
        global $DB;

        $mediaCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $this->id = $mediaCompRecord->unit_id;
        $this->compid = $mediaCompRecord->id;

        $mediaRecord = $DB->get_record($this->tablename, array("id" => $this->id));

        $this->inline = $mediaRecord->inline;
        $this->active = $mediaRecord->active;

        $childRecord = $DB->get_record("msm_compositor", array("parent_id" => $compid));

        if (!empty($childRecord))
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $childRecord->table_id));

            if ($childTable->tablename == "msm_img")
            {
                $this->media_type = "images";
                $image = new EditorImage();
                $image->loadData($childRecord->id);
                $this->image = $image;
            }
        }
        return $this;
    }

    /**
     * This method has a purpose of displaying the data extracted from DB from loadData
     * method by outputting the HTML code.  This method calls displayData from the EditorImage class.
     * 
     * @return string
     */
    public function displayData()
    {
        $htmlContent = '';
        $htmlContent .= $this->image->displayData();
        return $htmlContent;
    }

    /**
     * This method is triggered when the View navigation button on the editor is clicked to show the preview of the unit to the user.
     * It generates the appropriate HTML code to display the information as it is layed out on the MSM editor not according to how
     * the elements are structured in the database.  Hence allowing user to preview the material while making changes without having to 
     * commit to saving it in the database.
     * 
     * @return string
     */
    public function displayPreview()
    {
        $previewContent = '';
        $previewContent .= $this->image->displayPreview();
        return $previewContent;
    }

}

?>
