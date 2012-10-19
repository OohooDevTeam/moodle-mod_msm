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
        $('#symbolcontent').attr('class', 'rendercontent');        
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);      
        $('#symbolpanel').stop().animate({
            height:'90%', 
            opacity:1.0
        }, 500, function() {
            $('#symbolcontent').fadeIn('slow');          
                                    
        });
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
        $('#glossarycontent').attr('class', 'rendercontent');        
        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);      
        $('#glossarypanel').stop().animate({
            height:'90%', 
            opacity:1.0
        }, 500, function() {
            $('#glossarycontent').fadeIn('slow');
                                    
        });
    },
    function(){
        $('#glossarycontent').fadeOut('slow', function() {
            $('#glossarypanel').stop().animate({
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