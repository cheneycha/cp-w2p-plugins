<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('NBD_Updates') ) {
    class NBD_Updates{
        protected static $instance;
        private $_remote_url        = 'https://cmsmart.net/index.php?option=com_cmsmart&controller=product_api&task=info';
        private $remote_url         = '';
        private $product_id         = 1074;
        private $tested             = "5.4.2";
        private $requires           = "4.6";
        private $version            = NBDESIGNER_VERSION;
        private $product_name       = "Web to Print Online Designer";
        private $slug               = "web-to-print-online-designer";
        private $plugin             = 'web-to-print-online-designer/nbdesigner.php';
        private $active_installs    = 3000;
        private $homepage           = 'https://cmsmart.net/wordpress-plugins/woocommerce-online-product-designer-plugin';
        private $author             = '<a href="http://netbaseteam.com/">Netbaseteam</a>';
        private $author_profile     = 'https://profiles.wordpress.org/netbaseteam';
        private $author_homepage    = "http://netbaseteam.com/";
        private $first              = null;
        public function __construct() {
            //todo
        }
        public function init(){
            $license = $this->get_license();
            $this->remote_url = add_query_arg( 
                array( 
                    'product_id'    => $this->product_id, 
                    'license'       => $this->get_license(),
                    'domain'        => base64_encode(rtrim(get_bloginfo('wpurl'), '/')),
                    'slug'          =>  $this->slug
                ), 
                $this->_remote_url
            );
            if( $license != 'baf8a5ad99b188ee599512346d9dea19' ){
                add_action( 'upgrader_process_complete', array(&$this, 'after_update'), 10, 2 );
                add_filter('admin_footer', array(&$this, 'custom_css') );
            }
            add_filter( 'plugins_api', array( &$this, 'plugin_info' ), 20, 3 );
            add_filter( 'site_transient_update_plugins', array( &$this, 'push_update' ) );
        }
        public function custom_css(){
            echo '<style>
                    #plugin-information #section-description img {
                        max-width: 100%;
                    } 
                    #plugin-information #section-reviews img.avatar-16 {
                        width: 16px;
                        height: 16px;
                    }
                    .column-design {
                        width: 10%;
                    }
                </style>';
        }
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function get_license(){
            $_license = get_option('nbdesigner_license');
            if( $_license ){
                $license = (array) json_decode( $_license );
                return $license['key'];
            }
            return 'baf8a5ad99b188ee599512346d9dea19';
        }
        public function plugin_info( $res, $action, $args ){
            if( $action !== 'plugin_information' ) return false;
            if( $this->slug !== $args->slug ) return $res;

            $valid = true;

            if ( false == $remote = get_transient( 'nbd_upgrade_'.$this->slug ) ) {
                $remote = wp_remote_get( $this->remote_url, array(
                    'timeout' => 10,
                    'headers' => array(
                        'Accept' => 'application/json'
                    ))
                );

                if ( !is_wp_error( $remote ) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
                    $_remote = json_decode( $remote['body'] );
                    if( isset( $_remote->license_validated ) ){
                        set_transient( 'nbd_upgrade_'.$this->slug, $remote, 43200 );
                        set_transient( 'nbd_upgrade_news_'.$this->slug, $remote, 86400 );
                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }
            }

            if ( !is_wp_error( $remote ) && !empty( $remote['body'] ) && $valid ) {
                $remote = json_decode( $remote['body'] );
                if( $remote->license_validated ){
                    $res                    = new stdClass();
                    $last_update            = $remote->sections->changelog[0]->created;
                    $res->name              = $this->product_name;
                    $res->slug              = $this->slug;
                    $res->version           = $remote->sections->changelog[0]->version_number;
                    $res->tested            = $this->tested;
                    $res->requires          = $this->requires;
                    $res->active_installs   = $this->active_installs;
                    $res->author            = $this->author; 
                    $res->author_profile    = $this->author_profile; 
                    $res->download_link     = $remote->download_link;
                    $res->trunk             = $remote->download_link;
                    $res->last_updated      = $last_update;
                    $res->sections          = array(
                        'description'   => $remote->sections->description, 
                        'installation'  => $this->render_installation(), 
                        'changelog'     => $this->render_changelog( $remote->sections->changelog ), 
                        'screenshots'   => $this->render_screenshots( $remote->path_screenshot, $remote->sections->screenshots ),
                        'reviews'       => $this->render_reviews( $remote->sections->reviews ),
                        'faq'           => $this->render_faq( $remote->sections->faq )
                    );
                    $res->rating        = 92;
                    $res->ratings       = $this->get_rating( $remote->sections->reviews );
                    $res->num_ratings   = count( $remote->sections->reviews );
                    $res->banners       = array(
                        'low'   => $remote->path_images .'/'. $remote->banner->path_images .'/'. $remote->banner->name,
                        'high'  => $remote->path_images .'/'. $remote->banner->path_images .'/'. $remote->banner->name
                    ); 
                    $res->homepage      = $this->homepage; 
                }
                return $res;
            }
            return false;
        }
        public function after_update( $upgrader_object, $options ){
            if ( $options['action'] == 'update' && $options['type'] === 'plugin' )  {
                delete_transient( 'nbd_upgrade_'.$this->slug );
            }
        }
        public function push_update( $transient ){
            if ( empty( $transient->checked ) ) {
                return $transient;
            }
            $valid = true;
            if ( false == $remote = get_transient( 'nbd_upgrade_' . $this->slug ) ) {
                $last_check_time = get_transient( 'nbd_last_time_check_upgrade_news' );
                if( false === $last_check_time ){
                    $remote = wp_remote_get( $this->remote_url, array(
                        'timeout' => 10,
                        'headers' => array(
                            'Accept' => 'application/json'
                        ) )
                    );

                    if ( !is_wp_error( $remote ) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
                        $_remote = json_decode( $remote['body'] );
                        if( isset( $_remote->license_validated ) ){
                            set_transient( 'nbd_upgrade_'.$this->slug, $remote, 43200 );
                            set_transient( 'nbd_upgrade_news_'.$this->slug, $remote, 86400 );
                        }else{
                            $valid = false;
                        }
                    }else{
                        $valid = false;
                    }

                    $last_check_time = time();
                    set_transient( 'nbd_last_time_check_upgrade_news' , $last_check_time, 43200 );
                }
            }
            if ( !is_wp_error( $remote ) && !empty( $remote['body'] ) && $valid ) {
                $remote         = json_decode( $remote['body'] );
                $plugin_info    = get_plugin_data( WP_PLUGIN_DIR . '/' . $this->plugin );
                if ( $remote->license_validated && version_compare( $plugin_info['Version'], $remote->sections->changelog[0]->version_number, '<' ) && version_compare( $this->requires, get_bloginfo('version'), '<' ) ) {
                    $res                = new stdClass();
                    $res->slug          = $this->slug;
                    $res->plugin        = $this->plugin;
                    $res->new_version   = $remote->sections->changelog[0]->version_number;
                    $res->tested        = $this->tested;
                    $res->package       = $remote->download_link;
                    $res->url           = $this->author_homepage;
                    $transient->response[$res->plugin] = $res;
                }
            }
            return $transient;
        }
        public function render_reviews( $reviews ){
            $html_reviews = '';
            $thumb_av= 'https://images-products.s3.amazonaws.com/'; 
            if(is_array( $reviews) ){
                foreach($reviews as $key => $review){
                    if( $key > 9 ) break;
                    $date = new DateTime($review->created);
                    $rating = (int) $review->review_rating;
                    $html_reviews .= '<div class="review">';
                    $html_reviews .=    '<div class="review-head">';
                    $html_reviews .=        '<div class="reviewer-info">';
                    $html_reviews .=            '<div class="review-title-section">';
                    $html_reviews .=                '<h4 class="review-title">Rating</h4>';
                    $html_reviews .=                '<div class="star-rating">';
                    $html_reviews .=                    '<div class="wporg-ratings" aria-label="'.$rating.' out of 5 stars" data-title-template="%s out of 5 stars" data-rating="'.$rating.'" style="color:#ffb900;">';
                    for($i = 1; $i < 6 ; $i++){
                        if( $rating < $i ){
                            $html_reviews .=                 '<span class="star dashicons dashicons-star-empty"></span>';
                        }else{
                            $html_reviews .=                 '<span class="star dashicons dashicons-star-filled"></span>';
                        }
                    }
                    $html_reviews .=                    '</div>';
                    $html_reviews .=                '</div>';
                    $html_reviews .=            '</div>';
                    $html_reviews .=            '<p class="reviewer">';
                    $html_reviews .=                'By <a href="#"><img src="https://cmsmart.net/templates/cmsmart/images/default_avarta.jpg"  srcset="https://cmsmart.net/templates/cmsmart/images/default_avarta.jpg 2x" class="avatar avatar-16 photo" height="16" width="16"/></a><a hre="#" class="reviewer-name">'.$review->name.'</a> on <span class="review-date">'.$date->format('F j, Y').'</span>';
                    $html_reviews .=            '</p>';
                    $html_reviews .=        '</div>';
                    $html_reviews .=    '</div>';
                    $html_reviews .=    '<div class="review-body">'.$review->comment.'</div>';
                    $html_reviews .= '</div>';
                }
            }
            return $html_reviews;
        }
        public function render_changelog( $changelog ){
            $html_changelog = '';
            if(is_array( $changelog) ){
                foreach ( $changelog as $log ){
                    $date = new DateTime($log->created);
                    $html_changelog .= '<h4>'.$log->version_number.' &#8211; '.$date->format('F j, Y').'</h4>';
                    $html_changelog .= $log->descriptions;
                }
            }
            return $html_changelog;
        }
        public function get_rating( $reviews ){
            $rating = array(
                "5" =>  0,
                "4" =>  0,
                "3" =>  0,
                "2" =>  0,
                "1" =>  0
            );
            if(is_array( $reviews) ){
                foreach ( $reviews as $review ){
                    $rat = (int) $review->review_rating;
                    $rating[$rat]++;
                }
            }
            return $rating;
        }
        public function render_screenshots( $path_screenshot, $screenshots ){
            $html_screenshot = '';
            if(is_array( $screenshots) ){
                $html_screenshot .= '<ol>';
                foreach($screenshots as $screenshot){
                    $html_screenshot .= '<li>';
                    $html_screenshot .= '<a href="'.$path_screenshot.$screenshot->name.'"><img src="'.$path_screenshot.$screenshot->name.'"/></a><p>'.$screenshot->description.'</p>';
                    $html_screenshot .= '</li>';
                }
                $html_screenshot .= '</ol>';
            }
            return $html_screenshot;
        }
        public function render_installation(){
            $html_installation  = '';
            $html_installation .= '<h4>Minimum Requirements</h4>';
            $html_installation .= '<ul>';
            $html_installation .=   '<li>PHP 5.6.x or greater is required</li>';
            $html_installation .=   '<li>WoooCommerce 3.0.+ is required</li>';
            $html_installation .=   '<li>PHP allow_url_fopen is required</li>';
            $html_installation .=   '<li>MySQL 5.6 or greater is recommended</li>';
            $html_installation .=   '<li>PHP Imagick API is recommended. Imagick include lcms2 is required for JPG CMYK mode.</li>';
            $html_installation .= '</ul>';
            return $html_installation;
        }
        public function render_faq( $faqs ){
            $html_faqs = '';
            if(is_array( $faqs) ){
                foreach ( $faqs as $faq ){
                    $html_faqs .= '<h4>'.$faq->title.'</h4>';
                    $html_faqs .= '<ul><li>'.$faq->description.'</li></ul>';
                }
            }
            return $html_faqs;
        }
    }
}
$nbd_updates = NBD_Updates::instance();
$nbd_updates->init();