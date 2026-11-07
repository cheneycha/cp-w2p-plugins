<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if( !class_exists('NBD_Appearance_Customize') ) {
    class NBD_Appearance_Customize{
        protected static $instance;
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct() {
            add_shortcode( 'nbdesigner_studio', array( $this,'nbdesigner_studio_func' ) );
            add_filter( 'body_class', array( $this, 'add_body_class'), 20, 1 );
            add_action( 'nbd_js_config', array( $this, 'js_config' ) );
            $this->ajax();
            add_action( 'customize_register', array( $this, 'add_sections' ) );
            add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30 );
        }
        public function ajax(){
            $ajax_events = array(
                'nbds_get_product_template'   => true
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        public function add_body_class( $classes ){
            if( is_page() ) {
                global $post;
                if( $post->ID == nbd_get_page_id( 'studio' ) ){
                    $classes[] = 'nbd-studio-page';
                }
            }
            return $classes;
        }
        public function js_config(){
            if( isset( $_GET['src'] ) && $_GET['src'] == 'studio' ):
            ?>
            NBDESIGNCONFIG.force_hide_option = 1;
            <?php
            endif;
        }
        public function nbdesigner_studio_func( $atts ){
            $page       = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $atts = shortcode_atts(array(
                'per_page'   => 20,
                'page'       => $page,
                'list'       => false
            ), $atts);
            if( false !== $atts['list'] ){
                $ids = explode(',', $atts['list']);
                $products = array();
                foreach( $ids as $id ){
                    if(nbd_is_product($id) ){
                        $image = get_the_post_thumbnail_url( $id, 'post-thumbnail' );
                        if( !$image ) $image = wc_placeholder_img_src();
                        $product    = wc_get_product( $id );
                        $products[] = array(
                            'id'        => $id,
                            'src'       => $image,
                            'url'       => get_permalink( $id ),
                            'name'      => $product->get_title()
                        );
                    }
                }
            } else {
                $products       = nbd_get_products_has_design();
                $atts['total']  = count( $products );
                $products       = array_slice($products, ($page-1) * $atts['per_page'], $atts['per_page']);
            }
            $atts['products']   = $products;
            ob_start();
            if( false !== $atts['list'] ){
                nbdesigner_get_template( 'studio-widget.php', $atts );
            } else {
                nbdesigner_get_template( 'studio.php', $atts );
            }
            $content = ob_get_clean();
            return $content;
        }
        public function nbds_get_product_template(){
            global $wpdb;
            $product_id     = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
            $results        = array(
                'templates'  =>  array()
            );
            if( $product_id != 0 ){
                $sql            = "SELECT name, folder, thumbnail FROM {$wpdb->prefix}nbdesigner_templates ";
                $sql           .= " WHERE publish = 1 AND product_id = {$product_id} AND publish = 1 AND type IS NULL"; 
                $templates      = $wpdb->get_results($sql, 'ARRAY_A'); 
                foreach( $templates as $template ){
                    $path_preview = NBDESIGNER_CUSTOMER_DIR .'/' . $template['folder'] . '/preview';
                    if( $template['thumbnail'] ){
                        $image = wp_get_attachment_url( $template['thumbnail'] );
                    }else{
                        $listThumb = Nbdesigner_IO::get_list_images( $path_preview );
                        if( count($listThumb) ){
                            $image = Nbdesigner_IO::wp_convert_path_to_url(reset($listThumb));
                        }
                    }
                    $results['templates'][$template['folder']]['preview'] = $image;
                    $results['templates'][$template['folder']]['url']     = add_query_arg(array(
                        'product_id' => $product_id,
                        'reference'  => $template['folder'],
                        'src'        => 'studio'
                    ), getUrlPageNBD('create'));
                }
            }
            wp_send_json( $results );
            exit();
        }
        public function add_scripts() {
            ?>
            <script type="text/javascript">
                jQuery( document ).ready( function( $ ) {
                    wp.customize.section( 'nbdesigner_gallery_page', function( section ) {
                        section.expanded.bind( function( isExpanded ) {
                            if ( isExpanded ) {
                                wp.customize.previewer.previewUrl.set( '<?php echo getUrlPageNBD( 'gallery' ); ?>' );
                            }
                        } );
                    } );
                    wp.customize( 'nbdesigner_gallery_column', function( setting ) {
                        setting.bind( function( value ) {
                            
                        } );
                    });
                });
            </script>
            <?php
        }
	public function add_sections( $wp_customize ) {
            $wp_customize->add_panel( 'nbdesigner', array(
                    'priority'       => 200,
                    'capability'     => 'manage_woocommerce',
                    'theme_supports' => '',
                    'title'          => esc_html__( 'Nbdesigner', 'web-to-print-online-designer' ),
            ) );
            $this->add_gallery_page_section( $wp_customize );
	}
        public function add_gallery_page_section( $wp_customize ){
            $wp_customize->add_section(
                'nbdesigner_gallery_page',
                array(
                    'title'    => esc_html__( 'Gallery', 'web-to-print-online-designer' ),
                    'priority' => 10,
                    'panel'    => 'nbdesigner',
                )
            );
            $wp_customize->add_setting(
                'nbdesigner_gallery_column',
                array(
                    'default'              => 3,
                    'type'                 => 'option',
                    'capability'           => 'manage_woocommerce',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );
            $wp_customize->add_setting(
                'nbdesigner_gallery_hide_sidebar',
                array(
                    'default'              => 'n',
                    'type'                 => 'option',
                    'capability'           => 'manage_woocommerce'
                )
            );
            $wp_customize->add_control(
                'nbdesigner_gallery_hide_sidebar', array(
                    'label'         => esc_html__('Hide gallery sidebar', 'web-to-print-online-designer'),
                    'description'   => esc_html__('Choose whether the sidebar is hidden or not.', 'web-to-print-online-designer'),
                    'section'       => 'nbdesigner_gallery_page',
                    'settings'      => 'nbdesigner_gallery_hide_sidebar',
                    'type'          => 'radio',
                    'choices'       => array(
                        'n' => esc_html__( 'No', 'web-to-print-online-designer' ),
                        'y' => esc_html__( 'Yes', 'web-to-print-online-designer' )
                    )
                )
            );
            $wp_customize->add_control(
                'nbdesigner_gallery_column', array(
                    'label'         => esc_html__('Gallery page', 'web-to-print-online-designer'),
                    'description'   => esc_html__('How many columns of templates should be shown in gallery page?', 'woocommerce'),
                    'section'       => 'nbdesigner_gallery_page',
                    'settings'      => 'nbdesigner_gallery_column',
                    'type'          => 'number',
                    'input_attrs'   => array(
                        'min'   => 2,
                        'max'   => 4,
                        'step'  => 1,
                    ),
                )
            );
            $wp_customize->add_setting(
                'nbdesigner_artist_gallery_column',
                array(
                    'default'              => 4,
                    'type'                 => 'option',
                    'capability'           => 'manage_woocommerce',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );
            $wp_customize->add_control(
                'nbdesigner_artist_gallery_column', array(
                    'label'         => esc_html__('Artist page', 'web-to-print-online-designer'),
                    'description'   => esc_html__('How many columns of templates should be shown in artist page?', 'woocommerce'),
                    'section'       => 'nbdesigner_gallery_page',
                    'settings'      => 'nbdesigner_artist_gallery_column',
                    'type'          => 'number',
                    'input_attrs'   => array(
                        'min'   => 3,
                        'max'   => 5,
                        'step'  => 1,
                    ),
                )
            );
            $wp_customize->add_setting(
                'nbdesigner_gallery_gutter',
                array(
                    'default'              => 8,
                    'type'                 => 'option',
                    'capability'           => 'manage_woocommerce',
                    'sanitize_callback'    => 'absint',
                    'sanitize_js_callback' => 'absint',
                )
            );
            $wp_customize->add_control(
                'nbdesigner_gallery_gutter', array(
                    'label'         => esc_html__('Gallery element gutter', 'web-to-print-online-designer'),
                    'description'   => esc_html__('Gallery element gutter in px', 'web-to-print-online-designer'),
                    'section'       => 'nbdesigner_gallery_page',
                    'settings'      => 'nbdesigner_gallery_gutter',
                    'type'          => 'number',
                    'input_attrs'   => array(
                        'min'   => 0,
                        'max'   => 20,
                        'step'  => 1,
                    ),
                )
            );
        }
    }
}
function NBD_Appearance_Customize(){
    return NBD_Appearance_Customize::get_instance();
}
NBD_Appearance_Customize();