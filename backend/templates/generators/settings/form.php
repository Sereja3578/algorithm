<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'moduleName');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'baseSearchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'baseControllerClass');
echo $form->field($generator, 'generateRelationsFields')->checkbox();
echo $form->field($generator, 'db');
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'addingI18NStrings')->checkbox();
echo $form->field($generator, 'icon');

$js = <<<JS
    function lcfirst(str) {
        var caps = str;
        caps = caps.charAt(0).toLowerCase() + caps.slice(1);
        return caps;
    };     
        
    // hide `adding I18N strings` field when I18N is disabled
    $('form #generator-enablei18n').change(function () {
        $('form .field-generator-addingi18nstrings').toggle($(this).is(':checked'));
    });

    // hide `db` field when `generateRelationsFields` is disabled
    $('form #generator-generaterelationsfields').change(function () {
        $('form .field-generator-db').toggle($(this).is(':checked'));
    });

    setTimeout(function() {
        $('form .field-generator-addingi18nstrings').toggle($('form #generator-enablei18n').is(':checked'));
        $('form .field-generator-db').toggle($('form #generator-generaterelationsfields').is(':checked'));

        $("#generator-modelclass, #generator-modulename").on("change", function() {
            var o = $(this);
            if (o.closest("div").not(".has-error") && o.val() !== '') {
                var modelClass = o.val();
                    if (o.attr('id') == 'generator-modelclass') {
                        modelClassName = modelClass.split('\\\\').pop();
                        
                    } else {
                        var re = /\<class_name\>/;
                        if (re.test($("#generator-modelclass").val())) {
                            modelClassName = '<class_name>';
                        } else {
                            modelClassName = $("#generator-modelclass").val().split('\\\\').pop();
                        }
                    }
                    moduleName = $('#generator-modulename').val();
                    searchModelClass = 'backend\\\\modules\\\\'+moduleName +'\\\\models\\\\' + modelClassName + 'Search',
                    baseSearchModelClass = 'backend\\\\modules\\\\'+moduleName +'\\\\models\\\\base\\\\' + modelClassName + 'SearchBase',
                    controllerClass = 'backend\\\\modules\\\\'+moduleName+'\\\\controllers\\\\DefaultController',
                    viewPath = '@backend/modules/'+moduleName+'/views/default',
                    jQsearchModelClass = $("#generator-searchmodelclass"),
                    jQbaseSearchModelClass = $("#generator-basesearchmodelclass"),
                    jQcontrollerClass = $("#generator-controllerclass"),
                    jQviewPath = $("#generator-viewpath");
                    

                if (jQsearchModelClass.val() === '' || jQsearchModelClass.val() === jQsearchModelClass.data('generated')) {
                    jQsearchModelClass.val(searchModelClass).trigger("change");
                }
                if (jQbaseSearchModelClass.val() === '' || jQbaseSearchModelClass.val() === jQbaseSearchModelClass.data('generated')) {
                    jQbaseSearchModelClass.val(baseSearchModelClass).trigger("change");
                }
                if (jQcontrollerClass.val() === '' || jQcontrollerClass.val() === jQcontrollerClass.data('generated')) {
                    jQcontrollerClass.val(controllerClass).trigger("change");
                }
                if (jQviewPath.val() === '' || jQviewPath.val() === jQviewPath.data('generated')) {
                    jQviewPath.val(viewPath).trigger("change");
                }

                jQsearchModelClass.data('generated', searchModelClass);
                jQbaseSearchModelClass.data('generated', baseSearchModelClass);
                jQcontrollerClass.data('generated', controllerClass);
                jQviewPath.data('generated', viewPath);
            }
        });

        $("#generator-searchmodelclass").data('generated', $("#generator-searchmodelclass").val());
        $("#generator-basesearchmodelclass").data('generated', $("#generator-basesearchmodelclass").val());
        $("#generator-controllerclass").data('generated', $("#generator-controllerclass").val());
        $("#generator-viewpath").data('generated', $("#generator-viewpath").val());

    }, 30);
        
       
JS;

$this->registerJs($js);
