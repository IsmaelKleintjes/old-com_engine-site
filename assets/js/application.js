function getFileNames(fileElement, type)
{
    var files = fileElement.files;
    var filesViewer = jQuery('table#added-' + type);
    filesViewer.show();
    filesViewer = jQuery('table#added-' + type + ' > tbody');
    filesViewer.empty();

    var uploads = [];

    jQuery(files).each(function(){
        if(uploads.length < 5) {
            var html = '';
            html += '<tr>';
                html += '<td class="file-name">';
                html += jQuery(this)[0].name;
                html += '</td>';
                html += '<td class="remove-file">';
                    html += '<a href="javascript:void(0)" data-name="' + jQuery(this)[0].name.toLowerCase() + '-' + jQuery(this)[0].size + '"><span class="fa fa-times"></span></a>';
                html += '</td>';
            html += '</tr>';

            jQuery(filesViewer).append(html);
            uploads.push(jQuery(this)[0].name.toLowerCase() + '-' + jQuery(this)[0].size);
        }
    });

    jQuery('input[name="jform[uploads_' + type + ']"]').val(JSON.stringify(uploads));
}

jQuery(document).on('click', 'td.remove-file > a', function(){
    if(!confirm('Weet je zeker dat je dit bestand wil verwijderen?')){
        return;
    }

    var type = jQuery(this).closest('table').attr('id').replace('added-', '');
    var selector = jQuery('input[name="jform[uploads_' + type + ']"]');

    var uploads = jQuery.parseJSON(selector.val());

    uploads.splice(uploads.indexOf(jQuery(this).data('name')), 1);

    selector.val(JSON.stringify(uploads));

    jQuery(this).closest('tr').remove();
});

var Other = 'OVERIGE';

jQuery( "#jform_brand" ).on( "change", function() {
    getModels(jQuery(this).val(), "Model");
});

function getModels(brandValue, e) {
    var element = !!document.getElementById("jform_model") && document.getElementById("jform_model");
    element.options.length = 0, element.options[0] = new Option(e, "");
    var i = models[brandValue],
        o = 1;
    for (key in i) element.options[o++] = new Option(i[key], key)
    jQuery("#jform_model").each(function() {
        jQuery(this).append(jQuery("<option value=" + Other + ">" + Other + "</option>"))
    })

    jQuery('#jform_model').trigger("liszt:updated");
}