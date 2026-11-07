<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists( 'Nbdesigner_Settings' ) ) {
    class Nbdesigner_Settings {
        public $page_id             = '';
        public $tabs                = array();
        public $sections            = array();
        public $settings            = array();
        public $options             = array();
        public $blocks              = array();
        public $block_titles        = array();
        public $block_descriptions  = array();
        public $current_tab         = '';
        public function __construct( $parameters ) {
            if( isset( $parameters['page_id'] ) ) {
                $this->page_id = sanitize_key( $parameters['page_id'] );
            }
            if( isset( $parameters['tabs'] ) ) {
                $this->tabs = $parameters['tabs'];
            }  
            if( isset( $parameters['sections'] ) ) {
                $this->sections = $parameters['sections'];
            }
            $this->current_tab = isset( $_GET['tab'] ) ? wc_clean( $_GET['tab'] ) : key( $this->tabs );
            foreach( $this->tabs as $key => $value ) {
                $this->settings[$key] = array();
            }
        }
        public function add_blocks( $blocks ) {
            $this->blocks = $blocks;
            foreach( $this->blocks as $tab_block_key => $tab_block_value ) {
                $this->settings[$tab_block_key] = $tab_block_value;
                foreach( $this->blocks[$tab_block_key] as $block_key => $block_val ) {
                    $this->block_titles[$block_key] = $block_val;
                }
            }
            return $this->blocks;
        }   
        public function add_blocks_description( $descriptions = array() ) {
            $this->block_descriptions = $descriptions;
        }
        public function add_block_options( $block_key, $options ) {
            foreach ( $this->settings as $tab_key => $tab_value ) {
                if ( isset( $this->settings[$tab_key][$block_key] ) ) {
                    $this->settings[$tab_key][$block_key] = $options;
                }
            }
            $options_with_keys = array();
            foreach ($options as $option) {
                $options_with_keys[$option['id']] = $option;
            }
            $this->options = array_merge( $this->options, $options_with_keys );
            return $this->settings;
        } 
        public function output() {
            $this->output_header();
            $current_tab_blocks = $this->settings[$this->current_tab];
            foreach( $current_tab_blocks as $key => $value ) {
                echo '<h3>' . $this->block_titles[$key] . '</h3>';
                if( isset($this->block_descriptions[$key]) )
                    echo '<p class="description nbdesigner-block-description">' . $this->block_descriptions[$key] . '</p>';
                echo '<table class="form-table" id="' . $key . '">';
                $block_options = $current_tab_blocks[$key];
                if( is_array( $block_options ) ) {
                    foreach( $block_options as $block_option ) {
                        self::output_item( $block_option );
                    }
                }
                echo '</table>';
            }
            $this->output_footer();
        }
        public function output_header() {
            do_action( 'nbdesigner_settings_header_start', $this->page_id );
            echo '<form method="post" id="nbdesigner-options-form-' . $this->page_id . '" name="nbdesigner_options_form_' . $this->page_id . '" class="nbdesigner-settings-form">';
            wp_nonce_field($this->page_id . '_nonce');
            if (sizeof($this->tabs) == 1) {
                $keys = array_keys($this->tabs);
                echo '<h2>' . $this->tabs[$keys[0]];
            }
            else {
                echo '<h2 class="nav-tab-wrapper">';
                foreach ($this->tabs as $key => $value) {
                    $class = ( $key == $this->current_tab ) ? ' nav-tab-active' : '';
                    echo '<a href="?page=' . $this->page_id . '&tab=' . sanitize_key($key) . '" class="nav-tab ' . esc_attr($class) . '" id="nbdesigner-nav-tab--' . sanitize_key($key) . '">' . $value . '</a>';
                }
            }
            echo '</h2><br class="clear"/>';
            if (isset($_POST['nbdesigner_save_options_' . $this->page_id]) || isset($_POST['nbdesigner_reset_options_' . $this->page_id])) {
                $text = isset($_POST['nbdesigner_save_options_' . $this->page_id]) ? __( 'Settings saved.', 'web-to-print-online-designer' ) : __( 'Settings reseted.', 'web-to-print-online-designer' );
                echo '<div class="updated"><p><strong>' . $text . '</strong></p></div>';
            }
            do_action( 'nbdesigner_settings_header_end', $this->page_id );
        }
        public function output_footer() {
            do_action( 'nbdesigner_settings_footer_start', $this->page_id );
            ?>
                <br class="clear" />
                <p id="nbd-footer" class="nbd-footer">
                    <button type="submit" class="button-primary" name="nbdesigner_save_options_<?php echo $this->page_id; ?>"><?php _e( 'Save Options', 'web-to-print-online-designer' ); ?></button>
                    <button type="submit" class="button-secondary" name="nbdesigner_reset_options_<?php echo $this->page_id; ?>" ><?php _e( 'Reset', 'web-to-print-online-designer' ); ?></button>
                </p>
                <br class="clear" />
                <p class="nbd-import-export">
                    <a class="button-secondary" href="<?php echo admin_url( 'admin.php?nbd_export_setting=1' ); ?>" ><?php _e( 'Export Options', 'web-to-print-online-designer' ); ?></a>
                    <a class="button-secondary" onclick="NBDESIGNADMIN.importSettings(event)" ><?php _e( 'Import Options', 'web-to-print-online-designer' ); ?></a>
                    <input type="file" style="display: none;" id="nbd-import-settings-file" accept=".json" />
                    <span id="nbd-import-settings-loading" style="display: none;"><?php _e( 'Processing...', 'web-to-print-online-designer' ); ?></span>
                </p>
            </form>
            <?php
            do_action( 'nbdesigner_settings_footer_end', $this->page_id );
        }
        public function get_options_by_tab($tab_id) {
            if (isset($this->settings[$tab_id])) {
                $options_in_tab = array();
                $blocks = $this->settings[$tab_id];
                foreach ($blocks as $block) {
                    if (is_array($block)) {
                        foreach ($block as $option) {
                            $options_in_tab[$option['id']] = $option['default'];
                        }
                    }
                }
                return $options_in_tab;
            } else {
                return false;
            }
        }
        public function get_option($key) {
            if (get_option($key) === false) {
                return $this->get_default_option($key);
            } else {
                $value = get_option($key);
                if (!$this->not_empty($value) && $this->get_option_type($key) == 'number') {
                    return $this->get_default_option($key);
                }
                else if (is_array($value) || $this->get_option_type($key) == 'multiselect') {
                    return empty($value) || $value == 'no' || !is_array($value) ? "" : '"' . implode('","', $value) . '"';
                } else if( $this->get_option_type($key) == 'nbd-dynamic-list' ){
                    return unserialize( $value );
                }
                return $this->boolean_string_to_int($value);
            }
        }
        public function get_default_option($key) {
            if (isset($this->options[$key])) {
                return $this->boolean_string_to_int($this->options[$key]['default']);
            }
            return false;
        }
        public function get_option_type($key) {
            if (isset($this->options[$key])) {
                return $this->boolean_string_to_int($this->options[$key]['type']);
            }
            return false;
        }
        public static function output_item( $parameters, $product_id = null ) {
            $id             = isset($parameters['id']) ? $parameters['id'] : '';
            $title          = isset($parameters['title']) ? $parameters['title'] : '';
            $type           = isset($parameters['type']) ? $parameters['type'] : '';
            $description    = isset($parameters['description']) ? $parameters['description'] : false;
            $default        = isset($parameters['default']) ? $parameters['default'] : '';
            $css            = isset($parameters['css']) ? $parameters['css'] : '';
            $class          = isset($parameters['class']) ? $parameters['class'] : '';
            $prefix         = isset($parameters['prefix']) ? $parameters['prefix'] : false;
            $subfix         = isset($parameters['subfix']) ? $parameters['subfix'] : false;
            $depend_on      = isset($parameters['depend_on']) ? $parameters['depend_on'] : array( 'id' => '', 'value' => '', 'operator' => '' );
            if( is_null( $product_id ) ){
                $value = get_option($id) !== false ? get_option($id) : '';
            } else {
                $local_settings = get_post_meta( $product_id, '_nbls_settings', true );
                if( $local_settings ){
                    $local_settings = unserialize( $local_settings );
                } else {
                    $local_settings = array();
                }
                if( isset( $local_settings[$id] ) ){
                    $value = $local_settings[$id];
                } else {
                    $value = get_option($id) !== false ? get_option($id) : '';
                }
            }
            $options            = isset($parameters['options']) ? $parameters['options'] : array();
            $depend             = isset($parameters['depend']) ? $parameters['depend'] : array();
            $custom_attributes  = isset($parameters['custom_attributes']) ? $parameters['custom_attributes'] : array();
            $relations          = isset($parameters['relations']) ? $parameters['relations'] : array();
            $placeholder        = isset($parameters['placeholder']) ? 'placeholder="' . esc_attr($parameters['placeholder']) . '"' : '';
            $show               = true;
            if( isset($parameters['layout']) ){
                $show = nbd_check_availaible_option( $parameters['layout'] );
            }
            $input_html     = '';
            $input_class    = $class;
            $new_line_desc  = '<br class="clear" />';
            $current_value  = empty( $value ) && $value != '0' ? $default : $value;

            $custom_attributes_html = '';
            foreach ($custom_attributes as $ca_key => $ca_value) {
                $custom_attributes_html .= $ca_key . '="' . esc_attr($ca_value) . '"';
            }
            //text,number, checkbox
            if ($type == 'text' || $type == 'number' || $type == 'checkbox' || $type == 'colorpicker' || $type == 'upload') {
                $additional_attrs = '';
                $relation_attr = '';
                $current_value = stripslashes($current_value);
                if ($type == 'checkbox') {
                    $additional_attrs .= $current_value === 'yes' ? 'checked="checked"' : '';
                    $current_value = 'yes';
                    $new_line_desc = '';
                    if (!empty($relations))
                        $relation_attr = 'data-relation="' . http_build_query($relations) . '"';
                }
                else if ($placeholder == '') {
                    $placeholder = 'placeholder="' . esc_attr($default) . '"';
                }
                if ($type == 'colorpicker') {
                    $type = 'text';
                    $input_class .= ' nbdesigner-color-picker';
                }
                $input_html = '';
                if ($prefix !== false) $input_html .= $prefix;
                if ($type == 'upload')
                    $input_html .= '<a href="#" class="nbdesigner-add-image">+</a>';
                $input_html .= '<input type="' . esc_attr($type) . '" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" ' . $placeholder . ' value="' . esc_attr($current_value) . '" ' . $additional_attrs . ' style="' . $css . '" ' . $custom_attributes_html . ' class="' . esc_attr($input_class) . '" ' . $relation_attr . ' />';
                if ($type == 'upload') $input_html .= '<a href="#" class="nbdesigner-remove-image">-</a>';
                if ($subfix !== false) $input_html .= $subfix;
                $input_html .= $new_line_desc;
            }
            //textarea
            else if ($type == 'textarea') {
                $input_html = '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" '. $placeholder .' class="' . esc_attr($class) . '" style="' . esc_attr($css) . '">' . stripslashes(esc_textarea($current_value)) . '</textarea>' . $new_line_desc;
            }
            //select
            else if ($type == 'select' || $type == 'multiselect') {
                $multiple       = $type == 'multiselect' ? 'multiple' : '';
                $brackets       = $type == 'multiselect' ? '[]' : '';
                $input_html     = '<select id="' . esc_attr($id) . '" name="' . esc_attr($id . $brackets) . '" style="' . esc_attr($css) . '" class="' . esc_attr($class) . '" ' . $multiple . '>';
                foreach ($options as $option_key => $option_val) {
                    if (is_array($current_value)) {
                        $selected = selected(in_array($option_key, $current_value), true, false);
                    } else {
                        $selected = selected($current_value, $option_key, false);
                    }
                    $input_html .= '<option value="' . esc_attr($option_key) . '" ' . $selected . '>' . $option_val . '</option>';
                }
                $input_html .= '</select>' . $new_line_desc;
            }
            //radio
            else if ($type == 'radio') {
                $input_html .= '<div style="margin-bottom: 10px;"  id="' . esc_attr($id) . '">';
                foreach ($options as $option_key => $option_val) {
                    $relation_attr = '';
                    if (isset($relations[$option_key]))
                        $relation_attr = is_array($relations[$option_key]) ? 'data-relation="' . http_build_query($relations[$option_key]) . '"' : '';
                    $input_html .= '<p><label><input type="radio" ' . $relation_attr . ' name="' . esc_attr($id) . '" value="' . esc_attr($option_key) . '" ' . checked($current_value, $option_key, false) . ' />' . $option_val . '</label></p>';
                }
                $input_html .= '</div>';
            }
            //ace editor
            else if ($type == 'ace-editor') {
                $input_html = '<div id="' . esc_attr($id) . '" style="' . esc_attr($css) . '" class="nbdesigner-ace-editor ' . esc_attr($class) . '">' . stripslashes($current_value) . '</div><textarea class="nbdesigner-hidden" name="' . esc_attr($id) . '">' . stripslashes($current_value) . '</textarea>';
            } else if ($type == 'values-group') {
                $prefixes       = isset($parameters['prefixes']) ? $parameters['prefixes'] : array();
                $regexs         = isset($parameters['regexs']) ? $parameters['regexs'] : array();
                $head_th        = '';
                $head_td        = '';
                $prefixes_html  = '';
                $add_btn        = '';
                $regex_html     = '';
                foreach ($options as $key => $value) {
                    $head_th .= '<th>' . $value . '</th>';
                    if (isset($prefixes[$key]))
                        $prefixes_html = '<span class="nbdesigner-values-group-prefix">' . $prefixes[$key] . '</span>';
                    if (isset($regexs[$key]))
                        $regex_html = 'data-regex="' . esc_attr($regexs[$key]) . '"';
                    $head_td .= '<td>' . $prefixes_html . '<input type="text" id="nbdesigner-values-group-input--' . esc_attr($key) . '" ' . $regex_html . ' /></td>';
                }
                $head_th    .= '<th></th>';
                $head_td    .= '<td><a href="#" class="nbdesigner-values-group-add button-secondary" id="nbdesigner-values-group-add--' . $id . '">' . esc_html__('Add', 'web-to-print-online-designer') . '</a></td>';
                $input_html  = '<div id="' . esc_attr($id) . '" style="' . esc_attr($css) . '" class="nbdesigner-values-group ' . esc_attr($class) . '"><table><thead><tr>' . $head_th . '</tr><tr>' . $head_td . '</tr></thead><tbody></tbody></table></div><input class="nbdesigner-option-value nbdesigner-hidden" name="' . esc_attr($id) . '" value="' . esc_attr($current_value) . '" />';
            }
            //multivalues
            else if ($type == 'multivalues') {
                $input_html = '<div id="' . esc_attr($id) . '" class="nbdesigner-multi-values ' . esc_attr($class) . '"><input type="hidden" name="' . esc_attr($id) . '" value="' . esc_attr($current_value) . '" />';
                $values     = explode('|', $current_value);
                foreach ( $options as $key => $option_label ) {
                    $value       = $values[ $key ];
                    $input_html .= '<label>' . esc_attr( $option_label ) . ' <input step="any" style="'. esc_attr( $css ) .'" class="nbdesigner-multivalues-input" type="number" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $value ) . '" ' . $custom_attributes_html . ' /></label>&nbsp;';
                }
                $input_html .= '</div>';
            }else if($type == 'multicheckbox'){
                $input_html = '<div id="' . esc_attr($id) . '" class="nbdesigner-multi-checkbox ' . esc_attr($class) . '">';
                $enable_select = isset($parameters['enable_select']) ? $parameters['enable_select'] : 0;

                $input_html .= '<p>' . esc_html__('Select', 'web-to-print-online-designer').': <a class="nbd-select select-all">' . esc_html__('All', 'web-to-print-online-designer') . '</a>&nbsp;&nbsp;<a class="nbd-select select-none">None</a></p>';
                foreach ($options as $key => $label) {
                    $_depend = '';
                    $class = '';
                    if( is_null( $product_id ) ){
                        $val = nbdesigner_get_option( $key );
                    }else{
                        if( isset( $local_settings[$key] ) ){
                            $val = $local_settings[$key];
                        }else{
                            $val = nbdesigner_get_option( $key );
                        }
                    }
                    $op_checked = $val == 1 ? 'checked="checked"' : '';
                    if(isset($depend[$key]) && $depend[$key] != ''){
                        if( $depend[$key] != $key ){
                            $_depend = 'data-depend='.$depend[$key];
                            if( is_null( $product_id ) ){
                                $depend_val = nbdesigner_get_option( $depend[$key] );
                            }else{
                                if( isset( $local_settings[$depend[$key]] ) ){
                                    $depend_val = $local_settings[$depend[$key]];
                                }else{
                                    $depend_val = nbdesigner_get_option( $depend[$key] );
                                }
                            }
                            if( $depend_val != 1 ){
                                $class = 'nbd-hide';
                            }
                        }else{
                            $_depend = 'data-undepend="1"';
                        }
                    }
                    $input_html .= '<p class='.$class.'><input type="hidden" value="0" name="'. esc_attr($key) .'"/><input '.$_depend.' value="1" type="checkbox" '.$op_checked.' id="'. esc_attr($key) .'" name="'.esc_attr($key).'"/><label for="'. esc_attr($key) .'" style="'. esc_attr($css) .'">' .$label. '</label></p>';
                }
                $input_html .= '</div>';
            }else if($type == 'nbd-media'){
                $input_html .= '<div class="nbd-media-wrap">';
                $input_html .= '<input class="nbd-media-value" type="hidden" id="'. esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($current_value) . '"' . ' />';
                $src = nbd_get_image_thumbnail( $current_value );
                $img_class = $src ? '' : 'nbdesigner-disable';
                $input_html .= '<img class="nbd-media-img ' . $img_class . '" src="'. $src .'" /><br />';
                $reset_class = $current_value != '' ? '' : 'nbdesigner-disable';
                $input_html .= '<a class="button button-primary nbd-select-media" href="javascript: void(0)" onclick="NBDESIGNADMIN.selectSettingMedia( this )">'. esc_html__('Select', 'web-to-print-online-designer') .'</a>';
                $input_html .= '<a class="button nbdesigner-delete nbd-reset-media '. $reset_class .'" href="javascript: void(0)" onclick="NBDESIGNADMIN.resetSettingMedia( this )">'. esc_html__('Reset', 'web-to-print-online-designer') .'</a>';
                $input_html .= '</div>';
            }else if( $type == 'nbd-dynamic-list' ){
                $col1 = isset($parameters['col1']) ? $parameters['col1'] : '';
                $col2 = isset($parameters['col2']) ? $parameters['col2'] : '';
                $row  = '<div class="nbd-dynamic-list-item">';
                $row .=     '<div class="nbd-dynamic-list-item-wrap">';
                $row .=         '<input type="text" value="" placeholder="'. $col1 .'" name="'. esc_attr($id) .'[0][]"/>';
                $row .=     '</div>';
                $row .=     '<div class="nbd-dynamic-list-item-wrap">';
                $row .=         '<input type="text" value="" placeholder="'. $col2 .'" name="'. esc_attr($id) .'[1][]"/>';
                $row .=     '</div>';
                $row .=     '<div class="nbd-dynamic-list-item-wrap">';
                $row .=         '<a class="button delete">&times;</a>';
                $row .=     '</div>';
                $row .= '</div>';
                $input_html .= '<div class="nbd-dynamic-list-wrap">';
                $input_html .=      '<div class="nbd-dynamic-list">';
                $current_value = unserialize( $current_value );
                if( isset( $current_value[0] ) ){
                    foreach( $current_value[0] as $k => $v ){
                        $input_html .= '<div class="nbd-dynamic-list-item">';
                        $input_html .=     '<div class="nbd-dynamic-list-item-wrap">';
                        $input_html .=         '<input type="text" value="'. $v .'" placeholder="'. $col1 .'" name="'. esc_attr($id) .'[0][]"/>';
                        $input_html .=     '</div>';
                        $input_html .=     '<div class="nbd-dynamic-list-item-wrap">';
                        $input_html .=         '<input type="text" value="'. $current_value[1][$k] .'" placeholder="'. $col2 .'" name="'. esc_attr($id) .'[1][]"/>';
                        $input_html .=     '</div>';
                        $input_html .=     '<div class="nbd-dynamic-list-item-wrap">';
                        $input_html .=         '<a class="button delete">&times;</a>';
                        $input_html .=     '</div>';
                        $input_html .= '</div>';
                    }
                }
                $input_html .=      '</div>';
                $input_html .=      '<div class="nbd-dynamic-list-action">';
                $input_html .=          '<a class="button insert" data-row="'. esc_attr( $row ) .'">'. esc_html__('Add type', 'web-to-print-online-designer') .'</a>';
                $input_html .=      '</div>';
                $input_html .= '</div>';
            }
            $description_html = '';
            if ($description !== false) {
                $description_html = '<label for="' . $id . '"><span class="description">' . wp_kses_post($description) . '</span></label>';
            }
            if($show):
            ?>
            <tr data-option-id="<?php echo( $id ); ?>" data-depend="<?php echo( $depend_on['id'] ); ?>" 
                data-depend-value="<?php echo( $depend_on['value'] ); ?>" data-depend-operator="<?php echo( $depend_on['operator'] ); ?>">
                <th scope="row" <?php echo( $type === 'section_title' ? 'colspan="2" class="nbdesigner-section-title"' : '' ); ?>>
                <?php esc_html_e( $title ); ?>
                </th>
                <?php if ($type !== 'section_title'): ?>
                <td class="nbdesigner-option-type--<?php echo $type; ?> nbdesigner-clearfix">
                <?php if ($type == 'ace-editor') echo $description_html; ?>
                <?php echo $input_html; ?>
                <?php if ($type != 'ace-editor') echo $description_html; ?>
                </td>
                <?php endif; ?>
                <?php if( !is_null( $product_id ) ): ?> 
                <td style="text-align: center;"><span title="<?php esc_html_e('Delete', 'web-to-print-online-designer'); ?>" class="button" onclick="NBDESIGNADMIN.removeLocalSetting( this )">&times;</span></td>
                <?php endif; ?>
            </tr>
            <?php
            endif;
        }
        private function boolean_string_to_int($value) {
            if ($value === 'yes') {
                return 1;
            } else if ($value === 'no') {
                return 0;
            } else {
                return $value;
            }
        }
        private function not_empty($value) {
            return $value == '0' || !empty($value);
        }
        public function save_options(){
            $options_in_tab = $this->get_options_by_tab( $this->current_tab );
            $latest_recurrence = get_option( 'nbdesigner_notifications_recurrence', false );
            foreach ( $options_in_tab as $key => $value ) {
                $post_val = '';
                if ( !isset( $_POST[$key] ) && $this->get_option_type( $key ) == 'checkbox' ) {
                    $post_val = 'no';
                } else if ( isset( $_POST[$key] ) && $this->get_option_type( $key ) == 'text') {
                    $post_val = wc_clean( $_POST[$key] );
                } else if ( $this->get_option_type( $key ) == 'multiselect' ) {
                    if ( isset( $_POST[$key] ) ) $post_val = wc_clean( $_POST[$key] );
                } else if ( $this->get_option_type($key) == 'nbd-dynamic-list' ){
                    $_post_val  = isset( $_POST[$key] ) ? wc_clean( $_POST[$key] ) : array( 0 => array(), 1 => array() );
                    $post_val   = serialize( $_post_val );
                } else if( $this->get_option_type($key) != 'multicheckbox' ) {
                    if( !isset( $_POST[$key] ) ){
                        $post_val = nbdesigner_get_default_setting( $key );
                    }else{
                        $post_val = $this->get_option_type( $key ) == 'textarea' ? wp_kses_post( $_POST[$key] ) : wc_clean( $_POST[$key] );
                    }
                }
                update_option( $key, $post_val );
                if( $key == 'nbdesigner_truetype_fonts' ){
                    $this->remove_truetype_php_fonts( $post_val );
                }
                if( $key == 'nbdesigner_enable_ajax_cart' && $_POST[$key] == 'yes' ){
                    update_option( 'woocommerce_enable_ajax_add_to_cart', 'no' );
                }
            }
            $this->setting_cron_job( $latest_recurrence );
            $this->update_option_frontend();
            do_action( 'nbdesigner_save_options', $this->current_tab );
            $this->clear_transient_options();
        }
        public function update_option_frontend(){
            $settings = default_frontend_setting();
            foreach ($settings as $key => $val){
                if(isset($_POST[$key])){
                    $post_val = wc_clean( $_POST[$key] );
                    update_option($key, $post_val);
                }
            }
            $multicheckbox_settings = nbd_multicheckbox_settings();
            foreach ($multicheckbox_settings as $key => $val){
                if(isset($_POST[$key])){
                    $post_val = wc_clean( $_POST[$key] );
                    update_option($key, $post_val);
                }
            }
        }
        public function setting_cron_job($latest_recurrence){
            $notifications = get_option('nbdesigner_notifications');
            $recurrence = get_option('nbdesigner_notifications_recurrence');
            if($notifications == 'yes'){
                if($recurrence != $latest_recurrence){
                    wp_clear_scheduled_hook( 'nbdesigner_admin_notifications_event' );
                    wp_schedule_event(time(), $recurrence, 'nbdesigner_admin_notifications_event');
                }
                $timestamp = wp_next_scheduled( 'nbdesigner_admin_notifications_event' );
                if($timestamp == false){
                    wp_schedule_event(time(), $recurrence, 'nbdesigner_admin_notifications_event');
                }
            }else{
                wp_clear_scheduled_hook( 'nbdesigner_admin_notifications_event' );
            }
        } 
        public function remove_truetype_php_fonts( $truetype_list ){
            if( $truetype_list != '' ){
                $fonts = preg_split( '/\r\n|[\r\n]/', $truetype_list );
                if( count( $fonts ) ){
                    $all_fonts = nbd_get_fonts( true );
                    $need_remove = array();
                    foreach( $fonts as $font ){
                        $font = str_replace( ' ', '', strtolower( trim( $font ) ) );
                        foreach( $all_fonts as $afont ){
                            $afont_name = str_replace( ' ', '', strtolower( trim( $afont->name ) ) );
                            if( $font == $afont_name ){
                                $need_remove[] = str_replace( ' ', '', strtolower( trim( $afont->alias ) ) );
                            }
                        }
                    }
                    foreach( $need_remove as $rfont ){
                        $variations = array( '', 'b', 'i', 'bi' );
                        $exts = array( '.php', '.z', '.ctg.z' );
                        foreach( $variations as $variation ){
                            foreach( $exts as $ext ){
                                $path = K_PATH_FONTS . $rfont . $variation . $ext;
                                if( file_exists( $path ) ) unlink ( $path );
                            }
                        }
                    }
                }
            }
        }
        public function reset_options(){
            $options_in_tab = $this->get_options_by_tab( $this->current_tab );
            foreach( $options_in_tab as $key => $value ) {
                if( $this->get_option_type( $key ) == 'multicheckbox' ){
                    $options = (array) json_decode( $this->get_default_option( $key ) );
                    if($options){
                        foreach ( $options as $k => $v  ){
                            update_option( $k, $v );
                        }
                    }
                }else{
                    update_option( $key, $value );
                }
            }
            do_action( 'nbdesigner_reset_options', $this->current_tab );
            $this->clear_transient_options();
        }
        public function clear_transient_options(){
            delete_transient( 'nbd_all_settings' );
            delete_transient( 'nbd_all_frontend_settings' );
        }
    }
}
if( !class_exists( 'NBD_Local_Settings' ) ) {
    class NBD_Local_Settings {
        protected static $instance;
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct() {
            //todo
        }
        public function init(){
            $this->ajax();
            add_action( 'nbo_options_before_meta_box_tabs', array( $this, 'local_settings_tab' ) );
            add_action( 'nbo_options_before_meta_box_panels', array( $this, 'local_settings_panel' ) );
            add_action( 'nbo_save_options', array( $this, 'save_local_settings' ) );
        }
        public function ajax(){
            $ajax_events = array(
                'nbd_get_option_output' => false
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        public function get_local_settings(){
            require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/frontend.php' );
            require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/colors.php' );
            require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/appearance.php' );
            $frontend_options   = Nbdesigner_Settings_Frontend::get_options();
            $color_options      = Nbdesigner_Settings_Colors::get_options();
            $appearances        = Nbdesigner_Appearance_Settings::get_options();
            $group_titles       = array(
                'tool-text'             => esc_html__('Text Options', 'web-to-print-online-designer'),
                'tool-clipart'          => esc_html__('Clipart Options', 'web-to-print-online-designer'),
                'tool-image'            => esc_html__('Image Options', 'web-to-print-online-designer'),
                'tool-draw'             => esc_html__('Free draw Options', 'web-to-print-online-designer'),
                'tool-qrcode'           => esc_html__('Qr Code Options', 'web-to-print-online-designer'),
                'misc'                  => esc_html__('Misc', 'web-to-print-online-designer'),
                'color-setting'         => esc_html__('Setting color', 'web-to-print-online-designer'),
                'modern-layout'         => esc_html__('Modern layout', 'web-to-print-online-designer')
            );
            $option_groups = array(
               'tool-text'              => $frontend_options['tool-text'],
               'tool-clipart'           => $frontend_options['tool-clipart'],
               'tool-image'             => $frontend_options['tool-image'],
               'tool-draw'              => $frontend_options['tool-draw'],
               'tool-qrcode'            => $frontend_options['tool-qrcode'],
               'misc'                   => $frontend_options['misc'],
               'color-setting'          => $color_options['color-setting'],
               'modern-layout'          => $appearances['modern']
            );
            return array(
                'group_titles'  =>  $group_titles,
                'option_groups' =>  $option_groups
            );
        }
        public function nbd_get_option_output(){
            $sid            = ( isset( $_POST['sid'] ) && $_POST['sid'] != '' ) ? wc_clean( $_POST['sid'] ) : '';
            $product_id     = ( isset( $_POST['product_id'] ) && $_POST['product_id'] != '' ) ? absint( $_POST['product_id'] ) : 0;
            $result = array(
                'flag'  => 0
            );
            $option = array();
            if( $sid != '' && $product_id != 0 ){
                $settings = $this->get_local_settings();
                foreach( $settings['group_titles'] as $gkey => $gtext ){
                    foreach( $settings['option_groups'][$gkey] as $op ){
                        if( $op['id'] == $sid ){
                            $option = $op;
                        }
                    }
                }
                if( !empty( $option ) ){
                    $show = true;
                    if( isset( $option['layout'] ) ){
                        $show = nbd_check_availaible_option( $option['layout'] );
                    }
                    if( $show ){
                        ob_start();
                        Nbdesigner_Settings::output_item( $option, $product_id );
                        $html = ob_get_clean();
                        $result = array(
                            'flag'  => 1,
                            'html'  => $html
                        );
                    }
                }
            }
            wp_send_json( $result );
            exit;
        }
        public function build_option_output( $key, $settings, $product_id ){
            $option = array();
            foreach( $settings['group_titles'] as $gkey => $gtext ){
                foreach( $settings['option_groups'][$gkey] as $op ){
                    if( $op['id'] == $key ){
                        $option = $op;
                    }
                }
            }
            $html = '';
            if( !empty( $option ) ){
                $show = true;
                if( isset( $option['layout'] ) ){
                    $show = nbd_check_availaible_option( $option['layout'] );
                }
                if( $show ){
                    ob_start();
                    Nbdesigner_Settings::output_item( $option, $product_id );
                    $html = ob_get_clean();
                    $result = array(
                        'flag'  => 1,
                        'html'  => $html
                    );
                }
            }
            echo $html;
        }
        public function local_settings_tab(){
            ?>
            <li><a href="#nbd-local-settings"><span class="dashicons dashicons-art"></span> <?php esc_html_e('Design Editor Setting', 'web-to-print-online-designer'); ?></a></li>
            <?php
        }
        public function local_settings_panel(){
            $post_id        = get_the_ID();
            /* Local settings */
            $nbls_enable    = get_post_meta($post_id, '_nbls_enable', true);
            $keys_string    = get_post_meta($post_id, '_nbls_keys_string', true);
            $local_settings = get_post_meta($post_id, '_nbls_settings', true);
            $keys_arr       = array();
            if( $keys_string ){
                $keys_arr   = explode( '|', $keys_string );
            }
            if( $local_settings ){
                $local_settings = unserialize( $local_settings );
            } else {
                $local_settings = array();
            }
            $settings       = $this->get_local_settings();
            /* Extra local settings */
            $nbes_settings              = get_post_meta($post_id, '_nbes_settings', true);
            $nbes_enable_settings       = get_post_meta($post_id, '_nbes_enable_settings', true);
            $select_all_clipart_cat     = $select_all_font_cat = false;
            $nbes_settings_defalut      = apply_filters( 'nbes_settings', array(
                'clipart_cats'          => array(),
                'font_cats'             => array(),
                'background_colors'     => array( 'codes' => array(), 'names' => array() ),
                'foreground_colors'     => array( 'codes' => array(), 'names' => array() ),
                'force_fg'              => false,
                'combination_colors'    => array( 'bg_codes' => array(), 'bg_names' => array(), 'fg_codes' => array(), 'fg_names' => array() ),
                /* 3D preview */
                'td_preview'            => 0,
                'td_folder_name'        => '',
                'td_custom_mesh_name'   => 'custom',
                /* Sticker cutline preview */
                'sticker_preview'       => 0
            ), $post_id);
            if( $nbes_settings ){
                $nbes_settings      = unserialize( $nbes_settings );
                $nbes_settings      = array_merge( $nbes_settings_defalut, $nbes_settings );
            } else {
                $select_all_clipart_cat = $select_all_font_cat = true;
                $nbes_settings          = $nbes_settings_defalut;
            }
            if( $nbes_enable_settings ){
                $nbes_enable_settings   = unserialize( $nbes_enable_settings );
            }else{
                $nbes_enable_settings   = apply_filters( 'nbes_enable_settings', array(
                    'clipart'       => 0,
                    'font'          => 0,
                    'background'    => 0,
                    'foreground'    => 0,
                    'combination'   => 0
                ), $post_id);
            }
            $art_cat_path           = NBDESIGNER_DATA_DIR . '/art_cat.json';
            $art_cats               = file_exists( $art_cat_path ) ? (array)json_decode( file_get_contents( $art_cat_path ) ) : array();
            $font_cat_path          = NBDESIGNER_DATA_DIR . '/font_cat.json';
            $font_cats              = file_exists( $art_cat_path ) ? (array)json_decode( file_get_contents( $font_cat_path ) ) : array();
            include_once( NBDESIGNER_PLUGIN_DIR .'views/metaboxes/local-settings.php' );
        }
        public function save_local_settings( $post_id ){
            $nbls_enable    = isset( $_POST['_nbls_enable'] ) ? wc_clean( $_POST['_nbls_enable'] ) : '0';
            if( $nbls_enable == '1' ){
                $keys_string    = isset( $_POST['_nbls_keys_string'] ) ? trim( $_POST['_nbls_keys_string'] ) : '';
                $options_string = wc_clean( $_POST['_nbls_options_string'] );
                $options_arr    = explode( '|', $options_string );
                $settings       = $this->get_local_settings();
                $nbls_settings  = array();
                foreach( $options_arr as $key ){
                    $post_val = '';
                    if( !isset( $_POST[$key] ) && $this->get_option_type( $key, $settings ) == 'checkbox' ){
                        $post_val  = 'no';
                    } else if ( $this->get_option_type( $key, $settings ) == 'nbd-dynamic-list' ){
                        $_post_val = isset( $_POST[$key] ) ? $_POST[$key] : array( 0 => array(), 1 => array() );
                        $post_val  = serialize( $_post_val );
                    } else if ( $this->get_option_type( $key, $settings ) != 'multicheckbox' ) {
                        if( !isset( $_POST[$key] ) ){
                            $post_val = nbdesigner_get_default_setting( $key );
                        } else {
                            $post_val = $this->get_option_type($key, $settings) == 'textarea' ? wp_kses_post( $_POST[$key] ) : wc_clean( $_POST[$key] );
                        }
                    } else if ( isset( $_POST[$key] ) ){
                        $post_val  = wc_clean( $_POST[$key] );
                    }
                    $nbls_settings[$key] = $post_val;
                }
                update_post_meta( $post_id, '_nbls_keys_string', $keys_string );
                update_post_meta( $post_id, '_nbls_settings', serialize( $nbls_settings ) );
            }
            update_post_meta( $post_id, '_nbls_enable', $nbls_enable );
            if( isset( $_POST['_nbes_enable_settings'] ) ){
                update_post_meta( $post_id, '_nbes_enable_settings', serialize( $_POST['_nbes_enable_settings'] ) );
            }
            if( isset( $_POST['_nbes_settings'] ) ){
                update_post_meta( $post_id, '_nbes_settings', serialize( $_POST['_nbes_settings'] ) );
            }
        }
        public function get_option_type( $key, $settings ){
            $option_type = '';
            foreach( $settings['group_titles'] as $gkey => $gtext ){
                foreach( $settings['option_groups'][$gkey] as $op ){
                    if( $op['id'] == $key ){
                        $option_type = $op['type'];
                    }
                }
            }
            return $option_type;
        }
    }
}
$nbd_local_settings = NBD_Local_Settings::instance();
$nbd_local_settings->init();

if( !class_exists( 'NBD_Setting_Tools' ) ) {
    class NBD_Setting_Tools {
        protected static $instance;
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct() {
            //todo
        }
        public function init(){
            $this->ajax();
            if ( is_admin() ) {
                add_action( 'admin_init', array( $this, 'admin_redirects' ) );
            }
        }
        public function ajax(){
            $ajax_events = array(
                'nbd_import_settings' => false
            );
            foreach ( $ajax_events as $ajax_event => $nopriv ) {
                add_action( 'wp_ajax_' . $ajax_event, array( $this, $ajax_event ) );
                if ( $nopriv ) {
                    add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $ajax_event ) );
                }
            }
        }
        public function admin_redirects(){
            if( isset( $_GET['nbd_export_setting'] ) && $_GET['nbd_export_setting'] == 1 ){
                $settings   = nbdesigner_get_all_setting();
                $json       = json_encode( $settings );

                header("Content-Disposition: attachment; filename=settings.json");
                header("Content-type: application/json");
                echo $json;

                exit;
            }
        }
        public function nbd_import_settings(){
            $data = array(
                'mes'   => esc_html__( 'You do not have permission to do this action!', 'web-to-print-online-designer' ),
                'flag'  => 0
            );
            if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'delete_nbd_art' ) ) {
                wp_send_json( $data );
            }

            if ( !isset( $_FILES['file'] ) ){
                $data['mes'] = esc_html__( 'Please select a json setting file!', 'web-to-print-online-designer' );
                wp_send_json( $data );
            }

            $json       = file_get_contents( $_FILES['file']["tmp_name"] );
            $settings   = json_decode( $json, TRUE );
            if( !is_array( $settings ) || !count( $settings ) ){
                $data['mes'] = esc_html__( 'Wrong settings!', 'web-to-print-online-designer' );
                wp_send_json( $data );
            }

            foreach( $settings as $key => $value ){
                update_option( $key, $value );
            }

            $data = array(
                'mes'   => esc_html__( 'Imported successfully!', 'web-to-print-online-designer' ),
                'flag'  => 1
            );
            wp_send_json( $data );
        }
    }
}
$nbd_setting_tools = NBD_Setting_Tools::instance();
$nbd_setting_tools->init();