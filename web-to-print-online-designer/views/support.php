<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<?php
$changelogs = $nbd_news->sections->changelog;
$faqs = $nbd_news->sections->faq;
?>
<style type="text/css" >
.nbd-support-wrap {
    background: #f1f1f1;
    padding: 50px;
    border: 10px solid #fff;
    margin-top: 20px;
    margin-right: 20px;
    color: #608299;
}    
.nbd-welcome-nav {
    background-color: #0093d3;
    margin: -50px -50px 0 -50px;
}
.nbd-welcome-nav__version {
    float: right;
    color: #fff;
    display: inline-block;
    padding: 1em 2em;
}
.nbd-welcome-nav ul {
    margin: 0 0 0 1em;
}
.nbd-welcome-nav li {
    display: inline-block;
    margin: 0;
}
.nbd-welcome-nav a {
    text-decoration: none;
    color: #fff;
    display: inline-block;
    padding: 1em;
}
.nbd-logo {
    border: 0;
    background: #fff;
    display: block;
    margin: 0 auto;
    border-radius: 100%;
    width: 150px;
    height: 150px;
    text-align: center;
    position: relative;
    margin-top: -30px;
}
.nbd-logo img {
    position: absolute;
    top: 50%;
    left: 50%;
    -webkit-transform: translateX(-50%) translateY(-50%);
    -ms-transform: translateX(-50%) translateY(-50%);
    transform: translateX(-50%) translateY(-50%);
    width: 90px;
    height: auto;
}
.nbd-intro {
    max-width: 415px;
    margin: 0 auto;
    text-align: center;
}
.nbd-enhance {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: stretch;
    -ms-flex-align: stretch;
    align-items: stretch;
    margin: 4em 0;
}
.nbd-enhance .nbd-enhance__column {
    border: 1px solid #d8e2e9;
    width: 300px;
    margin: 0 1em;
    background: #fff;
}
.nbd-enhance .nbd-change-log {
    -webkit-box-ordinal-group: 2;
    -ms-flex-order: 1;
    order: 1;
    position: relative;
}
.nbd-enhance .nbd-faq {
    -webkit-box-ordinal-group: 3;
    -ms-flex-order: 2;
    order: 2;
}
.nbd-enhance .nbd-other-product {
    -webkit-box-ordinal-group: 4;
    -ms-flex-order: 3;
    order: 3;
}
.nbd-enhance .nbd-enhance__column h3 {
    font-size: 1em;
    font-weight: 400;
    color: #87a6bc;
    border-bottom: 1px solid #d8e2e9;
    padding: 1em 1.5em;
    margin: 0;
}
.nbd-enhance .nbd-change-log .nbd-change-log__wrap,
.nbd-enhance .nbd-faq .nbd-faq__wrap {
    min-height: 390px;
    max-height: 390px;
    overflow: scroll;
    margin: 0;
    padding: 0 1.5em 1.5em;
}
.nbd-intro {
    max-width: 415px;
    margin: 0 auto;
    text-align: center;
}

.nbd-project {
    text-align: center;
    clear: both;
}
.nbd-project p {
    font-size: .75em;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #aaa;
}
.nbd-project a {
    color: #00aadc;
    text-decoration: underline;
}
.nbd-project img {
    max-height: 20px;
    margin: 0 .5407911001em;
    margin-top: -3px;
    opacity: .5;
    background: #fff;
    padding: 3px;
    vertical-align: middle;    
}
.nbd-enhance .nbd-enhance__column img {
    width: 100%;
    height: auto;
}
.nbd-enhance__column.nbd-other-product p,
.nbd-enhance__column.nbd-other-product h4 {
    margin: 0 1.5em 1em;
}
.nbd-enhance__column.nbd-other-product h4 {
    margin-top: 1em;
}
.nbd-enhance__column.nbd-other-product h4 a {
    text-decoration: none;
}
@media (max-width: 768px){
    .nbd-logo {
        margin-top: 20px;
    }
    .nbd-enhance {
        display: block;
        margin: 2em 0;
    }   
    .nbd-enhance {
        margin-left: -50px;
    }
    .nbd-enhance .nbd-enhance__column {
        margin-bottom: 1em;
    }
}
</style>
<div class="nbd-support-wrap">
    <section class="nbd-welcome-nav">
        <span class="nbd-welcome-nav__version">NBDESIGNER <?php echo NBDESIGNER_VERSION; ?></span>
        <ul>
            <li><a href="https://cmsmart.net/support_ticket" target="_blank"><?php esc_html_e( 'Support', 'web-to-print-online-designer' ); ?></a></li>
            <li><a href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin/userguides" target="_blank"><?php esc_html_e( 'Documentation', 'web-to-print-online-designer' ); ?></a></li>
            <li><a href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin" target="_blank"><?php esc_html_e( 'Community', 'web-to-print-online-designer' ); ?></a></li>
        </ul>        
    </section>
    <div class="nbd-logo">
        <img src="<?php echo NBDESIGNER_PLUGIN_URL; ?>/assets/images/logo.svg" alt="Storefront" />
    </div> 
    <div class="nbd-intro">
        <p><?php esc_html_e('Hello! You might be interested in the following NBDesigner NEWS and our printing solutions.', 'web-to-print-online-designer'); ?></p>
    </div>
    <div class="nbd-enhance">
        <div class="nbd-enhance__column nbd-change-log">
            <h3><?php esc_html_e( 'Change log', 'web-to-print-online-designer' ); ?></h3>
            <div class="nbd-change-log__wrap">
                <?php 
                    if( is_array( $changelogs ) ):
                    foreach ( $changelogs as $log ):
                    $date = new DateTime($log->created);    
                ?>
                <h4><?php echo $log->version_number.' &#8211; '.$date->format('F j, Y'); ?></h4>
                <div><?php echo $log->descriptions; ?></div>
                <?php 
                    endforeach;
                    endif; 
                ?>
            </div>
        </div>
        <div class="nbd-enhance__column nbd-faq">
            <h3><?php esc_html_e( 'FAQs', 'web-to-print-online-designer' ); ?></h3>
            <div class="nbd-faq__wrap">
                <?php 
                    if( is_array( $faqs ) ):
                    foreach ( $faqs as $faq ):   
                ?>
                <h4><?php echo $faq->title; ?></h4>
                <div><?php echo $faq->description; ?></div>
                <?php 
                    endforeach;
                    endif; 
                ?>
            </div>            
        </div>
        <div class="nbd-enhance__column nbd-other-product">
            <h3><?php esc_html_e( 'Printing solution', 'web-to-print-online-designer' ); ?></h3>
            <a href="https://cmsmart.net/tshirt-printing-store-ecommerce-website-with-online-designer" target="_blank"><img src="<?php echo NBDESIGNER_PLUGIN_URL; ?>/assets/images/t-shirt.jpg" /></a>
            <h4><a href="https://cmsmart.net/tshirt-printing-store-ecommerce-website-with-online-designer" target="_blank">T-SHIRT PRINTING SOLUTION</a></h4>
            <p><?php esc_html_e( 'You have a T-shirt printing business and you want your customers to have a great experience at your site.', 'web-to-print-online-designer' ); ?></p>
            <a href="https://cmsmart.net/wordpress-themes/wordpress-printshop-website-templates-with-online-design-packages" target="_blank"><img src="<?php echo NBDESIGNER_PLUGIN_URL; ?>/assets/images/print-solution.jpg" /></a>
            <h4><a href="https://cmsmart.net/wordpress-themes/wordpress-printshop-website-templates-with-online-design-packages" target="_blank">PRINTING ECOMMERCE SOLUTION</a></h4>
            <p><?php esc_html_e( 'You got a big printing business and you want to manage all things clean and clear.', 'web-to-print-online-designer' ); ?></p>            
        </div>      
    </div>
    <div class="nbd-project">
        <p>
            <?php printf( esc_html__( 'A %s project', 'web-to-print-online-designer' ), '<a href="http://netbaseteam.com/" target="_blank"><img src="' . NBDESIGNER_PLUGIN_URL . '/assets/images/netbaseteam.png" alt="Netbase Team" /></a>' ); ?>
        </p>
    </div>
</div>
