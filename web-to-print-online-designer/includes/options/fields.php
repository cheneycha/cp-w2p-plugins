<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly 
if(!class_exists('NBD_FIELDS')){
    class NBD_Printing_Fields{
        public function __construct() {
            //todo
        }
        public function render_field( $field ){
            
        }
        public function render_container(){
            include_once(NBDESIGNER_PLUGIN_DIR . 'views/options/edit-option.php');
        }
    }
}