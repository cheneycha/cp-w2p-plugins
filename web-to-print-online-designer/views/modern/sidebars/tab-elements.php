<div class="<?php if( $active_elements ) echo 'active'; ?> tab" id="tab-element" nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-element" data-type="element" data-offset="20">
    <div class="nbd-search">
        <input ng-class="(!(resource.element.type == 'icon' || resource.element.type == 'flaticon' || resource.element.type == 'storyset') || !resource.element.onclick) ? 'nbd-disabled' : ''" ng-keyup="$event.keyCode == 13 && getMedia(resource.element.type, 'search')" type="text" name="search" placeholder="<?php esc_html_e('Search element', 'web-to-print-online-designer'); ?>" ng-model="resource.element.contentSearch"/>
        <i class="icon-nbd icon-nbd-fomat-search"></i>
    </div>     
    <div class="tab-main tab-scroll" >
        <div class="nbd-items-dropdown">
            <div class="main-items">
                <div class="items">
                    <div ng-if="!!settings.nbes_enable_settings && ( settings.nbes_enable_settings.background == 1 || settings.nbes_enable_settings.combination == 1 )" class="item item-color" data-type="color" data-api="false" ng-click="onClickTab('color', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-fill-color" ></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Color','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_draw'] == 'yes' && !settings.is_mobile" class="item" data-type="draw" data-api="false" ng-click="onClickTab('draw', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-drawing"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Draw','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_shapes'] == 'yes'" class="item" data-type="shapes" data-api="false" ng-click="onClickTab('shape', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-shapes"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Shapes','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_icons'] == 'yes'" class="item" data-type="icons" data-api="false" ng-click="onClickTab('icon', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-diamond"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Icons','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
<!--                     <div class="item" data-type="lines" data-api="false" ng-click="onClickTab('line', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-line"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Lines','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div> -->
                    <div ng-if="settings['nbdesigner_enable_flaticon'] == 'yes' && settings['nbdesigner_flaticon_api_key'] != ''" class="item" data-type="flaticon" data-api="false" ng-click="onClickTab('flaticon', 'element')">
                        <div class="main-item">
                            <div class="item-icon" style="padding: 20px 5px">
                                <i class="icon-nbd" style="font-size: 30px;background: #0E2A47;padding: 0 5px;border-radius: 4px;">
                                    <svg style="vertical-align: middle;" width="60px" viewBox="0 0 561 103" xmlns="http://www.w3.org/2000/svg"><g fill="none"><path d="M141.596 29.675c0-3.777 2.985-6.767 6.764-6.767h34.438a6.115 6.115 0 016.15 6.15 6.11 6.11 0 01-6.15 6.149h-27.674v13.091h23.719a6.112 6.112 0 016.151 6.15 6.109 6.109 0 01-6.151 6.149h-23.719v17.574c0 3.773-2.986 6.761-6.764 6.761-3.779 0-6.764-2.989-6.764-6.761V29.675zm52.248-.526c0-3.781 2.985-6.767 6.764-6.767 3.776 0 6.763 2.985 6.763 6.767v42.957h25.039a6.114 6.114 0 016.149 6.153 6.112 6.112 0 01-6.149 6.15h-31.802c-3.779 0-6.764-2.986-6.764-6.768V29.149zm48.047 46.561l21.438-48.407c1.492-3.341 4.215-5.357 7.906-5.357h.792c3.686 0 6.323 2.017 7.815 5.357l21.439 48.407c.436.967.701 1.845.701 2.723 0 3.602-2.809 6.501-6.414 6.501-3.161 0-5.269-1.845-6.499-4.655l-4.132-9.661h-27.059l-4.301 10.102c-1.144 2.631-3.426 4.214-6.237 4.214-3.517 0-6.24-2.81-6.24-6.325 0-.969.351-1.932.791-2.899zm38.041-17.044l-8.521-20.297-8.526 20.297h17.047zm34.932-23.279H301.86c-3.429 0-6.239-2.813-6.239-6.238 0-3.429 2.811-6.24 6.239-6.24h39.533c3.426 0 6.237 2.811 6.237 6.24 0 3.425-2.811 6.238-6.237 6.238h-13.001v42.785c0 3.773-2.99 6.761-6.764 6.761-3.779 0-6.764-2.989-6.764-6.761V35.387z" fill="#FFF"/><path d="M352.615 29.149c0-3.781 2.985-6.767 6.767-6.767 3.774 0 6.761 2.985 6.761 6.767v49.024a6.711 6.711 0 01-6.761 6.761c-3.781 0-6.767-2.989-6.767-6.761V29.149zm21.517 24.687v-.179c0-17.481 13.178-31.801 32.065-31.801 9.22 0 15.459 2.458 20.557 6.238 1.402 1.054 2.637 2.985 2.637 5.357 0 3.692-2.985 6.59-6.681 6.59-1.845 0-3.071-.702-4.044-1.319-3.776-2.813-7.729-4.393-12.562-4.393-10.364 0-17.831 8.611-17.831 19.154v.173c0 10.542 7.291 19.329 17.831 19.329 5.715 0 9.492-1.756 13.359-4.834 1.049-.874 2.458-1.491 4.039-1.491 3.429 0 6.325 2.813 6.325 6.236 0 2.106-1.056 3.78-2.282 4.834-5.539 4.834-12.036 7.733-21.878 7.733-18.095.001-31.535-13.97-31.535-31.627zm58.877 0v-.179c0-17.481 13.79-31.801 32.766-31.801 18.981 0 32.592 14.143 32.592 31.628v.173c0 17.483-13.785 31.807-32.769 31.807-18.973 0-32.589-14.144-32.589-31.628zm51.215 0v-.179c0-10.539-7.725-19.326-18.626-19.326-10.893 0-18.449 8.611-18.449 19.154v.173c0 10.542 7.73 19.329 18.626 19.329 10.901-.001 18.449-8.609 18.449-19.151zm22.009-24.515c0-3.774 2.99-6.763 6.767-6.763h1.401c3.252 0 5.183 1.583 7.029 3.953l26.093 34.265V29.059a6.675 6.675 0 016.681-6.677 6.672 6.672 0 016.671 6.677v48.934c0 3.78-2.987 6.765-6.764 6.765h-.436c-3.257 0-5.188-1.581-7.034-3.953l-27.056-35.492v32.944a6.676 6.676 0 01-13.351 0V29.321h-.001z" fill="#69E781"/><path d="M48.372 56.137h12.517L72.045 37.6H37.186L25.688 18.539h57.825L94.668 0H9.271a9.265 9.265 0 00-8.073 4.716 9.265 9.265 0 00.134 9.343l50.38 83.501a9.266 9.266 0 007.938 4.476 9.264 9.264 0 007.935-4.476l2.898-4.804-22.111-36.619z" fill="#FFF"/><path d="M93.575 18.539h.031v.004l21.652.004 2.705-4.488a9.262 9.262 0 00.133-9.343A9.26 9.26 0 00110.026 0h-5.294L93.575 18.539zm-5.284 8.817l-23.566 39.13 10.794 17.918 34.423-57.048z" fill="#69E781"/></g></svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Flaticon','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_storyset'] == 'yes'" class="item" data-type="storyset" data-api="false" ng-click="onClickTab('storyset', 'element')">
                        <div class="main-item">
                            <div class="item-icon" style="padding: 20px 5px">
                                <i class="icon-nbd" style="font-size: 30px;background: #0c4584;padding: 0 5px;border-radius: 4px;">
                                    <svg viewBox="0 0 299 65" fill="none" style="vertical-align: middle;" width="60px" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M42.3804 25.81C39.1335 24.2193 35.5927 23.3171 31.9804 23.16C28.6504 23.16 26.9804 24.33 26.9804 26.16C26.9804 27.99 29.3304 28.51 32.2904 28.95L35.1904 29.39C42.2504 30.46 46.1904 33.62 46.1904 39.14C46.1904 45.87 40.6704 50.21 31.1904 50.21C26.7404 50.21 20.9204 49.37 16.6904 46.37L20.1504 39.71C23.4309 41.898 27.3087 43.016 31.2504 42.91C35.3204 42.91 37.2504 41.77 37.2504 39.85C37.2504 38.27 35.6104 37.39 31.8404 36.85L29.1804 36.48C21.6404 35.48 17.9104 32.14 17.9104 26.59C17.9104 19.9 23.0904 15.93 31.6704 15.93C36.3969 15.8411 41.0743 16.9015 45.3004 19.02L42.3804 25.81Z" fill="#F8FAFB"/>
                                        <path d="M76.4204 26.35H64.6104V36.44C64.6104 40.51 66.7604 42 69.4904 42C71.7584 41.798 73.9406 41.0351 75.8404 39.78L78.8404 46.78C75.6968 49.0428 71.9136 50.2444 68.0404 50.21C59.9604 50.21 55.8304 45.63 55.8304 37.21V26.35H48.4404L51.7404 18.54H55.8304V7.07999H64.6104V18.54H76.4204V26.35Z" fill="#F8FAFB"/>
                                        <path d="M117.02 33.01C117.02 42.84 109.51 50.17 99.3503 50.17C89.1903 50.17 81.7803 42.84 81.7803 33.01C81.7803 23.18 89.2503 15.85 99.3703 15.85C109.49 15.85 117.02 23.19 117.02 33.01ZM90.7103 33.01C90.7103 38.29 94.4103 42.01 99.3503 42.01C104.29 42.01 108.03 38.3 108.03 33.01C108.03 27.72 104.3 24.01 99.3503 24.01C94.4003 24.01 90.7103 27.75 90.7103 33.01Z" fill="#F8FAFB"/>
                                        <path d="M161.52 62.92H151.9L163.3 42.4L149.24 16.75H159.24L168.24 33.5L176.83 16.75H186.45L161.52 62.92Z" fill="#F8FAFB"/>
                                        <path d="M146.72 17.42L143.14 25.61C141.731 24.9428 140.19 24.601 138.63 24.61C134.43 24.61 131.57 27.17 131.57 32.09V50.21H122.57V16.9H131.28V20.59C133.33 17.42 136.6 16.08 140.7 16.08C142.772 16.152 144.813 16.6064 146.72 17.42V17.42Z" fill="#F8FAFB"/>
                                        <path d="M7.44024 21.6H0.240234C0.248171 15.9003 2.51589 10.4363 6.5462 6.40598C10.5765 2.37566 16.0405 0.107943 21.7402 0.100006V7.29999C17.9525 7.31578 14.3244 8.82747 11.6461 11.5058C8.9677 14.1842 7.45603 17.8123 7.44024 21.6V21.6Z" fill="#1273EB"/>
                                        <path d="M291.321 43.3H298.521C298.513 48.9997 296.245 54.4637 292.215 58.494C288.184 62.5243 282.72 64.7921 277.021 64.8V57.6C278.903 57.6174 280.771 57.2593 282.513 56.5468C284.256 55.8343 285.839 54.7817 287.171 53.4503C288.502 52.119 289.555 50.5356 290.267 48.7928C290.98 47.0501 291.338 45.1827 291.321 43.3V43.3Z" fill="#1273EB"/>
                                        <path d="M212.3 24C209.166 22.0507 205.551 21.012 201.86 21C197.31 21 194.31 22.92 194.31 26C194.31 28.66 196.63 30 200.81 30.48L204.58 30.95C211.36 31.83 215.3 34.62 215.3 39.85C215.3 45.91 209.84 49.72 201.3 49.72C196.543 49.8103 191.88 48.381 187.99 45.64L190.32 41.87C192.74 43.75 195.98 45.34 201.37 45.34C206.76 45.34 210.16 43.55 210.16 40.18C210.16 37.62 208 36.07 203.49 35.53L199.68 35.1C192.5 34.22 189.14 31.02 189.14 26.27C189.14 20.27 194.22 16.6 201.87 16.6C206.339 16.5192 210.736 17.7236 214.54 20.07L212.3 24Z" fill="#F8FAFB"/>
                                        <path d="M250.52 33.06C250.523 33.6914 250.49 34.3225 250.42 34.95H224.85C225.56 41.82 230.38 45.36 236.14 45.36C239.896 45.3576 243.51 43.9277 246.25 41.36L248.94 44.8C247.204 46.4725 245.148 47.7773 242.895 48.6365C240.642 49.4956 238.239 49.8913 235.83 49.8C226.4 49.8 219.7 43.1 219.7 33.23C219.7 23.36 226.44 16.62 235.53 16.62C244.19 16.59 250.46 23.36 250.52 33.06ZM224.92 30.87H245.4C244.76 24.87 240.86 21.07 235.4 21.07C229.53 21.07 225.78 25.11 224.92 30.87Z" fill="#F8FAFB"/>
                                        <path d="M278.21 22.75H264.43V37.86C264.43 42.86 267.06 45.03 270.77 45.03C273.183 44.9925 275.524 44.2063 277.47 42.78L279.83 46.58C277.113 48.6586 273.781 49.7738 270.36 49.75C263.49 49.75 259.41 45.98 259.41 37.96V22.75H252.41V18.21H259.41V8.07999H264.41V18.23H278.19L278.21 22.75Z" fill="#F8FAFB"/>
                                    </svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Stories','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_qrcode'] == 'yes'" class="item" data-type="qr-code" data-api="false" ng-click="onClickTab('qrcode', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-qrcode"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Bar/QR-Code','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_vcard'] == 'yes' && settings.vcard_fields.length > 0" class="item" data-type="vcard" data-api="false" ng-click="onClickTab('vcard', 'element')">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-vcard">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                                        <path fill="#06d79c" d="M28 25h-3v-1c0.553 0 1-0.448 1-1 0-0.553-0.447-1-1-1h-2c-0.553 0-1 0.447-1 1 0 0.552 0.447 1 1 1v1h-13v-1c0.553 0 1-0.448 1-1 0-0.553-0.447-1-1-1h-2c-0.553 0-1 0.447-1 1 0 0.552 0.447 1 1 1v1h-4c-1.104 0-2-0.896-2-2v-14c0-1.104 0.896-2 2-2h24c1.104 0 2 0.896 2 2v14c0 1.104-0.896 2-2 2zM10 9.812c-1.208 0-2.188 1.287-2.188 2.875s0.98 2.875 2.188 2.875 2.188-1.287 2.188-2.875-0.98-2.875-2.188-2.875zM13.49 16.688c-0.539-0.359-2.091-0.598-2.091-0.598s-1.006 1.075-1.433 1.075c-0.428 0-1.434-1.075-1.434-1.075s-1.552 0.238-2.090 0.598c-0.539 0.358-0.777 2.261-0.777 2.261h8.615c0.001-0.001-0.157-1.84-0.79-2.261zM26 11h-9v1h9v-1zM26 13h-9v1h9v-1zM26 15h-9v1h9v-1zM26 17h-9v1h9v-1z"></path>
                                    </svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('vCard','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_frame'] == 'yes'" class="item" data-type="frame" data-api="false" ng-click="onClickTab('frame', 'element')">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-vcard">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#38b6ff" d="M7 19h10V4H7v15zm-5-2h4V6H2v11zM18 6v11h4V6h-4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Grid Frame','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_photo_frame'] == 'yes'" class="item" data-type="photoFrame" data-api="false" ng-click="onClickTab('photoFrame', 'element')">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-vcard">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path fill="#05A081" d="M20 4h-4l-4-4-4 4H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H4V6h4.52l3.52-3.5L15.52 6H20v14zM18 8H6v10h12"/></svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Photo Frame','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php if( $task == 'create' || ( $task == 'edit' && $design_type == 'template' ) ): ?>
                    <div class="item" data-type="image-shape" data-api="false" ng-click="onClickTab('image-shape', 'element')">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-vcard">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px"><path fill="#888" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Image Placeholder','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div ng-if="settings['nbdesigner_enable_google_maps'] == 'yes' && settings['nbdesigner_static_map_api_key'] != ''" class="item" data-type="maps" data-api="false" ng-click="onClickTab('maps', 'element')">
                        <div class="main-item">
                            <div class="item-icon" style="padding: 20px 5px">
                                <i class="icon-nbd maps-icon" >
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40" height="40" viewBox="0 0 480 480" xml:space="preserve"><g><g><path fill="#ea4335" d="M240,40c-35.346,0-64,28.654-64,64c0,35.346,28.654,64,64,64c35.33-0.04,63.96-28.67,64-64C304,68.654,275.346,40,240,40z M240,152c-26.499-0.026-47.974-21.501-48-48c0-26.51,21.49-48,48-48c26.51,0,48,21.49,48,48S266.51,152,240,152z"/></g></g><g><g><path fill="#4285f4" d="M474.528,224.416l-96-32c-1.407-0.468-2.915-0.54-4.36-0.208l-65.368,15.36C327.92,167.312,344,126.664,344,104 C344,46.562,297.438,0,240,0S136,46.562,136,104c0,22.456,15.776,62.544,34.608,102.4l-56.664-14.16 c-1.414-0.368-2.903-0.329-4.296,0.112l-104,32C2.29,225.385-0.001,228.487,0,232v240c0,4.418,3.582,8,8,8 c0.797-0.002,1.589-0.121,2.352-0.352l101.864-31.344l125.848,31.456c1.238,0.304,2.53,0.304,3.768,0L375.2,448.304l94.328,31.288 c4.202,1.365,8.715-0.934,10.081-5.137c0.258-0.793,0.39-1.622,0.391-2.455V232C479.997,228.558,477.794,225.504,474.528,224.416z M152,104c0.057-48.577,39.423-87.943,88-88c48.577,0.057,87.943,39.423,88,88c0,22.768-19.288,68.08-40.48,110.984 C269.464,251.568,250.04,286.4,240,304C218.168,265.712,152,146.184,152,104z M464,460.912l-85.96-28.504 c-1.402-0.477-2.912-0.547-4.352-0.2l-133.6,31.56L113.936,432.24c-1.412-0.363-2.897-0.325-4.288,0.112L16,461.168V237.904 l96.216-29.6l67.472,16.88c24.8,50.12,51.344,95.352,53.424,98.88c2.247,3.804,7.153,5.066,10.957,2.819 c1.163-0.687,2.132-1.657,2.819-2.819c2.072-3.504,28.28-48.2,52.952-97.944l75.784-17.816L464,237.768V460.912z"/></g></g></svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Maps','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pointer"></div>
            </div>
            <div class="result-loaded">
                <div class="content-items">
                    <div class="content-item type-color" data-type="color">
                        <div class="main-type">
                            <div ng-if="!!settings.nbes_enable_settings && settings.nbes_enable_settings.combination == 1">
                                <span class="heading-title"><?php esc_html_e('Combination colors','web-to-print-online-designer'); ?></span>
                                <div class="nbes-colors">
                                    <div class="nbes-color" ng-repeat="cbg_code in settings.nbes_settings.combination_colors.bg_codes track by $index">
                                        <div ng-style="{'background-color': cbg_code}" class="bg_color" 
                                             ng-click="selectCombinationColor( $index )"
                                             title="{{settings.nbes_settings.combination_colors.bg_names[$index] + ' - ' + settings.nbes_settings.combination_colors.fg_names[$index]}}">
                                            <span ng-style="{'color': settings.nbes_settings.combination_colors.fg_codes[$index]}">Aa</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div ng-if="!!settings.nbes_enable_settings && settings.nbes_enable_settings.background == 1 && settings.nbes_enable_settings.combination != 1">
                                <span class="heading-title"><?php esc_html_e('Background colors','web-to-print-online-designer'); ?></span>
                                <div class="nbes-colors">
                                    <div class="nbes-color bg-color" ng-repeat="bg_code in settings.nbes_settings.background_colors.codes track by $index">
                                        <div ng-style="{'background-color': bg_code}" class="bg_color" 
                                             ng-click="_changeBackgroundCanvas($index)"
                                             title="{{settings.nbes_settings.background_colors.names[$index]}}">
                                            <span ng-style="{'color': bg_code}"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-item type-draw" data-type="draw">
                        <div class="main-type">
                            <div class="free-draw-settings" ng-show="resource.drawMode.status">
                                <span class="heading-title"><?php esc_html_e('Free Drawing','web-to-print-online-designer'); ?></span>
                                <div class="brush" >
                                    <h3 class="color-palette-label" ><?php esc_html_e('Choose ','web-to-print-online-designer'); ?></h3>
                                    <button class="nbd-button nbd-dropdown">
                                        <?php esc_html_e('Brush','web-to-print-online-designer'); ?> <i class="icon-nbd icon-nbd-arrow-drop-down"></i>
                                        <div class="nbd-sub-dropdown" data-pos="left">
                                            <ul class="tab-scroll">
                                                <li ng-click="resource.drawMode.brushType = 'Pencil';changeBush()" ng-class="resource.drawMode.brushType == 'Pencil' ? 'active' : ''"><span><?php esc_html_e('Pencil','web-to-print-online-designer'); ?></span></li>
                                                <li ng-click="resource.drawMode.brushType = 'Circle';changeBush()" ng-class="resource.drawMode.brushType == 'Circle' ? 'active' : ''"><span><?php esc_html_e('Circle','web-to-print-online-designer'); ?></span></li>
                                                <li ng-click="resource.drawMode.brushType = 'Spray';changeBush()" ng-class="resource.drawMode.brushType == 'Spray' ? 'active' : ''"><span><?php esc_html_e('Spray','web-to-print-online-designer'); ?></span></li>
                                            </ul>
                                        </div>
                                    </button>
                                </div>
                                <ul class="main-ranges" >
                                    <li class="range range-brightness">
                                        <label><?php esc_html_e('Brush width ','web-to-print-online-designer'); ?></label>
                                        <div class="main-track">
                                            <input class="slide-input" type="range" step="1" min="1" max="100" ng-change="changeBush()" ng-model="resource.drawMode.brushWidth">
                                            <span class="range-track"></span>
                                        </div>
                                        <span class="value-display">{{resource.drawMode.brushWidth}}</span>
                                    </li>
                                </ul>
                                <div class="color">
                                    <h3 class="color-palette-label" ><?php esc_html_e('Brush color','web-to-print-online-designer'); ?></h3>
                                    <ul class="main-color-palette nbd-perfect-scroll" >
                                        <li class="color-palette-add" ng-init="showBrushColorPicker = false" ng-click="showBrushColorPicker = !showBrushColorPicker;" ng-style="{'background-color': currentColor}"></li>
                                        <li ng-repeat="color in listAddedColor track by $index" ng-click="resource.drawMode.brushColor=color; changeBush()" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background-color': color}"></li>
                                    </ul>
                                    <div class="pinned-palette default-palette" >
                                        <h3 class="color-palette-label" ><?php esc_html_e('Default palette','web-to-print-online-designer'); ?></h3>
                                        <ul class="main-color-palette" ng-repeat="palette in resource.defaultPalette" >
                                            <li ng-class="{'first-left': $first, 'last-right': $last}" ng-repeat="color in palette track by $index" ng-click="resource.drawMode.brushColor=color; changeBush()" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
                                        </ul>
                                    </div>
                                    <div class="nbd-text-color-picker" id="nbd-bg-color-picker" ng-class="showBrushColorPicker ? 'active' : ''" >
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
                                            <button class="nbd-button" ng-click="addColor();changeBush(currentColor);showBrushColorPicker = false;"><?php esc_html_e('Choose','web-to-print-online-designer'); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="nbd-color-palette-inner" >
                                    <div class="working-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'" >
                                        <ul class="main-color-palette tab-scroll">
                                            <li class="color-palette-item color-palette-add" ng-click="stageBgColorPicker.status = !stageBgColorPicker.status;" ></li>
                                            <li ng-repeat="color in listAddedColor track by $index"
                                                ng-click="changeBackgroundCanvas(color)"
                                                class="color-palette-item"
                                                data-color="{{color}}"
                                                title="{{color}}"
                                                ng-style="{'background-color': color}">
                                            </li>
                                        </ul>
                                        <div class="nbd-text-color-picker" id="nbd-stage-bg-color-picker" ng-class="stageBgColorPicker.status ? 'active' : ''">
                                            <spectrum-colorpicker
                                                ng-model="stageBgColorPicker.currentColor"
                                                options="{
                                                preferredFormat: 'hex',
                                                color: '#fff',
                                                flat: true,
                                                showButtons: false,
                                                showInput: true,
                                                containerClassName: 'nbd-sp'
                                                }">
                                            </spectrum-colorpicker>
                                            <div>
                                                <button class="nbd-button"
                                                    ng-click="changeBackgroundCanvas(stageBgColorPicker.currentColor);">
                                                        <?php esc_html_e('Choose', 'web-to-print-online-designer'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'">
                                        <h3 class="color-palette-label default" ><?php esc_html_e('Default palette', 'web-to-print-online-designer'); ?></h3>
                                        <ul class="main-color-palette tab-scroll" ng-repeat="palette in resource.defaultPalette">
                                            <li ng-class="{'first-left': $first, 'last-right': $last}"
                                                ng-repeat="color in palette track by $index"
                                                ng-click="changeBackgroundCanvas(color)"
                                                class="color-palette-item"
                                                data-color="{{color}}"
                                                title="{{color}}"
                                                ng-style="{'background': color}">
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'no'">
                                        <h3 class="color-palette-label"><?php esc_html_e('Color palette', 'web-to-print-online-designer'); ?></h3>
                                        <ul class="main-color-palette settings" >
                                            <li ng-repeat="color in __colorPalette track by $index" ng-class="{'first-left': $first, 'last-right': $last}" ng-click="changeBackgroundCanvas(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
                                        </ul>
                                    </div>
                                    <div><button class="nbd-button" ng-click="removeBackgroundCanvas()"><?php esc_html_e('Remove background', 'web-to-print-online-designer'); ?></button></div>
                                </div>
                            </div>
                            <div class="free-draw-options">
                                <div class="draw-item" ng-class="resource.drawMode.status ? 'active' : ''" ng-click="enableDrawMode()" title="<?php esc_html_e('Free Draw','web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd icon-nbd-drawing"></i>
                                </div>
                                <div class="draw-item" ng-click="addGeometricalObject( 'circle' )" title="<?php esc_html_e('Circle','web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd icon-nbd-layer-circle"></i>
                                </div>
                                <div class="draw-item" ng-click="addGeometricalObject( 'triangle' )" title="<?php esc_html_e('Triangle','web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd icon-nbd-layer-triangle"></i>
                                </div>
                                <div class="draw-item" ng-click="addGeometricalObject( 'rect' )" title="<?php esc_html_e('Rectangle','web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd icon-nbd-layer-rect"></i>
                                </div>
                                <div class="draw-item" ng-click="addGeometricalObject( 'hexagon' )" title="<?php esc_html_e('Hexagon','web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd icon-nbd-layer-polygon"></i>
                                </div>
                                <div class="draw-item" ng-click="addGeometricalObject( 'line' )" title="<?php esc_html_e('Line','web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd icon-nbd-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-item type-shapes" data-type="shapes" id="nbd-shape-wrap">
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art)" ng-repeat="art in resource.shape.data" repeat-end="onEndRepeat('shape')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                        </div>
                    </div>
                    <div class="content-item type-icons" data-type="icons" id="nbd-icon-wrap">
                        <div class="cliparts-category" ng-class="resource.icon.cat.length > 0 ? '' : 'nbd-hiden'">
                            <div class="nbd-button nbd-dropdown nbd-cat-dropdown">
                                <span>{{resource.icon.filter.currentCat.name}}</span>
                                <i class="icon-nbd icon-nbd-chevron-right rotate90"></i>
                                <div class="nbd-sub-dropdown" data-pos="center">
                                    <ul class="nbd-perfect-scroll">
                                        <li ng-click="changeCat('icon', cat)" ng-repeat="cat in resource.icon.cat"><span>{{cat.name}}</span><span>{{cat.total}}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art, $index)" ng-repeat="art in resource.icon.data" repeat-end="onEndRepeat('icon')">
                                <div class="mansory-item__inner">
                                    <img ng-src="{{art.url}}" /><span class="photo-desc">{{art.name}}</span>
                                    <?php if(!$valid_license): ?>
                                    <span class="nbd-pro-mark-wrap" ng-if="$index > 20">
                                        <svg class="nbd-pro-mark" fill="#F3B600" xmlns="http://www.w3.org/2000/svg" viewBox="-505 380 12 10"><path d="M-503 388h8v1h-8zM-494 382.2c-.4 0-.8.3-.8.8 0 .1 0 .2.1.3l-2.3.7-1.5-2.2c.3-.2.5-.5.5-.8 0-.6-.4-1-1-1s-1 .4-1 1c0 .3.2.6.5.8l-1.5 2.2-2.3-.8c0-.1.1-.2.1-.3 0-.4-.3-.8-.8-.8s-.8.4-.8.8.3.8.8.8h.2l.8 3.3h8l.8-3.3h.2c.4 0 .8-.3.8-.8 0-.4-.4-.7-.8-.7z"></path></svg>
                                        <?php esc_html_e('Pro','web-to-print-online-designer'); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-item type-flaticon" data-type="flaticon" id="nbd-flaticon-wrap">
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addArt(art, true, true)" ng-repeat="art in resource.flaticon.data" repeat-end="onEndRepeat('flaticon')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                        </div>
                    </div>
                    <div class="content-item type-storyset" data-type="storyset" id="nbd-storyset-wrap">
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addArt(art, true, true)" ng-repeat="art in resource.storyset.data" repeat-end="onEndRepeat('storyset')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                        </div>
                    </div>
                    <div class="content-item type-lines" data-type="lines" id="nbd-line-wrap">
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art)" ng-repeat="art in resource.line.data" repeat-end="onEndRepeat('line')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                        </div>
                    </div>
                    <div class="content-item type-qrcode" data-type="qr-code">
                        <div class="main-type">
                            <div class="main-input">
                                <input ng-model="resource.qrText" type="text" class="nbd-input input-qrcode" name="qr-code" placeholder="https://yourcompany.com">
                            </div>
                            <button ng-class="resource.qrText != '' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="addQrCode()"><?php esc_html_e('Create QRCode','web-to-print-online-designer'); ?></button>
                            <button ng-class="resource.qrText != '' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="addBarCode()"><?php esc_html_e('Create BarCode','web-to-print-online-designer'); ?></button>
                            <div class="main-qrcode">
                                
                            </div>
                            <svg id="barcode" ></svg>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_vcard'] == 'yes'" class="content-item type-vcard" data-type="vcard">
                        <p><?php esc_html_e('Your information','web-to-print-online-designer'); ?></p>
                        <div ng-repeat="field in settings.vcard_fields" class="md-input-wrap">
                            <input id="vf-{{field.key}}" ng-model="field.value" ng-class="field.value.length > 0 ? 'holder' : ''"/>
                            <label for="vf-{{field.key}}" >{{field.name}}<label/>
                        </div>
                        <p>
                            <span class="nbd-button" ng-click="generateVcard()"><?php esc_html_e('Generate','web-to-print-online-designer'); ?></span>
                        </p>
                    </div>
                    <div class="content-item type-frame" data-type="frame">
                        <div class="frames-wrapper">
                            <div class="frame-wrap" ng-repeat="frame in resource.frames track by $index" ng-click="addFrame(frame)">
                                <photo-frame data-frame="frame"></photo-frame>
                            </div>
                        </div>
                    </div>
                    <div class="content-item type-photoFrame" data-type="photoFrame" id="nbd-photoFrame-wrap">
                        <div class="mansory-wrap">
                            <div class="mansory-item" ng-click="addPhotoFrame(frame)" ng-repeat="frame in [] | range: ( resource.photoFrame.filter.currentPage * resource.photoFrame.filter.perPage > resource.photoFrame.filter.total ? resource.photoFrame.filter.total : ( resource.photoFrame.filter.currentPage * resource.photoFrame.filter.perPage ) )" repeat-end="onEndRepeat('photoFrame')">
                                <img ng-src="{{'//dpeuzbvf3y4lr.cloudfront.net/frames/preview/f' + ( frame + 1 ) + '.png'}}" alt="Photo Frame" />
                            </div>
                        </div>
                    </div>
                    <?php if( $task == 'create' || ( $task == 'edit' && $design_type == 'template' ) ): ?>
                    <div class="content-item type-image-shape" data-type="image-shape">
                        <div class="image-shape-wrapper">
                            <div>
                                <span class="shape_mask shape-type-{{n}}" ng-click="addMask(n)" ng-repeat="n in [] | range:25"></span>
                            </div>
                            <div class="custom_image_shape-wrapper">
                                <div><?php esc_html_e('Custom Shape','web-to-print-online-designer'); ?></div>
                                <textarea class="form-control hover-shadow nbdesigner_svg_code" rows="5" ng-change="getPathCommand()" ng-model="svgPath" placeholder="<?php esc_html_e('Enter svg code','web-to-print-online-designer'); ?>"/></textarea>
                                <button ng-class="pathCommand !='' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="addMask(-1)"><?php esc_html_e('Aadd Shape','web-to-print-online-designer'); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div ng-if="settings['nbdesigner_enable_google_maps'] == 'yes' && settings['nbdesigner_static_map_api_key'] != ''" class="content-item type-maps" data-type="maps">
                        <div class="google-maps-options">
                            <div class="google-maps-search">
                                <input ng-keyup="$event.keyCode == 13 && updateMapUrl()" ng-model="resource.maps.address" type="text" name="search" placeholder="<?php esc_attr_e('Your address','web-to-print-online-designer'); ?>">
                                <i class="icon-nbd icon-nbd-fomat-search" ng-click="updateMapUrl()"></i>
                            </div>
                            <div class="google-maps-option">
                                <select id="google-maps-maptype" ng-change="updateMapUrl()" ng-model="resource.maps.maptype">
                                    <option value="roadmap"><?php esc_html_e('Roadmap','web-to-print-online-designer'); ?></option>
                                    <option value="satellite"><?php esc_html_e('Satellite','web-to-print-online-designer'); ?></option>
                                    <option value="terrain"><?php esc_html_e('Terrain','web-to-print-online-designer'); ?></option>
                                    <option value="hybrid"><?php esc_html_e('Hybrid','web-to-print-online-designer'); ?></option>
                                </select>
                                <label for="google-maps-maptype" ><?php esc_html_e('Map type','web-to-print-online-designer'); ?><label/>
                            </div>
                            <div class="google-maps-option">
                                <select id="google-maps-maptype" ng-change="updateMapUrl()" ng-model="resource.maps.zoom">
                                    <?php foreach( range(1, 20) as $range ): ?>
                                    <option value="<?php echo $range; ?>"><?php echo $range; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="google-maps-maptype" ><?php esc_html_e('Map zoom','web-to-print-online-designer'); ?><label/>
                            </div>
                            <div class="google-maps-option">
                                <input type="number" ng-min="100" ng-max="640" ng-step="1" ng-model="resource.maps.width" id="google-maps-width" />
                                <label for="google-maps-width" ><?php esc_html_e('Map width','web-to-print-online-designer'); ?><label/>
                            </div>
                            <div class="google-maps-option">
                                <input type="number" ng-min="100" ng-max="640" ng-step="1" ng-model="resource.maps.height" id="google-maps-height" />
                                <label for="google-maps-height" ><?php esc_html_e('Map height','web-to-print-online-designer'); ?><label/>
                            </div>
                            <div class="google-maps-option">
                                <select id="google-maps-markers-label" ng-change="updateMapUrl()" ng-model="resource.maps.markers.label">
                                    <?php 
                                        $marker_labels = array_merge( array(''), range( 0, 9 ), range( 'A', 'Z' ) );
                                        foreach( $marker_labels as $marker_label ): 
                                    ?>
                                    <option value="<?php echo $marker_label; ?>"><?php echo $marker_label === '' ? __('None','web-to-print-online-designer') : $marker_label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="google-maps-markers-label" ><?php esc_html_e('Marker label','web-to-print-online-designer'); ?><label/>
                            </div>
                            <div class="google-maps-option">
                                <select id="google-maps-markers-size" ng-change="updateMapUrl()" ng-model="resource.maps.markers.size">
                                    <option value="normal"><?php esc_html_e('Normal','web-to-print-online-designer'); ?></option>
                                    <option value="mid"><?php esc_html_e('Mid','web-to-print-online-designer'); ?></option>
                                    <option value="small"><?php esc_html_e('Small','web-to-print-online-designer'); ?></option>
                                </select>
                                <label for="google-maps-markers-size" ><?php esc_html_e('Marker size','web-to-print-online-designer'); ?><label/>
                            </div>
                            <div class="google-maps-option">
                                <select id="google-maps-markers-color" ng-change="updateMapUrl()" ng-model="resource.maps.markers.color">
                                    <option ng-style="{'background-color': color, color: '#fff'}" ng-repeat="color in resource.defaultPalette[0]" value="{{color}}">{{color}}</option>
                                </select>
                                <label for="google-maps-markers-color" ><?php esc_html_e('Marker color','web-to-print-online-designer'); ?><label/>
                            </div>
                            <div class="google-maps-preview google-maps-option" ng-class="resource.maps.loading ? 'loading' : ''" ng-if="resource.maps.url !=''">
                                <span class="nbd-button" ng-click="addImageFromUrl( resource.maps.url )"><?php esc_html_e('Insert map','web-to-print-online-designer'); ?></span>
                                <img ng-click="addImageFromUrl( resource.maps.url )" ng-src="{{resource.maps.url}}" title="<?php esc_html_e('Click to insert this map','web-to-print-online-designer'); ?>"/>
                                <div class="loading-maps" >
                                    <svg class="circular" viewBox="25 25 50 50">
                                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nbdesigner-gallery" id="nbdesigner-gallery">
                </div>
                <div class="loading-photo" >
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>
            <div class="info-support">
                <span>Facebook</span>
                <i class="icon-nbd icon-nbd-clear close-result-loaded" ng-click="onClickTab('', 'element')"></i>
            </div>
        </div>
    </div>
</div>