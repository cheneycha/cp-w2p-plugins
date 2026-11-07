<?php
class NBD_Category extends Walker {
    public $tree_type = 'category';
    public $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );

        if ( $depth == 0 ) {
            $output .= $indent . '<ul class="children level-' . $depth . '">' . "\n";
        } else {
            $output .= "$indent<ul class='children level-$depth'>\n";
        }
    }
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        if ( $depth == 0 ) {
            $output .= "$indent</ul> <!-- .sub-category -->\n";
        } else {
            $output .= "$indent</ul>\n";
        }
    }   
    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract( $args );
        $indent = str_repeat( "\t", $depth );

        $url = '?cat=' . $category->term_id;
        $pre = '<svg class="before" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path d="M16.01 11H4v2h12.01v3L20 12l-3.99-4z"/></svg>';
        if ( $depth == 0 ) {
            $caret      = $args['has_children'] ? ' ' : '';
            $class_name = $args['has_children'] ? ' class="has-children parent-cat-wrap"' : ' class="parent-cat-wrap"';
            $output    .= $indent . '<li' . $class_name . '>' . "\n\t" .'<a href="' . $url . '">' .$pre. $category->name . $caret . '</a>' . "\n";
        } else {
            $caret = $args['has_children'] ? ' ' : '';
            $class_name = $args['has_children'] ? ' class="has-children"' : '';
            $output .= $indent . '<li' . $class_name . '><a href="' . $url . '">' .$pre. $category->name . $caret . '</a>';
        }
    }
    function end_el( &$output, $category, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        if ( $depth == 1 ) {
            $output .= "$indent</li><!-- .sub-block -->\n";
        } else {
            $output .= "$indent</li>\n";
        }
    }
}

class NBF_List_Category extends Walker {
    public $tree_type = 'category';
    public $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );

    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract( $args );
        $indent     = str_repeat( "&mdash;", $depth );
        $url        = 'term.php?taxonomy=nbd-faq-category&amp;post_type=nbd-faq&amp;tag_ID=' . $category->term_id . '&amp;wp_http_referer=' . urlencode( admin_url( 'edit-tags.php?taxonomy=nbd-faq-category&post_type=nbd-faq' ) );
        $class_name = $args['has_children'] ? ' class="has-children"' : '';
        $output    .= '<tr' . $class_name . '><th>' . $indent . ' <a href="' . $url . '">' . $category->name . '</a></th><td>' . $category->count . '</td></tr>';
    }
}

class NBF_Dropdown_Category extends Walker {
    public $tree_type = 'category';
    public $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );

    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract( $args );
        $indent     = str_repeat( "&mdash;", $depth ) . ' ';
        $output    .= '<option value="' . $category->term_id . '">' . $indent . $category->name . '</option>';
    }
}