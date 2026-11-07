<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('NBD_SHORTCODES')){
    class NBD_SHORTCODES {
        protected static $instance;
        public function __construct() {
            //todo something
        }
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function init(){
            add_shortcode( 'nbd_product', array($this,'nbd_products_func') );
            add_shortcode( 'nbd_template', array($this,'nbd_templates_func') );
        }
        public function check_nbd_active(){
            $is_activated = false;
            if(in_array('web-to-print-online-designer/nbdesigner.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
                $is_activated = true;
            }
            return $is_activated;
        }
        public function nbd_products_func( $atts ){
            if( !$this->check_nbd_active() ) return '';
            global $woocommerce_loop;
            $atts = shortcode_atts( array(
                'limit'         => '8',
                'columns'       => '4'
            ), $atts, 'nbd_products' );
            $woocommerce_loop['columns'] = $atts['columns'];
            $products = new WP_Query( array (   
                'post_type'         => 'product',
                'post_status'       => 'publish',
                'posts_per_page'    => $atts['limit'], 
                'orderby'           => 'date',
                'order'             => 'DESC',
                'meta_query'        => array(
                    array(
                        'key' => '_nbdesigner_enable',
                        'value' => 1,
                    )
                )
            )); 
            ob_start();
            if ( $products->have_posts() ) { ?>
                <?php woocommerce_product_loop_start(); ?>
                    <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                        <?php wc_get_template_part( 'content', 'product' ); ?>
                    <?php endwhile; // end of the loop. ?>
                <?php woocommerce_product_loop_end(); ?>
                <?php
            } else {
                do_action( "woocommerce_shortcode_products_loop_no_results", $atts );
            }
            woocommerce_reset_loop();
            wp_reset_postdata();
        }
        /* Shortcode [nbd_template row=2 per_row=4 limit=8 product_id=1 cat_id=1] */
        public function nbd_templates_func( $atts ){
            if( !$this->check_nbd_active() ) return '';

            $atts = shortcode_atts( array(
                'row'           => 4,
                'per_row'       => 2,
                'limit'         => 10,
                'product_id'    => 0,
                'cat_id'        => 0
            ), $atts, 'nbd_template');
            $atts['limit'] = $atts['row'] * $atts['per_row'];

            global $wpdb;
            $sql = "SELECT p.ID, p.post_title, t.id AS tid, t.name, t.folder, t.product_id, t.variation_id, t.user_id, t.thumbnail FROM {$wpdb->prefix}nbdesigner_templates AS t";
            $sql .= " LEFT JOIN {$wpdb->prefix}posts AS p ON t.product_id = p.ID";
            $sql .= " WHERE t.publish = 1 AND p.post_status = 'publish' AND publish = 1";

            if( isset( $atts['cat_id'] ) && $atts['cat_id'] != 0 ){
                $cat_id = absint( $atts['cat_id'] );
                $products = $this->get_all_product_design_in_category( $cat_id );
                if(is_array($products) && count($products) ){
                    $list_product = '';
                    foreach ($products as $pro){
                        $list_product .= ','.$pro->ID;
                    }
                    $list_product = ltrim($list_product, ',');
                    $sql .= " AND t.product_id IN ($list_product) ";
                }
            }

            if( isset( $atts['product_id'] ) && $atts['product_id'] != 0 ){
                $sql .= " AND p.ID = " . absint( $atts['product_id'] );
            }

            $sql .= " ORDER BY t.created_date DESC";
            $sql .= " LIMIT " . $atts['limit'];
            $posts = $wpdb->get_results($sql, 'ARRAY_A');
            $listTemplates = array();
            foreach ($posts as $p){
                $path_preview = NBDESIGNER_CUSTOMER_DIR .'/'.$p['folder']. '/preview';
                if( $p['thumbnail'] ){
                    $image = wp_get_attachment_url( $p['thumbnail'] );
                }else{
                    $listThumb = Nbdesigner_IO::get_list_images($path_preview);
                    $image = '';
                    if(count($listThumb)){
                        $image = Nbdesigner_IO::wp_convert_path_to_url(reset($listThumb));
                    }
                }
                $title = $p['name'] ?  $p['name'] : $p['post_title'];
                $listTemplates[] = array('tid' => $p['tid'], 'id' => $p['ID'], 'title' => $title, 'image' => $image, 'folder' => $p['folder'], 'product_id' => $p['product_id'], 'variation_id' => $p['variation_id'], 'user_id' => $p['user_id']);
            } 
            ob_start();
            nbdesigner_get_template('gallery/shortcode.php', array(
                'templates' => $listTemplates,
                'atts'      => $atts
            ));
            return ob_get_clean();
        }
        public function get_all_product_design_in_category( $cat_id ){
            $list_cat   = get_term_children($cat_id, 'product_cat');  
            $list_cat[] = (int)$cat_id;
            $products   = get_transient( 'nbd_design_products_cat_'.$cat_id );
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
                $products = get_posts($args_query);
                set_transient( 'nbd_design_products_cat_'.$cat_id , $products, DAY_IN_SECONDS );  
            } 
            return $products;
        }
    }
}
$nbd_shortcodes = NBD_SHORTCODES::instance();
$nbd_shortcodes->init();