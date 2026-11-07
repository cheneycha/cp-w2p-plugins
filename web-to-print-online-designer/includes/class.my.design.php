<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly   ?>
<?php
require_once(NBDESIGNER_PLUGIN_DIR . 'includes/launcher/class.designer.php');
class My_Design_Endpoint {

    /**
     * Custom endpoint name.
     *
     * @var string
     */
    public static $endpoint = 'my-designs';
    public $query_vars = array();
    /**
     * Plugin actions.
     */
    public function __construct() {
        //Declare query vars
        $this->query_vars = array(
            'my_designs'    => 'my-designs',
            'view_design'   => 'view-design',
            'artist_info'   => 'artist-info'
        );
    }
    public function init(){
        // Actions used to insert a new endpoint in the WordPress.
        add_action('init', array($this, 'add_endpoints'));
        add_filter('query_vars', array($this, 'add_query_vars'), 0);

        // Change the My Accout page title.
        add_filter('the_title', array($this, 'endpoint_title'));

        // Inserting your new tab/page into the My Account page.
        add_filter('woocommerce_account_menu_items', array($this, 'new_menu_items'));
        foreach ( $this->query_vars as $key => $var ){
            add_action('woocommerce_account_' . $var . '_endpoint', array($this, 'endpoint_content_'.$key), 10, 1);
        }
        
        //Inserting user info
        add_action( 'show_user_profile', array( $this, 'user_profile' ) );
        add_action( 'edit_user_profile', array( $this, 'user_profile' ) );  

        //Update user info
        add_action( 'personal_options_update', array( $this, 'process_user_option_update' ) );
        add_action( 'edit_user_profile_update', array( $this, 'process_user_option_update' ) );  
        
        //Design page breadcrumbs
        add_filter( 'woocommerce_get_breadcrumb', array( $this, 'design_page_breadcrumb'), 10 ,1  );
        add_filter( 'body_class', array($this, 'add_body_class'), 10, 1 );
        
        //Load assets
        add_action( 'wp_enqueue_scripts', array($this, 'nbd_gallery_enqueue_scripts') );
        
        //User update artist name
        $this->ajax();
        
        //Gallery
        add_shortcode( 'nbdesigner_gallery', array($this,'nbd_gallery_func') );
        //add_action( 'wp_head', array( &$this, 'set_open_graph_image' ), 1000 );
        add_action( 'delete_post', array($this,'delete_categories_transient') );
        add_action( 'save_post', array($this,'delete_categories_transient') );
        add_action( 'deleted_user', array($this,'delete_designs_transient') );
    }
    public function ajax(){
        $ajax_events = array(
                'nbd_update_artist_name'                            => true,
                'nbd_update_art'                                    => true,
                'nbd_get_designs_in_cart'                           => true,
                'nbd_get_user_designs'                              => true,
                'nbd_update_favorite_template'                      => true,
                'nbd_update_artist_info'                            => true,
                'nbd_save_for_later'                                => false,
                'nbd_update_my_template'                            => true,
                'nbd_delete_my_template'                            => true,
                'nbd_get_template_preview'                          => true,
                'nbd_delete_my_design'                              => true,
                'nbd_add_design_to_cart'                            => true,
                'nbd_get_list_product_ready_to_create_template'     => true,
                'nbd_get_preview_product_before_create_template'    => true,
                'nbd_get_next_gallery_page'                         => true,
                'nbd_search_template'                               => true
            );
        foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
            if ($nopriv) {
                // NBDesigner AJAX can be used for frontend ajax requests
                add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
            }
        }
    }
    /**
     * Register new endpoint to use inside My Account page.
     *
     * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
     */
    public function add_endpoints() {
        foreach ( $this->query_vars as $var ){
            add_rewrite_endpoint($var, EP_ROOT | EP_PAGES);
        }
    }

    /**
     * Add new query var.
     *
     * @param array $vars
     * @return array
     */
    public function add_query_vars($vars) {
        foreach ( $this->query_vars as $var ){
            $vars[] = $var;
        }
        return $vars;
    }

    /**
     * Set endpoint title.
     *
     * @param string $title
     * @return string
     */
    public function endpoint_title($title) {
        global $wp_query;
        foreach ( $this->query_vars as $var ){
            $is_endpoint = isset($wp_query->query_vars[$var]);
            if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
                switch ( $var ) {
                    case 'my-designs': 
                        $title = esc_html__('My designs', 'web-to-print-online-designer');
                        break;
                    case 'view-design': 
                        $title = esc_html__('View design', 'web-to-print-online-designer');
                        break;
                    case 'artist-info': 
                        $title = esc_html__('Artist info', 'web-to-print-online-designer');
                        break;
                }
                remove_filter('the_title', array($this, 'endpoint_title'));
            }
        }
        return $title;
    }

    /**
     * Insert the new endpoint into the My Account menu.
     *
     * @param array $items
     * @return array
     */
    public function new_menu_items($items) {
        // Remove the logout menu item.
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);

        // Insert your custom endpoint.
        $items[self::$endpoint] = esc_html__('My designs', 'web-to-print-online-designer');

        // Insert back the logout item.
        $items['customer-logout'] = $logout;

        return $items;
    }

    /**
     * Endpoint HTML content.
     */
    public function endpoint_content_my_designs() {
        global $wp;
        $current_page = absint($wp->query_vars['my-designs']);
        if( !$current_page ) $current_page = 1;
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $item_per_page = 10;
        $item_per_page = apply_filters('nbd_number_design_per_page', $item_per_page);
        $designs = $this->get_my_designs($user_id, $current_page, $item_per_page);
        $number_design = $this->count_designs($user_id);
        ob_start();
        nbdesigner_get_template('mydesign/my-designs.php', array(
            'user'          => $user, 
            'designs'       => $designs, 
            'total'         => $number_design, 
            'item_per_page' => $item_per_page,
            'current_page'  => $current_page ));
        $content = ob_get_clean();
        echo $content;
    }
    public function endpoint_content_view_design() {
        global $wp;
        $did = absint($wp->query_vars['view-design']);
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $design = $this->get_design($user_id, $did);
        ob_start();
        nbdesigner_get_template('mydesign/detail-design.php', array( 'design'  =>  $design, 'user_id' => $user_id ));
        $content = ob_get_clean();
        echo $content;
    }
    public function endpoint_content_artist_info() {
        ob_start();
        nbdesigner_get_template('mydesign/edit-info.php', array( ));
        $content = ob_get_clean();
        echo $content;
    }
    public function user_profile( $user ) {
        wp_nonce_field( 'nbd_user_profile_update', 'nbd_nonce' );
        require_once NBDESIGNER_PLUGIN_DIR . 'views/user-profile.php';
    }
    /**
     * Filter POST variables.
     *
     * @param string $var_name Name of the variable to filter.
     *
     * @return mixed
     */
    private function filter_input_post( $var_name ) {
        $val = filter_input( INPUT_POST, $var_name );
        if ( $val ) {
            return sanitize_text_field( $val );
        }
        return '';
    }    
    /**
     * Updates the user metas that (might) have been set on the user profile page.
     *
     * @param    int $user_id of the updated user.
     */
    public function process_user_option_update( $user_id ) {
        update_user_meta( $user_id, '_nbd_profile_updated', time() );

        $nonce_value = $this->filter_input_post( 'nbd_nonce' );

        if ( empty( $nonce_value ) ) { // Submit from alternate forms.
            return;
        }
        $settings = array(
            'artist_name'         => $this->filter_input_post( 'nbd_artist_name' ),
            'artist_phone'        => $this->filter_input_post( 'nbd_artist_phone' ),
            'artist_banner'       => $this->filter_input_post( 'nbd_artist_banner' ),
            'artist_address'      => $this->filter_input_post( 'nbd_artist_address' ),
            'artist_facebook'     => $this->filter_input_post( 'nbd_artist_facebook' ),
            'artist_twitter'      => $this->filter_input_post( 'nbd_artist_twitter' ),
            'artist_linkedin'     => $this->filter_input_post( 'nbd_artist_linkedin' ),
            'artist_youtube'      => $this->filter_input_post( 'nbd_artist_youtube' ),
            'artist_instagram'    => $this->filter_input_post( 'nbd_artist_instagram' ),
            'artist_flickr'       => $this->filter_input_post( 'nbd_artist_flickr' ),
            'artist_commission'   => $this->filter_input_post( 'nbd_artist_commission' ),
            'artist_description'  => $this->filter_input_post( 'nbd_artist_description' ),
            'create_design'       => $this->filter_input_post( 'nbd_create_design' ),
            'sell_design'         => $this->filter_input_post( 'nbd_sell_design' ),
            'auto_approve_design' => $this->filter_input_post( 'nbd_auto_approve_design' ),
            'payment'             => $this->filter_input_post( 'nbd_payment' )
        );
        if( check_admin_referer( 'nbd_user_profile_update', 'nbd_nonce' ) ){
            $designer = new NBD_Designer( $user_id );
            $designer->update( $settings );
            $this->delete_designs_transient();

            $enable_designer    = $this->filter_input_post( 'nbd_sell_design' );
            $user               = new \WP_User( $user_id );
            if( $enable_designer == 'on' ){
                $user_meta      = get_userdata( $user_id );
                $user_roles     = $user_meta->roles;
                $is_designer    = false;
                foreach( $user_roles as $role ){
                    $desiger_roles = array('administrator', 'shop_manager', 'designer');
                    if( in_array( $role, $desiger_roles ) ){
                        $is_designer    = true;
                    }
                }
                if( !$is_designer ){
                    $user->add_role( 'designer' );
                }
            }else{
                $user->remove_role( 'designer' );
            }
        }
    }
    public function nbd_update_artist_name(){
        if (!wp_verify_nonce($_POST['nbd_nonce'], 'nbd_artist_update') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }
        $result = array('flag' => 0, 'name' => '');
        $user_id = wp_get_current_user()->ID;

        $designer = new NBD_Designer( $user_id );
        $designer->update( array(
            'artist_name' => $this->filter_input_post( 'nbd_artist_name' )
        ) );

        $result['name'] = $this->filter_input_post( 'nbd_artist_name' );
        $result['flag'] = 1;
        echo json_encode($result); wp_die();
    }
    public function nbd_update_artist_info(){
        if ((!wp_verify_nonce($_POST['_wpnonce'], 'nbd_artist_settings_nonce') && NBDESIGNER_ENABLE_NONCE) || !isset( $_POST['user_id'] )) {
            die('Security error');
        }
        $user_id = get_current_user_id();
        if( $user_id != $_POST['user_id'] )  die('Security error');
        $list = array(
            'artist_name',
            'artist_phone',
            'artist_banner',
            'artist_address',
            'artist_facebook',
            'artist_twitter',
            'artist_linkedin',
            'artist_youtube',
            'artist_instagram',
            'artist_flickr',
            'artist_description',
            'payment'
        );
        $data = array();

        foreach( $_POST as $key => $value ) {
            foreach( $list as $val ) {
                if( 'nbd_' . $val == $key ) {
                    $data[ $val ] = $this->filter_input_post( $key );
                }
            }
        }
        if( isset( $_POST['gravatar_id'] ) && absint( $_POST['gravatar_id'] ) != 0 ){
            $data['gravatar_id'] = absint( $_POST['gravatar_id'] );
        }

        if( isset( $_POST['nbd_become_designer'] ) ){
            $user_meta      = get_userdata( $user_id );
            $user_roles     = $user_meta->roles;
            $user           = new \WP_User( $user_id );
            $is_designer    = false;
            if( $_POST['nbd_become_designer'] == 1 ){
                foreach( $user_roles as $role ){
                    $desiger_roles = array('administrator', 'shop_manager', 'designer');
                    if( in_array( $role, $desiger_roles ) ){
                        $is_designer    = true;
                    }
                }
                if( !$is_designer ){
                    $user->add_role( 'designer' );
                }
                $data['create_design']  = 'on';
            } else {
                foreach( $user_roles as $role ){
                    if( $role == 'designer' ){
                        $is_designer    = true;
                    }
                }
                if( $is_designer ){
                    $user->remove_role( 'designer' );
                    update_user_meta( $user_id, 'nbd_sell_design', '' );
                    $data['sell_design']    = '';
                    $data['create_design']  = '';
                }
            }
        }

        $designer = new NBD_Designer( $user_id );
        $designer->update( $data );

        $this->delete_designs_transient();

        wp_send_json(
            array(
                'result' => 1
            )
        );
    }
    public function nbd_update_art(){
        if (!wp_verify_nonce($_POST['nbd_nonce'], 'nbd_artist_update') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }
        $result     = array('flag' => 0);
        $price      = wc_clean( $_POST['nbd-design-price'] );
        $status     = wc_clean( $_POST['nbd-design-status'] );
        $id         = wc_clean( $_POST['nbd-design-id'] );
        global $wpdb;
        $table_name =  $wpdb->prefix . 'nbdesigner_mydesigns';
        $re = $wpdb->update($table_name, array(
            'price'     => $price,
            'publish'   => $status
        ), array( 'id' => $id));
        if($re) $result = array('flag' => 1);
        echo json_encode($result); wp_die();
    }
    private function insert_my_designs($product_id, $variation_id, $folder){
        global $wpdb;
        $created_date   = new DateTime();
        $user_id        = wp_get_current_user()->ID;
        $table_name     =  $wpdb->prefix . 'nbdesigner_mydesigns';
        $wpdb->insert($table_name, array(
            'product_id'    => $product_id,
            'variation_id'  => $variation_id,
            'price'         => 0,
            'folder'        => $folder,
            'user_id'       => $user_id,
            'publish'       => 1,
            'created_date'  => $created_date->format('Y-m-d H:i:s')
        ));
        return true;
    }
    public function get_my_designs( $user_id, $current_page, $item_per_page ){
        global $wpdb;
        $offset     = ($current_page - 1) * $item_per_page;
        $designs    = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE user_id = {$user_id} ORDER BY created_date DESC LIMIT {$item_per_page} OFFSET {$offset}" );
        return $designs;
    }
    public function get_design( $user_id, $did ){
        global $wpdb;
        $designs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE user_id = {$user_id} AND id = {$did}" );
        return $designs[0];
    }
    public static function get_template( $user_id, $tid ){
        global $wpdb;
        $templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE user_id = {$user_id} AND id = {$tid}" );
        if( isset( $templates[0] ) ) return $templates[0];
        return false;
    }
    public function count_designs( $user_id ){
        global $wpdb;
        $designs = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE user_id = {$user_id}" );
        return $designs;
    }
    public function nbd_get_designs_in_cart(){
        if( !isset( $_POST['did'] ) ){
            global $woocommerce; 
            $items = $woocommerce->cart->get_cart();
            $result = array(
                'flag'      => 1,
                'designs'   => array()
            );
            foreach( $items as $cart_item_key => $values ) {
                $nbd_session = WC()->session->get( $cart_item_key. '_nbd' );
                if( $nbd_session ){
                    $path_preview   = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/preview';
                    $listThumb      = Nbdesigner_IO::get_list_images( $path_preview );
                    $image          = '';
                    if( count( $listThumb ) ){
                        asort( $listThumb );
                        $image = Nbdesigner_IO::wp_convert_path_to_url( reset( $listThumb ) );
                        $result['designs'][] = array(
                            'id'    => $nbd_session,
                            'src'   => $image
                        );
                    }  
                }
            }
        }else { 
            $folder = wc_clean( $_POST['did'] );
            $result = nbd_get_template_by_folder( $folder );
            $result['flag'] = 1;
        }
        echo json_encode( $result );
        wp_die();
    }
    public function nbd_get_user_designs(){
        $user_id = wp_get_current_user()->ID;
        $result = array(
            'flag'   =>  1
        );
        if( $user_id ){
            if( !isset($_POST['did']) ){
                global $wpdb;
                $table_name =  $wpdb->prefix . 'nbdesigner_mydesigns';
                if( isset($_POST['product_id']) ){
                    $product_id     = absint( $_POST['product_id'] );
                    $variation_id   = absint( $_POST['variation_id'] );
                    $designs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE user_id = {$user_id} AND product_id = {$product_id} AND variation_id = {$variation_id} ORDER BY created_date DESC" );
                }else{
                    $designs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE user_id = {$user_id} ORDER BY created_date DESC" );
                }
                foreach( $designs as $design ){
                    $path_preview   = NBDESIGNER_CUSTOMER_DIR .'/'.$design->folder. '/preview';
                    $listThumb      = Nbdesigner_IO::get_list_images( $path_preview );
                    $image          = '';
                    if( count( $listThumb ) ){
                        asort($listThumb);
                        $image = Nbdesigner_IO::wp_convert_path_to_url( reset( $listThumb ) );
                        $result['designs'][] = array(
                            'id'    => $design->folder,
                            'src'   => $image
                        );
                    }
                }
            }else {
                $folder         = wc_clean( $_POST['did'] );
                $result         = nbd_get_template_by_folder( $folder );
                $result['flag'] = 1;
            }  
        } else {
            $result['flag'] = 0;
        }
        echo json_encode( $result );
        wp_die();
    }
    /**
     * Plugin install action.
     * Flush rewrite rules to make our custom endpoint available.
     */
    public static function install() {
        flush_rewrite_rules();
    }
    public static function nbdesigner_insert_table_my_design( $product_id, $variation_id, $folder ){
        global $wpdb;
        $created_date   = new DateTime();
        $user_id        = wp_get_current_user()->ID;
        $table_name     =  $wpdb->prefix . 'nbdesigner_mydesigns';
        $wpdb->insert($table_name, array(
            'product_id'    => $product_id,
            'variation_id'  => $variation_id,
            'folder'        => $folder,
            'user_id'       => $user_id,
            'created_date'  => $created_date->format('Y-m-d H:i:s')
        ));
        return true;
    }
    /**
     * Generate breadcrumb for design page
     *
     * @since 1.7.1
     *
     * @param array $crumbs
     *
     * @return array $crumbs
     */
    public function design_page_breadcrumb( $crumbs ){
        global $wp;
        if( is_page( wc_get_page_id( 'myaccount' ) ) && isset( $wp->query_vars['my-designs'] ) ){
            $my_designs = get_query_var('my-designs');
            $crumb = ($my_designs != '') ? sprintf(__('My designs page( %s )', 'web-to-print-online-designer'), $my_designs) : esc_html__('My designs', 'web-to-print-online-designer');
            $crumbs[] = array( $crumb, wc_get_endpoint_url( 'my-designs', $my_designs, wc_get_page_permalink( 'myaccount' ) ) );
        }
        return $crumbs;
    }
    public function add_body_class( $classes ){
        if( is_nbd_gallery_page() || is_nbd_designer_page() ){
            $classes[] = 'nbd-no-breadcrumb nbd-gallery';
        }
        if( is_nbd_gallery_page() ){
            $classes[] = 'nbd-gallery';
        }
        return $classes;
    }
    public function nbd_gallery_enqueue_scripts(){
        if( is_nbd_gallery_page() ){
            //wp_enqueue_script('nbd-gallery-js', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js', array('jquery'));
            //wp_enqueue_style('nbd-gallery-css', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css');
            wp_enqueue_script('masonry');
        }
        if( is_nbd_designer_page() ){
            wp_enqueue_style('nbd-artist-css', NBDESIGNER_CSS_URL . 'artist.css');
            wp_enqueue_script('masonry');
        }
    }
    public function nbd_gallery_func($atts, $content = null) {
        if ( is_null( WC()->session ) ) {
            return;
        }
        $page                   = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; 
        $per_row                = intval( apply_filters( 'nbd_gallery_designs_per_row', 3 ) );
        $row                    = apply_filters( 'nbd_gallery_designs_row', 5 );
        $favourite_templates    = $this->get_favourite_templates();
        $cat                    = (isset($_GET['cat']) && absint($_GET['cat'])) ? absint($_GET['cat']) : 0;
        $pid                    = (isset($_GET['pid']) && absint($_GET['pid'])) ? absint($_GET['pid']) : 0;
        $tag                    = isset( $_GET['tag'] ) ? wc_clean( $_GET['tag'] ) : '';
        $color                  = isset( $_GET['color'] ) ? wc_clean( $_GET['color'] ) : '';
        $search                 = isset( $_GET['search'] ) ? wc_clean( $_GET['search'] ) : '';
        $search_type            = isset( $_GET['search_type'] ) ? wc_clean( $_GET['search_type'] ) : '';
        $url                    = getUrlPageNBD('gallery');
        if($pid) $url = add_query_arg(array('pid' => $pid), $url);
        if($cat) $url = add_query_arg(array('cat' => $cat), $url);
        if( $tag != '' ){
            $url    = add_query_arg(array('tag' => $tag), $url);
        }
        if( $color != '' ){
            $url    = add_query_arg(array('color' => $color), $url);
        }
        if( $search != '' ){
            $url    = add_query_arg(array('search' => $search), $url);
            if( $search_type != '' ){
                $url    = add_query_arg(array('search_type' => $search_type), $url);
            }
        }
        $atts = shortcode_atts(array(
            'row'                   => $row,
            'per_row'               => $per_row,
            'pagination'            => 'true',
            'url'                   => $url,
            'des'                   => esc_html__( 'Gallery design templates', 'web-to-print-online-designer' ),
            'page'                  => $page,
            'cat'                   => $cat,
            'pid'                   => $pid,
            'color'                 => $color,
            'search'                => $search,
            'search_type'           => $search_type,
            'tag'                   => $tag,
            'favourite_templates'   => $favourite_templates,
            'fts'                   => $this->get_detail_favourite_templates(),
            'templates'             => array(),
            'products'              => nbd_get_products_has_design(),
            'categories'            => $this->get_categories_has_design(),
            'designers'             => $this->get_designers(),
            'total'                 => ( $pid != 0 || ( isset( $atts['pid'] ) && $atts['pid'] != 0 ) ) ? $this->count_total_template( $pid, false, $cat ) : $this->count_total_template( false, false, $cat )
        ), $atts);
        if( $atts['per_row'] > 6 ) $atts['per_row'] = 6;
        $atts['templates']  = $this->nbdesigner_get_templates_by_page( $page, absint($atts['row'] ), absint($atts['per_row'] ), $atts['pid'], false, false, $cat, $tag, $color, '', $search, $search_type );
        $atts['total']      = $this->nbdesigner_get_templates_by_page( $page, absint($atts['row'] ), absint($atts['per_row'] ), $atts['pid'], true, false, $cat, $tag, $color, '', $search, $search_type, true );

        ob_start();
        nbdesigner_get_template( 'gallery/main.php', $atts );
        return ob_get_clean();
    }
    public function nbd_get_next_gallery_page(){
        if (!wp_verify_nonce($_POST['nonce'], 'nbd_update_favourite_template') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }
        $result['flag'] = 0;
        $page           = absint( $_POST['page'] );
        $row            = absint( $_POST['row'] );
        $total          = absint( $_POST['total'] );
        $limit          = absint( $_POST['limit'] );
        $per_row        = absint( $_POST['per_row'] );
        $url            = esc_url( $_POST['url'] );
        $parts          = parse_url( $_POST['url'] );
        if( isset($parts['query']) ){
            parse_str($parts['query'], $query);
        }else{
            $query = array();
        }
        $pid            = isset($query['pid']) ? $query['pid'] : false;
        $cat            = isset($query['cat']) ? $query['cat'] : false;
        $tag            = isset($query['tag']) ? $query['tag'] : '';
        $color          = isset($query['color']) ? $query['color'] : '';
        $search         = isset($query['search']) ? $query['search'] : '';
        $search_type    = isset($query['search_type']) ? $query['search_type'] : '';
        $artist_id      = isset($query['id']) ? $query['id'] : false;
        $templates      = $this->nbdesigner_get_templates_by_page( $page, $row, $per_row, $pid, false, $artist_id, $cat, $tag, $color, '', $search, $search_type );
        $favourite_templates = $this->get_favourite_templates();
        ob_start();
        if( count( $templates ) ){
            $result['flag'] = 1;
            nbdesigner_get_template('gallery/gallery-item.php', array(
                'templates'             => $templates,
                'current_user_id'       => get_current_user_id(),
                'favourite_templates'   => $favourite_templates
            ));
            $result['items'] = ob_get_clean();
        }
        ob_start();
        nbdesigner_get_template('gallery/pagination.php', array(
            'total' => $total,
            'limit' => $limit,
            'url'   => $url,
            'page'  => $page
        ));
        $result['pagination'] = ob_get_clean();
        wp_send_json($result);
    }
    public function nbd_search_template(){
        global $wpdb;
        $sql    = "SELECT * FROM {$wpdb->prefix}nbdesigner_templates ";
        $result = array(
            'tag'       => '',
            'templates' => array()
        );

        $product_id     = absint( $_POST['product_id'] );
        $variation_id   = absint( $_POST['variation_id'] );
        $search         = wc_clean( $_POST['search'] );

        $term           = get_term_by( 'name', $search, 'template_tag' );
        if ( $term && ! is_wp_error( $term ) ) {
            $tag_query      = "( FIND_IN_SET('". $term->term_id ."', tags) > 0 )";
            $result['tag']  = $term->term_id;
        }else{
            $tag_query      = "name LIKE '%$search%'";
        }
        $sql .= "WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND publish = 1 AND $tag_query ORDER BY created_date DESC";
        
        $templates = $wpdb->get_results( $sql, 'ARRAY_A' );

        if( count( $templates ) ){
            foreach ( $templates as $tem ){
                $path_preview   = NBDESIGNER_CUSTOMER_DIR .'/'.$tem['folder']. '/preview';
                $listThumb      = Nbdesigner_IO::get_list_images( $path_preview );
                asort( $listThumb );
                if( count( $listThumb ) ){
                    $_temp          = array();
                    $_temp['id']    = $tem['folder'];
                    foreach( $listThumb as $img ){
                        $_temp['src'][] = Nbdesigner_IO::wp_convert_path_to_url( $img );
                    }
                    if( isset( $tem['thumbnail'] ) && $tem['thumbnail'] ){
                        $_temp['thumbnail'] = wp_get_attachment_url( $tem['thumbnail'] );
                    }else{
                        $_temp['thumbnail'] = $_temp['src'][0];
                    }
                    $result['templates'][] = $_temp;
                }
            }
        }

        wp_send_json( $result );
    }
    public function nbd_update_favorite_template(){
        if (!wp_verify_nonce($_POST['nonce'], 'nbd_update_favourite_template') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }
        $type           = wc_clean( $_POST['type'] );
        $template_id    = wc_clean( $_POST['template_id'] );
        $this->set_favourite_templates( $template_id, $type );
        $templates      = $this->get_favourite_templates(); 
        wp_send_json(
            array(
                'result'    => 1,
                'templates' => $templates
            )
        );
    }
    public function nbd_save_for_later(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $product_id     = absint( $_POST['product_id'] );
        $variation_id   = absint( $_POST['variation_id'] );
        $folder         = wc_clean( $_POST['folder'] );
        $design_folder  = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
        $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $folder;
        $design_path    = NBDESIGNER_CUSTOMER_DIR . '/' . $design_folder;
        Nbdesigner_IO::copy_dir( $path, $design_path );
        $result         = array(
            'flag'      => 1,
            'folder'    => $design_folder
        );
        if( isset( $_POST['pre_folder'] ) ){
            $did = $this->get_design_by_folder( wc_clean( $_POST['pre_folder'] ) );
            if( $did ) {
                $insert = $this->update_design( $did, $design_folder );
                if( $insert ){
                    $listThumb  = Nbdesigner_IO::get_list_images( $design_path . '/preview' );
                    $image      = '';
                    if( count( $listThumb ) ){
                        asort( $listThumb );
                        $image          = Nbdesigner_IO::wp_convert_path_to_url( reset( $listThumb ) );
                        $result['src']  = $image;
                    }
                }
            }
        } else {
            $insert = $this->insert_my_designs( $product_id, $variation_id, $design_folder );
        }
         // custom charmingprint
        do_action('after_save_for_late' , $design_folder);
        if( !$insert ) $result['flag'] = 0;
        wp_send_json( $result );
    }
    public function update_design( $id, $design_folder ){
        global $wpdb;
        $arr = array(
            'folder' => $design_folder
        );
        return $wpdb->update("{$wpdb->prefix}nbdesigner_mydesigns", $arr, array( 'id' => $id) );
    }
    public function get_design_by_folder( $folder ){
        global $wpdb;
        $re = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE folder = '{$folder}'" );
        if( $re ){
            return $re->id;
        }
        return false;
    }
    public function nbd_delete_my_design(){
        if (!wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }  
        $design_id = absint($_POST['design_id']);
        global $wpdb;
        $re = $wpdb->delete("{$wpdb->prefix}nbdesigner_mydesigns", array('id' => $design_id));
        $result = array('flag' =>  0);
        if( $re ){
            $result['flag'] = 1;
        }
        wp_send_json($result);
    }
    public static function get_favourite_templates(){
        $templates = array();
        if( WC()->session->__isset('nbd_favourite_templates') ){
            $templates = unserialize( WC()->session->get('nbd_favourite_templates') );
        }
        return $templates;
    }
    public function get_detail_favourite_templates(){
        global $wpdb;
        $ft         = array();
        $templates  = $this->get_favourite_templates();
        $ids        =  array_slice( $templates, -3, 3 );
        if( count( $ids ) ){
            $sql        = "SELECT t.*, p.post_title FROM {$wpdb->prefix}nbdesigner_templates AS t";
            $sql       .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
            $sql       .= " WHERE t.id IN (". implode( ",", $ids ) .")";
            $result     = $wpdb->get_results($sql, 'ARRAY_A');
            foreach ($result as $t){
                $path_preview = NBDESIGNER_CUSTOMER_DIR . '/' . $t['folder'] . '/preview';
                if( $t['thumbnail'] ){
                    $image = wp_get_attachment_url( $t['thumbnail'] );
                }else{
                    $listThumb  = Nbdesigner_IO::get_list_images( $path_preview );
                    $image      = '';
                    if( count( $listThumb ) ){
                        arsort( $listThumb );
                        $image = Nbdesigner_IO::wp_convert_path_to_url( reset( $listThumb ) );
                    }
                }
                $ft[] = array(
                    'id'            => $t['id'],
                    'title'         => $t['post_title'],
                    'product_id'    => $t['product_id'],
                    'variation_id'  => $t['variation_id'],
                    'folder'        => $t['folder'],
                    'img'           => $image
                );
            }
        }
        return $ft;
    }
    private function set_favourite_templates( $template_id, $type ){
        $templates = $this->get_favourite_templates();
        if( $type == 'like' ){
            $templates = array_merge( $templates, array( $template_id ) );
            if( count( $templates ) > 20 ) array_shift( $templates );
            $this->update_favourite_template( $template_id );
        }else{
            $templates = array_diff( $templates, [$template_id] );
        }
        $templates = array_unique( $templates );
        WC()->session->set( 'nbd_favourite_templates', serialize( $templates ) );
    }
    private function update_favourite_template( $template_id, $type = 'like' ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbdesigner_templates';
        $tem        = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = {$template_id}" );
        if( $tem ){
            $vote   = $tem->vote ? $tem->vote + 1 : 1;
            $re     = $wpdb->update( $table_name, array(
                'vote' => $vote
            ), array( 'id' => $template_id ) );
        }
    }
    public static function count_total_template($pid = false, $user_id = false, $cat =false){
        global $wpdb;
        $sql  = "SELECT COUNT(*) FROM {$wpdb->prefix}nbdesigner_templates AS t";
        $sql .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
        $sql .= " WHERE t.publish = 1 AND p.post_status = 'publish'";
        if($pid) $sql .= " AND t.product_id = " . $pid; 
        if( $user_id ) $sql .= " AND t.user_id = " . $user_id; 
        if( $cat ) {
            $products  = self::get_all_product_design_in_category( $cat );
            if( is_array( $products ) && count( $products ) ){
                $list_product = '';
                foreach ( $products as $pro ){
                    $list_product .= ',' . $pro->ID;
                }
                $list_product = ltrim( $list_product, ',' );
                $sql .= " AND t.product_id IN ($list_product) "; 
            }else {
                return 0;
            }
        }
        $count = $wpdb->get_var( $sql );
        return $count ? $count : 0;
    } 
    public static function get_all_product_design_in_category( $cat_id ){
        $list_cat   = get_term_children( $cat_id, 'product_cat' );
        $list_cat[] = (int)$cat_id;
        $products   = get_transient( 'nbd_design_products_cat_' . $cat_id );
        if( false === $products ){
            $args_query = array(
                'post_type'         => 'product',
                'post_status'       => 'publish',
                'meta_key'          => '_nbdesigner_enable',
                'orderby'           => 'date',
                'posts_per_page'    => -1,
                'meta_query'        => array(
                    array(
                        'key'   => '_nbdesigner_enable',
                        'value' => 1,
                    )
                ),
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $list_cat,
                        'operator'  => 'IN'
                    )
                )
            );
            $products = get_posts( $args_query );
            set_transient( 'nbd_design_products_cat_' . $cat_id , $products, DAY_IN_SECONDS );  
        } 
        return $products;
    }
    public static function nbdesigner_get_templates_by_page( $page = 1, $row = 5, $per_row = 3, $pid = false, $get_all = false, $user_id = false, $cat = false, $tag = '', $color = '', $type = '', $search = '', $search_type = '', $return_total = false ){
        $listTemplates = array();
        global $wpdb;
        $limit  = $row * $per_row;
        $offset = $limit * ( $page - 1 );
        $sql    = "SELECT p.ID, p.post_title, t.id AS tid, t.name, t.folder, t.product_id, t.variation_id, t.user_id, t.thumbnail, t.type FROM {$wpdb->prefix}nbdesigner_templates AS t";
        $sql   .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
        $sql   .= " WHERE t.publish = 1 AND p.post_status = 'publish' AND publish = 1";

        if( $tag != '' ){
            $tag_arr = explode( ',', $tag );
            if( count( $tag_arr ) ){
                $sql .= " AND ( ";
                foreach( $tag_arr as $k => $t ){
                    if( $t != '' ){
                        if( $k == 0 ){
                            $sql .= "( FIND_IN_SET('". $t ."', t.tags) > 0 )";
                        }else{
                            $sql .= " OR ( FIND_IN_SET('". $t ."', t.tags) > 0 )";
                        }
                    }
                }
                $sql .= " )";
            }
        }

        if( $color != '' ){
            $color_arr = explode( ',', $color );
            if( count( $color_arr ) ){
                $sql .= " AND ( ";
                foreach( $color_arr as $k => $c ){
                    if( $c != '' ){
                        if( $k == 0 ){
                            $sql .= "( FIND_IN_SET('". $c ."', t.colors) > 0 )";
                        }else{
                            $sql .= " OR ( FIND_IN_SET('". $c ."', t.colors) > 0 )";
                        }
                    }
                }
                $sql .= " )";
            }
        }

        if( $pid ){
            $sql .= " AND t.product_id = ".$pid;
        }else if( $cat ) {
            $products  = self::get_all_product_design_in_category( $cat );
            if( is_array( $products ) && count( $products ) ){
                $list_product = '';
                foreach ($products as $pro){
                    $list_product .= ','.$pro->ID;
                }
                $list_product = ltrim($list_product, ',');
                $sql .= " AND t.product_id IN ($list_product) ";
            } else {
                return $return_total ? 0 : array();
            }
        }

        if( $search != '' ){
            if( $search_type != 'design' ){
                $args   = array(
                    'role__in'   => array( 'designer', 'administrator', 'shop_manager' ),
                    'meta_query' => array(
                        array(
                            'relation' => 'AND',
                            array(
                                'key'     => 'nbd_sell_design',
                                'value'   => 'on',
                                'compare' => '='
                            ),
                            array(
                                'key'     => 'nbd_artist_name',
                                'value'   => esc_attr( $search ),
                                'compare' => 'LIKE'
                            )
                        )
                    )
                );

                $user_query     = new WP_User_Query( $args );
                $designers      = $user_query->get_results();
                $list_designer  = '';
                foreach ( $designers as $key => $designer ) {
                    $separate = $key > 0 ? ',' : '';
                    $list_designer .= $separate . $designer->ID;
                }

                if( $search_type == 'artist' ){
                    if( $list_designer != '' ){
                        $sql .= " AND t.user_id IN ($list_designer) ";
                    }
                }else{
                    if( $list_designer != '' ){
                        $sql .= " AND ( t.user_id IN ($list_designer) OR t.name LIKE '%$search%' ) ";
                    }else{
                        $sql .= " AND t.name LIKE '%" . $search . "%' ";
                    }
                }
            }else{
                $sql .= " AND t.name LIKE '%" . $search . "%' ";
            }
        }

        //if($pid) $sql .= " AND t.product_id = ".$pid;
        if( $user_id ) $sql .= " AND t.user_id = ".$user_id;
        $sql .= " ORDER BY t.created_date DESC";
        if( !$get_all ){
            $sql .= " LIMIT ".$limit." OFFSET ".$offset;
        }

        $posts = $wpdb->get_results( $sql, 'ARRAY_A' );
        if( $return_total ) return count( $posts );
        foreach ( $posts as $p ){
            $path_preview = NBDESIGNER_CUSTOMER_DIR .'/'.$p['folder']. '/preview';
            if( $p['thumbnail'] ){
                $image = wp_get_attachment_url( $p['thumbnail'] );
            }else{
                $listThumb = Nbdesigner_IO::get_list_images( $path_preview );
                $image = '';
                if( count( $listThumb ) ){
                    asort( $listThumb );
                    $image = Nbdesigner_IO::wp_convert_path_to_url( reset( $listThumb ) );
                }
            }
            $title = $p['name'] ?  $p['name'] : $p['post_title'];
            $listTemplates[] = array('tid' => $p['tid'], 'id' => $p['ID'], 'title' => $title, 'type' => $p['type'], 'image' => $image, 'folder' => $p['folder'], 'product_id' => $p['product_id'], 'variation_id' => $p['variation_id'], 'user_id' => $p['user_id']);
        }
        return $listTemplates;
    }
    public function get_categories_has_design(){
        global $wpdb;
        $categories = get_transient( 'nbd_design_category' );
        if ( false === $categories ) {
            $sql = "SELECT t.term_id,t.name, tt.parent FROM $wpdb->terms as t
                    LEFT JOIN $wpdb->term_taxonomy as tt on t.term_id = tt.term_id
                    LEFT JOIN $wpdb->term_relationships AS tr on tt.term_taxonomy_id = tr.term_taxonomy_id
                    LEFT JOIN $wpdb->posts AS p on tr.object_id = p.ID
                    LEFT JOIN {$wpdb->prefix}nbdesigner_templates AS nt on nt.product_id = p.ID
                    WHERE tt.taxonomy = 'product_cat'
                    AND p.post_type = 'product'
                    AND p.post_status = 'publish' GROUP BY t.term_id";  
            $categories = $wpdb->get_results( $sql );
            set_transient( 'nbd_design_category' , $categories );
        }
        return $categories;
    }
    public function delete_categories_transient(){
        delete_transient( 'nbd_design_category' );
    }
    public function get_designers(){
        $designers = get_transient( 'nbd_designers' );
        if ( false === $designers ) {
            $users = get_users(array(
                'meta_query' => array(
                    array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'nbd_create_design',
                            'value'   => 'on',
                            'compare' => '='
                        ),
                        array(
                            'key'     => 'nbd_sell_design',
                            'value'   => 'on',
                            'compare' => '='
                        )
                    )
                )
            ));
            $designers = array();
            foreach( $users as $key => $user ){
                $designers[$key]['id']          = $user->ID;
                $art_name                       = get_user_meta( $user->ID, 'nbd_artist_name');
                $designers[$key]['art_name']    = $art_name[0];
                $designers[$key]['art_id']      = $user->ID;
            }
            set_transient( 'nbd_designers' , $designers );
        }
        return $designers;
    }
    public function delete_designs_transient(){
        delete_transient( 'nbd_designers' );
    }
    public function set_open_graph_image(){
        if( isset( $_GET['nbd_share_id'] ) && $_GET['nbd_share_id'] != '' ){
            $folder     = wc_clean( $_GET['nbd_share_id'] );
            $path       = NBDESIGNER_CUSTOMER_DIR . '/' . $folder . '/preview';
            $images     = Nbdesigner_IO::get_list_images( $path, 1 );
            if( isset( $images[0] ) ){
                asort( $images );
                $image_url = Nbdesigner_IO::wp_convert_path_to_url( $images[0] );
                echo '<meta property="og:image" content="' . $image_url . '" />';
            }
        }
    }
    public function nbd_update_my_template(){
        if ( ( !wp_verify_nonce( $_POST['_wpnonce'], 'nbd_edit_template_nonce' ) && NBDESIGNER_ENABLE_NONCE ) || !user_can_edit_template( $_POST['user_id'] ) ) {
            die('Security error');
        }
        global $wpdb;
        $table_name =  $wpdb->prefix . 'nbdesigner_templates';
        $re = $wpdb->update( $table_name, array(
            'thumbnail' => wc_clean( $_POST['thumbnail'] ),
            'name'      => wc_clean( $_POST['name'] )
        ), array( 'id' => absint( $_POST['id'] ) ) );
        $result = array( 'flag' => 0 );
        if( $re ){
            $result['flag'] = 1;
        }
        wp_send_json( $result );
    }
    public function nbd_delete_my_template(){
        if ( ( !wp_verify_nonce( $_POST['_wpnonce'], 'nbd_edit_template_nonce' ) && NBDESIGNER_ENABLE_NONCE ) || !user_can_edit_template( $_POST['user_id'] ) ) {
            die('Security error');
        }  
        global $wpdb;
        $re = $wpdb->delete( "{$wpdb->prefix}nbdesigner_templates", array( 'id' => absint( $_POST['id'] ) ) );
        $result = array( 'flag' =>  0 );
        if( $re ){
            $result['flag'] = 1;
        }
        wp_send_json( $result );
    }
    public function nbd_get_template_preview(){
        if ( !wp_verify_nonce($_POST['nonce'], 'nbd_update_favourite_template') && NBDESIGNER_ENABLE_NONCE && is_user_logged_in() ) {
            die( 'Security error' );
        }
        global $wpdb;
        $result['flag'] = 0;
        $tid            = wc_clean( $_POST['template_id'] );
        $re             = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE id = {$tid}" );
        if( $re ){
            $result['flag'] = 1;
            ob_start();
            $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $re->folder . '/preview';
            $_images        = Nbdesigner_IO::get_list_images( $path, 1 );
            asort( $_images );
            $large_image    = Nbdesigner_IO::wp_convert_path_to_url( reset( $_images ) );
            foreach ( $_images as $img ){
                $images[] = Nbdesigner_IO::wp_convert_path_to_url( $img );
            }
            $name               = $re->name != '' ? $re->name : get_the_title( $re->product_id );
            $link_detail_design = add_query_arg(array('id' => $re->user_id, 'template_id' => $tid), getUrlPageNBD('designer'));
            $link_start_design  = add_query_arg(array( 'product_id' => $re->product_id, 'variation_id' => $re->variation_id, 'reference'  => $re->folder ), getUrlPageNBD( 'create' ) );

            if( $re->type == 'solid' ){
                $product = wc_get_product( $re->product_id );
                $link_start_design  = add_query_arg( array(
                    'design_id' => nbd_encode_design_id( $tid )
                ), $product->get_permalink() );
            }
            $designer       = new NBD_Designer( $re->user_id );
            $infos          = $designer->to_array();
            $designer_name  = isset( $infos['artist_name'] ) && $infos['artist_name'] != '' ? $infos['artist_name'] : $infos['first_name'] . ' ' . $infos['last_name'];
            $store_url      = add_query_arg( array( 'id' => $re->user_id ), getUrlPageNBD( 'designer' ) );

            nbdesigner_get_template('gallery/popup-preview.php', array(
                'id'                    => $tid,
                'name'                  => $name,
                'user_id'               => $re->user_id,
                'folder'                => $re->folder,
                'type'                  => $re->type,
                'images'                => $images,
                'large_image'           => $large_image,
                'link_detail_design'    => $link_detail_design,
                'link_start_design'     => $link_start_design,
                'designer_name'         => $designer_name,
                'store_url'             => $store_url
            ));
            $result['html'] = ob_get_clean();

        }
        wp_send_json( $result );
    }
    public function nbd_get_list_product_ready_to_create_template(){
        if ( !wp_verify_nonce($_POST['nonce'], 'nbd_update_favourite_template') && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $result['flag'] = 1;
        $products       = nbd_get_products_has_design();
        nbdesigner_get_template( 'gallery/popup-list-products.php', array(
            'products' => $products
        ) );
        $result['html'] = ob_get_clean();
        wp_send_json($result);
    }
    public function nbd_get_preview_product_before_create_template(){
        if (!wp_verify_nonce($_POST['nonce'], 'nbd_update_favourite_template') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        } 
        $result['flag'] = 1;  
        $product_id     = absint( $_POST['product_id'] );
        $art_id         = absint( $_POST['art_id'] );
        $product        = wc_get_product( $product_id );
        $image          = get_the_post_thumbnail_url( $product_id, 'post-thumbnail' );
        $image          = $image ? $image : wc_placeholder_img_src();
        $variations = get_nbd_variations( $product_id );
        $link_designer = add_query_arg( array( 'id' => $art_id ), getUrlPageNBD( 'designer' ) );
        //$link_create_template = add_query_arg(array('product_id' => $product_id,'task'  =>  'create','rd'    => urlencode($link_designer)), getUrlPageNBD('create'));
        $link_create_template = add_query_arg(array('product_id' => $product_id,'task'  =>  'create','rd'    => 'designer', 'aid' => $art_id), getUrlPageNBD('create'));
        nbdesigner_get_template('gallery/popup-preview-product.php', array(
            'name'          => $product->get_title(),
            'image'         => $image,
            'type'          => $product->get_type(),
            'variations'    => $variations,
            'link_create_template'  =>  $link_create_template
        ));
        $result['html'] = ob_get_clean();
        wp_send_json($result);
    }
    public function nbd_add_design_to_cart(){
        if ((!wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE) || !isset( $_POST['design_id'] ) ){
            die('Security error');
        }
        global $wpdb;
        $design_id  = wc_clean( $_POST['design_id'] );
        $re         = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE id = {$design_id}" );
        $result     = array('flag' =>  0);
        if( $re ){
            $product_id         = $re->product_id;
            $variation_id       = $re->variation_id;
            $folder             = $re->folder;
            $nbd_item_cart_key  = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id; 
            $item_folder        = substr(md5(uniqid()),0,10);
            $quantity           = 1;
            $path               = NBDESIGNER_CUSTOMER_DIR . '/' . $folder;
            $item_path          = NBDESIGNER_CUSTOMER_DIR . '/' . $item_folder;
            Nbdesigner_IO::copy_dir( $path, $item_path );
            WC()->session->set('nbd_item_key_'.$nbd_item_cart_key, $item_folder);
            $product_status    = get_post_status( $product_id );
            //if ( false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {
            if( nbd_add_to_cart(  $product_id, $variation_id, $quantity  ) ){
                do_action( 'woocommerce_ajax_added_to_cart', $product_id );
                $result['flag'] = 1;
            } else {
                $result['flag'] = 0;
                $result['mes']  = esc_html__('Error adding to the cart', 'web-to-print-online-designer');
            }
        }
        wp_send_json($result); 
    }
}