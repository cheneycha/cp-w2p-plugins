
<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
?>
<h1><?php esc_html_e('Live Chat & Design Monitor', 'web-to-print-online-designer'); ?></h1>
<div class="wrap">
    <div id="nbd-chat-app" ng-cloak>
        <div ng-controller="nbdChatCtrl" class="nbc-wrap">
            <div class="nbc-wrap-inner">
                <div class="nbc-sidebar">
                    <div class="nbc-sidebar-nav">
                        <div class="nbc-sidebar-nav-item" ng-click="adminLayout.activeNav = 'messages'" ng-class="adminLayout.activeNav == 'messages' ? 'active' : ''" title="<?php esc_html_e('Messages', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-comments"></span></div>
                        <div class="nbc-sidebar-nav-item" ng-click="adminLayout.activeNav = 'macros'; initMacro()" ng-class="adminLayout.activeNav == 'macros' ? 'active' : ''" title="<?php esc_html_e('Macros', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-controls-play"></span></div>
                    </div>
                    <div class="nbc-sidebar-panels">
                        <div class="nbc-sidebar-panel messages active" ng-class="adminLayout.activeNav == 'messages' ? 'active' : ''">
                            <div class="nbc-sidebar-panel-header">
                                <span><?php esc_html_e('Messages', 'web-to-print-online-designer'); ?></span>
                                <div class="nbc-mod-avatar">
                                    <img ng-src="{{user.avatar ? user.avatar : frontendLayout.defaultAvatar}}" />
                                    <div class="nbc-mod-status-wrap">
                                        <div class="nbc-mod-status-wrap-inner">
                                            <div class="nbc-mod-status" ng-class="user.status" ng-click="adminLayout.showStatusOpt = !adminLayout.showStatusOpt">
                                                <span class="dashicons dashicons-arrow-down-alt2"></span>
                                            </div>
                                            <div class="nbc-mod-status-options" ng-class="adminLayout.showStatusOpt ? 'active' : ''">
                                                <div class="nbc-mod-status-option" ng-click="toggleStatus('online')"><div class="nbc-status online"></div><?php esc_html_e('Online', 'web-to-print-online-designer'); ?></div>
                                                <div class="nbc-mod-status-option" ng-click="toggleStatus('offline')"><div class="nbc-status offline"></div><?php esc_html_e('Offline', 'web-to-print-online-designer'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nbc-sidebar-panel-body nbc-pf-scroll">
                                <div class="nbc-list-item" 
                                    ng-click="switchConversation( _user.conversation_id, _user.id )" 
                                    ng-repeat="_user in users track by $index" 
                                    ng-hide="_user.id == user.id || _user.is_mod || ( _user.chat_with && _user.chat_with != user.id )"
                                    ng-class="_user.conversation_id == user.connection ? 'active' : ''" >
                                    <div class="nbc-list-left">
                                        <img ng-src="{{_user.avatar ? _user.avatar : frontendLayout.defaultAvatar}}" />
                                    </div>
                                    <div class="nbc-list-body">
                                        <div class="nbc-list-user-name-wrap">
                                            <div class="nbc-list-user-name">{{_user.name}}</div>
                                            <div class="nbc-unread-msg" ng-show="_user.unreadMsg">{{_user.unreadMsg}}</div>
                                        </div>
                                        <div class="nbc-list-last-msg" ng-bind-html="_user.last_msg | message_trusted"></div>
                                        <div class="nbc-list-last-msg-time">{{_user.last_msg_time}}</div>
                                    </div>
                                </div>
                                <div class="nbc-no-item" ng-show="( users | userFilter: user.id).length == 0"><?php esc_html_e('No conversation found.', 'web-to-print-online-designer'); ?></div>
                            </div>
                        </div>
                        <div class="nbc-sidebar-panel macros" ng-class="adminLayout.activeNav == 'macros' ? 'active' : ''">
                            <div class="nbc-sidebar-panel-header">
                                <?php esc_html_e('Macros', 'web-to-print-online-designer'); ?> 
                                <a ng-click="addMacro()" class="button button-primary add-new"><?php _e('Add new'); ?></a>
                            </div>
                            <div class="nbc-sidebar-panel-body nbc-pf-scroll">
                                <div class="nbc-macro-actions">
                                    <select name="action" ng-model="adminLayout.macro.bulkAction">
                                        <option value=""><?php _e('Bulk Actions'); ?></option>
                                        <option value="delete"><?php _e('Delete'); ?></option>
                                    </select>
                                    <a ng-click="bulkActionMacro()" class="button"><?php _e('Apply'); ?></a>
                                </div>
                                <div class="nbc-macro-list">
                                    <table class="wp-list-table widefat fixed striped">
                                        <thead>
                                            <tr>
                                                <td id="cb" class="manage-column column-cb check-column">
                                                    <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All'); ?></label>
                                                    <input ng-click="updateMacroBulkList()" id="cb-select-all-1" type="checkbox">
                                                </td>
                                                <th class="manage-column"><?php esc_html_e('Macro content', 'web-to-print-online-designer'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="macro in adminLayout.macro.list">
                                                <th scope="row" class="check-column"><input ng-click="updateMacroBulkList()" type="checkbox" class="nbc-macro-bulk" value="{{macro.id}}"></th>
                                                <td>
                                                    <div ng-bind-html="macro.title | message_trusted" ></div>
                                                    <a ng-click="selectMacro(macro)"><?php esc_html_e('Edit', 'web-to-print-online-designer'); ?></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nbc-main">
                    <div class="nbc-main-panel" ng-class="adminLayout.activeNav == 'messages' ? 'active' : ''">
                        <div class="nbc-main-messages nbc-no-message" ng-show="user.connection == ''">
                            <span class="dashicons dashicons-admin-comments"></span>
                            <div class="nbc-no-message-notice"><?php esc_html_e( 'Select a conversation', 'web-to-print-online-designer' ); ?></div>
                        </div>
                        <div class="nbc-main-messages" ng-show="user.connection != ''">
                            <div class="nbc-messages-wrap">
                                <div class="nbc-messages-inner nbc-pf-scroll">
                                    <div class="nbc-message-container" ng-repeat="message in messages">
                                        <div class="nbc-message" ng-class="message.cssClass">
                                            <div class="nbc-flex">
                                                <div class="nbc-mr3">
                                                    <div class="nbc-avatar">
                                                        <img ng-src="{{message.avatar}}" />
                                                    </div>
                                                </div>
                                                <div class="nbc-message-inner">
                                                    <span class="nbc-message-edit">...
                                                        <div class="nbc-message-actions">
                                                            <div class="nbc-message-action" ng-click="initEditMsg(message.origin_content, message.msg_id )"><?php esc_html_e( 'Edit', 'web-to-print-online-designer' ); ?></div>
                                                            <div class="nbc-message-action delete" ng-click="deleteMsg(message.msg_id, $index )"><?php esc_html_e( 'Delete', 'web-to-print-online-designer' ); ?></div>
                                                        </div>
                                                    </span>
                                                    <div class="nbc-message-content" ng-bind-html="message.content | message_trusted"></div>
                                                </div>
                                            </div>
                                            <div class="nbc-message-time">{{message.time}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nbc-notify-typing" ng-show="status.partner_typing"><span>{{status.partner_name}}</span> <?php esc_html_e( 'is typing', 'web-to-print-online-designer' ); ?>{{status.typing_hellip}}</div>
                            <div class="nbc-messages-bottom">
                                <div class="nbc-messages-bottom-inner">
                                    <div class="nbc-textarea-wrap">
                                        <textarea ng-style="frontendLayout.textAreaCss" ng-keyup="changeCurrentMsg( $event )" ng-model="status.currentMsg" placeholder="<?php esc_html_e( 'Write a reply', 'web-to-print-online-designer' ); ?>"></textarea>
                                    </div>
                                    <div class="nbc-textarea-action giphy" ng-if="status.giphy.enable">
                                        <svg ng-click="status.giphy.show = !status.giphy.show; status.emoji.show = false; status.searchMedia = ''; initGiphy();" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><g><rect fill="none" height="24" width="24" x="0"/></g><g><g><rect fill="#999" height="6" width="1.5" x="11.5" y="9"/><path fill="#999" d="M9,9H6c-0.6,0-1,0.5-1,1v4c0,0.5,0.4,1,1,1h3c0.6,0,1-0.5,1-1v-2H8.5v1.5h-2v-3H10V10C10,9.5,9.6,9,9,9z"/><polygon fill="#999" points="19,10.5 19,9 14.5,9 14.5,15 16,15 16,13 18,13 18,11.5 16,11.5 16,10.5"/></g></g></svg>
                                        <div class="nbc-media-browser" ng-class="status.giphy.show ? 'active' : ''">
                                            <div class="nbc-media-browser-top">
                                                <div class="nbc-media-browser-search">
                                                    <input class="nbc-media-browser-search-input" type="text" placeholder="<?php esc_html_e( 'Search GIPHY..', 'web-to-print-online-designer' ); ?>" ng-keyup="updateGiphySearch( $event )" ng-model="status.searchMedia" />
                                                    <img class="nbc-powered-by" ng-src="{{status.images_url + 'giphy.png'}}"/>
                                                </div>
                                                <div class="nbc-media-browser-close" ng-click="status.giphy.show = false">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path fill="#999" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
                                                </div>
                                            </div>
                                            <div class="nbc-media-browser-body" nbc-pf-scroll>
                                                <div class="nbc-media-browser-body-col">
                                                    <img ng-click="pushMessage( img.src ); status.giphy.show = false;" ng-src="{{img.src}}" ng-repeat="img in status.giphy.images" ng-if="$odd"/>
                                                </div>
                                                <div class="nbc-media-browser-body-col">
                                                    <img ng-click="pushMessage( img.src );" ng-src="{{img.src}}" ng-repeat="img in status.giphy.images" ng-if="$even"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nbc-textarea-action emoji" ng-if="status.emoji.enable">
                                        <svg ng-click="status.emoji.show = !status.emoji.show; status.giphy.show = false; status.searchMedia = ''; initEmoji();" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><g><rect fill="none" height="24" width="24"/></g><g><g/><path fill="#999" d="M11.99,2C6.47,2,2,6.48,2,12c0,5.52,4.47,10,9.99,10C17.52,22,22,17.52,22,12C22,6.48,17.52,2,11.99,2z M8.5,8 C9.33,8,10,8.67,10,9.5S9.33,11,8.5,11S7,10.33,7,9.5S7.67,8,8.5,8z M12,18c-2.28,0-4.22-1.66-5-4h10C16.22,16.34,14.28,18,12,18z M15.5,11c-0.83,0-1.5-0.67-1.5-1.5S14.67,8,15.5,8S17,8.67,17,9.5S16.33,11,15.5,11z"/></g></svg>
                                        <div class="nbc-media-browser" ng-class="status.emoji.show ? 'active' : ''">
                                            <div class="nbc-media-browser-top">
                                                <div class="nbc-media-browser-search">
                                                    <input class="nbc-media-browser-search-input" type="text" placeholder="<?php esc_html_e( 'Search emoji..', 'web-to-print-online-designer' ); ?>" ng-keyup="updateEmojiSearch( $event )" ng-model="status.searchMedia" />
                                                </div>
                                                <div class="nbc-media-browser-close" ng-click="status.emoji.show = false">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path fill="#999" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
                                                </div>
                                            </div>
                                            <div class="nbc-media-browser-body emoji" nbc-pf-scroll>
                                                <div ng-repeat="cat in status.emoji.categories" ng-show="(cat.icons | filter: status.searchMedia).length">
                                                    <div class="nbc-emoji-cat-name">{{cat.name}}</div>
                                                    <div class="nbc-emoji-list">
                                                        <div ng-click="status.currentMsg = status.currentMsg + icon.char" ng-repeat="icon in cat.icons | filter: status.searchMedia" class="nbc-emoji">{{icon.char}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nbc-textarea-action" ng-click="pushMessage( status.currentMsg )" title="<?php esc_attr_e( 'Send', 'web-to-print-online-designer' ); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="nbc-main-messages-action-wrap" ng-show="user.connection != ''">
                            <div class="nbc-user-info-wrap">
                                <div class="nbc-user-info-head">
                                    <div class="nbc-user-info">
                                        <div class="nbc-user-info-name">{{status.conversationUser.name}}</div>
                                        <div class="nbc-user-info-email">
                                            <span class="dashicons dashicons-email"></span> {{status.conversationUser.email}}
                                        </div>
                                    </div>
                                    <div class="nbc-user-info-avatar">
                                        <img ng-src="{{status.conversationUser.avatar ? status.conversationUser.avatar : frontendLayout.defaultAvatar}}" />
                                    </div>
                                </div>
                                <div class="nbc-user-info-action">
                                    <div class="nbc-end-chat" ng-click="endChat()"><?php esc_html_e('End chat session', 'web-to-print-online-designer'); ?></div>
                                </div>
                            </div>
                            <div class="nbc-user-extra-info">
                                <div class="nbc-user-extra-info-item">
                                    <div class="nbc-user-extra-info-head"><span class="dashicons dashicons-admin-site"></span> <?php esc_html_e('IP Address', 'web-to-print-online-designer'); ?></div>
                                    <div class="nbc-user-extra-info-body">{{status.conversationUser.ip}}</div>
                                </div>
                                <div class="nbc-user-extra-info-item">
                                    <div class="nbc-user-extra-info-head"><span class="dashicons dashicons-visibility"></span> <?php esc_html_e('Current page', 'web-to-print-online-designer'); ?></div>
                                    <div class="nbc-user-extra-info-body">{{status.conversationUser.current_page}}</div>
                                </div>
                                <div class="nbc-user-extra-info-item">
                                    <div class="nbc-user-extra-info-head"><span class="dashicons dashicons-backup"></span> <?php esc_html_e('Elapsed time', 'web-to-print-online-designer'); ?></div>
                                    <div class="nbc-user-extra-info-body">{{status.conversationUser.chat_duration}}</div>
                                </div>
                            </div>
                            <div class="nbc-macro-wrap">
                                <select id="nbc-macro-select" style="width:100%;">
                                    <option value=""></option>
                                    <?php echo apply_filters( 'nbc_macros', '' ) ?>
                                </select>
                            </div>
                            <div class="nbc-monitor-design" ng-click="toggleSharedDesign()" ng-class="status.conversationUser.share_design ? 'active' : ''">
                                <div class="nbc-monitor-design-title">
                                    <?php esc_html_e('View shared design', 'web-to-print-online-designer'); ?>
                                </div>
                                <div class="nbc-monitor-design-icon">
                                    <span class="dashicons dashicons-welcome-view-site"></span>
                                </div>
                            </div>
                            <div class="nbc-attach-files">
                                <a class="nbc-attach-file trigger" ng-class="status.conversationUser.attach_files ? 'active' : ''" ng-click="adminLayout.showAttach = !adminLayout.showAttach;"><span class="dashicons dashicons-paperclip"></span> <span><?php esc_html_e('Attach files', 'web-to-print-online-designer'); ?></span></a>
                                <div class="nbc-attach-files-wrap nbc-pf-scroll" ng-class="adminLayout.showAttach ? 'active' : ''">
                                    <a class="nbc-attach-file" ng-repeat="file in status.conversationUser.attach_files" target="_blank" href="{{file.link}}"><span class="dashicons dashicons-paperclip"></span> <span class="nbc-attach-filename">{{file.name}}</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="nbc-main-panel-loading" ng-class="adminLayout.loadingMsg ? 'active' : ''">
                            <div class="nbc-main-panel-loading-inner" >
                                <svg class="circular" viewBox="25 25 50 50">
                                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="nbc-main-panel macros" ng-class="adminLayout.activeNav == 'macros' ? 'active' : ''">
                        <div>
                            <input ng-model="adminLayout.macro.currentMacro.title" type="text" class="nbc-macro-title" placeholder="<?php esc_attr_e('Macro title', 'web-to-print-online-designer'); ?>" />
                        </div>
                        <div class="nbc-macro-content">
                            <textarea ng-model="adminLayout.macro.currentMacro.content" rows="20" cols="40" placeholder="<?php esc_attr_e('Macro content..', 'web-to-print-online-designer'); ?>"">Can I help you!</textarea>
                        </div>
                        <div class="nbc-macro-edit-action">
                            <a ng-click="updateMacro()" class="button button-primary"><?php _e('Update'); ?></a>
                        </div>
                        <div class="nbc-main-panel-loading" ng-class="adminLayout.macro.loading ? 'active' : ''">
                            <div class="nbc-main-panel-loading-inner" >
                                <svg class="circular" viewBox="25 25 50 50">
                                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nbc-loading active" ng-class="status.loading ? 'active' : ''">
                <div class="nbc-loading-inner" >
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>
            <div class="nbc-shared-design-wrap" ng-class="adminLayout.openSharedDesignPop ? 'active' : ''">
                <div class="nbc-shared-design-inner">
                    <div class="nbc-shared-design-stages">
                        
                    </div>
                    <div class="nbc-shared-design-nav" ng-show="adminLayout.totalStage > 1">
                        <div class="nbc-shared-design-nav-item prev" onclick="NBCDESIGNMONITOR.loadPrevSharedStage()"><span class="dashicons dashicons-arrow-left"></span></div>
                        <div class="nbc-shared-design-nav-item next" onclick="NBCDESIGNMONITOR.loadNextSharedStage()"><span class="dashicons dashicons-arrow-right"></span></div>
                    </div>
                    <div class="nbc-shared-design-close" title="<?php esc_attr_e('Close popup', 'web-to-print-online-designer'); ?>" ng-click="adminLayout.openSharedDesignPop = false"><span class="dashicons dashicons-no-alt"></span></div>
                    <div class="nbc-main-panel-loading" ng-class="adminLayout.loadingDesign ? 'active' : ''">
                        <div class="nbc-main-panel-loading-inner" >
                            <svg class="circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>