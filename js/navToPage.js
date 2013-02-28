/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function navToPage(unitid)
{
    var nextRecords = $('#stack').val();
    var prevRecords = $('#prevstack').val();
    var currentRecord = $('#current').val();
    var newCurrentData='';
    var newNextData='';
    var newPrevData='';
    var pgnumber = null;
     var selected = null;
    
    var eachCurrentData = currentRecord.split('/');
    
    $(this).ready(function() {    
       
        var nextArray = nextRecords.split(',');
        var prevArray = prevRecords.split(',');
        
        //usually in sets of 4
        if(eachCurrentData.length > 3)
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
                if(prevArray == '')
                {
                    prevArray[0] = currentRecord;
                }
                else
                {
                    prevArray.push(currentRecord);
                }
          
                while((nextArray.length-1) >= (selected+1))
                {
                    var lastElement = nextArray.pop();
                    prevArray.push(lastElement);
                }
                
                newCurrentData= nextArray.pop();               
            
                for(var i=0; i < prevArray.length-1; i++)
                {
                    if((prevArray[i] != '')||(prevArray[i] != null)||(prevArray[i] != 'undefined'))
                    {
                        newPrevData += prevArray[i] + ',';

                    }
                }
               
                if(prevArray.length-1 > 0)
                {
                    newPrevData += prevArray[prevArray.length-1]; 
                }
                
                for(var i=0; i < nextArray.length-1; i++)
                {
                    if((nextArray[i] != '')||(nextArray[i] != null)||(nextArray[i] != 'undefined'))
                    {
                        newNextData += nextArray[i] + ',';

                    }
                }
                                
                if(nextArray.length-1 > 0)
                {
                    newNextData += nextArray[nextArray.length-1];
                }
                
               
                pgnumber = prevArray.length+1;
                updatepgnumber(pgnumber);
           
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
                if(nextArray == '')
                {
                    nextArray[0] = currentRecord;
                }
                else
                {
                    nextArray.push(currentRecord);
                }
                
                while((prevArray.length-1) >= (selected+1))
                {
                    var lastElement = prevArray.pop();                   
                    nextArray.push(lastElement);
                }
            
                // at selected index
                newCurrentData= prevArray.pop();
            
                for(var i=0; i < prevArray.length-1; i++)
                {
                    if((prevArray[i] != '')||(prevArray[i] != null)||(prevArray[i] != 'undefined'))
                    {
                        newPrevData += prevArray[i] + ',';

                    }
                }
                
                if(prevArray.length-1 > 0)
                {
                    newPrevData += prevArray[prevArray.length-1]; 
                }
                
                for(var i=0; i < nextArray.length-1; i++)
                {
                    if((nextArray[i] != '')||(nextArray[i] != null)||(nextArray[i] != 'undefined'))
                    {
                        newNextData += nextArray[i] + ',';

                    }
                }
                if(nextArray.length=1 > 0)
                {
                    newNextData += nextArray[nextArray.length-1];
                }
                
                pgnumber = prevArray.length+1;
                updatepgnumber(pgnumber);
                
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

function updatepgnumber(pgnumber) {
       
    var pgnum = ''+pgnumber+'';
    document.getElementById('pg').innerHTML= 'pg.'+pgnum;
}
