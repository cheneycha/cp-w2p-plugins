<?php
    if (!defined('ABSPATH')) exit;
?>
<div id="nbdl-popup">
    <div class="nbdl-popup-inner">
        <span class="nbdl-popup-close" id="nbdl-popup-close">&times;</span>
        <div class="nbdl-popup-content">
            <?php do_action('before_nbd_launcher_product_list'); ?>
            <h3 class="nbdl-title"><?php esc_html_e('Choose your product', 'web-to-print-online-designer'); ?></h3>
            <div class="nbdl-popup-products">
                <?php 
                    foreach( $products as $product ):
                ?>
                <div class="nbdl-product-wrapper" data-id="<?php echo( $product['product_id'] ); ?>" data-upload="<?php echo( $product['allow_upload_solid'] ); ?>">
                    <a>
                        <div class="nbdl-product-image-container">
                            <img class="nbdl-product-img" src="<?php echo esc_url( $product['src'] ); ?>" alt="<?php echo( $product['name'] ); ?>" />
                        </div>
                        <button class="button nbdl-start-this-btn">
                            <span><?php esc_html_e('Start with this', 'web-to-print-online-designer'); ?></span>
                        </button>
                    </a>
                    <div class="nbdl-product-text">
                        <div class="nbdl-product-name"><?php echo( $product['name'] ); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php do_action('after_nbd_launcher_product_list'); ?>
        </div>
        <div class="nbdl-options-wrap">
            <div class="nbdl-options-inner">
                <div class="nbdl-options-header">
                    <h3><?php esc_html_e('What would you like to do with your design?', 'web-to-print-online-designer'); ?></h3>
                </div>
                <div class="nbdl-actions-wrap">
                    <a class="nbdl-action nbdl-upload-action">
                        <div class="nbdl-action-overlay">
                            <div class="nbdl-action-icon upload">
                                <svg viewBox="0 0 49 30" width="100%" height="100%"><g stroke-width=".8" fill="none" fill-rule="evenodd"><path d="M39.793 8.384c.001-.053.004-.106.004-.16 0-2.56-2.061-4.637-4.603-4.637-1.226 0-2.339.485-3.164 1.273A13.124 13.124 0 0 0 22.726 1C16.135 1 10.672 5.86 9.673 12.217c-.055 0-.11-.004-.164-.004-4.7 0-8.509 3.838-8.509 8.572 0 4.59 3.58 8.336 8.08 8.56v.012h28.362c5.822 0 10.542-4.755 10.542-10.62 0-5.052-3.502-9.276-8.191-10.353z" stroke="#4B4F54" stroke-dasharray="2,2" fill="#FFF"></path><path d="M26.597 14.366l-2.054-2.022v11.691h-.749V12.343l-2.053 2.023-.53-.522 2.429-2.39.529-.522.53.522 2.427 2.39-.53.522zm-2.453-.974h.05l-.025-.024-.025.024z" stroke="#52575C" stroke-linejoin="round" fill="#52575C"></path></g></svg>
                            </div>
                            <div class="nbdl-action-info">
                                <div class="nbdl-action-header">
                                    <?php esc_html_e('Upload solid design', 'web-to-print-online-designer'); ?>
                                </div>
                                <ul>
                                    <li><?php esc_html_e('Your design is perfect', 'web-to-print-online-designer'); ?></li>
                                    <li><?php esc_html_e('No need more any adjustment', 'web-to-print-online-designer'); ?></li>
                                </ul>
                            </div>
                            <div class="nbdl-action-continue-icon">
                                <svg viewBox="0 0 24 24" width="100%" height="100%"><path d="M10.5 18.5a1 1 0 0 1-.71-1.71l4.8-4.79-4.8-4.79A1 1 0 0 1 11.2 5.8l6.21 6.2-6.21 6.21a1 1 0 0 1-.7.29z"></path></svg>
                            </div>
                        </div>
                    </a>
                    <a class="nbdl-action nbdl-design-action">
                        <div class="nbdl-action-overlay">
                            <div class="nbdl-action-icon design">
                            <svg viewBox="0 0 49 39" width="100%" height="100%"><g fill="none" fill-rule="evenodd"><path stroke="#4B4F54" stroke-dasharray="4,4" fill="#FFF" d="M2.577 2.627h44.165v34.392H2.577z"></path><path stroke="#52575C" fill="#FFF" d="M1.225 1.232H4.38v3.253H1.225zM44.939 1.232h3.155v3.253h-3.155zM1.225 35.16H4.38v3.253H1.225zM44.939 35.16h3.155v3.253h-3.155z"></path><path d="M32.663 23.91a.459.459 0 0 1-.46-.473v-.917c-.582.901-1.486 1.454-2.711 1.454-1.87 0-3.294-1.517-3.294-3.618 0-2.102 1.424-3.619 3.294-3.619 1.225 0 2.129.553 2.711 1.454v-.916c0-.269.2-.474.46-.474s.46.205.46.474v6.162c0 .268-.2.474-.46.474zm-3.049-6.367c-1.532 0-2.497 1.17-2.497 2.813 0 1.643.965 2.812 2.497 2.812 1.578 0 2.59-1.28 2.59-2.812 0-1.533-1.012-2.813-2.59-2.813zm-4.658 6.368c-.23 0-.353-.143-.414-.3l-1.256-2.892h-5.27l-1.257 2.891c-.061.158-.184.3-.414.3-.275 0-.444-.19-.444-.426 0-.079.03-.158.061-.22l4.26-9.813c.091-.205.214-.316.429-.316.214 0 .337.11.429.316l4.259 9.812c.03.063.061.142.061.221 0 .237-.169.427-.444.427zm-4.305-9.45l-2.284 5.42h4.566l-2.282-5.42z" fill="#FFC600"></path><path fill="#CC9E00" d="M15.646 25.865h18.477v.5H15.646z"></path></g></svg>
                            </div>
                            <div class="nbdl-action-info">
                                <div class="nbdl-action-header">
                                    <?php esc_html_e('Create editable design', 'web-to-print-online-designer'); ?>
                                </div>
                                <ul>
                                    <li><?php esc_html_e('Flexible to edit design', 'web-to-print-online-designer'); ?></li>
                                    <li><?php esc_html_e('Adapt to every customers', 'web-to-print-online-designer'); ?></li>
                                </ul>
                            </div>
                            <div class="nbdl-action-continue-icon">
                                <svg viewBox="0 0 24 24" width="100%" height="100%"><path d="M10.5 18.5a1 1 0 0 1-.71-1.71l4.8-4.79-4.8-4.79A1 1 0 0 1 11.2 5.8l6.21 6.2-6.21 6.21a1 1 0 0 1-.7.29z"></path></svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <?php
            ob_start();
            nbdesigner_get_template("launcher/store/upload-form.php", array( 'tags' => $tags ));
            $form = ob_get_clean();
            echo $form;
        ?>
    </div>
</div>