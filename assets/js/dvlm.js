function addtags(id) {
    var tags = $('#message').val();
    $('#message').val(tags+" #"+id+" ");
}

$(document).ready(function() {

    // check twitter message length and add url to tweet button
    $('#message').on('keyup',function(){
        if ($('#message').val().length > 140){
            $('.earn-twt').addClass('disabled');
        }
        if ($('#message').val().length <= 140){
            $('.earn-twt').removeClass('disabled');
            //var base_url = 'https://twitter.com/intent/tweet?text=';
            //base_url += encodeURI($('#message').val());
            //$('.earn-twt').attr('href',base_url);
        }
    });
                    
    $(".m_btn").click(function() {
        $(".m_menu").slideToggle();
    });

    $('.earn-fb').click(function(){
        var url = $('#post-form').attr('action');
        url += '/Facebook'
        $('#post-form').attr('action',url);
        $('#post-form').submit();
    });

    $('.earn-twt').click(function(){
        var url = $('#post-form').attr('action');
        url += '/Twitter'
        $('#post-form').attr('action',url);
        $('#post-form').submit();

    });

});