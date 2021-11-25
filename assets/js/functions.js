var $ = jQuery.noConflict();

$(document).ready(function($){
    $('.owl-carousel').owlCarousel({
        loop: true,
        nav: true,
        items: 1,
        dots: true,
        navText: ['Vorige', 'Volgende'],
        autoplay: true,
        autoplayTimeout: 5000,
        autoplaySpeed: 2000,
        navSpeed: 2000,
        dotsSpeed: 2000
    });

    $(".validate-form").each(function(){
        $(this).validate({ ignore: []});
    });

    if($("div.QapTcha input").addClass('QapTchaRequired')) {
        $.validator.addMethod("QapTchaRequired", function(value, element) {
            return value ? false : true;
        });
    }
    $.extend($.validator.messages, {
        required: "Dit veld is verplicht.",
        email: "Vul hier een geldig e-mailadres in.",
        QapTchaRequired: "Verplicht om te verschuiven."
    });
});

