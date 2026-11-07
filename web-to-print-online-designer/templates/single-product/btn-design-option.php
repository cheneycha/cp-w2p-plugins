<?php 
    if (!defined('ABSPATH')) exit; 
    $btn_id = 'open_m-custom-design-wrap';
    if( isset( $nboo_archive_mode ) && $nboo_archive_mode ){
        $btn_id = 'nboo-open_m-custom-design-wrap';
    }
?>
<div class="layout__item">
    <div class="layout__item__inner" id="<?php echo $btn_id; ?>">
        <div class="item__layout custom_design">
            <div class="tile__media-wrap">
                <div class="tile-action__image-wrap">
                    <svg viewBox="0 0 49 39" width="100%" height="100%"><g fill="none" fill-rule="evenodd"><path stroke="#4B4F54" stroke-dasharray="4,4" fill="#FFF" d="M2.577 2.627h44.165v34.392H2.577z"></path><path stroke="#52575C" fill="#FFF" d="M1.225 1.232H4.38v3.253H1.225zM44.939 1.232h3.155v3.253h-3.155zM1.225 35.16H4.38v3.253H1.225zM44.939 35.16h3.155v3.253h-3.155z"></path><path d="M32.663 23.91a.459.459 0 0 1-.46-.473v-.917c-.582.901-1.486 1.454-2.711 1.454-1.87 0-3.294-1.517-3.294-3.618 0-2.102 1.424-3.619 3.294-3.619 1.225 0 2.129.553 2.711 1.454v-.916c0-.269.2-.474.46-.474s.46.205.46.474v6.162c0 .268-.2.474-.46.474zm-3.049-6.367c-1.532 0-2.497 1.17-2.497 2.813 0 1.643.965 2.812 2.497 2.812 1.578 0 2.59-1.28 2.59-2.812 0-1.533-1.012-2.813-2.59-2.813zm-4.658 6.368c-.23 0-.353-.143-.414-.3l-1.256-2.892h-5.27l-1.257 2.891c-.061.158-.184.3-.414.3-.275 0-.444-.19-.444-.426 0-.079.03-.158.061-.22l4.26-9.813c.091-.205.214-.316.429-.316.214 0 .337.11.429.316l4.259 9.812c.03.063.061.142.061.221 0 .237-.169.427-.444.427zm-4.305-9.45l-2.284 5.42h4.566l-2.282-5.42z" fill="#FFC600"></path><path fill="#CC9E00" d="M15.646 25.865h18.477v.5H15.646z"></path></g></svg>
                </div>
            </div>      
            <div class="tile__text-wrap">
                <div class="tile__text-wrap-inner">
                    <h3 class="h__block"><?php esc_html_e('Design here online', 'web-to-print-online-designer'); ?></h3>
                    <ul>
                        <li>- <?php esc_html_e('Already have your concept', 'web-to-print-online-designer'); ?></li>
                        <li>- <?php esc_html_e('Customise every detail', 'web-to-print-online-designer'); ?></li>
                    </ul>                                            
                </div>
                <svg class="tile--horizontal__chevron" viewBox="0 0 24 24" width="100%" height="100%"><path d="M10.5 18.5a1 1 0 0 1-.71-1.71l4.8-4.79-4.8-4.79A1 1 0 0 1 11.2 5.8l6.21 6.2-6.21 6.21a1 1 0 0 1-.7.29z"></path></svg>
            </div> 
        </div>    
    </div>
</div>