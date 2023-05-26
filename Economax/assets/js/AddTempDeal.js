console.log('AddTempDeal.js loaded')

$(document).ready(function() {
    $('.increase-temperature').click(function () {
        updateTemperature($(this).attr('id_deal'), "increase");
    });
    $('.decrease-temperature').click(function () {
        updateTemperature($(this).attr('id_deal'), "decrease");
    });

    function updateTemperature(dealId, type) {
        let url = "";
        if(type === "increase")
            url = "/deal/edit/" + dealId + "/temperature/increase";
        else
            url = "/deal/edit/" + dealId + "/temperature/decrease";
        $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                $('#temp-' + dealId).text(data.temperature)
            }
        });
    }
});