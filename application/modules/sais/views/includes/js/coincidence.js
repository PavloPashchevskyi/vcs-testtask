function selectCoincidenceAttribute(attribute) {
    $.ajax({
        type: "post",
        url:"/sais/"+attribute+"/select",
        dataType:"json",
        success: function(response) {
            for(key in response) {
                $('select[name="select'+attribute+'"]').append(
                        '<option value="'+response[key][''+attribute+'_id']+'">'+response[key][''+attribute+'_name']+'</option>'
                        );
            }
        },
        error: function() {
            console.log('Unable to select coincidence attribute '+attribute+'!');
        }
    });
}

function addCoincidence() {
    var conc_id=$('select[name="selectconclusion"] option:selected').val(),
        cond_id=$('select[name="selectcondition"] option:selected').val(),
        pr=$('#cbPresence').prop('checked');
    $.ajax({
        type:"post",
        url:"/sais/coincidence/save",
        dataType:"json",
        data: {
            conclusion_id:conc_id,
            condition_id:cond_id,
            presence:pr
        },
        success: function() {
            window.location.replace("/sais/coincidence/add");
        },
        error: function() {
            console.log('Unable to save coincidence!');
        }
    });
}

function addCoincidenceAttribute(attribute) {
    var attr_name=$('input[name="'+attribute+'_name"]').val();
    $.ajax({
        type:"post",
        url:"/sais/coincidence/add"+attribute,
        dataType:"json",
        data: {
            conclusion_name: (attribute=='conclusion') ? attr_name : null,
            condition_name: (attribute=='condition') ? attr_name : null, 
        },
        success: function() {
            window.location.replace("/sais/coincidence/add");
        },
        error: function() {
            console.log('Unable to save coincidence attribute '+attribute);
        }
    });
}

function editCoincidence() {
    var coinc_id = $('input[name="coincidence_id"]').val(),
        conc = $('select[name="selectconclusion"] option:selected').val(),
        cond = $('select[name="selectcondition"] option:selected').val(),
        pr = $('input[name="presence"]').prop('checked');
    $.post("/sais/coincidence/edit/"+coinc_id, {
            new_conclusion_id:conc,
            new_condition_id:cond,
            new_presence:pr
    }, null, "json");
}

function editCoincidenceAttribute(attribute) {
    var coinc_id = $('input[name="coincidence_id"]').val(),
        attr_name = $('input[name="'+attribute+'_name"]').val();
    var ajaxObject = {
        type:"post",
        url:"/sais/coincidence/edit"+attribute+"/"+coinc_id,
        dataType:"json",
        data: {
            new_conclusion_name:(attribute == 'conclusion') ? attr_name : null,
            new_condition_name:(attribute == 'condition') ? attr_name : null
        },
        success: function() {
            window.location.replace("/sais/coincidence/prepareedit/"+coinc_id);
        },
        error: function() {
            console.log('Unable to edit '+attribute);
        }
    }
    $.ajax(ajaxObject);
}

function deleteCoincidence() {
    var coinc_id = $('input[name="coincidence_id"]').val(),
        confirm = window.confirm("Do you really want to delete coincidence #"+coinc_id+'?');
    if(confirm) {
        $.post("/sais/coincidence/delete/"+coinc_id, null, function() {
            window.location.replace("/sais/coincidence/show");
        }, "json");
    }
}

function deleteCoincidenceAttribute(attribute) {
    var coinc_id = $('input[name="coincidence_id"]').val(),
        confirm = window.confirm(
            "If you delete a conclusion, all records related to this conclusion will be deleted from the detail tables. Do you really want to delete?"
            );
    if(confirm) {
        $.post("/sais/coincidence/delete"+attribute+"/"+coinc_id, null, function() {
            window.location.replace("/sais/coincidence/show");
        }, "json");
    }
}

$(document).ready(function() {
    $('#divconclusion').css("display", "none");
    $('#divcondition').css("display", "none");
    var conclusionclicksq = 0;
    $('#buttonconclusion').click(function() {
        conclusionclicksq++;
        if(conclusionclicksq % 2 != 0) {
            $('#divconclusion').css("display", "block");
        }
        else {
            $('#divconclusion').css("display", "none");
        }
    });
    var conditionclicksq = 0;
    $('#buttoncondition').click(function() {
        conditionclicksq++;
        if(conditionclicksq % 2 != 0) {
            $('#divcondition').css("display", "block");
        }
        else {
            $('#divcondition').css("display", "none");
        }
    });
    var conclusionfocusesq = 0;
    $('select[name="selectconclusion"]').focus(function() {
        conclusionfocusesq++;
        if(conclusionfocusesq == 1) {
            selectCoincidenceAttribute("conclusion");
        }
    });
    var conditionfocusesq = 0;
    $('select[name="selectcondition"]').focus(function() {
        conditionfocusesq++;
        if(conditionfocusesq == 1) {
            selectCoincidenceAttribute("condition");
        }
    });
    $('input[name="submitcoincidence"]').click(function() {
        if($('select[name="selectconclusion"] option:selected').val()>=1 && $('select[name="selectcondition"] option:selected').val()>=1) {
            addCoincidence();
        }
        else {
            $('#divmessages').text('Condition and conclusion must be selected');
        }
    });
    $('input[name="submitconclusion"]').click(function() {
        if($('input[name="conclusion_name"]').val().length>=1) {
            addCoincidenceAttribute('conclusion');
        }
        else {
            $('#divmessages').text('Conclusion field must not be empty!');
        }
    });
    $('input[name="submitcondition"]').click(function() {
        if($('input[name="condition_name"]').val().length>=1) {
            addCoincidenceAttribute('condition');
        }
        else {
            $('#divmessages').text('Condition field must not be empty!');
        }
    });
    $('input[name="submitnewcoincidence"]').click(function() {
        editCoincidence();
    });
    $('input[name="submitnewconclusion"]').click(function() {
        editCoincidenceAttribute('conclusion');
    });
    $('input[name="submitnewcondition"]').click(function() {
        editCoincidenceAttribute('condition');
    });
    $('input[name="deletecoincidence"]').click(function() {
        deleteCoincidence();
    });
    $('input[name="deleteconclusion"]').click(function() {
        deleteCoincidenceAttribute('conclusion');
    });
    $('input[name="deletecondition"]').click(function() {
        deleteCoincidenceAttribute('condition');
    });
    $('a[id^="delete_coincidence_"]').click(function() {
        deleteCoincidence();
    });
});
