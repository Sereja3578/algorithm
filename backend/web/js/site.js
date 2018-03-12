/**
 * Created by ilichev on 07.03.2018.
 */

$(function () {
    gamesChanceVisualization();
});

function gamesChanceVisualization() {
    $('.game-chance-field').parent().hide();

    $('#algorithmparams-games').on('change', function () {

        var values = $(this).val();

        $.ajax({
            method : 'post',
            'success' : function ($data) {
                $.each(values, function (index, gameId) {
                    $('.field-game_' + gameId).show();
                });
            }
        });

    });
}