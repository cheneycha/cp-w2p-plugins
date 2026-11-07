<div class="<?php if( $active_photos ) echo 'active'; ?> tab" ng-show="settings['nbdesigner_enable_image'] == 'yes'" id="tab-photo" nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-photo" data-type="photo" data-offset="30">
    <div ng-if="pt_color.key == 'pt_color1' || pt_color.key == 'pt_color2' || pt_color.key == 'pt_color3' " style="color: #f00;padding: 60px 0 40px 0;font-size: 16px;line-height: 20px;font-weight: 600;" >
        <div ng-if="pt_color.key == 'pt_color2' && !settings.is_admin">
            <?php esc_html_e('Not available with this option!','web-to-print-online-designer'); ?>
        </div>
        <div ng-if="pt_color.key == 'pt_color1' || pt_color.key == 'pt_color3' || settings.is_admin"class="result-loaded">
            <div class="content-items">
                <div ng-class="settings['nbdesigner_upload_show_term'] !== 'yes' ? 'accept' : '' " class="content-item type-upload" data-type="image-upload">
                    <div ng-show="settings.nbdesigner_upload_designs_php_logged_in == 'yes' && !settings.is_logged">
                        <p><?php esc_html_e('You need to be logged in to upload images!','web-to-print-online-designer'); ?></p>
                        <button class="nbd-button nbd-hover-shadow" ng-click="login()"><?php esc_html_e('Login','web-to-print-online-designer'); ?></button>
                    </div>
                    <div ng-hide="settings.nbdesigner_upload_designs_php_logged_in == 'yes' && !settings.is_logged">
                        <div class="nbd-progress-bar">
                            <div class="nbd-progress-bar-inner" ng-style="{'width': resource.upload.progressBar + '%'}">
                                <span class="indicator" ng-style="{'left': 'calc(' + resource.upload.progressBar + '% - 15px)'}">{{resource.upload.progressBar}}</span>
                            </div>
                        </div>
                        <div ng-if="!settings.is_admin" class="form-upload nbd-dnd-file" nbd-dnd-file="customUploadFile(files)">
                            <i class="icon-nbd icon-nbd-cloud-upload"></i>
                            <span><?php esc_html_e('Click or drop images here','web-to-print-online-designer'); ?></span>
                            <input type="file" 
                                <?php echo is_available_imagick() ? 'accept="image/*, .pdf"' : 'accept="image/*"'; ?> 
                                style="display: none;" <?php if($settings['nbdesigner_upload_multiple_images'] == 'yes') echo 'multiple'; ?>/>
                        </div>
                        <div ng-if="settings.is_admin" class="form-upload nbd-dnd-file" nbd-dnd-file="uploadFile(files)">
                            <i class="icon-nbd icon-nbd-cloud-upload"></i>
                            <span><?php esc_html_e('Click or drop images here','web-to-print-online-designer'); ?></span>
                            <input type="file" 
                                <?php echo is_available_imagick() ? 'accept="image/*, .pdf"' : 'accept="image/*"'; ?> 
                                style="display: none;" <?php if($settings['nbdesigner_upload_multiple_images'] == 'yes') echo 'multiple'; ?>/>
                        </div>
                        <div class="allow-size">
                            <span><?php esc_html_e('Accept file types','web-to-print-online-designer'); ?>: <strong>{{settings.is_admin ? 'svg, png, jpg, jpeg <?php echo is_available_imagick() ? ', pdf' : ''; ?>' : 'svg'}}</strong></span>
                            <span><?php esc_html_e('Max file size','web-to-print-online-designer'); ?>: <strong>{{settings['nbdesigner_maxsize_upload']}} MB</strong></span>
                            <span><?php esc_html_e('Min file size','web-to-print-online-designer'); ?>: <strong>{{settings['nbdesigner_minsize_upload']}} MB</strong></span>
                            <span ng-if="settings.nbdesigner_upload_multiple_images == 'yes'"><?php esc_html_e('Max upload files','web-to-print-online-designer'); ?>: <strong>{{settings['nbdesigner_max_upload_files_at_once']}}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nbdesigner-gallery" id="nbdesigner-gallery">
                <div nbd-drag="img.url" extenal="true" type="image" class="nbdesigner-item" ng-click="resource.addImageContext = 'manual'; addImageFromUrl(img.url)" ng-repeat="img in resource.photo.data" repeat-end="onEndRepeat('photo')"><img ng-src="{{img.preview}}"><span class="photo-desc">{{img.des}}</span></div>
            </div>
        </div>
    </div>
    <div class="nbd-search" ng-if="pt_color.key != 'pt_color1' && pt_color.key != 'pt_color2'">
        <input ng-class="(resource.personal.status || !resource.photo.onclick) ? 'nbd-disabled' : ''" ng-keyup="$event.keyCode == 13 && getPhoto(resource.photo.type, 'search')" type="text" name="search" placeholder="<?php esc_html_e('Search photo', 'web-to-print-online-designer'); ?>" ng-model="resource.photo.photoSearch"/>
        <i class="icon-nbd icon-nbd-fomat-search"></i>
    </div>
    <div class="tab-main tab-scroll">
        <div class="nbd-items-dropdown">
            <div class="main-items" ng-if="pt_color.key != 'pt_color1' && pt_color.key != 'pt_color2' && pt_color.key != 'pt_color3'">
                <div class="items">
                    <div class="item" ng-click="onClickTab('upload', 'photo')" ng-if="settings['nbdesigner_enable_upload_image'] == 'yes'" data-type="image-upload" data-api="false">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-file-upload"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Image upload"><?php esc_html_e('Upload','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="item" ng-click="onClickTab('url', 'photo')" ng-if="settings['nbdesigner_enable_image_url'] == 'yes'" data-type="image-url" data-api="false">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-attachment"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Image url"><?php esc_html_e('Image url','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php if($fbID != ''): ?>
                    <div class="item" ng-click="onClickTab('facebook', 'photo')" data-type="facebook" ng-if="settings['nbdesigner_enable_facebook_photo'] == 'yes'" data-api="false">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-facebook-logo"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Facebook"><?php esc_html_e('Facebook','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php 
                        $insID = nbdesigner_get_option( 'nbdesigner_instagram_app_id', '' );
                        $insSc = nbdesigner_get_option( 'nbdesigner_instagram_app_secret', '' );
                        if( $insID != '' && $insSc != '' ): 
                    ?>
                    <div class="item" ng-click="onClickTab('instagram', 'photo')" data-type="instagram" ng-if="settings['nbdesigner_enable_instagram_photo'] == 'yes'" data-api="false">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-instagram-logo"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Instagram"><?php esc_html_e('Instagram','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php $dbID = nbdesigner_get_option('nbdesigner_dropbox_app_id'); if($dbID != ''): ?>
                    <div class="item" ng-click="onClickTab('dropbox', 'photo')" ng-if="settings['nbdesigner_enable_dropbox_photo'] == 'yes'" data-type="dropbox" data-api="false">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-dropbox-logo"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Dropbox"><?php esc_html_e('Dropbox','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="item" ng-click="initWebcam()" ng-if="hasGetUserMedia && settings['nbdesigner_enable_image_webcam'] == 'yes'" data-type="webcam" data-api="false">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-webcam"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Webcam"><?php esc_html_e('Webcam','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php if( $valid_license ): ?>
                    <div class="item" ng-click="onClickTab('Pixabay', 'photo')" ng-if="settings['nbdesigner_enable_pixabay'] == 'yes'" data-type="pixabay" data-api="true">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-pixabay"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Pixabay"><?php esc_html_e('Pixabay','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="item" ng-click="onClickTab('Unsplash', 'photo')" ng-if="settings['nbdesigner_enable_unsplash'] == 'yes'" data-type="unsplash" data-api="true">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-camera-alt"></i></div>
                            <div class="item-info">
                                <span class="item-name" title="Unsplash"><?php esc_html_e('Unsplash','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php if( $settings['nbdesigner_pexels_api_key'] != '' ): ?>
                    <div class="item" ng-click="onClickTab('Pexels', 'photo')" ng-if="settings['nbdesigner_enable_pexels'] == 'yes'" data-type="pexels" data-api="true">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-nbd" style="font-size: 0;">
                                    <svg width="40px" height="40px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 0h28a2 2 0 0 1 2 2v28a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z" fill="#05A081"/>
                                        <path d="M13 21h3.863v-3.752h1.167a3.124 3.124 0 1 0 0-6.248H13v10zm5.863 2H11V9h7.03a5.124 5.124 0 0 1 .833 10.18V23z" fill="#fff"/>
                                    </svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name" title="Pexels"><?php esc_html_e('Pexels','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="item" ng-click="onClickTab('Freepik', 'photo')" ng-if="settings['nbdesigner_enable_freepik'] == 'yes'" data-type="freepik" data-api="true">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-nbd" style="font-size: 0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40px" height="40px" viewBox="0 0 266 223.5" height="2101">
                                        <g >
                                            <path fill="#1e61c6" d="M46.3 157.8c-1-5.4-2.3-10.7-3-16.1-2.8-21.1 1.7-40.6 14.2-58C67.3 70 80.7 60.5 96.1 53.9c18.5-7.9 38.4-12.2 58.5-12.6 17.9-.5 33.5 6.1 47.1 17.3 14 11.6 24.8 25.9 33.1 42 4 7.8 7.7 15.8 11.5 23.8.4.9.7 1.9.9 2.9-5.3 3.2-10.4 6.5-15.8 9.4-20 11-41.6 18.5-64.1 22.4-12.3 2.1-24.7 3.3-37.1 4.1-11.8.8-23.6 1.2-35.4.9-16.1-.4-32.1-2.9-48.5-6.3zm25.4-53.6c-.3 17.6 14 31.8 31.4 32 16.8.2 32.5-12.7 32.5-31.6 0-19.6-15.1-32-32-32-17.5 0-31.8 14.1-31.9 31.6zm122.9 9.2c9.5.7 20.1-7.2 20.2-20.2.1-10.8-8.6-19.6-19.4-19.7h-.4c-11-.2-20 8.6-20.2 19.5v.3c0 11.4 8.5 20.1 19.8 20.1z"/>
                                            <path fill="#1e60c6" d="M247.8 131.8c1.4 16.8-2.8 31.6-11.2 45.2-11.4 18.4-28.4 30-48.4 37.3-20.4 7.4-42.1 10.3-63.7 8.7-12.9-.9-25.9-2.8-37.5-8.9-20-10.6-32.4-27.5-38.9-49-.3-1.3-.4-2.7-.5-4 70.5 16.3 137.4 9.1 200.2-29.3z"/>
                                            <path fill="#2163c7" d="M244.5 70.7c5.2-1.9 5.2-2 4.7-6.9-.3-2.8-.4-5.6 1-8.3.6-1.1.5-2.5.6-3.8.2-4 3.5-7.1 7.5-7.1s7.4 3.2 7.6 7.2c0 4-3.2 7.3-7.2 7.5-1.3.1-2.6 0-4.4 0-.2 2-.6 3.8-.4 5.5.6 4.9-.2 9-5.8 11.1l11.4 20.2-17.1 10.6C235.2 90 224.2 76.2 211 63.6l10.2-12.4c9.5 4.5 16.6 11.7 23.3 19.5zM38.7 132.6L22.1 127c-.8-10 1.3-19.3 5-28.4l-5.8-9.2c-2.1 0-4.2.4-6-.1-4-.9-7.9-2.1-11.6-3.6C.8 84.5-.5 80.5.3 77.6c1.1-3.5 4.4-5.7 8-5.4 3.4.3 6.2 2.9 6.7 6.3.2 1.6 0 3.3 0 5.3h8.8c3.7 1.5 3.5 6 6.3 8.9 5.5-7.2 10.9-14.4 19.1-19.5l7.6 5.9c-5.8 8-12 15.5-14.1 24.8-2.1 9.4-2.7 18.8-4 28.7z"/>
                                            <path fill="#2162c6" d="M106.7 48.2l-3.4-8.4c9.4-7.5 20.1-10.6 31.4-12.1.2-3-1.3-4.3-3.6-5.5-7.8-4-9.1-13.4-2.9-19.1 3.7-3.4 8.1-3.9 12.5-2.2 4 1.4 6.8 5.1 7.1 9.4.7 4.6-1.5 9.1-5.5 11.4-1.1.6-2.3 1.1-3.4 1.6l1.2 3.8c10.1-.2 20-.1 29.8 3.8l-1.4 8.3c-20.7.1-41.7-1-61.8 9z"/>
                                        </g>
                                    </svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name" title="Freepik"><?php esc_html_e('Freepik','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php do_action('nbd_modern_sidebar_photo_icons'); ?>
                </div>
                <div class="pointer"></div>
            </div>
            <div class="result-loaded" ng-if="pt_color.key != 'pt_color1' && pt_color.key != 'pt_color2' && pt_color.key != 'pt_color3'">
                <div class="content-items">
                    <div ng-class="settings['nbdesigner_upload_show_term'] !== 'yes' ? 'accept' : '' " class="content-item type-upload" data-type="image-upload">
                        <div ng-show="settings.nbdesigner_upload_designs_php_logged_in == 'yes' && !settings.is_logged">
                            <p><?php esc_html_e('You need to be logged in to upload images!','web-to-print-online-designer'); ?></p>
                            <button class="nbd-button nbd-hover-shadow" ng-click="login()"><?php esc_html_e('Login','web-to-print-online-designer'); ?></button>
                        </div>
                        <div ng-hide="settings.nbdesigner_upload_designs_php_logged_in == 'yes' && !settings.is_logged">
                            <div class="nbd-progress-bar">
                                <div class="nbd-progress-bar-inner" ng-style="{'width': resource.upload.progressBar + '%'}">
                                    <span class="indicator" ng-style="{'left': 'calc(' + resource.upload.progressBar + '% - 15px)'}">{{resource.upload.progressBar}}</span>
                                </div>
                            </div>
                            <div class="form-upload nbd-dnd-file" nbd-dnd-file="uploadFile(files)">
                                <i class="icon-nbd icon-nbd-cloud-upload"></i>
                                <span><?php esc_html_e('Click or drop images here','web-to-print-online-designer'); ?></span>
                                <input type="file" 
                                    <?php echo is_available_imagick() ? 'accept="image/*, .pdf"' : 'accept="image/*"'; ?> 
                                    style="display: none;" <?php if($settings['nbdesigner_upload_multiple_images'] == 'yes') echo 'multiple'; ?>/>
                            </div>
                            <div class="allow-size">
                                <span><?php esc_html_e('Accept file types','web-to-print-online-designer'); ?>: <strong><?php echo is_available_imagick() ? 'png, jpg, svg, pdf' : 'png, jpg, svg'; ?></strong></span>
                                <span><?php esc_html_e('Max file size','web-to-print-online-designer'); ?>: <strong>{{settings['nbdesigner_maxsize_upload']}} MB</strong></span>
                                <span><?php esc_html_e('Min file size','web-to-print-online-designer'); ?>: <strong>{{settings['nbdesigner_minsize_upload']}} MB</strong></span>
                                <span ng-if="settings.nbdesigner_upload_multiple_images == 'yes'"><?php esc_html_e('Max upload files','web-to-print-online-designer'); ?>: <strong>{{settings['nbdesigner_max_upload_files_at_once']}}</strong></span>
                            </div>
                            <div class="nbd-term" ng-if="settings['nbdesigner_upload_show_term'] == 'yes'">
                                <div class="nbd-checkbox">
                                    <input id="accept-term" type="checkbox">
                                    <label for="accept-term">&nbsp;</label>
                                </div>
                                <span class="term-read"><?php esc_html_e('I accept the terms','web-to-print-online-designer'); ?></span>
                            </div>
                            <div id="nbd-upload-wrap">
                                <div class="mansory-wrap">
                                    <div nbd-drag="img.url" nbd-img="img" extenal="false" type="image" class="mansory-item" ng-click="resource.addImageContext = 'manual'; addImageFromUrl(img, false, img.ilr);" ng-repeat="img in resource.upload.data track by $index" repeat-end="onEndRepeat('upload')"><img ng-src="{{img.url}}"></div>
                                </div>
                            </div>
                            <div class="clear-local-images-wrap" ><span ng-click="_localStorage.delete('nbduploaded')"><?php esc_html_e('Clear all uploaded images','web-to-print-online-designer'); ?></span></div>
                        </div>
                    </div>
                    <div class="content-item type-url" data-type="image-url">
                        <div class="form-group">
                            <label><?php esc_html_e('Image Url','web-to-print-online-designer'); ?></label>
                            <div class="input-group">
                                <input nbd-capture="addImageFromClipboard(file, type)" class="image-url" type="text" name="image-url" ng-model="resource.imageFromUrl" placeholder="<?php esc_html_e('Enter image url, allow: jpg, png, svg','web-to-print-online-designer'); ?>"/>
                                <button ng-class="resource.imageFromUrl !='' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="resource.addImageContext = 'manual'; addImageFromUrl(resource.imageFromUrl);"><?php esc_html_e('insert','web-to-print-online-designer'); ?></button>
                            </div>
                            <div class="google-driver-wrap">
                                <?php if( nbdesigner_get_option( 'nbdesigner_enable_google_drive', 'yes' ) == 'yes' 
                                        && nbdesigner_get_option( 'nbdesigner_google_api_key', '' ) != '' 
                                        && nbdesigner_get_option( 'nbdesigner_google_client_id', '' ) != '' ): ?>
                                <button onclick="onApiLoad()" class="nbd-button" >
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <title>drive</title>
                                        <path fill="#efc75e" d="M14.165 12.423l0.056 0.095h0.111l5.668-0.026-0.166-0.285-6.372-10.969h-0.111l-5.669 0.023 0.166 0.285c0 0 6.317 10.876 6.317 10.876z"></path>
                                        <path fill="#3db39e" d="M9.508 6.912l-0.056-0.096-2.915-4.985-0.164 0.285-6.373 11.009 0.056 0.095 2.915 4.986 0.165-0.285 6.318-10.914c0 0 0.054-0.095 0.054-0.095z"></path>
                                        <path fill="#26a6d1" d="M7.111 13.734h-0.11l-0.055 0.094-2.709 4.648-0.164 0.286h12.998l0.055-0.096 2.874-4.931h-12.889z"></path>
                                    </svg>
                                    <?php esc_html_e('Pick From Google Drive','web-to-print-online-designer'); ?>
                                </button>
                                <script type="text/javascript">
                                    var developerKey = '<?php echo nbdesigner_get_option('nbdesigner_google_api_key'); ?>';
                                    var clientId = "<?php echo nbdesigner_get_option('nbdesigner_google_client_id'); ?>";
                                    var _scope = ['https://www.googleapis.com/auth/drive.readonly'];
                                    var locale = '<?php echo( $locale ); ?>';
                                    var pickerApiLoaded = false;
                                    var oauthToken;
                                    function onApiLoad() {
                                        if( oauthToken ){
                                            createPicker();
                                        }else{
                                            gapi.load('auth', {'callback': onAuthApiLoad});
                                            gapi.load('picker', {'callback': onPickerApiLoad});                                               
                                        }
                                    }
                                    function onAuthApiLoad() {
                                        window.gapi.auth.authorize({
                                              'client_id': clientId,
                                              'scope': _scope,
                                              'immediate': false
                                            },
                                            handleAuthResult
                                        );
                                    }
                                    function onPickerApiLoad() {
                                        pickerApiLoaded = true;
                                        createPicker();
                                    }
                                    function handleAuthResult(authResult) {
                                        if (authResult && !authResult.error) {
                                           oauthToken = authResult.access_token;
                                           createPicker();
                                        }
                                    }
                                    function createPicker() {
                                        if (pickerApiLoaded && oauthToken) {
                                        var picker = new google.picker.PickerBuilder().
                                                addViewGroup(
                                                    new google.picker.ViewGroup(google.picker.ViewId.DOCS_IMAGES).
                                                    addView(google.picker.ViewId.DOCS_IMAGES)).
                                                setLocale(locale).    
                                                setOAuthToken(oauthToken).
                                                setDeveloperKey(developerKey).
                                                setCallback(pickerCallback).
                                                build();
                                        picker.setVisible(true);
                                        }
                                    }
                                    function pickerCallback(data) {
                                        var url = 'nothing';
                                        if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
                                            var doc = data[google.picker.Response.DOCUMENTS][0],
                                            url = doc[google.picker.Document.URL],
                                            scope = angular.element(document.getElementById("designer-controller")).scope(); 
                                            scope.resource.imageFromUrl = url;
                                            scope.resource.gapi = {'fileId': doc.id, 'oAuthToken': oauthToken, 'name': doc.name};
                                            scope.updateApp()
                                        }
                                    }
                                </script>
                                <script type="text/javascript" src="https://apis.google.com/js/api.js" gapi_processed="true"></script>  
                                <?php endif; ?>
                            </div>
                        </div>
                        <div ng-show="settings['nbdesigner_enable_svg_code'] == 'yes'" class="svg-code-editor-wrap">
                            <div class="form-group">
                                <label><?php esc_html_e('SVG Code','web-to-print-online-designer'); ?></label>
                                <textarea class="form-control hover-shadow nbdesigner_svg_code" rows="10" ng-model="resource.svgCode"  placeholder="<?php esc_html_e('Enter svg code','web-to-print-online-designer'); ?>"/></textarea>
                                <button ng-class="resource.svgCode !='' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="addSvgFromString(resource.svgCode)"><?php esc_html_e('Insert SVG','web-to-print-online-designer'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_facebook_photo'] == 'yes'" class="content-item type-facebook" data-type="facebook" id="nbd-facebook-wrap">
                        <?php if( $fbID != '' ): ?>
                        <div id="fb-root--"></div>
                        <div class="fb-login-button" data-max-rows="1" data-size="medium" data-show-faces="false" data-auto-logout-link="false" data-scope="user_photos" onlogin="nbdOnFBLogin(null)"></div>
                        <div class="mansory-wrap">
                            <div nbd-drag="img.url" extenal="true" type="image" class="mansory-item" ng-click="resource.addImageContext = 'manual'; addImageFromUrl(img.url)" ng-repeat="img in resource.facebook.data | limitTo: resource.facebook.filter.perPage * resource.facebook.filter.currentPage" repeat-end="onEndRepeat('facebook')"><img ng-src="{{img.preview}}"><span class="photo-desc">{{img.des}}</span></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if( $insID != '' && $insSc != '' ): ?>
                    <div ng-if="settings['nbdesigner_enable_instagram_photo'] == 'yes'" class="content-item type-instagram button-login" data-type="instagram" id="nbd-instagram-wrap">
                        <button class="nbd-button nbd-hover-shadow" ng-click="authenticateInstagram()" ng-hide="resource.instagram.token != ''">
                            <i class="icon-nbd icon-nbd-instagram-logo"></i>
                            <span><?php esc_html_e('Log in','web-to-print-online-designer'); ?></span>
                        </button>
                        <button class="nbd-button nbd-hover-shadow" ng-click="logoutInstagram()" ng-show="resource.instagram.token != ''">
                            <i class="icon-nbd icon-nbd-instagram-logo"></i>
                            <span><?php esc_html_e('Log out','web-to-print-online-designer'); ?></span>
                        </button>
                        <div class="mansory-wrap">
                            <div nbd-drag="img.url" extenal="true" type="image" class="mansory-item" ng-click="resource.addImageContext = 'manual'; addImageFromUrl(img.url)" ng-repeat="img in resource.instagram.data | limitTo: resource.instagram.filter.perPage * resource.instagram.filter.currentPage" repeat-end="onEndRepeat('instagram')"><img ng-src="{{img.preview}}"><span class="photo-desc">{{img.des}}</span></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if( $dbID != '' && $settings['nbdesigner_enable_dropbox_photo'] != 'no' ): ?>
                    <div class="content-item type-dropbox" data-type="dropbox" id="nbd-dropbox-wrap">
                        <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?php echo( $dbID ); ?>"></script>
                        <script type="text/javascript">
                            NBDESIGNCONFIG['enable_dropbox'] = true;
                        </script>
                        <div id="nbdesigner_dropbox"></div>
                        <div class="mansory-wrap">
                            <div nbd-drag="img.url" extenal="true" type="image" class="mansory-item" ng-click="resource.addImageContext = 'manual'; addImageFromUrl(img.url)" ng-repeat="img in resource.dropbox.data | limitTo: resource.dropbox.filter.perPage * resource.dropbox.filter.currentPage" repeat-end="onEndRepeat('dropbox')"><img ng-src="{{img.preview}}"><span class="photo-desc">{{img.des}}</span></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="content-item type-webcam" data-type="webcam">
                        <?php esc_html_e('webcam','web-to-print-online-designer'); ?>
                    </div>
                    <?php do_action('nbd_modern_sidebar_photo_images'); ?>
                </div>
                <div class="nbdesigner-gallery" id="nbdesigner-gallery">
                    <div nbd-drag="img.url" extenal="true" type="image" class="nbdesigner-item" ng-click="resource.addImageContext = 'manual'; addImageFromUrl(img.url)" ng-repeat="img in resource.photo.data" repeat-end="onEndRepeat('photo')"><img ng-src="{{img.preview}}"><span class="photo-desc">{{img.des}}</span></div>
                </div>
                <div class="loading-photo" >
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
                <div class="tab-load-more" style="display: none;" ng-show="!resource.photo.onload && resource.photo.data.length && (resource.photo.filter.totalPage == 0 || resource.photo.filter.currentPage < resource.photo.filter.totalPage)">
                    <a class="nbd-button" ng-click="scrollLoadMore('#tab-photo', 'photo')"><?php esc_html_e('Load more','web-to-print-online-designer'); ?></a>
                </div>
            </div>
            <div class="info-support">
                <span>Facebook</span>
                <i class="icon-nbd icon-nbd-clear close-result-loaded" ng-click="onClickTab('', 'photo')"></i>
            </div>
        </div>
    </div>
</div>