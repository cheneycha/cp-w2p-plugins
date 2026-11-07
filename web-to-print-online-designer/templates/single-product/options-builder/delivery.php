<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbo-delivery" ng-if="valid_form && nbd_fields['<?php echo $delivery_field['id']; ?>'].enable">
    <p><?php _e('Choose Quantity & Delivery Speed', 'web-to-print-online-designer'); ?></p>
    <div class="nbo-delivery-wrapper" <?php if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View' && $nbd_qv_type == '2') echo 'nbd-perfect-scroll'; ?>>
        <table>
            <thead>
                <tr>
                    <th class="nbo-delivery-icon-wrap" rowspan="2">
                        <span class="nbo-delivery-icon">
                            <svg height="430pt" viewBox="0 -67 430.08 430" width="430pt" xmlns="http://www.w3.org/2000/svg"><path d="m428.96875 179.441406-21.769531-33.308594c-9.613281-14.71875-25.945313-23.507812-43.699219-23.507812h-56.300781v-32.398438c0-19.78125-16.085938-35.867187-35.867188-35.867187h-105.394531c-5.914062-30.886719-33.101562-54.3203125-65.6875-54.3203125-36.898438 0-66.914062 30.0195315-66.914062 66.9140625 0 23.199219 11.878906 43.664063 29.863281 55.671875h-56.371094c-3.773437 0-6.828125 3.054688-6.828125 6.828125s3.054688 6.828125 6.828125 6.828125h68.265625v13.652344h-27.308594c-3.769531 0-6.824218 3.054687-6.824218 6.828125 0 3.773437 3.054687 6.824219 6.824218 6.824219h27.308594v13.652343h-68.265625c-3.773437 0-6.828125 3.054688-6.828125 6.828125 0 3.773438 3.054688 6.828125 6.828125 6.828125h68.265625v30.234375c-12.035156 5.308594-20.480469 17.316406-20.480469 31.292969v6.738281c0 3.773438 3.058594 6.828125 6.828125 6.828125h60.546875c3.882813 16.703125 18.859375 29.203125 36.730469 29.203125 17.875 0 32.847656-12.5 36.730469-29.203125h131.339843c3.886719 16.703125 18.863282 29.203125 36.730469 29.203125 17.867188 0 32.847657-12.5 36.730469-29.203125h23.003906c3.773438 0 6.824219-3.054687 6.824219-6.828125v-75.984375c0-1.328125-.386719-2.628906-1.109375-3.734375zm-381.980469-112.488281c0-29.367187 23.894531-53.261719 53.261719-53.261719 29.371094 0 53.261719 23.894532 53.261719 53.261719s-23.890625 53.261719-53.261719 53.261719c-29.367188 0-53.261719-23.894532-53.261719-53.261719zm41.757813 123.941406h13.652344c3.773437 0 6.828124-3.054687 6.828124-6.828125 0-3.773437-3.054687-6.828125-6.828124-6.828125h-13.652344v-13.652343h47.789062c3.769532 0 6.824219-3.050782 6.824219-6.824219 0-3.773438-3.054687-6.828125-6.824219-6.828125h-47.789062v-13.652344h88.746094c3.769531 0 6.828124-3.054688 6.828124-6.828125s-3.058593-6.828125-6.828124-6.828125h-40.191407c17.707031-11.824219 29.453125-31.855469 29.8125-54.613281h104.222657c12.246093 0 22.210937 9.96875 22.210937 22.214843v127.972657h-204.714844c-.027343 0-.054687.007812-.085937.007812zm204.800781 61.4375h-97.476563c-1.074218-7.851562-4.558593-14.929687-9.703124-20.480469h107.179687zm-204.714844-20.480469h42.238281c-5.140624 5.550782-8.628906 12.628907-9.699218 20.480469h-53.105469c.046875-11.296875 9.257813-20.480469 20.566406-20.480469zm69.886719 49.6875c-13.277344 0-24.085938-10.804687-24.085938-24.085937 0-13.277344 10.808594-24.085937 24.085938-24.085937 13.28125 0 24.085938 10.808593 24.085938 24.085937 0 13.28125-10.804688 24.085937-24.085938 24.085937zm204.800781 0c-13.277343 0-24.085937-10.804687-24.085937-24.085937 0-13.277344 10.808594-24.085937 24.085937-24.085937 13.28125 0 24.085938 10.808593 24.085938 24.085937 0 13.28125-10.804688 24.085937-24.085938 24.085937zm52.90625-29.207031h-15.554687c-2.511719-18.386719-18.285156-32.617187-37.351563-32.617187-19.066406 0-34.835937 14.230468-37.351562 32.617187h-18.96875v-116.050781h56.300781c13.121094 0 25.1875 6.472656 32.273438 17.316406l20.652343 31.609375zm0 0"/><path d="m374.6875 257.453125c0 6.167969-5 11.167969-11.167969 11.167969s-11.167969-5-11.167969-11.167969 5-11.167969 11.167969-11.167969 11.167969 5 11.167969 11.167969zm0 0"/><path d="m169.886719 257.453125c0 6.167969-5 11.167969-11.167969 11.167969-6.164062 0-11.164062-5-11.164062-11.167969s5-11.167969 11.164062-11.167969c6.167969 0 11.167969 5 11.167969 11.167969zm0 0"/><path d="m109.226562 66.953125v-29.867187c0-3.773438-3.054687-6.824219-6.828124-6.824219-3.769532 0-6.824219 3.050781-6.824219 6.824219v27.640624l-13.933594 19.113282c-2.222656 3.046875-1.554687 7.3125 1.492187 9.53125 1.21875.890625 2.625 1.316406 4.019532 1.316406 2.105468 0 4.183594-.972656 5.519531-2.808594l15.246094-20.90625c.847656-1.164062 1.308593-2.570312 1.308593-4.019531zm0 0"/><path d="m361.21875 143.105469h-13.058594c-3.773437 0-6.828125 3.054687-6.828125 6.828125v47.785156c0 3.773438 3.054688 6.828125 6.828125 6.828125h45.945313c4.28125 0 8.113281-2.214844 10.261719-5.914063 2.148437-3.699218 2.152343-8.128906.035156-11.820312l-15.941406-27.902344c-5.566407-9.742187-16.007813-15.804687-27.242188-15.804687zm-6.230469 47.789062v-34.132812h6.230469c6.347656 0 12.242188 3.417969 15.386719 8.925781l14.402343 25.207031zm0 0"/></svg>
                        </span>
                        <div class="nbo-delivery-qty"><?php _e('Quantities', 'web-to-print-online-designer'); ?></div>
                    </th>
                    <th class="nbo-delivery-date-wrap-title" colspan="<?php echo count( $delivery_field['general']['attributes']["options"] ); ?>"><div><?php _e('Printing Price (excl. Delivery Fee)', 'web-to-print-online-designer'); ?></div></th>
                </tr>
                <tr>
                    <?php 
                        foreach ( $delivery_field['general']['attributes']["options"] as $key => $attr ): 
                            $delivery_days  = absint( $attr['delivery'] );
                            $delivery_date  = date('Y-m-d', strtotime("+{$delivery_days} weekdays"));
                            $date           = date_i18n( 'l', strtotime( "$delivery_date" ) );
                            $day            = date_i18n( 'j', strtotime( "$delivery_date" ) );
                            $month          = date_i18n( 'M', strtotime( "$delivery_date" ) );
                    ?>
                    <th>
                        <div class="nbo-delivery-date-wrap">
                            <div class="nbo-delivery-date-inner">
                                <div class="nbo-delivery-date-title"><?php echo $attr['name']; ?></div>
                                <div class="nbo-delivery-date"><?php echo $date; ?></div>
                                <div class="nbo-delivery-date2"><span class="day"><?php echo $day; ?></span> <?php echo $month; ?></div>
                            </div>
                        </div>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="(bIndex, _break) in turnaround_quantity_breaks">
                    <td>{{_break.val}}</td>
                    <td ng-repeat="(tIndex, t) in turnaround_matrix[bIndex]" ng-class="{'active': t.active, 'nbdd_disable': !t.show}" class="nbo-delivery-date-selector" ng-click="change_delivery_date( bIndex, tIndex, $event );">
                        <span ng-if="t.show" class="nbo-delivery-total" ng-bind-html="t.total_cart_price | to_trusted"></span>
                        <span ng-if="t.show" class="nbo-delivery-price-item"><span ng-bind-html="t.final_price | to_trusted" ></span> <?php _e('per item', 'web-to-print-online-designer'); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="nbo-delivery-custom-quantity" ng-show="valid_form && nbd_fields['<?php echo $delivery_field['id']; ?>'].enable">
    <span class="nbd-button" ng-hide="custom_quantity" ng-click="custom_quantity = !custom_quantity;" ><?php _e('Add custom quantity', 'web-to-print-online-designer'); ?></span>
    <input type="number" min="1" step="1" ng-show="custom_quantity" ng-model="quantity" ng-keyup="$event.keyCode == 13 && update_turnaround_matrix();" />
    <span class="nbd-button update-custom-quantity" ng-show="custom_quantity" ng-click="update_turnaround_matrix(); custom_quantity = !custom_quantity;">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <title><?php _e('update', 'web-to-print-online-designer'); ?></title>
            <path d="M9 16.172l10.594-10.594 1.406 1.406-12 12-5.578-5.578 1.406-1.406z"></path>
        </svg>
    </span>
</div>