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
 * *************************************************************************
 */

/**
 * This class is representing all the image elements that needs to be exported as XML document.
 * ExportImage class is called only by the ExportMedia class.  It inherits methods from the abstract
 * class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportImage extends ExportElement
{

    public $id;                          // ID of the current image element in the msm_img database table
    public $compid;                      // ID of the current image element in the msm_compositor database table
    public $msmid;                       // msm instance ID
    public $msm_name;                    // name of this msm instance
    public $src;                         // source image file path
    public $width;                       // width of the image 
    public $height;                      // height of the image
    public $description;                 // description that is associated with this image element
    public $extended_caption;            // any extra information that is associated with this image
    public $imagemaps = array();         // ExportImageMap objects that are associated with this image (TODO!)

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with image element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from the ExportImageMap(TODO) class.  The DOMElement object that is returned from exportData
     * calls from class mentioned above is then appended to the image DOMElement and is returned to be appended to the 
     * media element.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $imgCreator = new DOMDocument();
        $imgNode = $imgCreator->createElement("img");
        $imgNode->setAttribute("id", "$this->msmid-$this->compid");

        $srcInfo = explode("/", $this->src);

        // get file name 
        $modifiedsrc = explode("||", $srcInfo[sizeof($srcInfo) - 1]);

        // change the src path to point to the pics folder in the temp folder before being compressed as a zip file
        $src = "$this->msm_name/pics/" . $modifiedsrc[0];

        $imgNode->setAttribute("src", $src);
        $imgNode->setAttribute("height", $this->height);
        $imgNode->setAttribute("width", $this->width);

        return $imgNode;
    }

    /**
     * This method is used to pull all relevant data linked with image elements from the database table
     * "msm_img".  It also calls the loadDbData method from the ExportImageMap(TODO) classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               ID of this image element in msm_compositor database table
     * @return \ExportImage
     */
    public function loadDbData($compid)
    {
        global $DB;

        $imgCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $imgUnitRecord = $DB->get_record("msm_img", array("id" => $imgCompRecord->unit_id));
        $msmData = $DB->get_record("msm", array("id"=>$imgCompRecord->msm_id));

        $this->id = $imgUnitRecord->id;
        $this->msmid = $imgCompRecord->msm_id;
        $this->msm_name = preg_replace("/\s+/", '', $msmData->name) . $imgCompRecord->msm_id;
        $this->compid = $compid;
        $this->src = $imgUnitRecord->src;
        $this->description = $imgUnitRecord->description;
        $this->extended_caption = $imgUnitRecord->extended_caption;
        $this->height = $imgUnitRecord->height;
        $this->width = $imgUnitRecord->width;
        // add code for image mapping when available

        return $this;
    }

}

?>
