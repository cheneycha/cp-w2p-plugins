<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
?>
<table class="wp-list-table widefat fixed striped posts faqs faqs-availabled">
    <thead>
        <tr>
            <th class="check">&nbsp;</th>
            <th><?php esc_html_e('FAQ', 'web-to-print-online-designer'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $faqs as $faq ): ?>
        <tr data-id="<?php echo $faq->ID; ?>">
            <th class="check"><input type="checkbox" value="<?php echo $faq->ID; ?>"/></th>
            <td><a class="nbf-title" href="post.php?post=<?php echo $faq->ID;?>&action=edit"><?php echo $faq->post_title; ?></a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>