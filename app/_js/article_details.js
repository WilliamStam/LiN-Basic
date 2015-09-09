$(document).on("click",".image-modal",function(e){
	e.preventDefault();
	var src = $(this).attr("href");
	var str = '<img src="'+src+'" style="width:100%; max-width:600px;" />';
	$("#photo-viewer").modal('show').find('.modal-body').html(str);
	
	
	return false;
})