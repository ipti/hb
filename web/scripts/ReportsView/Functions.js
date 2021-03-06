/**
 * Post the FORM to add a new Address.
 * 
 * @param {form} $form
 */
function submitConsultationLetter($form) {
    $.ajax({
        url: $form.attr("action"),
        method: $form.attr("method"),
        data: $form.serialize()
    }).done(function (result) {
        $(".report-body *").remove();
        $(".report-body").html(result);
        $(".report-content").show();
        $("#print-button").show();
        
    });
};

function submitAnamnese($form){
    $.ajax({
        url: $form.attr("action"),
        method: $form.attr("method"),
        data: $form.serialize()
    }).done(function (result) {
        $("#anamnese-header *").remove();
        $("#anamnese-header").html(result.student);
        //$("#prescription *").remove();
        $("#prescription").html(result.prescription);
        $(".report-content").show();
        $("#print-button").show();
        
    });
};

$(document).ready(function(){
    $(this).closest('form').get(0).reset();
});