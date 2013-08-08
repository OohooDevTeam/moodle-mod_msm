/**
 **************************************************************************
 **                              MSM                                     **
 **************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim(modified by), Erik Kallevig(original author)**
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 **************************************************************************
 **************************************************************************/

/*
***************************plugin by*******************************************
Title: jShowOff: a jQuery Content Rotator Plugin
Author: Erik Kallevig
Version: 0.1.2
Website: http://ekallevig.com/jshowoff
License: Dual licensed under the MIT and GPL licenses.

jShowOff Options

animatePause : whether to use 'Pause' animation text when pausing [boolean, defaults to true]
autoPlay : whether to start playing immediately [boolean, defaults to true]
changeSpeed : speed of transition [integer, milliseconds, defaults to 600]
controls : whether to create & display controls (Play/Pause, Previous, Next) [boolean, defaults to true]
controlText : custom text for controls [object, 'play', 'pause', 'previous' and 'next' properties]
cssClass : custom class to add to .jshowoff wrapper [string]
effect : transition effect [string: 'fade', 'slideLeft' or 'none', defaults to 'fade']
hoverPause : whether to pause on hover [boolean, defaults to true]
links : whether to create & display numeric links to each slide [boolean, defaults to true]
speed : time each slide is shown [integer, milliseconds, defaults to 3000]

******************************************************************************/

/**
 * This jquery plugin is used in view.php to create the "slideshow" of units to view page by page.
 * The orignial plugin has been modified to dynamically load each unit as user navigates between pages by
 * adding AJAX calls to prev and next buttons.
 */

(function($) {
    // default global vars
    var gallery;
    var $cont;
    var timer;
    // var preloadedImg;
    var howManyInstances;
    var counter;
    var uniqueClass;
    var cssClass;
    var $wrap;
    var pagenumber;
 
    var config = {
        animatePause : true,
        autoPlay : true,
        changeSpeed : 600,
        controls : true,
        controlText : {
            play :	'Play',
            pause :	'Pause',
            next :	'Next',
            previous :	'Previous'
        },
        effect : 'none',
        hoverPause : true,
        links : true,
        speed : 3000
    };


    $.fn.jshowoff = function(settings) {
        // merge default global variables with custom variables, modifying 'config'
        if (settings) $.extend(true, config, settings);

        // make sure speed is at least 20ms longer than changeSpeed
        if (config.speed < (config.changeSpeed+20)) {
            alert('jShowOff: Make speed at least 20ms longer than changeSpeed; the fades aren\'t always right on time.');
            return this;
        };
        // create slideshow for each matching element invoked by .jshowoff()
        this.each(function(i) {
            
            gallery = $(this).children().remove();
            $cont = $(this);
            timer = '';
            counter = 0;
            preloadedImg = [];
            howManyInstances = $('.jshowoff').length+1;	
            uniqueClass = 'jshowoff-'+howManyInstances;
            pagenumber = 1;

            cssClass = config.cssClass != undefined ? config.cssClass : '';

            // set up wrapper
            $cont.css('position','relative').wrap('<div class="jshowoff '+uniqueClass+'" />');
            
            if($.browser.msie) // IE counts the lenght as 1 when the other browser counts it as 2
            {
                $cont.children().remove();
            }
            
            $wrap = $('.'+uniqueClass);
            $wrap.css('position','relative').addClass(cssClass);

            // add first slide to wrapper

            $(gallery[0]).clone().appendTo($cont);
            // preloadImg();
            
            // add controls
            if(config.controls){
                addControls();
                if(config.autoPlay==false){
                    $('.'+uniqueClass+'-play').addClass(uniqueClass+'-paused jshowoff-paused').text(config.controlText.play);
                };
            };
            // addControls();
            
            // // add slide links
            // if(config.links){
            // addSlideLinks();
            // $('.'+uniqueClass+'-slidelinks a').eq(0).addClass(uniqueClass+'-active jshowoff-active');
            // };
            
            // display error message if no slides present
            if(gallery.length<1){
                $('.'+uniqueClass).append('<p>For jShowOff to work, the container element must have child elements.</p>');
            };

        // end .each
        });

        return this;

    // end .jshowoff
    };


    // utility for loading slides
    function transitionTo(gallery,index) {	
        var oldCounter = counter;
        if((counter >= gallery.length) || (index >= gallery.length)) {
            counter = 0;
            var e2b = true;
        }
        else if((counter < 0) || (index < 0)) {
            counter = gallery.length-1;
            var b2e = true;
        }
        else {
            counter = index;
        }

        if(config.effect=='slideLeft'){
            var newSlideDir, oldSlideDir;
            function slideDir(dir) {
                newSlideDir = dir=='right' ? 'left' : 'right';
                oldSlideDir = dir=='left' ? 'left' : 'right';	
            };

            counter >= oldCounter ? slideDir('left') : slideDir('right') ;

            $(gallery[counter]).clone().appendTo($cont).slideIt({
                direction:newSlideDir,
                changeSpeed:config.changeSpeed
            });
            if($cont.children().length>1){
                $cont.children().eq(0).css('position','absolute').slideIt({
                    direction:oldSlideDir,
                    showHide:'hide',
                    changeSpeed:config.changeSpeed
                },function(){
                    $(this).remove();
                });
            };
        } else if (config.effect=='fade') {
            $(gallery[counter]).clone().appendTo($cont).hide().fadeIn(config.changeSpeed,function(){
                if($.browser.msie)this.style.removeAttribute('filter');
            });
            if($cont.children().length>1){
                $cont.children().eq(0).css('position','absolute').fadeOut(config.changeSpeed,function(){
                    $(this).remove();
                });
            };
        } else if (config.effect=='none') {
            $(gallery[counter]).clone().appendTo($cont);
            if($cont.children().length>1){
                $cont.children().eq(0).css('position','absolute').remove();
            };
        };

        // update active class on slide link
        if(config.links){
            $('.'+uniqueClass+'-active').removeClass(uniqueClass+'-active jshowoff-active');
            $('.'+uniqueClass+'-slidelinks a').eq(counter).addClass(uniqueClass+'-active jshowoff-active');
        };
    };	
    // is the rotator currently in 'play' mode
    // function isPlaying(){
    // return $('.'+uniqueClass+'-play').hasClass('jshowoff-paused') ? false : true;
    // };
    //
    // stop slide rotation
    function pause(src) {
        clearInterval(timer);
        
        if(!src || src=='playBtn') $('.'+uniqueClass+'-play').text(config.controlText.play).addClass('jshowoff-paused '+uniqueClass+'-paused');
        if(config.animatePause && src=='playBtn'){
            alert("in condition 2 of pause");
            $('<p class="'+uniqueClass+'-pausetext jshowoff-pausetext">'+config.controlText.pause+'</p>').css({
                fontSize:'62%',
                textAlign:'center',
                position:'absolute',
                top:'40%',
                lineHeight:'100%',
                width:'100%'
            }).appendTo($wrap).addClass(uniqueClass+'pauseText').animate({
                fontSize:'600%',
                top:'30%',
                opacity:0
            }, {
                duration:500,
                complete:function(){
                    $(this).remove();
                }
            });
        }
    };

    // load the next slide
    function next() {
        var stackRecordNum;
        var pgnumber;
        
        var prevrecords = $('#prevstack').val();
        var separatedprevrecords = prevrecords.split(",");
        
        var records = $('#stack').val();
        var separatedrecords = records.split(",");
        
        var functioninput = $('#functioninput').val();
        
        goToAndPause(counter);
        // at the end of the book and press next, it should go back to the beginning of the book
        if(separatedrecords == '')
        {
            stackRecordNum = 0;
        }
        else
        {
            stackRecordNum = separatedrecords.length;
        }
        
        if(stackRecordNum == '0')
        {
            pgnumber = 1;
        }
        else if(separatedprevrecords == '')
        {
            pgnumber =2;
        }
        else
        {
            if((functioninput == '')||(functioninput == 'undefined')||(functioninput == null))
            {
                pgnumber = separatedprevrecords.length+1;
            }
            else
            {
                // alert(functioninput);
                pgnumber = separatedprevrecords.length+2;
            }
            
        }
        
        updatepgnumber(pgnumber);
        
        
        $('#features').load('../msm/XMLImporter/ajaxcall.php',
        {
            stackstring: $('#stack').val(),
            prevstackstring: $('#prevstack').val(),
            currentvalue: $('#current').val(),
            functionname: 'next'
        },
        function(){
                    
            $('.dialogs').dialog({
                autoOpen: false,
                height: 'auto',
                width: 605
            });
            $('.leftbox').animate({
                scrollTop: '0px'
            }, 800);
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        });
        $('#stack').remove();
        $('#prevstack').remove();
        $('#current').remove();
        $('#functioninput').remove();
        
        stackRecordNum = null;
        pgnumber = null;
    };

    // load the previous slide
    function previous() {
        var stackRecordNum;
        var pgnumber;
        
        var nextrecords = $('#stack').val();
        var separatedNext = nextrecords.split(",");
        
        var records = $('#prevstack').val();
        var separatedrecords = records.split(",");
        
        var functioninput = $('#functioninput').val();
        
        goToAndPause(counter);
        
        if(separatedrecords == '')
        {
            stackRecordNum = 0;
        }
        else
        {
            stackRecordNum = separatedrecords.length;
        }
              
        if(stackRecordNum == '0')
        {
            
            pgnumber = separatedNext.length+1;
        }
        else
        {
            if((functioninput == '')||(functioninput == 'undefined')||(functioninput == null))
            {
                pgnumber = separatedrecords.length-1;
            }
            else
            {
                pgnumber = separatedrecords.length;
            }
        }
               
        updatepgnumber(pgnumber);
        
        $('#features').load('../msm/XMLImporter/ajaxcall.php',
        {
            stackstring: $('#stack').val(),
            prevstackstring: $('#prevstack').val(),
            currentvalue: $('#current').val(),
            functionname: 'previous'
        },
        function(){
           
            $('.dialogs').dialog({
                autoOpen: false,
                height: 'auto',
                width: 605
            });
            $('.leftbox').animate({
                scrollTop: '0px'
            }, 800);
            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        });
        $('#stack').remove();
        $('#prevstack').remove();
        $('#current').remove();
        $('#functioninput').remove();
        
        stackRecordNum = null;
        pgnumber = null;
    };

    function updatepgnumber(pgnumber) {
       
        var pgnum = ''+pgnumber+'';
        document.getElementById('pg').innerHTML= 'pg.'+pgnum;
    }

    // is the rotator in mid-transition
    function isBusy() {
        return $cont.children().length>1 ? true : false;
    };

    // load a specific slide
    function goToAndPause(index) {
        $cont.children().stop(true,true);
        if((counter != index) || ((counter == index) && isBusy())){
            if(isBusy()) {
                $cont.children().eq(0).remove();
            }
            transitionTo(gallery,index);
            pause();
        };
    // added to convert the Math symbols when the pages are changed
        
       
    };

    // load images into memory
    // function preloadImg() {
    // $(gallery).each(function(i){
    // $(this).find('img').each(function(i){
    // preloadedImg[i] = $('<img>').attr('src',$(this).attr('src'));
    // });
    // });
    // };

    // generate and add play/pause, prev, next controls
    function addControls() {

        $('.controller').append('<p class="jshowoff-controls '+uniqueClass+'-controls"> <a class="jshowoff-prev '+uniqueClass+'-prev" href="#null">'+config.controlText.previous+'</a><span class="jshowoff-page" id="pg">pg.'+pagenumber+'</span><a class="jshowoff-next '+uniqueClass+'-next" href="#null">'+config.controlText.next+'</a></p>');
        $('.'+uniqueClass+'-controls a').each(function(){
            //if($(this).hasClass('jshowoff-play')) $(this).click(function(){ isPlaying() ? pause('playBtn') : play(); return false; } );
            if($(this).hasClass('jshowoff-prev')) $(this).click(function(){
                previous();
                return false;
            });
            if($(this).hasClass('jshowoff-next')) $(this).click(function(){
                next();
                return false;
            });
        });
    };
    
    gallery=null;
    $cont=null;
    timer=null;
    // var preloadedImg;
    howManyInstances=null;
    counter=null;
    uniqueClass=null;
    cssClass=null;
    $wrap=null;
    
// // generate and add slide links
// function addSlideLinks() {
// $wrap.append('<p class="jshowoff-slidelinks '+uniqueClass+'-slidelinks"></p>');
// $.each(gallery, function(i, val) {
// var linktext = $(this).attr('title') != '' ? $(this).attr('title') : i+1;
// $('<a class="jshowoff-slidelink-'+i+' '+uniqueClass+'-slidelink-'+i+'" href="#null">'+linktext+'</a>').bind('click', {
// index:i
// }, function(e){
// goToAndPause(e.data.index);
// return false;
// }).appendTo('.'+uniqueClass+'-slidelinks');
// });
// };

// end closure
})(jQuery);

//
//(function($) {
//
// $.fn.slideIt = function(settings1,callback) {
//
// // default global vars
// var config1 = {
// direction : 'left',
// showHide : 'show',
// changeSpeed : 600
// };
//
// // merge default global variables with custom variables, modifying 'config1'
// if (settings1) $.extend(config1, settings1);
//
// this.each(function(i) {
// $(this).css({
// left:'auto',
// right:'auto',
// top:'auto',
// bottom:'auto'
// });
// var measurement = (config1.direction == 'left') || (config1.direction == 'right') ? $(this).outerWidth() : $(this).outerHeight();
// var startStyle = {};
// startStyle['position'] = $(this).css('position') == 'static' ? 'relative' : $(this).css('position');
// startStyle[config1.direction] = (config1.showHide == 'show') ? '-'+measurement+'px' : 0;
// var endStyle = {};
// endStyle[config1.direction] = config1.showHide == 'show' ? 0 : '-'+measurement+'px';
// $(this).css(startStyle).animate(endStyle,config1.changeSpeed,callback);
// // end .each
// });
//
// return this;
//
// // end .slideIt
// };
//
//// end closure
//})(jQuery);

/**
//**************************************************************************
//**                              MSM                                     **
//**************************************************************************
//* @package     mod                                                      **
//* @subpackage  msm                                                      **
//* @name        msm                                                      **
//* @copyright   University of Alberta                                    **
//* @link        http://ualberta.ca                                       **
//* @author      Ga Young Kim                                             **
//* @author      Erik Kallevig (modified by Ga Young Kim)
//* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
//**************************************************************************
//**************************************************************************/
//
///* The following plugin orginally by Erik Kallevig has been changed to
// * better accommodate the use in this module
// */
//
///*
//***************************plugin by*******************************************
//Title:		jShowOff: a jQuery Content Rotator Plugin
//Author:		Erik Kallevig
//Version:	0.1.2
//Website:	http://ekallevig.com/jshowoff
//License: 	Dual licensed under the MIT and GPL licenses.
//
//jShowOff Options
//
//animatePause :		whether to use 'Pause' animation text when pausing [boolean, defaults to true]
//autoPlay :			whether to start playing immediately [boolean, defaults to true]
//changeSpeed :		speed of transition [integer, milliseconds, defaults to 600]
//controls :			whether to create & display controls (Play/Pause, Previous, Next) [boolean, defaults to true]
//controlText :		custom text for controls [object, 'play', 'pause', 'previous' and 'next' properties]
//cssClass :			custom class to add to .jshowoff wrapper [string]
//effect :			transition effect [string: 'fade', 'slideLeft' or 'none', defaults to 'fade']
//hoverPause :		whether to pause on hover [boolean, defaults to true]
//links :				whether to create & display numeric links to each slide [boolean, defaults to true]
//speed :				time each slide is shown [integer, milliseconds, defaults to 3000]
//
// ******************************************************************************/
//
//(function($) {
//    // default global vars
//    var gallery;
//    var $cont;
//    var timer;
//    //    var preloadedImg;
//    var howManyInstances;
//    var counter;
//    var uniqueClass;
//    var cssClass;
//    var $wrap;
//    var pagenumber;
// 
//    var config = {
//        animatePause : true,
//        autoPlay : true,
//        changeSpeed : 600,
//        controls : true,
//        controlText : {
//            play :		'Play',
//            pause :		'Pause',
//            next :		'Next',
//            previous :	'Previous'
//        },
//        effect : 'none',
//        hoverPause : true,
//        links : true,
//        speed : 3000
//    };
//
//
//    $.fn.jshowoff = function(settings) {
//        // merge default global variables with custom variables, modifying 'config'
//        if (settings) $.extend(true, config, settings);
//
//        // make sure speed is at least 20ms longer than changeSpeed
//        if (config.speed < (config.changeSpeed+20)) {
//            alert('jShowOff: Make speed at least 20ms longer than changeSpeed; the fades aren\'t always right on time.');
//            return this;
//        };
//        // create slideshow for each matching element invoked by .jshowoff()
//        this.each(function(i) {
//            gallery = $(this).children().remove();
//            $cont = $(this);  
//            timer = '';   
//            counter = 0;
//            preloadedImg = [];
//            howManyInstances = $('.jshowoff').length+1;		
//            uniqueClass = 'jshowoff-'+howManyInstances;
//            pagenumber = 1;
//		
//            cssClass = config.cssClass != undefined ? config.cssClass : '';
//		  
//            // set up wrapper
//            $cont.css('position','relative').wrap('<div class="jshowoff '+uniqueClass+'" />');
//            
//            if($.browser.msie) // IE counts the lenght as 1 when the other browser counts it as 2
//            {
//                $cont.children().remove();
//            }
//            
//            $wrap = $('.'+uniqueClass);
//            $wrap.css('position','relative').addClass(cssClass);
//		
//            // add first slide to wrapper
//		
//            $(gallery[0]).clone().appendTo($cont);
//            //            preloadImg();
//            
//            // add controls
//            if(config.controls){
//                addControls();
//                if(config.autoPlay==false){
//                    $('.'+uniqueClass+'-play').addClass(uniqueClass+'-paused jshowoff-paused').text(config.controlText.play);
//                };
//            };
//            //            addControls();
//            
//            //            // add slide links
//            //            if(config.links){
//            //                addSlideLinks();
//            //                $('.'+uniqueClass+'-slidelinks a').eq(0).addClass(uniqueClass+'-active jshowoff-active');
//            //            };
//            
//            // display error message if no slides present
//            if(gallery.length<1){
//                $('.'+uniqueClass).append('<p>For jShowOff to work, the container element must have child elements.</p>');
//            };
//			
//        // end .each
//        });
//	
//        return this;
//		
//    // end .jshowoff
//    };
//
//			
//    // utility for loading slides
//    function transitionTo(gallery,index) {
//        var oldCounter = counter;
//        if((counter >= gallery.length) || (index >= gallery.length)) {
//            counter = 0;
//            var e2b = true;
//        }
//        else if((counter < 0) || (index < 0)) {
//            counter = gallery.length-1;
//            var b2e = true;
//        }
//        else {
//            counter = index;
//        }
//
//        if(config.effect=='slideLeft'){
//            var newSlideDir, oldSlideDir;
//            function slideDir(dir) {
//                newSlideDir = dir=='right' ? 'left' : 'right';
//                oldSlideDir = dir=='left' ? 'left' : 'right';					
//            };
//		
//            counter >= oldCounter ? slideDir('left') : slideDir('right') ;
//
//            $(gallery[counter]).clone().appendTo($cont).slideIt({
//                direction:newSlideDir,
//                changeSpeed:config.changeSpeed
//            });
//            if($cont.children().length>1){
//                $cont.children().eq(0).css('position','absolute').slideIt({
//                    direction:oldSlideDir,
//                    showHide:'hide',
//                    changeSpeed:config.changeSpeed
//                },function(){
//                    $(this).remove();
//                });
//            };
//        } else if (config.effect=='fade') {
//            $(gallery[counter]).clone().appendTo($cont).hide().fadeIn(config.changeSpeed,function(){
//                if($.browser.msie)this.style.removeAttribute('filter');
//            });
//            if($cont.children().length>1){
//                $cont.children().eq(0).css('position','absolute').fadeOut(config.changeSpeed,function(){
//                    $(this).remove();
//                });
//            };
//        } else if (config.effect=='none') {
//            $(gallery[counter]).clone().appendTo($cont);
//            if($cont.children().length>1){
//                $cont.children().eq(0).css('position','absolute').remove();
//            };
//        };
//				
//        // update active class on slide link
//        if(config.links){
//            $('.'+uniqueClass+'-active').removeClass(uniqueClass+'-active jshowoff-active');
//            $('.'+uniqueClass+'-slidelinks a').eq(counter).addClass(uniqueClass+'-active jshowoff-active');
//        };
//    };	
//    // is the rotator currently in 'play' mode
//    //    function isPlaying(){
//    //        return $('.'+uniqueClass+'-play').hasClass('jshowoff-paused') ? false : true;
//    //    };
//    //            
//    // stop slide rotation
//    function pause(src) {
//        clearInterval(timer);
//        
//        if(!src || src=='playBtn') $('.'+uniqueClass+'-play').text(config.controlText.play).addClass('jshowoff-paused '+uniqueClass+'-paused');
//        if(config.animatePause && src=='playBtn'){
//            alert("in condition 2 of pause");
//            $('<p class="'+uniqueClass+'-pausetext jshowoff-pausetext">'+config.controlText.pause+'</p>').css({
//                fontSize:'62%', 
//                textAlign:'center', 
//                position:'absolute', 
//                top:'40%', 
//                lineHeight:'100%', 
//                width:'100%'
//            }).appendTo($wrap).addClass(uniqueClass+'pauseText').animate({
//                fontSize:'600%', 
//                top:'30%', 
//                opacity:0
//            }, {
//                duration:500,
//                complete:function(){
//                    $(this).remove();
//                }
//            });
//        }
//    };
//			
//    // load the next slide
//    function next() {      
//        
//        var stackRecordNum;
//        var pgnumber;
//        
//        var prevrecords = $('#prevstack').val();
//        var separatedprevrecords = prevrecords.split(",");
//        
//        var records = $('#stack').val();
//        var separatedrecords = records.split(",");
//        
//        var functioninput = $('#functioninput').val();
//        
//        goToAndPause(counter);
//        // at the end of the book and press next, it should go back to the beginning of the book
//        if(separatedrecords == '')
//        {
//            stackRecordNum = 0;
//        }
//        else
//        {
//            stackRecordNum = separatedrecords.length;
//        }
//        
//        console.log(separatedprevrecords);
//        
//        if(stackRecordNum == '0')
//        {
//            pgnumber = 1;
//        }       
//        else if(separatedprevrecords == '')
//        {
//            pgnumber =2;
//        }
//        else
//        {
//                
//            console.log(functioninput);
//            if((functioninput == '')||(functioninput == 'undefined')||(functioninput == null))
//            {
//                pgnumber = separatedprevrecords.length+1;
//            }
//            else
//            {
//                //                alert(functioninput);
//                pgnumber = separatedprevrecords.length+2;
//            }
//            
//        }
//        
//        updatepgnumber(pgnumber);      
//        
//        
//        $('#features').load('../msm/XMLImporter/ajaxcall.php', 
//        {                    
//            stackstring: $('#stack').val(),
//            prevstackstring: $('#prevstack').val(),
//            currentvalue: $('#current').val(),
//            functionname: 'next'
//        },
//        function(){     
//                    
//            $('.dialogs').dialog({
//                autoOpen: false,
//                height: 'auto',
//                width: 605
//            });
//            $('.leftbox').animate({
//                scrollTop: '0px'
//            }, 800);
//            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);    
//        });
//        $('#stack').remove();
//        $('#prevstack').remove();
//        $('#current').remove();
//        $('#functioninput').remove();
//        
//        stackRecordNum = null;    
//        pgnumber = null;
//    };
//
//    // load the previous slide
//    function previous() {
//        var stackRecordNum;    
//        var pgnumber;
//        
//        var nextrecords = $('#stack').val();
//        var separatedNext = nextrecords.split(",");
//        
//        var records = $('#prevstack').val();
//        var separatedrecords = records.split(",");
//        
//        var functioninput = $('#functioninput').val();
//        
//        goToAndPause(counter);  
//        
//        if(separatedrecords == '')
//        {
//            stackRecordNum = 0;
//        }
//        else
//        {
//            stackRecordNum = separatedrecords.length;
//        }
//              
//        if(stackRecordNum == '0')
//        {
//            
//            pgnumber = separatedNext.length+1;
//        }
//        else
//        {
//            if((functioninput == '')||(functioninput == 'undefined')||(functioninput == null))
//            {
//                pgnumber = separatedrecords.length-1;
//            }
//            else
//            {
//                pgnumber = separatedrecords.length;
//            }
//        }       
//               
//        updatepgnumber(pgnumber);
//        
//        $('#features').load('../msm/XMLImporter/ajaxcall.php', 
//        {
//            stackstring: $('#stack').val(),
//            prevstackstring: $('#prevstack').val(),
//            currentvalue: $('#current').val(),
//            functionname: 'previous'
//        },
//        function(){     
//           
//            $('.dialogs').dialog({
//                autoOpen: false,
//                height: 'auto',
//                width: 605
//            });
//            $('.leftbox').animate({
//                scrollTop: '0px'
//            }, 800);
//            MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
//        });
//        $('#stack').remove();
//        $('#prevstack').remove();
//        $('#current').remove();
//        $('#functioninput').remove();
//        
//        stackRecordNum = null;    
//        pgnumber = null;
//    };
//			
//    function updatepgnumber(pgnumber) {
//        var pgnum = ''+pgnumber+'';
//        $("#msm_slide_pageNumber").val(pgnum);
//    }
//			
//    // is the rotator in mid-transition
//    function isBusy() {
//        return $cont.children().length>1 ? true : false;
//    };
//			
//    // load a specific slide
//    function goToAndPause(index) {
//        $cont.children().stop(true,true);
//        if((counter != index) || ((counter == index) && isBusy())){
//            if(isBusy()) {
//                $cont.children().eq(0).remove();
//            }
//            transitionTo(gallery,index);
//            pause();
//        };
//    // added to convert the Math symbols when the pages are changed
//        
//       
//    };
//
//    // load images into memory
//    //    function preloadImg() {
//    //        $(gallery).each(function(i){
//    //            $(this).find('img').each(function(i){
//    //                preloadedImg[i] = $('<img>').attr('src',$(this).attr('src'));					
//    //            });
//    //        });
//    //    };
//				
//    // generate and add play/pause, prev, next controls
//    function addControls() {
//
//        $('.controller').append('<div class="jshowoff-controls '+uniqueClass+'-controls">\n\
//                                     <a class="jshowoff-prev //'+uniqueClass+'-prev" href="#null">'+config.controlText.previous+'</a>\n\
//                                    <div id="msm_pgnumber_container" style="display:inline; position:relative;" ondblclick="changePage();"><span class="jshowoff-page" id="pg">pg.\n\
//                                       <input id="msm_slide_pageNumber" name="msm_slide_pageNumber" disabled="disabled" value="//'+pagenumber+'"/>\n\
//                                    </span></div>\n\
//                                    <a class="jshowoff-next //'+uniqueClass+'-next" href="#null">'+config.controlText.next+'</a>\n\
//                                </div>//');
//        
//        $('.'+uniqueClass+'-controls a').each(function(){
//            //if($(this).hasClass('jshowoff-play')) $(this).click(function(){ isPlaying() ? pause('playBtn') : play(); return false; } );
//            if($(this).hasClass('jshowoff-prev')) $(this).click(function(){
//                previous();
//                return false;
//            });
//            if($(this).hasClass('jshowoff-next')) $(this).click(function(){        
//                next();
//                return false;
//            });
//        });
//        
//    };
//    
//    
//    
//    gallery=null;
//    $cont=null;
//    timer=null;
//    //    var preloadedImg;
//    howManyInstances=null;
//    counter=null;
//    uniqueClass=null;
//    cssClass=null;
//    $wrap=null;
//    
////    // generate and add slide links
////    function addSlideLinks() {
////        $wrap.append('<p class="jshowoff-slidelinks '+uniqueClass+'-slidelinks"></p>');
////        $.each(gallery, function(i, val) {
////            var linktext = $(this).attr('title') != '' ? $(this).attr('title') : i+1;
////            $('<a class="jshowoff-slidelink-'+i+' '+uniqueClass+'-slidelink-'+i+'" href="#null">'+linktext+'</a>').bind('click', {
////                index:i
////            }, function(e){
////                goToAndPause(e.data.index);
////                return false;
////            }).appendTo('.'+uniqueClass+'-slidelinks');
////        });
////    };	
//
//// end closure
//})(jQuery);
//
////
////(function($) {
////
////    $.fn.slideIt = function(settings1,callback) {
////	
////        // default global vars
////        var config1 = {
////            direction : 'left',
////            showHide : 'show',
////            changeSpeed : 600
////        };
////		
////        // merge default global variables with custom variables, modifying 'config1'
////        if (settings1) $.extend(config1, settings1);
////		
////        this.each(function(i) {	
////            $(this).css({
////                left:'auto',
////                right:'auto',
////                top:'auto',
////                bottom:'auto'
////            });
////            var measurement = (config1.direction == 'left') || (config1.direction == 'right') ? $(this).outerWidth() : $(this).outerHeight();
////            var startStyle = {};
////            startStyle['position'] = $(this).css('position') == 'static' ? 'relative' : $(this).css('position');
////            startStyle[config1.direction] = (config1.showHide == 'show') ? '-'+measurement+'px' : 0;
////            var endStyle = {};
////            endStyle[config1.direction] = config1.showHide == 'show' ? 0 : '-'+measurement+'px';
////            $(this).css(startStyle).animate(endStyle,config1.changeSpeed,callback);
////        // end .each
////        });
////	
////        return this;
////		
////    // end .slideIt
////    };
////
////// end closure
////})(jQuery);
//
//function changePage()
//{
//    $("#msm_slide_pageNumber").removeAttr("disabled");
//       
//    $("#msm_slide_pageNumber").keypress(function(e){
//        var beforePrevString = $("#prevstack").val();
//        var beforeNextString = $("#stack").val();
//        var beforeCurrentString = $("#current").val();
//        var pagenumber = '';
//        
//        if(beforePrevString == '')
//        {
//            if(beforeCurrentString != '')
//            {
//                pagenumber = 1;
//            }
//            else
//            {
//                pagenumber = 2;
//            }
//        }
//        else
//        {
//            pagenumber = beforePrevString.split(",").length+1;
//        }
//        
//        var flag = true;
//            
//        if(e.which == 13) // enter key is pressed
//        {
//            var prevString = null;
//            var nextString = null;
//            var currentString = null;
//            var pgDiff = null;
//                  
//            var pgNum = $("#msm_slide_pageNumber").val();           
//                   
//            if(pgNum > pagenumber)
//            {
//                pgDiff = pgNum-pagenumber-1;
//                           
//                for(var i=pgDiff; i > 0; i--)
//                {
//                    prevString = $("#prevstack").val();
//                    nextString = $("#stack").val();
//                    currentString = $("#current").val();
//                        
//                    if(currentString == '')
//                    {
//                        alert("Invalid Page Number!");
//                        flag = false;
//                        $("#prevstack").val(beforePrevString);
//                        $("#stack").val(beforeNextString);
//                        $("#current").val(beforeCurrentString);
//                        break;
//                    }
//                   
//                    $("#prevstack").val(currentString+","+prevString);
//                    var nextArray = nextString.split(",");
//                    $("#current").val(nextArray[nextArray.length-1]);
//                    var newNextString = nextArray[0];
//                                  
//                    for(var j=1; j < nextArray.length-1; j++)
//                    {
//                        newNextString += ","+nextArray[j];
//                    }
//                    $("#stack").val(newNextString);
//                }
//                           
//            }
//            else if(pgNum < pagenumber)
//            {
//                pgDiff = pagenumber-pgNum-1;
//                        
//                for(var i=pgDiff; i > 0; i--)
//                {
//                    prevString = $("#prevstack").val();
//                    nextString = $("#stack").val();
//                    currentString = $("#current").val();
//                   
//                    if(currentString == '')
//                    {
//                        alert("Invalid Page Number!");
//                        flag = false;
//                        $("#prevstack").val(beforePrevString);
//                        $("#stack").val(beforeNextString);
//                        $("#current").val(beforeCurrentString);
//                        break;
//                    }
//                        
//                    $("#stack").val(nextString+","+currentString);
//                    var prevArray = prevString.split(",");
//                    $("#current").val(prevArray[0]);
//                    var newPrevString = prevArray[1];
//                                  
//                    for(var j=2; j < prevArray.length-1; j++)
//                    {
//                        newPrevString += ","+prevArray[j];
//                    }
//                    $("#prevstack").val(newPrevString);
//                }
//            }
//            
//            if(flag)
//            {
//                updatepgnumber(pgNum); 
//                $("#msm_slide_pageNumber").attr("disabled", "disabled");
//        
//        
//                $('#features').load('../msm/XMLImporter/ajaxcall.php', 
//                {                    
//                    stackstring: $('#stack').val(),
//                    prevstackstring: $('#prevstack').val(),
//                    currentvalue: $('#current').val(),
//                    functionname: 'next'
//                },
//                function(){     
//                    
//                    $('.dialogs').dialog({
//                        autoOpen: false,
//                        height: 'auto',
//                        width: 605
//                    });
//                    $('.leftbox').animate({
//                        scrollTop: '0px'
//                    }, 800);
//                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);    
//                });
//                $('#stack').remove();
//                $('#prevstack').remove();
//                $('#current').remove();
//                $('#functioninput').remove();
//            }
//            else
//            {
//                updatepgnumber(pagenumber); 
//                $("#msm_slide_pageNumber").attr("disabled", "disabled");
//            }
//        }
//            
//    });
//}