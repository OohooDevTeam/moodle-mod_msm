/**
 **************************************************************************
 **                              MSM                                     **
 **************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 **************************************************************************
 **************************************************************************/

/**
 * This function gives functionality to the small vertical menus on the right side of the
 * view.php for displaying the table of content, glossary, symbol and author indices.
 */

$(function() {   
    $('#toc').click(function () {  
        if($('#tocpanel').height() > 0)
        {
            $('#toccontent').fadeOut('slow', function() {
                $('#tocpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
        }
        else
        {
            $('.slidepanelcontent').not('#toccontent').fadeOut('slow', function() {
                $('.panel').not('#tocpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
            $('#tocpanel').stop().animate({
                height:'90%', 
                opacity:1.0
            }, 500, function() {
                $('#toccontent').fadeIn('slow');                                   
            });  
        }
    });
    
    $('#symbol').click(function () {  
        if($('#symbolpanel').height() > 0)
        {
            $('#symbolcontent').fadeOut('slow', function() {
                $('#symbolpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
        }
        else
        {
            $('.slidepanelcontent').not('#symbolcontent').fadeOut('slow', function() {
                $('.panel').not('#symbolpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
            $('#symbolpanel').stop().animate({
                height:'90%', 
                opacity:1.0
            }, 500, function() {
                $('#symbolcontent').fadeIn('slow');          
                                        
            });
            if($("#symbolindex").length == 0)
            {
                $('#symbolcontent').load('../msm/XMLImporter/loadSymbol.php', 
                {
                    moduleinfo: $('#instanceid').val()
                },
                function(){           
                    $('.dialogs').dialog({
                        autoOpen: false,
                        height: 'auto',
                        width: 605
                    });
                    $('#symbolindex').treeview({
                        animated: 'fast',
                        collapsed: true
                    });
    
                
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                });
            }  
        }
    });
    
    $('#glossary').click(function () {  
        if($('#glossarypanel').height() > 0)
        {
            $('#glossarycontent').fadeOut('slow', function() {
                $('#glossarypanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
        }
        else
        {
            $('.slidepanelcontent').not('#glossarycontent').fadeOut('slow', function() {
                $('.panel').not('#glossarypanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
            $('#glossarypanel').stop().animate({
                height:'90%', 
                opacity:1.0
            }, 500, function() {
                $('#glossarycontent').fadeIn('slow');
                                    
            });
            if($("#glossaryindex").length == 0)
            {
                $('#glossarycontent').load('../msm/XMLImporter/loadGlossary.php', 
                {
                    moduleinfo: $('#instanceid').val()
                },
                function(){           
                    $('.dialogs').dialog({
                        autoOpen: false,
                        height: 'auto',
                        width: 605
                    });
            
                    $('#glossaryindex').treeview({
                        animated: 'fast',
                        collapsed: true
                    });
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                });
            } 
        }
    });
    
    $('#author').click(function () {  
        if($('#authorpanel').height() > 0)
        {
            $('#authorcontent').fadeOut('slow', function() {
                $('#authorpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
        }
        else
        {
            $('.slidepanelcontent').not('#authorcontent').fadeOut('slow', function() {
                $('.panel').not('#authorpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
            
            $('#authorpanel').stop().animate({
                height:'90%', 
                opacity:1.0
            }, 500, function() {
                $('#authorcontent').fadeIn('slow');
                                    
            });
            if($("#authorindex").length == 0)
            {
                $('#authorcontent').load('../msm/XMLImporter/loadAuthor.php', 
                {
                    moduleinfo: $('#instanceid').val()
                },
                function(){           
                    $('.dialogs').dialog({
                        autoOpen: false,
                        height: 'auto',
                        width: 605
                    });
                    $('#authorindex').treeview({
                        animated: 'fast',
                        collapsed: true
                    });
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                });
            }            
        }
    });
    
    $('#contact').click(function () {  
        if($('#contactpanel').height() > 0)
        {
            $('#contactcontent').fadeOut('slow', function() {
                $('#contactpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
        }
        else
        {
            // closing any other panels that are not contact related
            $('.slidepanelcontent').not('#contactcontent').fadeOut('slow', function() {
                $('.panel').not('#contactpanel').stop().animate({
                    height:'0px', 
                    opacity:0.1
                }, 500); //slide the #panel back to a width of 0
            });
            $('#contactpanel').stop().animate({
                height:'90%', 
                opacity:1.0
            }, 500, function() {
                $('#contactcontent').fadeIn('slow');                                   
            });            
        }
    });
});