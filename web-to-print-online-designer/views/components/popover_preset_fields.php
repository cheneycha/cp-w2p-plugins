<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="nbd-popover-preset" class="shadow">
    <div >
        <p><label>Field</label><input placeholder="Name" /><span></span></p>
        <textarea class="form-control" ng-model="editable.text" ng-change="ChangeText()"></textarea>
        <div class="nb-col-6">
            <label>Top</label><br /><input />
        </div>
        <div class="nb-col-6">
            <label>Left</label><br /><input />
        </div>        
        <p><label>Font</label><input /></p>
    </div>
</div>
<style type="text/css">
    #nbd-popover-preset {
        width: 320px;
        background: #fff;
        position: fixed;
        z-index: 999;
        top: 50px;
        right: 65px;
        padding: 15px;
        font-size: 12px;
    }
</style>    

