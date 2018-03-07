/**
 * Created by ilichev on 07.03.2018.
 */

$(function () {

    $('.game-chance-field').parent().hide();

    $('#algorithmparams-games').on('change', function () {
        var values = $(this).val();

        $.ajax({
            url : '/algorithm/default/create',
            method : 'post',
            'success' : function ($data) {
                $.each(values, function (index, gameId) {
                    $('.field-game_' + gameId).show();
                });
            }
        });

    });
});