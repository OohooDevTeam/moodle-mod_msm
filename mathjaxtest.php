

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/development-bundle/themes/ui-lightness/jquery.ui.all.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/jquery.splitter.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/MsmDisplay.css'/>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/slideNav.css'/>";
echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/css/jquery.treeview.css'/>";
echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/css/jshowoff.css' type='text/css'/>";

echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/jquery-1.7.1.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/external/jquery.bgiframe-2.1.2.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.core.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.widget.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.mouse.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.draggable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.position.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.resizable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.dialog.js'></script>";

// these js files need to be after the development-bundle
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery.splitter-0.6.js'></script>";


echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery.jshowoff.js'></script>";
//echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/maphilight/jquery.maphilight.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/popup.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/infoopen.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/navMenu.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/navToPage.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jTreeview/lib/jquery.cookie.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jTreeview/jquery.treeview.js'></script>";


//echo "<script type ='text/javascript' src='$CFG->wwwroot/mod/msm/js/jimagemapster.js'></script>";
//echo "<script type='text/x-mathjax-config' src='$CFG->wwwroot/mod/msm/js/mathjax/config/local/local.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML,local/local'></script>";

$content = '';

$content .= '<ul id="navigation">
            <li class="toc"><a href="" id="toc"><span>Table of Contents</span></a></li>
            <li class="author"><a href=""><span>Authors</span></a></li>
            <li class="symbol"><a href="" id="symbol"><span>Symbols</span></a></li>
            <li class="glossary"><a href="" id="glossary"><span>Glossary</span></a></li>
            <li class="biblio"><a href=""><span>Bibliography</span></a></li>
            <li class="contact"><a href="" id="contact"><span>Contact</span></a></li>
        </ul>';


$symbolfilename = '2-1-msm_symbolindex.html';
if (file_exists($symbolfilename))
{
    $symbolfile = fopen($symbolfilename, 'r');
    $content .= fread($symbolfile, filesize($symbolfilename));
    fclose($symbolfile);
}
else
{
    echo "file " . $symbolfilename . "does not exist.";
}

$content .= "<script type='text/javascript'>
               $(document).ready(function() {
                   $('#symbolcontent').treeview({
                    animated: 'fast',
                    collapsed: true
                   });
                   
                $('.slidepanelcontent').hide();
                     
                $('.dialogs').dialog({
                    autoOpen: false,
                    height: 'auto',
                    width: 605
                });  
              });
                
             </script>";

echo $content;

?>
