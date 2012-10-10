/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function navToPage(unitid)
{
    var nextRecords = $('#stack').val();
    var prevRecords = $('#prevstack').val();
    var currentRecord = $('#current').val();
    var selected = null;
    
    //    console.log('currentRecord: '+ currentRecord);
    
    var newCurrentData = null;
    var newNextData = null;
    var newPrevData = null;
    
    var eachCurrentData = currentRecord.split('/');
    
    $(this).ready(function() {    
        var nextArray = nextRecords.split(',');
        var prevArray = prevRecords.split(',');
        //usually in sets of 4
        if(eachCurrentData.legnth > 3)
        {
            if(eachCurrentData[1] == unitid)
            {
                selected = 'current';                        
            }
        }
        
        if(selected == 'current')
        {
            alert('This is the current page.');
        }
        
        // the target unit page is not the current page
        // first search for the id number in the next array
        else if(selected == null)
        {
            $.each(nextArray, function(i, nextInfo) {
                var eachNextData = nextInfo.split('/');
               
                if(eachNextData.length > 3)
                {
                    if(eachNextData[1] ==  unitid)
                    {
                        selected = i;
                        return false;
                    }
                }
            });
            
            if(selected != null)
            {
                //            alert(selected);
                prevArray.push(currentRecord);
          
                while((nextArray.length-1) >= (selected+1))
                {
                    var lastElement = nextArray.pop();
                    prevArray.push(lastElement);
                }
            
                // at selected index
                newCurrentData= nextArray.pop();
            
                for(var i=0; i < prevArray.length-1; i++)
                {
                    newPrevData += prevArray[i] + ',';
                }
                newPrevData += prevArray[prevArray.length-1];
                
                for(var i=0; i < nextArray.length-1; i++)
                {
                    newNextData += nextArray[i] + ',';
                }
                newNextData += nextArray[nextArray.length-1];
            
                $('#features').load('../msm/XMLImporter/ajaxcall.php', 
                {
                    stackstring: newNextData,
                    prevstackstring: newPrevData,
                    currentvalue: newCurrentData,
                    functionname: 'toc'
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
            }
        }        
       
        if(selected == null)
        {
            $.each(prevArray, function(i, prevInfo) {
                var eachPrevData = prevInfo.split('/');
               
                if(eachPrevData.length > 3)
                {
                    if(eachPrevData[1] ==  unitid)
                    {
                        selected = i;
                        return false;
                    }
                }
            });
            
            //previous array has the unit value
            if(selected != null)
            {
                //            alert(selected);
                nextArray.push(currentRecord);
          
                while((prevArray.length-1) >= (selected+1))
                {
                    var lastElement = prevArray.pop();
                    nextArray.push(lastElement);
                }
            
                // at selected index
                newCurrentData= prevArray.pop();
            
                for(var i=0; i < prevArray.length-1; i++)
                {
                    newPrevData += prevArray[i] + ',';
                }
                newPrevData += prevArray[prevArray.length-1];
                
                for(var i=0; i < nextArray.length-1; i++)
                {
                    newNextData += nextArray[i] + ',';
                }
                newNextData += nextArray[nextArray.length-1];
            
                $('#features').load('../msm/XMLImporter/ajaxcall.php', 
                {
                    stackstring: newNextData,
                    prevstackstring: newPrevData,
                    currentvalue: newCurrentData,
                    functionname: 'toc'
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
            }
        }      
   
    });   
}
