<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="modal fade" id="dg-myclipart">
    <div class="modal-dialog"
        ng-class="(settings['nbdesigner_enable_upload_image'] == 'yes' && settings['nbdesigner_enable_instagram_photo'] == 'yes' && settings['nbdesigner_enable_image_webcam'] == 'yes'
                    && settings['nbdesigner_enable_image_url'] == 'yes' && settings['nbdesigner_enable_facebook_photo'] == 'yes' ) ? 'modal-lg' : ''" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>				
                <ul role="tablist" id="upload-tabs" class="nbdesigner_modal_tab">
                    <li class="active" ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'"><a href="#upload-computer" role="tab" data-toggle="tab"><i class="fa fa-cloud-upload visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['UPLOAD_PHOTO']) ? langs['UPLOAD_PHOTO'] : "Upload"}}</span></a></li>					
                    <li ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'"><a href="#uploaded-photo" role="tab" data-toggle="tab"><i class="fa fa-cloud visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['PHOTO_UPLOADED']) ? langs['PHOTO_UPLOADED'] : "Uploaded"}}</span></a></li>
                    <li ng-show="settings['nbdesigner_enable_image_url'] == 'yes'"><a href="#nbdesigner_url" role="tab" data-toggle="tab"><i class="fa fa-link visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['IMAGE_URL']) ? langs['IMAGE_URL'] : "Image Url"}}</span></a></li>
                    <li ng-show="settings['nbdesigner_enable_facebook_photo'] == 'yes'"><a href="#nbdesigner_facebook" role="tab" data-toggle="tab"><i class="fa fa-facebook-square visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['FACEBOOK']) ? langs['FACEBOOK'] : "Facebook"}}</span></a></li>
                    <li ng-show="settings['nbdesigner_enable_instagram_photo'] == 'yes'"><a href="#nbdesigner_instagram" role="tab" data-toggle="tab"><i class="fa fa-instagram visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['INSTAGRAM']) ? langs['INSTAGRAM'] : "Instagram"}}</span></a></li>
                    <li ng-show="settings['nbdesigner_enable_dropbox_photo'] == 'yes'"><a href="#nbdesigner_dropbox" role="tab" data-toggle="tab"><i class="fa fa-dropbox visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['DROPBOX']) ? langs['DROPBOX'] : "Dropbox"}}</span></a></li> 
                    <li ng-if="hasGetUserMedia && !modeMobile" ng-click="initWebcam()" ng-show="settings['nbdesigner_enable_image_webcam'] == 'yes'"><a href="#nbdesigner_webcam" role="tab" data-toggle="tab"><i class="fa fa-camera visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['WEBCAM']) ? langs['WEBCAM'] : "Webcam"}}</span></a></li>
                    <li ng-show="settings['nbdesigner_enable_pixabay'] == 'yes'"><a href="#nbdesigner_pixabay" role="tab" data-toggle="tab"><i class="fa fa-camera visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['PIXABAY']) ? langs['PIXABAY'] : "Pixabay"}}</span></a></li>
                    <li ng-show="settings['nbdesigner_enable_unsplash'] == 'yes'"><a href="#nbdesigner_unsplash" role="tab" data-toggle="tab"><i class="fa fa-camera visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['PIXABAY']) ? langs['PIXABAY'] : "Unsplash"}}</span></a></li>
                </ul>
            </div>
            <div class="modal-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="upload-computer" ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'">
                        <?php 
                            $login_required = (nbdesigner_get_option('nbdesigner_upload_designs_php_logged_in') !== 'no' && !is_user_logged_in()) ? 1 : 0;
                            if($login_required):
                        ?>
                        <p>{{(langs['MES_LOGIN_TO_UPLOAD']) ? langs['MES_LOGIN_TO_UPLOAD'] : "You need to be logged in to upload images!"}}</p>
                        <?php else: ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>{{(langs['CHOOSE_FILE']) ? langs['CHOOSE_FILE'] : "Choose file upload"}}</label>
<!--                                    <input type="file" id="files-upload" autocomplete="off" ng-file-select="onFileSelect($files)" accept="image/*" multiple/><br />-->
                                    <p>      
                                        <input type="file" id="files-upload" autocomplete="off" ng-file-select="onFileSelect($files)" class="inputfile" <?php if( $enable_upload_multiple == 'yes' ) echo 'multiple'; ?> accept="image/*" /> 
                                        <label for="files-upload" class="hover-shadow shadow">
                                            <span></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                                                <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"> 
                                                </path>
                                            </svg>
                                            <span>{{(langs['CHOOSE_IMAGE']) ? langs['CHOOSE_IMAGE'] : "Choose image(s)"}}</span>
                                        </label>
                                    </p> 
                                    <p><b>{{listImageBeforeUpload}}</b></p>
                                    <p>
                                        <small>{{(langs['ACCEPT_FILE_TYPES']) ? langs['ACCEPT_FILE_TYPES'] : "Accept file types"}}: <strong>png, jpg, gif</strong>
                                        <br />{{(langs['MAX_FILE_SIZE']) ? langs['MAX_FILE_SIZE'] : "Max file size"}}: <strong>{{settings.nbdesigner_maxsize_upload}} MB</strong><br /> {{(langs['MIN_FILE_SIZE']) ? langs['MIN_FILE_SIZE'] : "Min file size"}}: <strong>{{settings.nbdesigner_minsize_upload}} MB</strong></small>
                                    </p>
                                    <?php 
                                        $show_term = nbdesigner_get_option('nbdesigner_upload_show_term');
                                        if($show_term == 'yes'):
                                    ?>                                
                                    <div class="nbdesigner-term">
                                        <div class="md-checkbox" style="display: inline-block; margin: 0;">
                                            <input id="accept_term" type="checkbox">
                                            <label for="accept_term" class="">&nbsp;</label>
                                        </div>                             
                                        <a data-toggle="modal" data-target="#term-modal">{{(langs['TERM']) ? langs['TERM'] : "I accept the terms"}}</a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>							
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary shadow nbdesigner_upload" id="action-upload" ng-click="startUpload()">{{(langs['UPLOAD']) ? langs['UPLOAD'] : "Upload"}}</button>
                                </div>
                            </div>                         
                        </div>
                        <?php endif; ?>
                    </div>										
                    <div class="tab-pane" id="uploaded-photo" ng-show="settings['nbdesigner_enable_upload_image'] == 'yes'">
                        <div class="row" id="dag-files-images">
                            <span class="view-thumb" ng-repeat="url in uploadURL | reverse | limitTo : imgPageSize">
                                <img class="img-responsive img-thumbnail nbdesigner_upload_image shadow hover-shadow" ng-src="{{url}}" ng-click="addImage(url, readyReplaceImage)"  spinner-on-load/>
                            </span>                                                    
                        </div>						
                        <div id="image-load-more" ng-show="(uploadURL.length > 10) && (uploadURL.length > imgPageSize)"><button type="button" style="margin-top: 10px;" class="btn btn-primary shadow nbdesigner_upload" ng-click="imgPageSize = imgPageSize +10">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button></div>
                        <div class="progress progress-bar-container" ng-show="loading">
                            <div class="progress-bar progress-bar-striped"  role="progressbar" aria-valuenow="{{progressUpload}}"
                                 aria-valuemin="0" aria-valuemax="100" ng-style="{'width': progressUpload + '%'}" >{{progressUpload}}%</div>
                        </div>                                                
                        <div class="row col-md-12">
                            <span class="help-block">{{(langs['CLICK_IMAGE_TO_ADD']) ? langs['CLICK_IMAGE_TO_ADD'] : "Click image to add design"}}.</span>
                        </div>
                    </div>
                    <div class="tab-pane" id="nbdesigner_pixabay">
                        <p>
                            <img style="height: 20px;" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/images/pixabay.svg'; ?>" />
                            <input style="height: 32px; vertical-align: top;" ng-model="pixabay_key" class="form-control hover-shadow nbdesigner_image_url"/>
                            <a ng-click="searchPixabay()" class="btn btn-primary shadow nbdesigner_upload"><i class="fa fa-search"></i></a>
                        </p>
                        <div id="pixabay_results">
                            <span class="view-thumb" ng-repeat="img in pixabayImages | reverse | limitTo: pixabayPerPage * pixabayPage">
                                <img class="img-responsive img-thumbnail nbdesigner_upload_image shadow hover-shadow" data-url="" ng-src="{{img.preview}}" ng-click="addFacebookImage(img, $event)"  spinner-on-load/>
                            </span>   
                        </div>
                        <div ng-show="pixabayPage <= pixabayPages">
                            <button class="btn btn-primary shadow nbdesigner_upload" style="margin-right: 15px; margin-top: 10px;" ng-click="pixabayPage = pixabayPage + 1;callPixabayApi()">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                            <img id="pixabay_loading" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/images/loading.svg'; ?>"/>
                        </div>
                    </div>
                    <div class="tab-pane" id="nbdesigner_unsplash">
                        <p>
                            <a href="https://unsplash.com/" target="_blank"><svg class="_2m4hn" version="1.1" viewBox="0 0 32 32" width="32" height="32" aria-labelledby="unsplash-home" aria-hidden="false"><title id="unsplash-home">Unsplash Home</title><path d="M20.8 18.1c0 2.7-2.2 4.8-4.8 4.8s-4.8-2.1-4.8-4.8c0-2.7 2.2-4.8 4.8-4.8 2.7.1 4.8 2.2 4.8 4.8zm11.2-7.4v14.9c0 2.3-1.9 4.3-4.3 4.3h-23.4c-2.4 0-4.3-1.9-4.3-4.3v-15c0-2.3 1.9-4.3 4.3-4.3h3.7l.8-2.3c.4-1.1 1.7-2 2.9-2h8.6c1.2 0 2.5.9 2.9 2l.8 2.4h3.7c2.4 0 4.3 1.9 4.3 4.3zm-8.6 7.5c0-4.1-3.3-7.5-7.5-7.5-4.1 0-7.5 3.4-7.5 7.5s3.3 7.5 7.5 7.5c4.2-.1 7.5-3.4 7.5-7.5z"></path></svg></a>
                            <input style="height: 32px; vertical-align: top;" ng-model="unsplash_key" class="form-control hover-shadow nbdesigner_image_url"/>
                            <a ng-click="searchUnsplash()" style="vertical-align: top;" class="btn btn-primary shadow nbdesigner_upload"><i class="fa fa-search"></i></a>
                        </p>
                        <div id="unsplash_results">
                            <span class="view-thumb" ng-repeat="img in unsplashImages | reverse | limitTo: unsplashPerPage * unsplashPage">
                                <img class="img-responsive img-thumbnail nbdesigner_upload_image shadow hover-shadow" data-url="" ng-src="{{img.preview}}" ng-click="addFacebookImage(img, $event)"  spinner-on-load/>
                            </span>                              
                        </div>
                        <div ng-show="unsplashPage <= unsplashPages">
                            <button class="btn btn-primary shadow nbdesigner_upload" style="margin-right: 15px; margin-top: 10px;" ng-click="unsplashPage = unsplashPage + 1;callUnsplashApi()">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                            <img id="unsplash_loading" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/images/loading.svg'; ?>"/>
                        </div>                        
                    </div>
                    <div class="tab-pane" id="nbdesigner_facebook" ng-show="settings['nbdesigner_enable_facebook_photo'] == 'yes'">
                        <?php 
                            $fbID = nbdesigner_get_option('nbdesigner_facebook_app_id');
                            if($fbID != ''):
                                include_once 'tab_facebook_photo.php'; 
                            else:                            
                        ?>
                        <p>{{(langs['MES_FACEBOOK']) ? langs['MES_FACEBOOK'] : "Please fill Facebook app ID"}}</p>
                        <?php endif; ?>
                        <div id="uploaded-facebook"></div>
                        <div>
                            <input type="hidden" id="nbdesigner_fb_next" value=""/>
                            <button style="margin-right: 15px; margin-top: 10px;" id="facebook-load-more" type="button" class="hidden btn btn-primary shadow nbdesigner_upload" ng-click="loadMoreFacebookPhoto()">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                            <img id="loading_fb_upload" class="hidden" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/images/ajax-loader.gif'; ?>" />
                        </div>
                    </div>
                    <div id="nbdesigner_instagram" class="tab-pane" ng-show="settings['nbdesigner_enable_instagram_photo'] == 'yes'">
                        <?php 
                            $insID = nbdesigner_get_option('nbdesigner_instagram_app_id');
                            if($insID == ''): ?>
                            <p>{{(langs['MES_INSTAGRAM']) ? langs['MES_INSTAGRAM'] : "Please fill Instagram app ID"}}</p>
                        <?php  else:  ?>
                        <p>
                            <button class="btn btn-primary shadow nbdesigner_upload" id="instagram_login" ng-click="authenticateInstagram()">
                                <i class="fa fa-instagram" aria-hidden="true"></i>
                                <span>Login</span>
                            </button>
                            <button class="btn btn-primary shadow nbdesigner_upload" ng-show="instaAccessToken != ''" id="instagram_logout">
                                <i class="fa fa-power-off" aria-hidden="true"></i>
                                <span ng-click="switchInstagram()">Logout</span>
                            </button> 
                        </p>        
                        <?php endif; ?>
                        <div id="instagram_images"></div>
                    </div>
                    <div id="nbdesigner_dropbox" class="tab-pane">
                        <?php 
                            $dbID = nbdesigner_get_option('nbdesigner_dropbox_app_id');
                            if($dbID == ''): ?>
                            <p>{{(langs['MES_DROPBOX']) ? langs['MES_DROPBOX'] : "Please fill Dropbox app ID"}}</p>
                        <?php  else:  ?>
                            <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?php echo $dbID; ?>"></script>
                            <script>
                                var options = {
                                    success: function(files) {
                                        var scope = angular.element(document.getElementById("designer-controller")).scope();
                                        scope.getDropboxImages(files);
                                        if (scope.$root.$$phase !== "$apply" && scope.$root.$$phase !== "$digest") scope.$apply()
                                    },                                    
                                    linkType: "direct",
                                    multiselect: true,
                                    extensions: ['.jpg', '.jpeg', '.png']
                                };
                                var button = Dropbox.createChooseButton(options);
                                document.getElementById("nbdesigner_dropbox").appendChild(button);
                            </script>    
                        <?php endif; ?>
                            <div id="dropbox_images"></div>
                    </div>                   
                    <div class="tab-pane" id="nbdesigner_url" ng-show="settings['nbdesigner_enable_image_url'] == 'yes'">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>{{(langs['IMAGE_URL1']) ? langs['IMAGE_URL1'] : "Image URL"}}</label>
                                    <input style="height: 33px;" id="nbd_image_url" class="form-control hover-shadow nbdesigner_image_url" ng-model="imageFromUrl"  placeholder="{{(langs['ENTER_YOUR_IMAGE_URL']) ? langs['ENTER_YOUR_IMAGE_URL'] : 'Enter your image url, allow: jpg, png, svg'}}"/>
                                    <?php if( nbdesigner_get_option('nbdesigner_enable_google_drive') == 'yes' 
                                            && nbdesigner_get_option('nbdesigner_google_api_key') != '' 
                                            && nbdesigner_get_option('nbdesigner_google_client_id') != '' ): ?>
                                    <script type="text/javascript">
                                        var developerKey = '<?php echo nbdesigner_get_option('nbdesigner_google_api_key'); ?>';
                                        var clientId = "<?php echo nbdesigner_get_option('nbdesigner_google_client_id'); ?>";
                                        var _scope = ['https://www.googleapis.com/auth/drive.readonly'];
                                        var locale = '<?php echo $locale; ?>';
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
                                                var doc = data[google.picker.Response.DOCUMENTS][0];
                                                url = doc[google.picker.Document.URL];
                                                document.getElementById('nbd_image_url').value = url;  
                                                var scope = angular.element(document.getElementById("designer-controller")).scope(); 
                                                scope.imageFromUrl = url;
                                                scope.gapi = {'fileId': doc.id, 'oAuthToken': oauthToken, 'name': doc.name};
                                                if (scope.$root.$$phase !== "$apply" && scope.$root.$$phase !== "$digest") scope.$apply()                                               
                                            }
                                        }
                                    </script>                  
                                    <button onclick="onApiLoad()" class="btn btn-primary shadow nbdesigner_upload pick_from_gd">
                                        <svg style="vertical-align: middle; margin-right: 15px;" version="1.1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                            <title>drive</title>
                                            <path fill="#efc75e" d="M14.165 12.423l0.056 0.095h0.111l5.668-0.026-0.166-0.285-6.372-10.969h-0.111l-5.669 0.023 0.166 0.285c0 0 6.317 10.876 6.317 10.876z"></path>
                                            <path fill="#3db39e" d="M9.508 6.912l-0.056-0.096-2.915-4.985-0.164 0.285-6.373 11.009 0.056 0.095 2.915 4.986 0.165-0.285 6.318-10.914c0 0 0.054-0.095 0.054-0.095z"></path>
                                            <path fill="#26a6d1" d="M7.111 13.734h-0.11l-0.055 0.094-2.709 4.648-0.164 0.286h12.998l0.055-0.096 2.874-4.931h-12.889z"></path>
                                        </svg>                          
                                        {{(langs['PICK_FROM_GOOGLE_DRIVE']) ? langs['PICK_FROM_GOOGLE_DRIVE'] : "Pick From Google Drive"}}
                                    </button>
                                    <script type="text/javascript" src="https://apis.google.com/js/api.js" gapi_processed="true"></script>  
                                    <?php endif; ?>
                                </div>
                            </div>    
                        </div>                          
                        <div class="row" ng-show="settings['nbdesigner_enable_svg_code'] == 'yes'">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>{{(langs['SVG_CODE']) ? langs['SVG_CODE'] : "SVG Code"}}</label>
                                    <textarea class="form-control hover-shadow nbdesigner_svg_code" rows="10" ng-model="svgCode"  placeholder="{{(langs['ENTER_SVG_CODE']) ? langs['ENTER_SVG_CODE'] : 'Enter svg code'}}"/></textarea>
                                </div>
                            </div>    
                        </div>                        
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary shadow nbdesigner_upload"  ng-click="addImageFromUrl()">{{(langs['INSERT']) ? langs['INSERT'] : "Insert"}}</button>
                                </div>
                            </div>                            
                        </div>                        
                    </div>
                    <div ng-if="hasGetUserMedia && !modeMobile" class="tab-pane" id="nbdesigner_webcam" ng-show="settings['nbdesigner_enable_image_webcam'] == 'yes'">
                        <div class="row">
                            <div class="col-xs-12 con-webcam" id="my_camera" ng-show="statusWebcam"></div>    
                            <div class="col-xs-12 con-webcam off" ng-show="!statusWebcam">
                                <i class="fa fa-camera icon-camera" aria-hidden="true"></i>
                            </div>                               
                        </div>
                        <div style="margin-top: 15px;">
                            <button ng-disabled="!statusWebcam" class="btn btn-primary shadow nbdesigner_upload" ng-click="pauseWebcam()">{{(langs['PAUSE']) ? langs['PAUSE'] : "Pause"}}</button>                     
                            <button ng-disabled="!statusWebcam" class="btn btn-primary shadow nbdesigner_upload" ng-click="unPauseWebcam()">{{(langs['UNPAUSE']) ? langs['UNPAUSE'] : "Un Pause"}}</button>                     
                            <button class="btn btn-primary shadow nbdesigner_upload" ng-click="resetWebcam()">{{(langs['STOPWEBCAM']) ? langs['STOPWEBCAM'] : "Stop Webcam"}}</button> 
                            <button ng-disabled="!statusWebcam" class="btn btn-primary shadow nbdesigner_upload" ng-click="takeSnapshot()">{{(langs['CAPTURE']) ? langs['CAPTURE'] : "Capture"}}</button>   
                        </div>
                    </div>                          
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="term-modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content nbdesigner-term-modal">
                <div class="modal-body">
                    <div class="modal-body">                       
                        <?php echo stripslashes(nbdesigner_get_option('nbdesigner_upload_term')); ?>
                    </div>                   
                </div>                
            </div>    
        </div>          
    </div>    
</div>