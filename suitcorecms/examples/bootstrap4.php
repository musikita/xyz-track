<script>
    jQuery(document).ready(function(){

        $("<?= $validator['selector']; ?>").each(function() {
            $(this).data('validator', $(this).validate({
                errorElement: 'span',
                errorClass: 'invalid-feedback',

                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length || element.parent('.form-group').length ||
                        element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                        element.parent().append(error);
                        // else just place the validation message immediately after the input
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).siblings('.select2').addClass('on-validation').find('.select2-selection__rendered').removeClass('form-control is-valid').addClass('form-control is-invalid');
                    } else {
                        $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
                    }
                },

                <?php if (isset($validator['ignore']) && is_string($validator['ignore'])) { ?>

                ignore: "<?= $validator['ignore']; ?>",
                <?php } ?>


                unhighlight: function(element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).siblings('.select2').addClass('on-validation').find('.select2-selection__rendered').removeClass('form-control is-invalid').addClass('form-control is-valid');
                    } else {
                        $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
                    }
                },

                success: function (element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).siblings('.select2').addClass('on-validation').find('.select2-selection__rendered').removeClass('form-control is-invalid').addClass('form-control is-valid');

                    } else {
                        $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
                    }
                },

                focusInvalid: false, // do not focus the last invalid input
                <?php if (Config::get('jsvalidation.focus_on_error')) { ?>
                invalidHandler: function (form, validator) {

                    if (!validator.numberOfInvalids())
                        return;

                    if (validator.errorList[0].element.scrollIntoViewIfNeeded) {
                        validator.errorList[0].element.scrollIntoViewIfNeeded();
                    } else {
                        $('html, body').animate({
                            scrollTop: $(validator.errorList[0].element).offset().top
                        }, <?= Config::get('jsvalidation.duration_animate') ?>);
                    }

                    $(validator.errorList[0].element).focus();

                },
                <?php } ?>

                rules: <?= json_encode($validator['rules']); ?>
            }));
        });
    });
</script>
