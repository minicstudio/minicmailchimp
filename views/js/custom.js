$(document).ready(function(){
	$('.list-selector').change(function(){
		var list = $(this).attr('data-list');
		var id = $(this).find('option:selected').attr('value');
		$('.visible-list').hide().removeClass('visible-list');
		$('#'+list+'-'+id).show().addClass('visible-list');	
	});
});