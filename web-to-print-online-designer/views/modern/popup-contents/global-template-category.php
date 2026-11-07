<div class="nbd-popup popup-template" data-animate="bottom-to-top">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="overlay-main active">
            <div class="loaded">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
        <div class="head">
            <h2><?php esc_html_e('Save this template','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
            <div class="main-body">
                <div>
                    <label for="category_template"><?php esc_html_e('Category','web-to-print-online-designer'); ?></label>
                    <select class="process-select" ng-model="templateCat" id="category_template">
                        <option ng-repeat="cat in templateCats" ng-value="{{cat.id}}"><span>{{cat.name}}</span></option>
                    </select>
                </div>
                <div class="template-name-wrap">
                    <label for="template-name"><?php esc_html_e('Name','web-to-print-online-designer'); ?></label>
                    <input ng-model="templateName" id="template-name"/>
                </div>
                <div class="action-wrap">
                    <button ng-class="templateName != '' ? '' : 'nbd-disabled' " class="nbd-button" ng-click="saveData('template')">
                        <?php esc_html_e('Save','web-to-print-online-designer'); ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>
