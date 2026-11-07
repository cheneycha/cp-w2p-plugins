<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<?php
    $user_info = nbd_get_artist_info($user->ID);
    wp_enqueue_media();
?>
<div class="nbd-user-settings">
    <h2 id="nbd-user-setting"><?php printf( esc_html__( '%1$s settings', 'web-to-print-online-designer' ), 'NBDesigner' ); ?></h2> 
    <div class="nbd-section">
        <label><?php esc_html_e( 'Banner', 'web-to-print-online-designer' ); ?></label>
        <div class="nbd-banner" style="padding-bottom: <?php echo ( $user_info['banner_height'] / $user_info['banner_width'] * 100 ) . '%'; ?>;">
            <?php $banner = $user_info['nbd_artist_banner']; ?>
            <div class="image-wrap<?php echo $banner ? '' : ' nbd-hide'; ?>">
                <?php $banner_url = $banner ? wp_get_attachment_url( $banner ) : ''; ?>
                <input type="hidden" class="nbd-file-field" value="<?php echo( $banner ); ?>" name="nbd_artist_banner">
                <img class="nbd-banner-img" src="<?php echo esc_url( $banner_url ); ?>">

                <a class="close nbd-remove-banner-image">&times;</a>
            </div>
            <div class="nbd-button-area<?php echo $banner ? ' nbd-hide' : ''; ?>">
                <p><a href="#" class="nbd-banner-drag button button-primary"><?php esc_html_e( 'Upload banner', 'web-to-print-online-designer' ); ?></a></p>
                <p class="description"><?php esc_html_e( '(Upload a banner for your store. )', 'web-to-print-online-designer' ); ?></p>
            </div>
        </div>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_name"><?php esc_html_e( 'Artist Name', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="text" id="nbd_artist_name" name="nbd_artist_name"
            value="<?php echo esc_attr( $user_info['nbd_artist_name'] ); ?>"/>
    </div>
    <div class="nbd-section">
        <label for="nbd_artist_description"><?php esc_html_e( 'About the artist', 'web-to-print-online-designer' ); ?></label>
        <textarea rows="5" cols="30" id="nbd_artist_description" name="nbd_artist_description" style="width: 500px;" ><?php echo esc_attr( $user_info['nbd_artist_description'] ); ?></textarea>
    </div>
    <div class="nbd-section">
        <label for="nbd_auto_approve_design"><?php esc_html_e( 'Auto approve designs', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="checkbox" id="nbd_auto_approve_design" name="nbd_auto_approve_design"
            value="on" <?php echo ( $user_info['nbd_auto_approve_design'] === 'on' ) ? 'checked' : ''; ?> />
        <p style="display: inline-block; "><?php esc_html_e('Designs created by this designer will be approved automatically', 'web-to-print-online-designer'); ?></p>
    </div>
    <div class="nbd-section">
        <label for="nbd_create_permission"><?php esc_html_e( 'Create designs', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="checkbox" id="nbd_create_permission" name="nbd_create_design"
            value="on" <?php echo ( $user_info['nbd_create_design'] === 'on' ) ? 'checked' : ''; ?> />  
        <p style="display: inline-block; "><?php esc_html_e('Allow user create designs', 'web-to-print-online-designer'); ?></p>
    </div>
    <div class="nbd-section">
        <label for="nbd_sell_permission"><?php esc_html_e( 'Sell designs', 'web-to-print-online-designer' ); ?></label>
        <input class="regular-text" type="checkbox" id="nbd_sell_permission" name="nbd_sell_design"
            value="on" <?php echo ( $user_info['nbd_sell_design'] === 'on' ) ? 'checked' : ''; ?> />  
        <p style="display: inline-block; "><?php esc_html_e('Allow user sell his/her designs', 'web-to-print-online-designer'); ?></p>   
    </div> 
    <div class="nbd-section">
        <label for="nbd_artist_commission"><?php esc_html_e( 'Artist Commission %', 'web-to-print-online-designer' ); ?></label>
        <div style="display: inline-block; ">
            <input class="small-text" type="number" id="nbd_artist_commission" name="nbd_artist_commission"
                value="<?php echo esc_attr( $user_info['nbd_artist_commission'] ); ?>"/> <br />
            <p><?php esc_html_e(' % artist gets from each design', 'web-to-print-online-designer'); ?></p>     
        </div>
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
    <div class="nbd-section">
        <label for="nbd_payment"><?php esc_html_e( 'Payment infomation', 'web-to-print-online-designer' ); ?></label>
        <textarea name="nbd_payment" rows="5" cols="30" style="width: 500px;"><?php echo esc_attr( $user_info['nbd_payment'] ); ?></textarea>
    </div>
</div>
<style type="text/css" >
    .nbd-banner {
        border: 4px dashed #d8d8d8;
        margin: 0;
        overflow: hidden;
        position: relative;
        text-align: center;
        width: 700px;
        display: inline-block;

    } 
    .nbd-banner .image-wrap {
        position: absolute;
        top: 0;
        left: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .nbd-hide { display: none; }
    .nbd-button-area { 
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }
    .nbd-button-area.nbd-hide {
        display: none;
    }
    .nbd-button-area p {
        margin-bottom: 0;
        flex: 1 0 100%;
    }
    .nbd-banner img { max-width:100%; }
    .nbd-banner .nbd-remove-banner-image {
        position: absolute;
        width: 100%;
        height: 100%;
        background: #000;
        top: 0;
        left: 0;
        opacity: .7;
        font-size: 100px;
        color: #f00;
        top: 0;
        display: none;
        justify-content: center;
        align-items: center;
        text-decoration: none !important;
    }
    .nbd-banner:hover .nbd-remove-banner-image {
        display:flex;
        cursor: pointer;
    }  
    .nbd-section {
        margin-bottom: 15px;
    }
</style>
<script type="text/javascript">
    jQuery(function($){
        var banner_width = parseInt( <?php echo $user_info['banner_width']; ?> ),
        banner_height = parseInt( <?php echo $user_info['banner_height']; ?> );
        var nbdUploadMedia = function( width, height, callback ){
            var fileFrame;

            if ( fileFrame ) {
                fileFrame.open();
                return;
            }

            const fileStatesOptions = {
                library: wp.media.query({ type: 'image' }),
                multiple: false,
                title: "<?php _e('Select and crop', 'web-to-print-online-designer'); ?>",
                priority: 20,
                filterable: 'uploaded',
                autoSelect: true,
                suggestedWidth: parseInt( width ),
                suggestedHeight: parseInt( height )
            };

            const cropControl = {
                id: "control-id",
                params: {
                    width: parseInt( width ),
                    height: parseInt( height ),
                    flex_width: false,
                    flex_height: false
                }
            }

            cropControl.mustBeCropped = function (flexW, flexH, dstW, dstH, imgW, imgH) {
                if (true === flexW && true === flexH) {
                    return false;
                }

                if (true === flexW && dstH === imgH) {
                    return false;
                }

                if (true === flexH && dstW === imgW) {
                    return false;
                }

                if (dstW === imgW && dstH === imgH) {
                    return false;
                }

                if (imgW <= dstW) {
                    return false;
                }

                return true;
            }

            const calculateImageSelectOptions = function(attachment, controller){
                let xInit = parseInt( width );
                let yInit = parseInt( height );
                let flexWidth = false;
                let flexHeight = false;

                let ratio, xImg, yImg, realHeight, realWidth, imgSelectOptions;

                realWidth = attachment.get('width');
                realHeight = attachment.get('height');

                let control = controller.get('control');
                controller.set('canSkipCrop', !control.mustBeCropped(flexWidth, flexHeight, xInit, yInit, realWidth, realHeight));

                ratio = xInit / yInit;
                xImg = realWidth;
                yImg = realHeight;

                if (xImg / yImg > ratio) {
                    yInit = yImg;
                    xInit = yInit * ratio;
                } else {
                    xInit = xImg;
                    yInit = xInit / ratio;
                }

                imgSelectOptions = {
                    handles: true,
                    keys: true,
                    instance: true,
                    persistent: true,
                    imageWidth: realWidth,
                    imageHeight: realHeight,
                    x1: 0,
                    y1: 0,
                    x2: xInit,
                    y2: yInit
                };

                if (flexHeight === false && flexWidth === false) {
                    imgSelectOptions.aspectRatio = xInit + ':' + yInit;
                }
                if (flexHeight === false) {
                    imgSelectOptions.maxHeight = yInit;
                }
                if (flexWidth === false) {
                    imgSelectOptions.maxWidth = xInit;
                }

                return imgSelectOptions;
            }

            const fileStates = [new wp.media.controller.Library(fileStatesOptions), new wp.media.controller.CustomizeImageCropper({
                imgSelectOptions: calculateImageSelectOptions,
                control: cropControl
            })];

            const mediaOptions = {
                title: "<?php _e('Select image', 'web-to-print-online-designer'); ?>",
                button: {
                    text: "<?php _e('Select image', 'web-to-print-online-designer'); ?>",
                    close: false
                },
                multiple: false
            };

            
            mediaOptions.states = fileStates;

            fileFrame = wp.media(mediaOptions);

            fileFrame.on('select', function(){
                fileFrame.setState('cropper');
            });

            fileFrame.on('cropped', function(croppedImage){
                callback(croppedImage);
                fileFrame = null;
            });

            fileFrame.on('skippedcrop', function(){
                const selection = fileFrame.state().get('selection');

                const files = selection.map(function(attachment){
                    return attachment.toJSON();
                });

                const file = files.pop();

                callback(file);

                fileFrame = null;
            });

            fileFrame.on('close', function(){
                fileFrame = null;
            });

            fileFrame.open();
        };
        NBD_Settings = {

            init: function() {
                jQuery('a.nbd-banner-drag').on('click', this.imageUpload);
                jQuery('a.nbd-remove-banner-image').on('click', this.removeBanner);
            },

            imageUpload: function(e) {
                e.preventDefault();
                nbdUploadMedia( banner_width, banner_height, NBD_Settings.setBannerImage );
            },

            setBannerImage: function( image ) {
                var wrap = jQuery('.nbd-user-settings');
                var btnArea = wrap.find('.nbd-button-area');
                wrap.find('input.nbd-file-field').val(image.id);
                wrap.find('img.nbd-banner-img').attr('src', image.url);
                jQuery('.image-wrap', wrap).removeClass('nbd-hide');
                btnArea.addClass('nbd-hide');
            },

            removeBanner: function(e) {
                e.preventDefault();

                var self = jQuery(this);
                var wrap = self.closest('.image-wrap');
                var btnArea = wrap.siblings('.nbd-button-area');

                wrap.find('input.nbd-file-field').val('0');
                wrap.addClass('nbd-hide');
                btnArea.removeClass('nbd-hide');
            }
        };

        NBD_Settings.init();
    });
</script>
