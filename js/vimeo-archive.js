jQuery(document).ready(function(){

    // On open, copy the data-src to src, to enable video playing.
   jQuery('.modal').on('shown.bs.modal', function (e) {
        jQuery(this).find("iframe").prop("src", function(){
            // Set their src attribute to the value of data-src
            return jQuery(this).data("src");
        });
    });

    //On Close, remove the src, to shutdown playing of the video.
    jQuery('.modal').on('hidden.bs.modal', function (e) {				
        jQuery(this).find('iframe').attr('src', '');
//        $(this).find('iframe').attr('src', src);
    });

});