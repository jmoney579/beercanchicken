$(document).ready($(function () {

    // -----------------------------------------------------------------------------------
    // Select Sib Min
    // -----------------------------------------------------------------------------------			

    $('select#minSib').selectToUISlider({
        labels: 5, 					//0=no tick lables or #=total number of tick marks
        step: 1, 						//tick mark step
        tooltip: true, 			//show tooltips, boolean
        tooltipSrc: 'text', 	//or 'value'
        labelSrc: 'text', 		//or 'value'
        sliderOptions: {
            change: function (e, ui) {
                if (ui.value >= 0) {
                    $('#txtMinSib').val('' + (ui.value + 1));
                } else {
                    $('#txtMinSib').val('');
                }
                $("input[id$=txtMinSib]").focus().select();
            }
        }
    }); //.hide();    // Use this to hide the select dropdown rem next line

    if ($('select#minSib option:selected').val() >= 0) {
        $('#txtMinSib').val($('select#minSib option:selected').val());  // if pre-select update text field with select value
    }
    //$("input[id$=txtMinSib]").focus().select();

    $('#txtMinSib').bind('change', function () {
        $('#minSib').val($('#txtMinSib').val());
        $('#minSib').trigger('change');
    });

    // -----------------------------------------------------------------------------------
    // Select Sib Maximum
    // -----------------------------------------------------------------------------------			

    $('select#maxSib').selectToUISlider({
        labels: 5, 					//0=no tick lables or #=total number of tick marks
        step: 1, 						//tick mark step
        tooltip: true, 			//show tooltips, boolean
        tooltipSrc: 'text', 	//or 'value'
        labelSrc: 'text', 		//or 'value'
        sliderOptions: {
            change: function (e, ui) {
                if (ui.value >= 0) {
                    $('#txtMaxSib').val('' + (ui.value + 1));
                } else {
                    $('#txtMaxSib').val('');
                }
                $("input[id$=txtMaxSib]").focus().select();
            }
        }
    }).hide();    // Use this to hide the select dropdown rem next line

    if ($('select#maxSib option:selected').val() >= 0) {
        $('#txtMaxSib').val($('select#maxSib option:selected').val());  // if pre-select update text field with select value
    }
    //$("input[id$=txtMaxSib]").focus().select();

    $('#txtMaxSib').bind('change', function () {
        $('#maxSib').val($('#txtMaxSib').val());
        $('#maxSib').trigger('change');
    });

    // -----------------------------------------------------------------------------------
    // Select Age Min
    // -----------------------------------------------------------------------------------			

    $('select#minAge').selectToUISlider({
        labels: 6, 					//0=no tick lables or #=total number of tick marks
        step: 1, 						//tick mark step
        tooltip: true, 			//show tooltips, boolean
        tooltipSrc: 'text', 	//or 'value'
        labelSrc: 'text', 		//or 'value'
        sliderOptions: {
            change: function (e, ui) {
                if (ui.value > 0) {
                    $('#txtMinAge').val(ui.value);  // was "'' + (ui.value + -1)" between the val()
                } else {
                    $('#txtMinAge').val('');
                }
                $("input[id$=txtMinAge]").focus().select();
            }
        }
    }).hide();    // Use this to hide the select dropdown rem next line

    if ($('select#minAge option:selected').val() >= 0) {
        $('#txtMinAge').val($('select#minAge option:selected').val());  // if pre-select update text field with select value
    }
    //$("input[id$=txtMinAge]").focus().select();

    $('#txtMinAge').bind('change', function () {
        $('#minAge').val($('#txtMinAge').val());
        $('#minAge').trigger('change');
    });

    // -----------------------------------------------------------------------------------
    // Select Age Maximum
    // -----------------------------------------------------------------------------------			

    $('select#maxAge').selectToUISlider({
        labels: 6, 					//0=no tick lables or #=total number of tick marks
        step: 1, 						//tick mark step
        tooltip: true, 			//show tooltips, boolean
        tooltipSrc: 'text', 	//or 'value'
        labelSrc: 'text', 		//or 'value'
        sliderOptions: {
            change: function (e, ui) {
                if (ui.value > 0) {
                    $('#txtMaxAge').val(ui.value);
                } else {
                    $('#txtMaxAge').val('');
                }
                $("input[id$=txtMaxAge]").focus().select();
            }
        }
    }).hide();    // Use this to hide the select dropdown rem next line

    if ($('select#maxAge option:selected').val() < 0) {
        $('select#maxAge option:selected').val(0);
    }

    if ($('select#maxAge option:selected').val() >= 0) {
        $('#txtMaxAge').val($('select#maxAge option:selected').val());  // if pre-select update text field with select value
    }
    //$("input[id$=txtMaxAge]").focus().select();

    $('#txtMaxAge').bind('change', function () {
        $('#maxAge').val($('#txtMaxAge').val());
        $('#maxAge').trigger('change');
    });

    //$("input[id$=txtMaxAge]").blur();

}));