<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="nbd-chat-app" ng-cloak 
    <?php if( isset( $in_editor ) && $in_editor ): ?>
        class="nbc-inside-editor" 
    <?php endif; ?>
>
    <div ng-controller="nbdChatCtrl" class="nbc-wrap">
        <div ng-hide="!frontendLayout.connected" ng-click="toggleChatPopup()" class="nbc-button <?php echo $enable_fb ? 'enable_fb' : ''; ?>" ng-class="frontendLayout.popupStatus ? 'open' : ''">
            <div class="nbc-button-inner">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nbc-unread-msg" ng-show="frontendLayout.unreadMsg && !( status.busyMode && frontendLayout.firstTime )">{{frontendLayout.unreadMsg}}</div>
            <div class="nbc-bubble-msg" ng-show="frontendLayout.bubbleSatus && !( status.busyMode && frontendLayout.firstTime )">
                <?php echo stripslashes(nbdesigner_get_option( 'nbdesigner_live_chat_greeting', 'Hi 👋👋. How can we help you?' )); ?>
                <span class="nbc-bubble-msg-close" ng-click="$event.stopPropagation();frontendLayout.bubbleSatus = false">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </span>
            </div>
        </div>
        <div class="nbc-popup-wrap" ng-class="frontendLayout.popupStatus ? 'open' : ''">
            <div class="nbc-popup-inner">
                <div class="nbc-popup-panels">
                    <div class="nbc-panel-intro">
                        <div class="nbc-popup-header"></div>
                        <div class="nbc-popup-title-wrap">
                            <div class="nbc-popup-title-inner"ng-if="frontendLayout.activeNav != 'faq'">
                                <div class="nbc-popup-title"><?php echo stripslashes(nbdesigner_get_option( 'nbdesigner_live_chat_popup_title', '' )); ?></div>
                                <div class="nbc-popup-title-status">
                                    {{status.activeMod > 0 ? '<?php esc_html_e( 'Online', 'web-to-print-online-designer' ); ?>' : '<?php esc_html_e( 'Offline', 'web-to-print-online-designer' ); ?>'}}
                                    <div ng-hide="status.activeMod == 0" title="{{status.busyMode && frontendLayout.firstTime ? '<?php esc_html_e( 'Busy', 'web-to-print-online-designer' ); ?>' : ''}}" class="nbc-mod-status" ng-class="status.busyMode && frontendLayout.firstTime ? 'busy' : ''"></div>
                                </div>
                            </div>
                            <div class="nbc-popup-title-inner faq" ng-if="frontendLayout.activeNav == 'faq'">
                                <div class="nbc-popup-title">
                                    <div ng-if="frontendLayout.faq.currentCatId == 0"><?php esc_html_e( 'Help Center', 'web-to-print-online-designer' ); ?></div>
                                    <div ng-if="frontendLayout.faq.currentCatId != 0" class="nbc-faq-cat-back" ng-click="browserFaqCat(frontendLayout.faq.currentCat, 'back')">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path fill="#fff" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
                                    </div>
                                    <div class="nbc-faq-top-cat-title" ng-if="frontendLayout.faq.currentCatId != 0" ng-bind-html="frontendLayout.faq.currentCatTitle | message_trusted"></div>
                                </div>
                                <div class="nbc-popup-title-status"></div>
                            </div>
                            <div class="nbc-popup-close" ng-click="toggleChatPopup()">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path fill="#fff" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="nbc-panel-content nbc-pf-scroll">
                        <div class="nbc-panel-content-intro" ng-hide="frontendLayout.activeNav == 'faq'">
                            <div><?php echo stripslashes( nbdesigner_get_option( 'nbdesigner_live_chat_welcome_msg', 'Ask us anything. We will reply as soon as possible.' ) ); ?></div>
                            <div class="nbc-list-mod">
                                <div class="nbc-mod" ng-repeat="mod in status.mods | limitTo: 5">
                                    <div class="nbc-mod-avatar">
                                        <img ng-src="{{mod.avatar}}" />
                                    </div>
                                    <div class="nbc-mod-name">{{mod.name}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="nbc-panel-content-intro" ng-show="frontendLayout.activeNav == 'faq'">
                            <div class="nbc-faq-intro"><?php esc_html_e( 'Hi! How can we help you?', 'web-to-print-online-designer' ); ?></div>
                            <div class="nbc-faq-search-wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                                <input type="text" ng-class="{'deactive': frontendLayout.faq.loading}" ng-model="frontendLayout.faq.search" placeholder="<?php esc_html_e( 'Search for articles or subjects...', 'web-to-print-online-designer' ); ?>" class="nbc-faq-search-input">
                            </div>
                        </div>
                        <div class="nbc-panel-content-intro-border">&nbsp;</div>
                        <div class="nbc-popup-panel messages" ng-class="frontendLayout.activeNav == 'messages' ? 'active' : ''">
                            <div ng-hide="status.busyMode && frontendLayout.firstTime">
                                <div class="nbc-message-container" ng-repeat="message in messages">
                                    <div class="nbc-message" ng-class="message.cssClass">
                                        <div class="nbc-flex">
                                            <div class="nbc-mr3">
                                                <div class="nbc-avatar">
                                                    <img ng-src="{{message.avatar}}" />
                                                </div>
                                            </div>
                                            <div class="nbc-message-inner">
                                                <div class="nbc-message-content" ng-bind-html="message.content | message_trusted"></div>
                                            </div>
                                        </div>
                                        <div class="nbc-message-time">{{message.time}}</div>
                                    </div>
                                </div>
                                <div ng-show="frontendLayout.showLoginFrom && !frontendLayout.joined">
                                    <div class="nbc-email">
                                        <div class="nbc-form-input-wrap">
                                            <input id="nbc-name" ng-change="validateLoginForm()" ng-model="frontendLayout.name" placeholder="<?php esc_html_e( 'Name', 'web-to-print-online-designer' ); ?>" />
                                            <label for="nbc-name"><?php esc_html_e( 'Name', 'web-to-print-online-designer' ); ?></label>
                                        </div>
                                        <div class="nbc-form-input-wrap">
                                            <input id="nbc-email" ng-change="validateLoginForm()" ng-model="frontendLayout.email" placeholder="<?php esc_html_e( 'Email', 'web-to-print-online-designer' ); ?>" />
                                            <label for="nbc-email"><?php esc_html_e( 'Email', 'web-to-print-online-designer' ); ?></label>
                                        </div>
                                        <div ng-click="frontendLayout.connecting = true; addUser();" ng-class="{'valid': frontendLayout.validEmail, 'connecting': frontendLayout.connecting }" class="nbc-start-conversation-btn">
                                            <?php esc_html_e( 'Start conversation', 'web-to-print-online-designer' ); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M12 6v3l4-4-4-4v3c-4.42 0-8 3.58-8 8 0 1.57.46 3.03 1.24 4.26L6.7 14.8c-.45-.83-.7-1.79-.7-2.8 0-3.31 2.69-6 6-6zm6.76 1.74L17.3 9.2c.44.84.7 1.79.7 2.8 0 3.31-2.69 6-6 6v-3l-4 4 4 4v-3c4.42 0 8-3.58 8-8 0-1.57-.46-3.03-1.24-4.26z"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div ng-show="status.busyMode && frontendLayout.firstTime">
                                <ng-include src="'nbc_email'"></ng-include>
                            </div>
                        </div>
                        <?php if( $show_fb_msg == 'yes' ): ?>
                        <div class="nbc-popup-panel facebook" ng-class="frontendLayout.activeNav == 'facebook' ? 'active' : ''">
                            <div class="nbc-message-container">
                                <div class="nbc-message outbound">
                                    <div class="nbc-flex">
                                        <div class="nbc-mr3">
                                            <div class="nbc-avatar">
                                                <svg height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="m15.997 3.985h2.191v-3.816c-.378-.052-1.678-.169-3.192-.169-3.159 0-5.323 1.987-5.323 5.639v3.361h-3.486v4.266h3.486v10.734h4.274v-10.733h3.345l.531-4.266h-3.877v-2.939c.001-1.233.333-2.077 2.051-2.077z"/></svg>
                                            </div>
                                        </div>
                                        <div class="nbc-message-inner">
                                            <div class="nbc-message-content">
                                                <?php esc_html_e( 'Reach us on Facebook! Start a conversation using the button below and we will try to reply as soon as possible.', 'web-to-print-online-designer' ); ?>
                                            </div>
                                            <a class="nbc-btn rounded" target="_blank" href="//<?php echo $fb_page_url; ?>"><?php esc_html_e( 'Open Facebook', 'web-to-print-online-designer' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if( $show_whatsapp_msg == 'yes' ): ?>
                        <div class="nbc-popup-panel whatsapp" ng-class="frontendLayout.activeNav == 'whatsapp' ? 'active' : ''">
                            <div class="nbc-message-container">
                                <div class="nbc-message outbound">
                                    <div class="nbc-flex">
                                        <div class="nbc-mr3">
                                            <div class="nbc-avatar">
                                                <svg height="682pt" viewBox="-23 -21 682 682.66669" width="682pt" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="m544.386719 93.007812c-59.875-59.945312-139.503907-92.9726558-224.335938-93.007812-174.804687 0-317.070312 142.261719-317.140625 317.113281-.023437 55.894531 14.578125 110.457031 42.332032 158.550781l-44.992188 164.335938 168.121094-44.101562c46.324218 25.269531 98.476562 38.585937 151.550781 38.601562h.132813c174.785156 0 317.066406-142.273438 317.132812-317.132812.035156-84.742188-32.921875-164.417969-92.800781-224.359376zm-224.335938 487.933594h-.109375c-47.296875-.019531-93.683594-12.730468-134.160156-36.742187l-9.621094-5.714844-99.765625 26.171875 26.628907-97.269531-6.269532-9.972657c-26.386718-41.96875-40.320312-90.476562-40.296875-140.28125.054688-145.332031 118.304688-263.570312 263.699219-263.570312 70.40625.023438 136.589844 27.476562 186.355469 77.300781s77.15625 116.050781 77.132812 186.484375c-.0625 145.34375-118.304687 263.59375-263.59375 263.59375zm144.585938-197.417968c-7.921875-3.96875-46.882813-23.132813-54.148438-25.78125-7.257812-2.644532-12.546875-3.960938-17.824219 3.96875-5.285156 7.929687-20.46875 25.78125-25.09375 31.066406-4.625 5.289062-9.242187 5.953125-17.167968 1.984375-7.925782-3.964844-33.457032-12.335938-63.726563-39.332031-23.554687-21.011719-39.457031-46.960938-44.082031-54.890626-4.617188-7.9375-.039062-11.8125 3.476562-16.171874 8.578126-10.652344 17.167969-21.820313 19.808594-27.105469 2.644532-5.289063 1.320313-9.917969-.664062-13.882813-1.976563-3.964844-17.824219-42.96875-24.425782-58.839844-6.4375-15.445312-12.964843-13.359374-17.832031-13.601562-4.617187-.230469-9.902343-.277344-15.1875-.277344-5.28125 0-13.867187 1.980469-21.132812 9.917969-7.261719 7.933594-27.730469 27.101563-27.730469 66.105469s28.394531 76.683594 32.355469 81.972656c3.960937 5.289062 55.878906 85.328125 135.367187 119.648438 18.90625 8.171874 33.664063 13.042968 45.175782 16.695312 18.984374 6.03125 36.253906 5.179688 49.910156 3.140625 15.226562-2.277344 46.878906-19.171875 53.488281-37.679687 6.601563-18.511719 6.601563-34.375 4.617187-37.683594-1.976562-3.304688-7.261718-5.285156-15.183593-9.253906zm0 0" fill-rule="evenodd"/></svg>
                                            </div>
                                        </div>
                                        <div class="nbc-message-inner">
                                            <div class="nbc-message-content">
                                                <?php esc_html_e( 'Reach us on WhatsApp! Start a conversation using the button below and we will try to reply as soon as possible.', 'web-to-print-online-designer' ); ?>
                                            </div>
                                            <a class="nbc-btn rounded" target="_blank" href="https://api.whatsapp.com/send?phone=<?php echo $whatsapp_phone; ?>"><?php esc_html_e( 'Open WhatsApp', 'web-to-print-online-designer' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if( $show_send_mail == 'yes' ): ?>
                        <div class="nbc-popup-panel email" ng-class="frontendLayout.activeNav == 'email' ? 'active' : ''">
                            <ng-include src="'nbc_email'"></ng-include>
                        </div>
                        <?php endif; ?>
                        <?php do_action( 'nbc_extra_panel' ); ?>
                    </div>
                </div>
                <div class="nbc-notify-typing" ng-show="status.partner_typing"><span>{{status.partner_name}}</span> <?php esc_html_e( 'is typing', 'web-to-print-online-designer' ); ?>{{status.typing_hellip}}</div>
                <div class="nbc-popup-bottom" ng-if="frontendLayout.activeNav == 'messages' && frontendLayout.joined && !(status.busyMode && frontendLayout.firstTime)">
                    <div class="nbc-popup-bottom-inner">
                        <div class="nbc-textarea-wrap">
                            <textarea ng-style="frontendLayout.textAreaCss" ng-keyup="changeCurrentMsg( $event )" ng-model="status.currentMsg" placeholder="<?php esc_html_e( 'Write a reply', 'web-to-print-online-designer' ); ?>"></textarea>
                        </div>
                        <div class="nbc-textarea-action giphy" ng-if="status.giphy.enable" ng-hide="status.currentMsg.length">
                            <svg ng-click="status.giphy.show = !status.giphy.show; status.emoji.show = false; status.searchMedia = ''; initGiphy();" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><g><rect fill="none" height="24" width="24" x="0"/></g><g><g><rect fill="#999" height="6" width="1.5" x="11.5" y="9"/><path fill="#999" d="M9,9H6c-0.6,0-1,0.5-1,1v4c0,0.5,0.4,1,1,1h3c0.6,0,1-0.5,1-1v-2H8.5v1.5h-2v-3H10V10C10,9.5,9.6,9,9,9z"/><polygon fill="#999" points="19,10.5 19,9 14.5,9 14.5,15 16,15 16,13 18,13 18,11.5 16,11.5 16,10.5"/></g></g></svg>
                            <div class="nbc-media-browser" ng-class="( status.giphy.show && status.currentMsg.length == 0 ) ? 'active' : ''">
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
                                        <input class="nbc-media-browser-search-input" type="text" placeholder="<?php esc_html_e( 'Search emoji..', 'web-to-print-online-designer' ); ?>" ng-keyup="updateEmojiSearch( $event )" ng-model="status.searchMedia" ng-model-options="{ debounce: 300 }" />
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
                        <div class="nbc-textarea-action design-monitor <?php if( !( isset( $in_editor ) && $in_editor ) ) echo 'outside-editor'; ?>" ng-click="toggleShareDesign()" ng-class="frontendLayout.shareDesign ? 'active' : ''" title="{{frontendLayout.shareDesign ? '<?php esc_html_e( 'Turn off share design', 'web-to-print-online-designer' ); ?>' : '<?php esc_html_e( 'Turn on share design', 'web-to-print-online-designer' ); ?>'}}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M21 3H3c-1.1 0-2 .9-2 2v3h2V5h18v14h-7v2h7c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM1 18v3h3c0-1.66-1.34-3-3-3zm0-4v2c2.76 0 5 2.24 5 5h2c0-3.87-3.13-7-7-7zm0-4v2c4.97 0 9 4.03 9 9h2c0-6.08-4.93-11-11-11z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="nbc-popup-nav">
                    <div class="nbc-popup-nav-item messages" ng-click="activeTab('messages')" ng-class="frontendLayout.activeNav == 'messages' ? 'active' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                    </div>
                    <?php if( $show_fb_msg == 'yes' ): ?>
                    <div class="nbc-popup-nav-item" ng-click="activeTab('facebook')" ng-class="frontendLayout.activeNav == 'facebook' ? 'active' : ''">
                        <svg height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg"><path fill="#999" d="m15.997 3.985h2.191v-3.816c-.378-.052-1.678-.169-3.192-.169-3.159 0-5.323 1.987-5.323 5.639v3.361h-3.486v4.266h3.486v10.734h4.274v-10.733h3.345l.531-4.266h-3.877v-2.939c.001-1.233.333-2.077 2.051-2.077z"/></svg>
                    </div>
                    <?php endif; ?>
                    <?php if( $show_whatsapp_msg == 'yes' ): ?>
                    <div class="nbc-popup-nav-item" ng-click="activeTab('whatsapp')" ng-class="frontendLayout.activeNav == 'whatsapp' ? 'active' : ''">
                        <svg height="682pt" viewBox="-23 -21 682 682.66669" width="682pt" xmlns="http://www.w3.org/2000/svg"><path fill="#999" d="m544.386719 93.007812c-59.875-59.945312-139.503907-92.9726558-224.335938-93.007812-174.804687 0-317.070312 142.261719-317.140625 317.113281-.023437 55.894531 14.578125 110.457031 42.332032 158.550781l-44.992188 164.335938 168.121094-44.101562c46.324218 25.269531 98.476562 38.585937 151.550781 38.601562h.132813c174.785156 0 317.066406-142.273438 317.132812-317.132812.035156-84.742188-32.921875-164.417969-92.800781-224.359376zm-224.335938 487.933594h-.109375c-47.296875-.019531-93.683594-12.730468-134.160156-36.742187l-9.621094-5.714844-99.765625 26.171875 26.628907-97.269531-6.269532-9.972657c-26.386718-41.96875-40.320312-90.476562-40.296875-140.28125.054688-145.332031 118.304688-263.570312 263.699219-263.570312 70.40625.023438 136.589844 27.476562 186.355469 77.300781s77.15625 116.050781 77.132812 186.484375c-.0625 145.34375-118.304687 263.59375-263.59375 263.59375zm144.585938-197.417968c-7.921875-3.96875-46.882813-23.132813-54.148438-25.78125-7.257812-2.644532-12.546875-3.960938-17.824219 3.96875-5.285156 7.929687-20.46875 25.78125-25.09375 31.066406-4.625 5.289062-9.242187 5.953125-17.167968 1.984375-7.925782-3.964844-33.457032-12.335938-63.726563-39.332031-23.554687-21.011719-39.457031-46.960938-44.082031-54.890626-4.617188-7.9375-.039062-11.8125 3.476562-16.171874 8.578126-10.652344 17.167969-21.820313 19.808594-27.105469 2.644532-5.289063 1.320313-9.917969-.664062-13.882813-1.976563-3.964844-17.824219-42.96875-24.425782-58.839844-6.4375-15.445312-12.964843-13.359374-17.832031-13.601562-4.617187-.230469-9.902343-.277344-15.1875-.277344-5.28125 0-13.867187 1.980469-21.132812 9.917969-7.261719 7.933594-27.730469 27.101563-27.730469 66.105469s28.394531 76.683594 32.355469 81.972656c3.960937 5.289062 55.878906 85.328125 135.367187 119.648438 18.90625 8.171874 33.664063 13.042968 45.175782 16.695312 18.984374 6.03125 36.253906 5.179688 49.910156 3.140625 15.226562-2.277344 46.878906-19.171875 53.488281-37.679687 6.601563-18.511719 6.601563-34.375 4.617187-37.683594-1.976562-3.304688-7.261718-5.285156-15.183593-9.253906zm0 0" fill-rule="evenodd"/></svg>
                    </div>
                    <?php endif; ?>
                    <?php if( $show_send_mail == 'yes' ): ?>
                    <div class="nbc-popup-nav-item" ng-hide="status.busyMode && frontendLayout.firstTime" ng-click="activeTab('email')" ng-class="frontendLayout.activeNav == 'email' ? 'active' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    </div>
                    <?php endif; ?>
                    <?php do_action( 'nbc_extra_nav' ); ?>
                </div>
            </div>
        </div>
        <script type="text/ng-template" id="nbc_email">
            <div class="nbc-email">
                <div class="nbc-email-intro">
                    <?php esc_html_e( 'Leave us an email! Fill out the form below to send us an email. We will try to reply as soon as possible.', 'web-to-print-online-designer' ); ?>
                </div>
                <div class="nbc-form-input-wrap">
                    <input id="nbc-name" ng-change="resetMailForm()" ng-model="frontendLayout.name" placeholder="<?php esc_html_e( 'Name', 'web-to-print-online-designer' ); ?>" />
                    <label for="nbc-name"><?php esc_html_e( 'Name', 'web-to-print-online-designer' ); ?></label>
                </div>
                <div class="nbc-form-input-wrap">
                    <input id="nbc-email" ng-change="resetMailForm()" ng-model="frontendLayout.email" placeholder="<?php esc_html_e( 'Email', 'web-to-print-online-designer' ); ?>" />
                    <label for="nbc-email"><?php esc_html_e( 'Email', 'web-to-print-online-designer' ); ?></label>
                </div>
                <div class="nbc-form-input-wrap">
                    <input id="nbc-email-content" ng-change="resetMailForm()" ng-model="frontendLayout.emailContent" placeholder="<?php esc_html_e( 'Message', 'web-to-print-online-designer' ); ?>" />
                    <label for="nbc-email-content"><?php esc_html_e( 'Message', 'web-to-print-online-designer' ); ?></label>
                </div>
                <div class="nbc-form-input-wrap nbc-mail-notice-wrap">
                    <div class="nbc-invalid-email" ng-show="!frontendLayout.validEmail"><?php esc_html_e( 'Invalid email format', 'web-to-print-online-designer' ); ?></div>
                    <div class="nbc-sent-email-sucess" ng-show="frontendLayout.sentEmailSucess"><?php esc_html_e( 'Email send successfully', 'web-to-print-online-designer' ); ?></div>
                </div>
                <div class="nbc-form-input-wrap">
                    <div id="nbc-send-mail" ng-click="sendMail()">
                        <?php esc_html_e( 'Send', 'web-to-print-online-designer' ); ?>
                    </div>
                </div>
            </div>
        </script>
    </div>
</div>
<?php 
    $recaptcha_key = nbdesigner_get_option( 'nbdesigner_v3_recaptcha_key', '' );
    if( nbdesigner_get_option( 'nbdesigner_enable_recaptcha_live_chat', 'no' ) == 'yes' && $recaptcha_key != '' && nbdesigner_get_option( 'nbdesigner_v3_recaptcha_secret_key', '' ) != '' ):
?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $recaptcha_key; ?>"></script>
<script type="text/javascript">
    window.nbl_recaptcha = '<?php echo $recaptcha_key; ?>';
</script>
<?php endif;