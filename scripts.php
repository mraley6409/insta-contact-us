<script>
    var nonce ='<?php echo wp_create_nonce('wp_rest');?>';

    (function ($){
        $('#contact-us-form__form').submit(function (event) {
            event.preventDefault();

            var form = $(this).serialize();
            $.ajax({
                method:'post',
                url:'<?php echo get_rest_url(null,'insta-contact-us/v1/send-email');?>',
                headers: {'X-WP-Nonce': nonce },
                data: form
            })
        });
    })(jQuery)
</script>