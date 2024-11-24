function load_gallery_dir(id)
{
	$.post("../../../index.php?id=" + id, { ajax: "ajax" },
		function(data){
			$('#gallerybody').html(data);
	});

}