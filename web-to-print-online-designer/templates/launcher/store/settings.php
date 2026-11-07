<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
wp_enqueue_media(); 
?>
<form method="post" id="nbd-artist-form"  action="" class="nbd-artist-form" style="margin-top: 15px;">
    <?php wp_nonce_field( 'nbd_artist_settings_nonce' ); ?>
    <input type="hidden" value="<?php echo( $designer_id ); ?>" name="user_id"/>
    <div class="nbd-banner" style="padding-bottom: <?php echo ( $banner_height / $banner_width * 100 ) . '%'; ?>;">
        <?php $banner = $user_info['nbd_artist_banner']; ?>
        <div class="image-wrap<?php echo $banner ? '' : ' nbd-hide'; ?>">
            <?php $banner_url = $banner ? wp_get_attachment_url( $banner ) : ''; ?>
            <input type="hidden" class="nbd-file-field" value="<?php echo( $banner ); ?>" name="nbd_artist_banner">
            <img class="nbd-banner-img" src="<?php echo esc_url( $banner_url ); ?>">
            <a class="close nbd-remove-banner-image">&times;</a>
        </div>
        <div class="button-area<?php echo $banner ? ' nbd-hide' : ''; ?>">
            <p><a href="#" class="nbd-banner-drag button button-primary"><?php esc_html_e( 'Upload banner', 'web-to-print-online-designer' ); ?></a></p>
            <p class="description">
                <?php esc_html_e( 'Upload a banner for your design store.', 'web-to-print-online-designer' ); ?>
                <span><?php esc_html_e( 'Size: ', 'web-to-print-online-designer' ); ?><?php echo $banner_width; ?> &times; <?php echo $banner_height; ?></span>
            </p>
        </div>
    </div>
    <div class="nbd-section nbd_artist_gravatar-wrap">
        <label for="nbd_artist_gravatar"><?php esc_html_e( 'Artist Avatar', 'web-to-print-online-designer' ); ?></label>
        <div class="nbd_artist_gravatar-right">
            <input type="hidden" id="nbd_artist_gravatar" name="gravatar_id" value="<?php echo esc_attr( $user_info['gravatar'] ); ?>"/>
            <img src="<?php echo esc_attr( $user_info['gravatar_url'] ); ?>" class="nbd_gravatar"/>
            <a class="nbd-chagne-avatar"><?php esc_html_e( 'Change avatar', 'web-to-print-online-designer' ); ?></a>
        </div>
    </div>
    <div class="nbd-section nbd_artist_name-wrap">
        <label for="nbd_artist_name"><?php esc_html_e( 'Artist Name', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_name" name="nbd_artist_name"
            value="<?php echo esc_attr( $user_info['nbd_artist_name'] ); ?>"/>
        <a href="<?php echo add_query_arg(array('id' => $designer_id), getUrlPageNBD('designer')); ?>">
            <?php esc_html_e( 'View own design store', 'web-to-print-online-designer' ); ?>
        </a>
    </div>
    <div class="nbd-section nbd_artist_description">
        <label for="nbd_artist_description" ><?php esc_html_e( 'About the artist', 'web-to-print-online-designer' ); ?></label>
        <textarea rows="5" cols="30" id="nbd_artist_description" name="nbd_artist_description" ><?php echo esc_attr( $user_info['nbd_artist_description'] ); ?></textarea>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_address"><?php esc_html_e( 'Address', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_address" name="nbd_artist_address"
            value="<?php echo esc_attr( $user_info['nbd_artist_address'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_phone"><?php esc_html_e( 'Phone Number', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_phone" name="nbd_artist_phone"
            value="<?php echo esc_attr( $user_info['nbd_artist_phone'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_facebook"><?php esc_html_e( 'Facebook', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_facebook" name="nbd_artist_facebook"
            value="<?php echo esc_attr( $user_info['nbd_artist_facebook'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_twitter"><?php esc_html_e( 'Twitter', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_twitter" name="nbd_artist_twitter"
            value="<?php echo esc_attr( $user_info['nbd_artist_twitter'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_linkedin"><?php esc_html_e( 'LinkedIn', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_linkedin" name="nbd_artist_linkedin"
            value="<?php echo esc_attr( $user_info['nbd_artist_linkedin'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_youtube"><?php esc_html_e( 'Youtube', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_youtube" name="nbd_artist_youtube"
            value="<?php echo esc_attr( $user_info['nbd_artist_youtube'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_instagram"><?php esc_html_e( 'Instagram', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_instagram" name="nbd_artist_instagram"
            value="<?php echo esc_attr( $user_info['nbd_artist_instagram'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_flickr"><?php esc_html_e( 'Flickr', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_flickr" name="nbd_artist_flickr"
            value="<?php echo esc_attr( $user_info['nbd_artist_flickr'] ); ?>"/>
    </div>
    <div class="nbd-section nbd_artist_description">
        <label for="nbd_payment"><?php esc_html_e( 'Payment infomation', 'web-to-print-online-designer' ); ?></label>
        <textarea name="nbd_payment" rows="5" cols="30" placeholder="<?php esc_attr_e( 'Paypal: email&#x0a;Bank account', 'web-to-print-online-designer' ); ?>"><?php echo esc_attr( $user_info['nbd_payment'] ); ?></textarea>
    </div>
    <div class="nbd-section">
        <input type="submit" value="<?php esc_html_e('Update informations', 'web-to-print-online-designer'); ?>" />
        <img class="nbd-loading loaded" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" />
    </div>
</form>
<?php  do_action( 'nbd_artist_info_after_form', $designer_id, $user_info ); ?>
<script type="text/javascript">
    jQuery(function(){
        var banner_width = parseInt( <?php echo $banner_width; ?> ),
        banner_height = parseInt( <?php echo $banner_height; ?> );
        var nbdUploadMedia = function( width, height, callback ){
            var fileFrame;

            if ( fileFrame ) {
                fileFrame.open();
                return;
            }

            fileFrame = wp.media.frames.fileFrame = wp.media({
                title: "<?php _e('Select image', 'web-to-print-online-designer'); ?>",
                button: {
                    text: jQuery( this ).data( 'uploader_button_text' )
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            fileFrame.on( 'select', function() {
                var attachment = fileFrame.state().get('selection').first().toJSON();
                callback(attachment);
            });

            fileFrame.open();
        };
        NBD_Settings = {

            init: function() {
                jQuery('a.nbd-banner-drag').on('click', this.imageUpload);
                jQuery('a.nbd-remove-banner-image').on('click', this.removeBanner);
                jQuery('a.nbd-chagne-avatar').on('click', this.changeAvatar);
            },

            imageUpload: function(e) {
                e.preventDefault();
                nbdUploadMedia( banner_width, banner_height, NBD_Settings.setBannerImage );
            },

            setBannerImage: function( image ) {
                var wrap = jQuery('#nbd-artist-form');
                var btnArea = wrap.find('.button-area');
                wrap.find('input.nbd-file-field').val(image.id);
                wrap.find('img.nbd-banner-img').attr('src', image.url);
                jQuery('.image-wrap', wrap).removeClass('nbd-hide');
                btnArea.addClass('nbd-hide');
            },

            removeBanner: function(e) {
                e.preventDefault();

                var self = jQuery(this);
                var wrap = self.closest('.image-wrap');
                var btnArea = wrap.siblings('.button-area');

                wrap.find('input.nbd-file-field').val('0');
                wrap.addClass('nbd-hide');
                btnArea.removeClass('nbd-hide');
            },

            changeAvatar: function(e){
                e.preventDefault();
                nbdUploadMedia( 100, 100, function( image ){
                    var wrap = jQuery('#nbd-artist-form');
                    wrap.find('#nbd_artist_gravatar').val(image.id);
                    wrap.find('img.nbd_gravatar').attr('src', image.url);
                } );
            }
        };

        NBD_Settings.init(); 
        jQuery('#nbd-artist-form').submit(function(ev) {
            ev.preventDefault(); 
            var formdata = jQuery('#nbd-artist-form').find('input, textarea, select').serialize();
            formdata = formdata + '&action=nbd_update_artist_info';
            jQuery('img.nbd-loading').removeClass('loaded');
            jQuery('#nbd-artist-form').addClass('loading');
            jQuery.post(nbds_frontend.url, formdata, function(res) {
                jQuery('img.nbd-loading').addClass('loaded');
                jQuery('#nbd-artist-form').removeClass('loading');
                if(res['result'] == 1) alert('Update successful!'); else alert('Oop! try again later!');
            }, 'json');
        });
    });
</script>