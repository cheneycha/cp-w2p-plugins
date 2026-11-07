
<div class="product-options">
    <h3 class="color-palette-label" ><?php esc_html_e('Document colors','web-to-print-online-designer'); ?></h3>
    <ul class="main-color-palette nbd-perfect-scroll" >
        <li class="color-palette-add" ng-click="showBgColorPalette()" ng-style="{'background-color': currentColor}"></li>
        <li ng-repeat="color in listAddedColor track by $index" ng-click="changeBackground(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background-color': color}"></li>
    </ul>
    <div class="pinned-palette default-palette" >
        <h3 class="color-palette-label" ><?php esc_html_e('Default palette','web-to-print-online-designer'); ?></h3>
        <ul class="main-color-palette" ng-repeat="palette in resource.defaultPalette">
            <li ng-class="{'first-left': $first, 'last-right': $last}" ng-repeat="color in palette track by $index" ng-click="changeBackground(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
        </ul>   
    </div>
    <div class="nbd-text-color-picker" id="nbd-bg-color-picker" ng-class="showBgColorPicker ? 'active' : ''" >
        <spectrum-colorpicker
            ng-model="currentColor"
            options="{
                    preferredFormat: 'hex',
                    color: '#fff',
                    flat: true,
                    showButtons: false,
                    showInput: true,
                    containerClassName: 'nbd-sp'
            }">
        </spectrum-colorpicker>
        <div style="text-align: <?php echo (is_rtl()) ? 'right' : 'left'?>">
            <button class="nbd-button" ng-click="addColor();changeBackground(currentColor);"><?php esc_html_e('Choose','web-to-print-online-designer'); ?></button>
        </div>
    </div>
</div>

