$(document).ready(function () {
    // Máscara de telefone SP
    var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        spOptions = {
            onKeyPress: function (val, e, field, options) {
                field.mask(SPMaskBehavior.apply({}, arguments), options);
            },
            clearIfNotMatch: true
        };
    $('.sp_celphones').mask(SPMaskBehavior, spOptions);

    // Swiper Depoimentos
    var swiperTestimony = new Swiper('.swiper-testimony', {
        grabCursor: true,
        slidesPerView: 1,
        spaceBetween: 32,
        autoplay: {
            delay: 8000,
        },
        navigation: {
            nextEl: ".swiper-testimony .swiper-button-next",
            prevEl: ".swiper-testimony .swiper-button-prev",
        },
    });
});