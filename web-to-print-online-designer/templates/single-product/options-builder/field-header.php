<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-field-header">
    <label for='nbd-field-<?php echo $field['id']; ?>'>
        <?php echo $field['general']['title']; ?>
        <?php if( $field['general']['required'] == 'y' ): ?>
        <span class="nbd-required">*</span>
        <?php endif; ?>
    </label> 
    <?php if( $field['general']['description'] != '' ): ?>
    <span data-position="<?php echo $tooltip_position; ?>" data-tip="<?php echo html_entity_decode( $field['general']['description'] ); ?>" class="nbd-help-tip"></span>
    <?php endif; ?>
    <?php if( $options['display_type'] == 5 ): ?>
        <span class="nbo-minus nbo-toggle" ng-click="toggle_field( $event )">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        </span>
        <span class="nbo-plus nbo-toggle" ng-click="toggle_field( $event )">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        </span>
    <?php endif; ?>
</div>