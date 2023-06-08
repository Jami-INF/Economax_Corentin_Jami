$(document).ready(function() {
    $('.increase-temperature').click(function () {
        updateTemperature($(this).attr('id_deal'), "increase");
    });
    $('.decrease-temperature').click(function () {
        updateTemperature($(this).attr('id_deal'), "decrease");
    });
    $('.add-favorite').click(function () {
        addFavorite($(this).attr('id_deal'));
    });

    function updateTemperature(dealId, type) {
        let url = "";
        if(type === "increase")
            url = "/deal/edit/" + dealId + "/temperature/increase";
        else
            url = "/deal/edit/" + dealId + "/temperature/decrease";
        $.ajax({
            url: url,
            method: 'POST',
            success: function (data) {
                $('#temp-' + dealId).text(data.temperature+"°");
            }
        });
    }
    function addFavorite(dealId) {
        let url = "/deal/edit/" + dealId + "/favorite/add";
        $.ajax({
            url: url,
            method: 'POST',
            success: function (data) {
                $('#favorite-' + dealId).setAttribute("class", "fas fa-heart");
            }
        });
    }
});