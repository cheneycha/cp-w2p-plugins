<?php if (!defined('ABSPATH')) exit; ?>
<?php 
    if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): 
        $no_group   = 0;
        $nbo_groups = array();
        foreach( $options['groups'] as $group ){
            if( isset( $group['fields'] ) && count( $group['fields'] ) > 0 ){
                $no_group++;
                $nbo_groups[] = $group;
            }
        }
?>
<div class="nbo-group-timeline-container" ng-class="totalGroupPage > 1 ? 'paged' : ''">
    <div class="nbo-group-timeline-wrap">
        <div class="nbo-group-timeline-line" ng-style="{'width': <?php echo ( $no_group + 1 ) * 150; ?> + 'px', transform: 'translateX(' + groupTimeLineTranslate + ')'}">
            <?php foreach( $nbo_groups as $g_index => $nbo_group ): ?>
                <div class="nbo-group-timeline-step" 
                    ng-class="{ 'active': current_group_panel == <?php echo $g_index; ?>, 'over': current_group_panel > <?php echo $g_index; ?>}" 
                    ng-style="{'left': <?php echo ( $g_index + 1 ) * 150; ?> + 'px'}"
                    ng-click="changeGroupPanel($event, <?php echo $g_index; ?>)" >
                    <div class="nbo-group-timeline-step-inner">
                        <span class="nbo-group-timeline-tooltip"><?php echo $nbo_group['title']; ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="nbo-group-timeline-fill-line" ng-style="{transform: 'scaleX(' + ( current_group_panel + 1 ) / ( no_of_group + 1 ) + ')'}"></div>
        </div>
    </div>
    <div class="nbo-group-timeline-paged nbo-group-timeline-paged-prev" ng-click="changeGroupPage($event, 'prev')" ng-class="currentGroupPage == 0 ? 'nbo-disabled' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
            <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
        </svg>
    </div>
    <div class="nbo-group-timeline-paged nbo-group-timeline-paged-next" ng-click="changeGroupPage($event, 'next')" ng-class="currentGroupPage == ( totalGroupPage - 1 ) ? 'nbo-disabled' : ''">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
            <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
        </svg>
    </div>
</div>
<div class="nbo_group_panel_wrap">
    <div class="nbo_group_panel_wrap_inner" ng-init="no_of_group=<?php echo $no_group; ?>" ng-style="{'width': <?php echo $no_group * 100; ?> + '%', transform: 'translateX(-' + current_group_panel * 100 / no_of_group + '%)'}">
<?php endif; ?>
<?php 
    foreach( $options['groups'] as $group ): 
        if( isset( $group['fields'] ) && count( $group['fields'] ) > 0 ):
            $cols = (int) $group['cols'];
            if( count( $group['fields'] ) < $cols ) $cols = count( $group['fields'] );
?>
<div class="nbo-group-wrap nbo-flex-col-<?php echo $cols; ?>" <?php if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): ?>ng-style="{width: 100 / no_of_group + '%'}"<?php endif; ?> >
    <div class="nbo-group-header">
        <span class="group-title">
            <?php 
                if( $group['image'] != 0 ):
                    $group_image_url = nbd_get_image_thumbnail( $group['image'] );
            ?>
            <span class="nbo-group-icon"><span><img src="<?php echo $group_image_url; ?>" /></span></span>
            <?php endif; ?>
            <span><?php echo $group['title']; ?></span>
            <?php if( $group['des'] != '' ): ?>
            <span data-position="<?php echo $tooltip_position; ?>" data-tip="<?php echo $group['des']; ?>" class="nbd-help-tip"></span>
            <?php endif; ?>
        </span>
    </div>
    <div class="nbo-group-body">
        <?php 
            foreach( $group['fields'] as $f ){
                $f_index    = get_field_index_by_id( $f, $options["fields"] );
                $field      = $options["fields"][$f_index];
                $class      = $field['class'];
                if( $field['general']['enabled'] == 'y' && $field['need_show'] ) include( $field['template'] );
            }
        ?>
        <span class="nbo-group-toggle" ng-click="toggle_group($event)">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
            </svg>
        </span>
    </div>
    <?php if( $group['note'] != '' ): ?>
    <div class="nbo-group-footer">
        <?php echo $group['note']; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; endforeach;
    if( isset( $options['group_panel'] ) && $options['group_panel'] == 'on' ): ?>
        </div>
    </div>
    <div>
        <span class="nbo_group_panel_prev" ng-click="changeGroupPanel($event, 'prev')" ng-class="current_group_panel == 0 ? 'nbo-disabled' : ''">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
            </svg>
            <span><?php _e('Prev', 'web-to-print-online-designer'); ?></span>
        </span>
        <span class="nbo_group_panel_next" ng-click="changeGroupPanel($event, 'next')" ng-class="current_group_panel == ( no_of_group - 1 ) ? 'nbo-disabled' : ''">
            <span><?php _e('Next', 'web-to-print-online-designer'); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
            </svg>
        </span>
    </div>
<?php endif;