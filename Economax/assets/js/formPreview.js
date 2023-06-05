console.log('formPreview.js loaded')

// Preview Advert
$('#advert_title').on('change', function () {
    $('.preview-title').text($(this).val());
});

$('#advert_description').on('change', function () {
    $('.preview-description').text($(this).val());
});

$('#advert_price').on('change', function () {
    $('.preview-price').text($(this).val() + ' €');
});

// Preview Promo Code

$('#promo_code_title').on('change', function () {
    $('.preview-title').text($(this).val());
});

$('#promo_code_description').on('change', function () {
    $('.preview-description').text($(this).val());
});

$('#promo_code_typeReduc').on('change', function () {
    if($(this).val() === 'amount') {
        $('.preview-price').text($('#promo_code_value').val() + ' €');
    }
    else {
        $('.preview-price').text($('#promo_code_value').val() + ' %');
    }
});
$('#promo_code_value').on('change', function () {
    if($('#promo_code_typeReduc').val() === 'amount') {
        $('.preview-price').text($(this).val() + ' €');
    }
    else {
        $('.preview-price').text($(this).val() + ' %');
    }
});