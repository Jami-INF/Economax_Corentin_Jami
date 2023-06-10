$(document).ready(function() {
    $('.increase-temperature').click(function () {
        updateTemperature($(this).attr('id_deal'), "increase");
    });
    $('.decrease-temperature').click(function () {
        updateTemperature($(this).attr('id_deal'), "decrease");
    });
    $('.interact-favorite').click(function () {
        interactFavorite($(this).attr('id_deal'));
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

    function interactFavorite(dealId) {
        let iconElement = document.getElementById("favo-deal-" + dealId);
        if(iconElement.classList.contains("bi-heart")) {
            addFavorite(dealId);
        } else {
            removeFavorite(dealId);
        }
    }
    function addFavorite(dealId) {
        let url = "/deal/edit/" + dealId + "/favorite/add";
        $.ajax({
            url: url,
            method: 'POST',
            success: function (data) {
                //console.log(data); // todo:popup
                let iconElement = document.getElementById("favo-deal-" + dealId);
                iconElement.classList.remove("bi-heart");
                iconElement.classList.add("bi-heart-fill");
            }
        });
    }
    function removeFavorite(dealId) {
        let url = "/deal/edit/" + dealId + "/favorite/remove";
        $.ajax({
            url: url,
            method: 'POST',
            success: function (data) {
                //console.log(data); // todo:popup
                let iconElement = document.getElementById("favo-deal-" + dealId);
                iconElement.classList.remove("bi-heart-fill");
                iconElement.classList.add("bi-heart");
            }
        });
    }
});