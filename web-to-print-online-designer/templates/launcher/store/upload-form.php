<?php
    if (!defined('ABSPATH')) exit;

    $default_max_upload = nbd_get_max_upload_default();
    $max_upload_size    = nbdesigner_get_option( 'nbdesigner_maxsize_upload_file', $default_max_upload );
    $max_upload_size    = $max_upload_size > $default_max_upload ? $default_max_upload : $max_upload_size;
    $unit               = nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm');
?>
<div class="nbdl-upload-form" id="nbdl-upload-form">
    <div class="nbdl-upload-form-inner">
        <div class="nbdl-upload-form-sidebar">
            <div class="nbdl-uf-sidebar-header">
                <div class="nbdl-uf-progress">
                    <span style="width: 0%" id="nbdl-uf-progress-indicator"></span>
                </div>
                <h3 class="nbdl-uf-sidebar-header-title" data-text-phase-1="<?php esc_html_e('Upload necessary files', 'web-to-print-online-designer'); ?>"
                    data-text-phase-2="<?php esc_html_e('Add More Products', 'web-to-print-online-designer'); ?>">
                    <?php esc_html_e('Upload necessary files', 'web-to-print-online-designer'); ?>
                </h3>
            </div>
            <div class="nbdl-uf-sidebar-body">
                <div class="nbdl-phase-1">
                    <div class="nbdl-uf-sidebar-section nbdl-guidelines-section">
                        <div class="nbdl-uf-sidebar-section-title">
                            <?php esc_html_e('Download design guidelines', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-subtitle">
                            <?php esc_html_e('If you use our downloadable templates, delete the guide layers before saving your files. If you do not delete them, they will show up on the print.', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-side-download-guidelines">
                            <a href="" target="_blank" class="nbdl-guideline" title="guidelines">
                                <div>
                                    <svg viewBox="0 0 48 48" class="svg-icon" role="presentation" aria-hidden="true" ><path d="M10 42h28v2H10zM23 4.008v28.649L11.844 21.97l-1.384 1.444L24 36.385l13.54-12.971-1.384-1.444L25 32.657V4.008h-2z"></path></svg>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="nbdl-uf-sidebar-divider nbdl-guidelines-section-divider"></div>
                    <div class="nbdl-uf-sidebar-section">
                        <div class="nbdl-uf-sidebar-section-title">
                            <?php esc_html_e('Product preview', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-subtitle">
                            <?php esc_html_e('It will be shown in design archive page.', 'web-to-print-online-designer'); ?><br />
                            <?php esc_html_e('Accept extension: png, jpg( jpeg )', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-upload __product-preview">
                            <input type="file" data-check="product_preview" data-max-size="<?php echo $max_upload_size; ?>" accept=".png, .jpg, .jpeg" name="nbdl-preview"/>
                            <span class="nbdl-upload-warning"><?php esc_html_e('Please check file extension or file size!', 'web-to-print-online-designer'); ?></span>
                        </div>
                    </div>
                    <div class="nbdl-uf-sidebar-divider"></div>
                    <div class="nbdl-uf-sidebar-section">
                        <div class="nbdl-uf-sidebar-section-title">
                            <?php esc_html_e('Design file', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-subtitle">
                            <?php esc_html_e('Compress all necessary files( PSD, AI, SVG, fonts... ) in one ZIP file.', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-upload __design">
                            <input type="file" data-check="design" data-max-size="<?php echo $max_upload_size; ?>" accept=".zip" name="nbdl-design-file" />
                            <span class="nbdl-upload-warning"><?php esc_html_e('Please check file extension or file size!', 'web-to-print-online-designer'); ?></span>
                        </div>
                    </div>
                    <div class="nbdl-uf-sidebar-divider"></div>
                    <div class="nbdl-uf-sidebar-section nbdl-uf-side-preview-upload-wrap">
                        <div class="nbdl-uf-sidebar-section-title">
                            <?php esc_html_e('Side content design previews', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-subtitle">
                            <?php esc_html_e('Allow accept: PNG, JPG. Max image dimension: ', 'web-to-print-online-designer'); ?><span class="nbdl-max-dimension"></span>px &times; <span class="nbdl-max-dimension"></span>px
                        </div>
                        <div class="nbdl-uf-side-preview-upload">
                            <div class="nbdl-uf-side-preview-name"><?php esc_html_e('Side name', 'web-to-print-online-designer'); ?></div>
                            <div class="nbdl-uf-sidebar-section-upload">
                                <input type="file" data-check="side_previews" data-max-size="<?php echo $max_upload_size; ?>" accept=".png, .jpg, .jpeg" name="nbdl-side-preview[]"/>
                                <span class="nbdl-upload-warning"><?php esc_html_e('Please check file extension or file size or file dimension!', 'web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="nbdl-uf-sidebar-divider"></div>
                    <div class="nbdl-uf-sidebar-section">
                        <div class="nbdl-uf-sidebar-section-title">
                            <?php esc_html_e('Design name', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-subtitle">
                            <?php esc_html_e('A short name represents for your design concept.', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-content">
                            <input name="nbdl-design-name" id="nbdl-design-name" maxlength="255" placeholder="<?php esc_attr_e('Design name', 'web-to-print-online-designer'); ?>" type="text" class="woocommerce-Input woocommerce-Input--text input-text nbdl-design-name"/>
                        </div>
                    </div>
                    <div class="nbdl-uf-sidebar-divider"></div>
                    <div class="nbdl-uf-sidebar-section">
                        <div class="nbdl-uf-sidebar-section-title">
                            <?php esc_html_e('Tags', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-subtitle">
                            <?php esc_html_e('Select tags for design filter', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbdl-uf-sidebar-section-content">
                            <?php if( empty( $tags ) ): ?>
                                <?php esc_html_e('No tag', 'web-to-print-online-designer'); ?>
                            <?php else: ?>
                            <select name="nbdl-side-tags" id="nbdl-side-tags" multiple data-placeholder="<?php _e('Search tag name', 'web-to-print-online-designer'); ?>" >
                            <?php foreach( $tags as $tag ): ?>
                                <option value="<?php echo $tag['term_id']; ?>" ><?php echo $tag['name']; ?></option>
                            <?php endforeach; ?>
                            </select>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="nbdl-phase-2">
                    <div class="nbdl-selected-product-row nbdl-origin">
                        <div class="nbdl-selected-product-name"></div>
                        <div class="nbdl-selected-product-checkbox">
                            <label class="nbdl-realated-product-checkbox">
                                <input type="checkbox" checked="checked"/>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="nbdl-selected-product-row nbdl-hidden nbdl-related">
                        <div class="nbdl-selected-product-name"></div>
                        <div class="nbdl-selected-product-checkbox">
                            <label class="nbdl-realated-product-checkbox">
                                <input type="checkbox" value=""/>
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nbdl-uf-sidebar-footer">
                <button class="button" id="nbdl-uf-back">← <?php esc_html_e('Back', 'web-to-print-online-designer'); ?></button>
                <button class="button nbdl-primary-btn nbdl-inactive" id="nbdl-uf-continue" data-text-continue="<?php esc_attr_e('Continue', 'web-to-print-online-designer'); ?>" data-text-submit="<?php esc_attr_e('Submit', 'web-to-print-online-designer'); ?>" >
                    <span><?php esc_html_e('Continue', 'web-to-print-online-designer'); ?></span> →
                </button>
            </div>
        </div>
        <div class="nbdl-upload-form-stage">
            <div class="nbdl-uf-stage-inner">
                <div class="nbdl-stage-wrap">
                    <div class="nbdl-stage-switcher">
                        <div class="nbdl-stage-switcher-btn"></div>
                    </div>
                    <div class="nbdl-stage-preview-area">
                        <div class="nbdl-stage-design-base-wrap">
                            <div class="nbdl-stage-design-base">
                                <div class="nbdl-product-color-base"></div>
                                <div class="nbdl-product-base default" data-default="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>g_template.png" >
                                    <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>g_template.png" class="nbdl-product-base-img"/>
                                    <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>spinner.svg" class="nbdl-product-base-loading"/>
                                </div>
                                <div class="nbdl-content-design-area">
                                    <div class="nbdl-design-area-info">i
                                        <div class="nbdl-design-area-info-tip">
                                            <?php esc_html_e('Design area', 'web-to-print-online-designer'); ?> <span class="nbdl-design-area-width"></span> &times; <span class="nbdl-design-area-height"></span> <?php echo $unit; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="nbdl-product-overlay default">
                                    <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>g_template.png" class="nbdl-product-overlay-img"/>
                                    <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>spinner.svg" class="nbdl-product-overlay-loading"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nbdl-realated-product-panel">
                <div class="nbdl-realated-product-panel-inner">
                    <div class="nbdl-realated-product-wrap">
                        <div class="nbdl-realated-product-header">
                            <h3><?php esc_html_e('Great design. Now you can add more products!', 'web-to-print-online-designer'); ?></h3>
                            <p><?php esc_html_e('To help you get up and running, we’ve created some additional products based on your original design.', 'web-to-print-online-designer'); ?></p>
                        </div>
                        <div class="nbdl-realated-product-list">
                            <div class="nbdl-realated-product">
                                <div class="nbdl-realated-product-inner">
                                    <div class="nbdl-realated-product-checkbox-wrap">
                                        <label class="nbdl-realated-product-checkbox">
                                            <input type="checkbox" value=""/>
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="nbdl-realated-product-image-wrap">
                                        <div class="nbdl-realated-product-preview">
                                            <div class="nbdl-realated-product-color-base"></div>
                                            <div class="nbdl-realated-product-base" >
                                                <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>spinner.svg" class="nbdl-realated-product-base-loading"/>
                                            </div>
                                            <div class="nbdl-realated-content-design-area"></div>
                                            <div class="nbdl-realated-product-overlay">
                                                <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>spinner.svg" class="nbdl-realated-product-overlay-loading"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nbdl-realated-product-name"></div>
                                    <button class="button nbdl-releted-btn">
                                        <span><?php esc_html_e('Select', 'web-to-print-online-designer'); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="nbdl-no-realated">
                            <p>¯\_(ツ)_/¯</p>
                            <p><?php esc_html_e('No related product has found yet.', 'web-to-print-online-designer'); ?></p>
                        </div>
                    </div>
                    <div class="nbdl-related-loading" id="nbdl-related-loading">
                        <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>spinner.svg"/>
                        <span><?php esc_html_e('Processing...', 'web-to-print-online-designer'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="nbdl-loading" id="nbdl-loading">
            <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/'; ?>spinner.svg"/>
            <span><?php esc_html_e('Processing...', 'web-to-print-online-designer'); ?><span class="nbdl-submit-indicator"></span></span>
        </div>
    </div>
</div>