/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {            
    $('#toc').toggle(function () {
        $('#tocpanel').stop().animate({
            height:'90%', 
            opacity:1.0
        }, 500, function() {
            $('#toccontent').fadeIn('slow');
                                    
        });  
    },
    function(){
        $('#toccontent').fadeOut('slow', function() {
            $('#tocpanel').stop().animate({
                height:'0px', 
                opacity:0.1
            }, 500); //slide the #panel back to a width of 0
        });
    });
    
    $('#symbol').toggle(function () {    
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
    },
    function(){
        $('#symbolcontent').fadeOut('slow', function() {
            $('#symbolpanel').stop().animate({
                height:'0px', 
                opacity:0.1
            }, 500); //slide the #panel back to a width of 0
        });
    });
    
    $('#glossary').toggle(function () {     
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
    },
    function(){
        $('#glossarycontent').fadeOut('slow', function() {
            $('#glossarypanel').stop().animate({
                height:'0px', 
                opacity:0.1
            }, 500); //slide the #panel back to a width of 0
        });
    });
    
    $('#author').toggle(function () {   
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
    },
    function(){
        $('#authorcontent').fadeOut('slow', function() {
            $('#authorpanel').stop().animate({
                height:'0px', 
                opacity:0.1
            }, 500); //slide the #panel back to a width of 0
        });
    });
    
    $('#contact').toggle(function () {      
        $('#contactpanel').stop().animate({
            height:'90%', 
            opacity:1.0
        }, 500, function() {
            $('#contactcontent').fadeIn('slow');
            $('#contact').toggleClass('navActive');                        
        });  
    },
    function(){
        $('#contactcontent').fadeOut('slow', function() {
            $('#contactpanel').stop().animate({
                height:'0px', 
                opacity:0.1
            }, 500); //slide the #panel back to a width of 0
        });
    });
});