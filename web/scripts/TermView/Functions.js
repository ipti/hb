/**
 * Post the FORM to add a new Address.
 * 
 * @param {form} $form
 */
function submitUpdateTerm(tid) {
    $.ajax({
        url: "/index.php?r=term/up&id=" + tid
    }).done(function () {
        $.pjax.reload({container: '#pjaxTerm'});
    });
}