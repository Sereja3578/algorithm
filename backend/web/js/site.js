/**
 * Created by ilichev on 07.03.2018.
 */

$(function () {
    gamesChanceVisualization();
});

function gamesChanceVisualization() {
    var values = $(this).val();

    $('.game-chance-field').parent().hide();

    $('#algorithmparams-games').on('change', function () {
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