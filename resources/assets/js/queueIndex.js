(function($) {
  $(document).ready(function() {

    var $modal = $('#imagePreviewModal');

    $('.item-image').click(function(){
      var url = $(this).attr('data-url');
      var title = url.indexOf('unsplash_images') == -1 ? url : 'Unsplash image';
      $modal.find('.modal-title').html(title);
      $modal.find('.the-image').html('<img src="' + url + '" />');
      $modal.modal('show');
    });

  });

})(jQuery);
