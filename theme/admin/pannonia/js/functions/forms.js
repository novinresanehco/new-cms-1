$(function() {

	//===== Tags =====//	
		
	$('.tags').tagsInput({width:'50%'});
	$('.tags-autocomplete').tagsInput({
		width:'100%',
		autocomplete_url:'tags_autocomplete.html'
	});
	$('.tip').tooltip();
	$('.focustip').tooltip({'trigger':'focus'});
});








