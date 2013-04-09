$(document).ready(function(){
	$('.list-selector').change(function(){
		var id = $(this).find('option:selected').attr('value');
		$('.visible-list').hide().removeClass('visible-list');
		$('#'+id).show().addClass('visible-list');

		
	});
});