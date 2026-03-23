$(document).ready(function () {

    $('nav a').click(function(e) {

        var block;
        e.preventDefault();
        block = $(this).attr('name');

        $('html, body').animate({
            scrollTop: $('.' + block).offset().top
        }, 800);
    });
});