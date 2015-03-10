/**
 * Post the FORM to add a new Address.
 * 
 * @param {form} $form
 */
function submitLetter($form) {
    $.ajax({
        url: $form.attr("action"),
        method: $form.attr("method"),
        data: $form.serialize()
    }).done(function (result) {
        $(".report-body *").remove();
        $(".report-body").html(result);
    });
};