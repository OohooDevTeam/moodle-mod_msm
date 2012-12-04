/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $("#msm_setting_form").submit(function(event) { 
        // prevents page from navigating to the form posted php page
        event.preventDefault();
        
        var formData = $("#msm_setting_form").serializeArray();
        //        var targetURL = $("#msm_setting_form").attr("action");  
   
        var inputValue = '';
        var value = '';
        var isEmpty = false;
        
        $(formData).each(function(index, element){
            if(element.value == "Others")
            {
                if($("#msm_type_specifiedType").val() == '')
                {
                    $("<div class='dialogs' id='msm_emptyComposition'> Please specify the type of your composition. </div>").appendTo('#msm_type_specifiedType');
                                        
                    $("#msm_emptyComposition").dialog({
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $('#msm_type_specifiedType').css('border-color', '#FFA500');
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                    isEmpty = true;
                }
            }
            
            if(!isEmpty)
            {
                if(index != formData.length-1)
                {
                    if(element.value != '')
                    {
                        inputValue += element.name + "|" + element.value + ",";

                    }
                    else
                    {
                        if(element.name != "msm_type_input")
                        {
                            value = $('#'+element.name).attr("placeholder").split(")");
                            if(value.length == 2)
                            {
                                inputValue += element.name + "|" + $.trim(value[1]) + ",";
                            }
                        }                        
                    }
                }
                else
                {
                    if(element.value != '')
                    {
                        inputValue += element.name + "|" + element.value;
                    }
                    else
                    {
                        value = $('#'+element.name).attr("placeholder").split(")");
                        if(value.length == 2)
                        {
                            inputValue += element.name + "|" + $.trim(value[1]);
                        }
                    }
                }
            }
            
            
        });
        
        
        if(!isEmpty)
        {
            $('#msm_unit_name_input').val(inputValue);
            $("#msm_setting_dialog").dialog("close");
        }
         
      
        
    //        var newdata = [];
    //        $.ajax({
    //            type: "POST",
    //            url: targetURL,
    //            data: formData,
    //            success: function(d) { 
    //                
    //                
    //            },
    //            error: function() {
    //                alert("error in ajax at saveSetting.js");
    //            }
    //        });
        
    });
});
