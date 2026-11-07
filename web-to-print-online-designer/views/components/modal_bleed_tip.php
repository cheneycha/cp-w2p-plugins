<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="dg-bleed-tip" class="modal fade nbdesigner_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 15px;">
                <b>{{(langs['TIP']) ? langs['TIP'] : "Tip"}}</b>
                <button style="margin-top: 0;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>	
            </div>            
            <div class="modal-body" style="padding: 15px;">
                <div>
                    <p><span class='nbd-bleed-notation'></span> <b>{{(langs['TRIM_LINE']) ? langs['TRIM_LINE'] : "Trim line"}}</b></p>  
                    <p>{{(langs['TRIM_LINE_TIP']) ? langs['TRIM_LINE_TIP'] : "Everything outside this trim / bleed line will be trimmed off. However, you should still extend your design to the edge of bleed zone to prevent the appearance of irregular white borders."}}</p>    
                </div>
                <div>
                    <p><span class='nbd-safe-zone-notation-box'></span> <b>{{(langs['SAFE_ZONE']) ? langs['SAFE_ZONE'] : "Trim Safe zone"}}</b></p>  
                    <p>{{(langs['SAFE_ZONE_TIP']) ? langs['SAFE_ZONE_TIP'] : "You will want to keep all your important content within this green box."}}</p>    
                </div>                
                <div>
                    <div class="md-checkbox">
                        <input id="nbd-bleed-notify" type="checkbox" ng-click="ignoreMessage()">
                        <label for="nbd-bleed-notify" class="">{{(langs['DONT_SHOW_MES']) ? langs['DONT_SHOW_MES'] : "Don't show this message again"}}</label>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>