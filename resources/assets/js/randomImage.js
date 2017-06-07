(function($) {
  $(document).ready(function() {

    var $modal = $('#randomImageModal');
    var actuallySubmit = false;

    // Random image form submit function
    $('.random-image-form').submit(function(e){

      if(actuallySubmit) {
        return;
      }

      e.preventDefault();

      $('.luckyBtn').addClass('loading');

      $.get('/lucky-url', function (url) {
        $modal.find('.the-image').html('<img src="' + url + '" />');
        $modal.modal('show');
        $('.luckyBtn').removeClass('loading');
      });

      return false;

    });

    // Don't use this image button handler
    $('.container').on('click', '.rand-image-no', function() {
      $('.rand-image-no').addClass('loading');
      $.get('/lucky-url', function (url) {
        $modal.find('.the-image').html('<img src="' + url + '" />');
        $modal.modal('show');
        $('.rand-image-no').removeClass('loading');
      });
    });

    // Do use this image button handler
    $('.container').on('click', '.rand-image-yeah', function() {
      actuallySubmit = true;
      $('input[name="randImgUrl"]').val($modal.find('img').attr('src'));
      $('.random-image-form').submit();
    });

  });

})(jQuery);
