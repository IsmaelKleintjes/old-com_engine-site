
jQuery(document).ready(function(){
	
	jQuery(".imagePreview").fancybox();
	
	
	var settings = 
	{
		'auto'             : true,
		'buttonText'       : 'Selecteer bestanden',
		'buttonClass'      : 'uploadButton btn',
		'fileSizeLimit'    : sizeLimit,
		'fileType'         : 'image',
		'queueID'          : 'queue',
		'queueSizeLimit'   : 10,
		'width'            : '150',
		'formData'         : {
			object         : object,
			object_id      : object_id
		},
		'uploadScript'     : 'index.php?option=com_engine&task=media4u.save',
		'onUploadComplete' : function(file, data){
			var data = jQuery.parseJSON(data);
			array = [];
			for( var i in data ) {
				array[i] = data[i];
			}
			if(typeof(array["error"]) == "string"){
				if(array["error"] == "false"){
					if(array["default"] == "1"){
						var star = "star";
					} else {
						var star = "star-empty";
					}
					message = "<tr object_id='"+array["id"]+"'><td class='order hidden-phone'><span><i class='icon-menu icon-align-justify sortable-handler'></i></span></td><td class='phone-center'><div class='btn uploadDefault btn-small'><i class='icon-"+star+"'></i></div></td><td><center><img class='uploadImage' src='..//"+array["url"]+"' /></center></td><td class='phone-center'><div class='btn btn-danger btn-mini uploadDelete'><i class='icon-remove icon-trash icon-white'></i></div></td></tr>";
					i = jQuery(".table tbody").children().length;
					
					jQuery(".table tbody").append(message);
					jQuery("#uploadMessages").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4>Succes!</h4>Bestand is opgeslagen</div>").show();
				} else {
					jQuery("#uploadErrors").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button><h4>Fout!</h4>"+array["error"]+"</div>").show();
				}
			}
		},
		'onQueueComplete'  : function(uploads){
			setTimeout(function(){
				jQuery('#file_upload').uploadifive("clearQueue");
				jQuery("#uploadMessages, #uploadErrors").fadeOut(500);
			}, 3000);
		},
		'onFallback': function(){
			alert('no HTML5');
			jQuery("#file_upload").uploadify({
				'uploader': settings.uploadScript,
				'swf': "components/com_engine/assets/uploadify.swf",
				'formData': settings.formData,
				'onUploadSuccess': settings.onUploadComplete,
				'onQueueComplete': function(uploads){
					setTimeout(function(){
						jQuery('#file_upload').uploadify("cancel", "*");
						jQuery("#uploadMessages, #uploadErrors").delay(1000).fadeOut(500);
					}, 3000);
				},
				'onFallback' : function() {
					alert('Flash was not detected.');
				}
			});
		
		
    	}
	}

	jQuery('#file_upload').uploadifive( settings );

	jQuery("body").delegate(".uploadDelete", "click", function(){
		if(confirm("Weet u zeker dat u dit bestand wil verwijderen?")){
			id = jQuery(this).parents("tr").attr("object_id");
			index = jQuery(".table tbody tr").index(jQuery(this).parents("tr"));
			jQuery(this).parents("tr").remove();
			jQuery.ajax({
				url: 'index.php?option=com_engine&task=media4u.delete',
				type: 'POST',
				data: {
					id: id
				}
			});
		}
	});
	
	jQuery("body").delegate(".uploadDefault", "click", function(){
		id = jQuery(this).parents("tr").attr("object_id");
		jQuery(".icon-star").addClass("icon-star-empty").removeClass("icon-star");
		jQuery(this).children("i").addClass("icon-star").removeClass("icon-star-empty");
		jQuery.ajax({
			url: 'index.php?option=com_engine&task=media4u.setDefault',
			type: 'POST',
			data: {
				id: id,
				object_id: object_id
			}
		});
	});
	jQuery(".uploadLabel").focusout(function(e){
		label = jQuery(this).val();
		id = jQuery(this).parents("tr").attr("object_id");
		jQuery.ajax({
			url: 'index.php?option=com_engine&task=media4u.saveImage',
			type: 'POST',
			data: {
				label: label,
				id: id
			}
		});
	});	
	
	jQuery(".sortable tbody").sortable({
		axis: 'y',
		handle: '.sortable-handler',
		cursor: 'n-resize',
		opacity: 0.5,
		helper:function (e, ui) {
			ui.children().each(function () {
				jQuery(this).width(jQuery(this).width());
			});
			return ui;
		},
		stop: function(event, ui){
			sortRows();
		}
	});	
});

function sortRows()
{
	i = 0;
	var items = [];
	jQuery(".sortable tbody tr").each(function(){
		items[i] = jQuery(this).attr("object_id");
		i++;
	});

	jQuery.ajax({
		url: 'index.php?option=com_engine&task=media4u.saveOrder',
		type: 'POST',
		dataType: 'JSON',
		data: {
			data: JSON.stringify(items, null, 2)
		}
	});	
}