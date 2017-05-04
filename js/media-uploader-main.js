(function($) {
	$(function() {
		var custom_uploader = wp.media({
			title: 'Choose Image',
			library: {
				type: 'image'
			},
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		custom_uploader.on('select', function () {
			var images = custom_uploader.state().get('selection');

			images.each(function(file) {
				$('#template_no_image').val(file.toJSON().url);
			});
		});

		$('#media-upload').on('click', function(e) {
			e.preventDefault();
			custom_uploader.open();
		});
	});
})(jQuery);