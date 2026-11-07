var nbdChatApp = angular.module('nbdChatApp', []);
var nbc_delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();
nbdChatApp.controller('nbdChatCtrl', function( $scope, $timeout, $interval, NBDChatFactory ) {
    /* Admin-Layout */
    $scope.adminLayout = {
        activeNav: 'messages',
        openSharedDesignPop: false,
        loadingDesign: false,
        totalStage: 1,
        showStatusOpt: false,
        loadingMsg: false,
        showAttach: false,
        macro: {
            init: false,
            loading: false,
            list: [],
            bulkAction: '',
            bulkList: [],
            currentMacro: {}
        }
    };
    $scope.status = {
        loading: true,
        activeMod: 0,
        mods: [],
        busyMode: false,
        notify: false,
        sounds: [],
        currentMsg: '',
        currentMsgKey: undefined,
        partner_name: '',
        partner_typing: false,
        typing_hellip: '.',
        conversationUser: {},
        images_url: nbd_live_chat.assets_url + 'images/',
        searchMedia: '',
        giphy: {
            enable: nbd_live_chat.enable_giphy == 'yes' && nbd_live_chat.giphy_app_key != '',
            images: [],
            show: false,
            init: false
        },
        emoji: {
            enable: nbd_live_chat.enable_emoji == 'yes',
            show: false,
            init: false,
            categories: []
        }
    };
    $scope.toggleSharedDesign = function(){
        $scope.adminLayout.openSharedDesignPop = !$scope.adminLayout.openSharedDesignPop;
        if( $scope.adminLayout.openSharedDesignPop ){
            if( $scope.ref.designs ){
                var design_ref = $scope.ref.designs.orderByChild('create_at').startAt( Date.now() );
                design_ref.on('child_added', function (new_snap) {
                    var data = new_snap.val();
                    if( data ){
                        if( data.conversation_id == $scope.user.connection ){
                            $scope.adminLayout.loadingDesign = true;
                            $scope.loadSharedDesign( data );
                        }
                    }
                });
            }
        }else{
            if( $scope.ref.designs ) $scope.ref.designs.off();
        }
    };
    $scope.loadSharedDesign = function( data ){
        NBCDESIGNMONITOR.loadDesign( data, function( totalStage ){
            $scope.adminLayout.loadingDesign = false;
            $scope.adminLayout.totalStage = totalStage;
        });
    };
    $scope.loadPrevSharedStage = function(){
        //todo
    };
    $scope.loadNextSharedStage = function(){
        //todo
    };
    $scope.initMacro = function(){
        if( $scope.adminLayout.macro.init ) return;
        $scope.adminLayout.macro.loading = true;
        NBDChatFactory.post('nbc_get_macros', {}, function(data){
            var res = JSON.parse( data );
            if( res.flag == 1 ){
                $scope.adminLayout.macro.init = true;
                $scope.adminLayout.macro.list = res.list;
            }else{
                console.log('Fail to get macros!');
            }
            $scope.adminLayout.macro.loading = false;
        });
    };
    $scope.selectMacro = function( macro ){
        $scope.adminLayout.macro.currentMacro = macro;
    };
    $scope.updateMacro = function(){
        var formData = {
            id: $scope.adminLayout.macro.currentMacro.id,
            title: $scope.adminLayout.macro.currentMacro.title,
            content: $scope.adminLayout.macro.currentMacro.content
        };
        $scope.adminLayout.macro.loading = true;
        NBDChatFactory.post('nbc_update_macro', formData, function(data){
            var res = JSON.parse( data );
            if( res.flag == 1 ){
                $scope.adminLayout.macro.list = res.list;
                if( $scope.adminLayout.macro.currentMacro.id == 0 ){
                    $scope.adminLayout.macro.currentMacro.id = res.id;
                }
                angular.forEach( $scope.adminLayout.macro.list, function( macro ){
                    if( macro.id == $scope.adminLayout.macro.currentMacro.id ){
                        $scope.adminLayout.macro.currentMacro = macro;
                    }
                });
            } else {
                console.log('Fail to update macros!');
            }
            $scope.adminLayout.macro.loading = false;
        });
    };
    $scope.addMacro = function(){
        $scope.adminLayout.macro.currentMacro = {
            id: 0,
            title: '',
            content: ''
        }
    };
    $scope.updateMacroBulkList = function(){
        $timeout(function(){
            $scope.adminLayout.macro.bulkList = [];
            jQuery.each(jQuery('.nbc-macro-bulk'), function(index, input){
                if( jQuery(input).prop('checked') ){
                    $scope.adminLayout.macro.bulkList.push( jQuery(input).val() );
                }
            });
        }, 100);
    };
    $scope.bulkActionMacro = function(){
        if( $scope.adminLayout.macro.bulkAction == 'delete' ){
            var con = confirm( nbd_live_chat.langs.confirm_delete_macro );
            if( con == true ){
                if( $scope.adminLayout.macro.bulkList.length ){
                    var formData = {
                        ids: $scope.adminLayout.macro.bulkList
                    };
                    $scope.adminLayout.macro.loading = true;
                    NBDChatFactory.post('nbc_delete_macros', formData, function(data){
                        var res = JSON.parse( data );
                        if( res.flag == 1 ){
                            $scope.adminLayout.macro.list = res.list;
                        }else{
                            console.log('Fail to delete macros!');
                        }
                        $scope.adminLayout.macro.loading = false;
                    });
                }
            }
        }
    };

    $scope.users = [];
    $scope.messages = [];

    $scope.ref = {
        init: null,
        token: null
    };

    $scope.user = {
        status: 'offline',
        logged: true,
        chat_with: ''
    };
    $scope.get_token = function( callback ){
        NBDChatFactory.post('nbd_get_user_token', {}, function(data){
            var res = JSON.parse(data);
            if( res.flag == 1 ){
                $scope.ref.token = res.token;
                $scope.user.id = res.user_id;
                callback();
            }else{
                console.log('Fail to get token!');
            }
        });
    };
    $scope.initApp = function(){
        var config = {
            apiKey     : nbd_live_chat.api_key,
            authDomain : nbd_live_chat.project_id + '.firebaseapp.com',
            databaseURL: 'https://' + nbd_live_chat.project_id + '.firebaseio.com'
        };

        firebase.initializeApp(config);
        $scope.ref.init         = firebase.database().ref();
        $scope.ref.connect      = firebase.database().ref('.info/connected');
        $scope.ref.messages     = firebase.database().ref('messages');
        $scope.ref.users        = firebase.database().ref('users');
        $scope.ref.designs      = firebase.database().ref('designs');
        $scope.ref.sessions     = firebase.database().ref('sessions');
        $scope.ref.active_mod   = firebase.database().ref('active_mod');

        $scope.login();
    };
    $scope.login = function(){
        firebase.auth().signInWithCustomToken($scope.ref.token).then(function () {
            $scope.ref.users.once('value', function (snap) {
                var users = snap.val();
                if (users !== null) {
                    $scope._users = $scope.users;
                    $scope.users = [];
                    angular.forEach(users, function(user, user_id){
                        if( user.logged ) {
                            if( nbd_live_chat.is_admin ){
                                angular.forEach($scope._users, function(_user){
                                    if( _user.id == user_id ){
                                        user.last_msg = _user.last_msg ? _user.last_msg : '';
                                        user.last_msg_time = _user.last_msg_time ? _user.last_msg_time : '';
                                    }
                                });
                            }
                            $scope.users.push( user );
                        }
                    });
                    $scope.updateActiveMod();
                    $scope.getChatMode();
                }
                $scope.initUser( $scope.user.id );
            });

            if( !nbd_live_chat.is_admin ) {
                $scope.frontendLayout.connected = true;
            }
        }).catch(function (error) {
            console.error(error.code, error.message);
        });
    };
    $scope.logout = function(){
        if( $scope.ref.user ) {
            $scope.offline();
            $scope.ref.user.child('logged').set(false);
            $scope.ref.user.off();
            $scope.ref.users.off();
            $scope.ref.messages.off();
            $scope.ref.sessions.off();
            $scope.ref.designs.off();
        }
    };
    $scope.initUser = function( userId ){
        $scope.ref.user = $scope.ref.users.child( userId );
        $scope.ref.user.once('value', function( snap ) {
            var userData = snap.val();
            if ( !userData ) userData = {};
            $scope.getUser( userId, userData );
        });
    };
    $scope.startConnect = function(){
        $scope.purgeData();
        $scope.updateUserConnection();
        $scope.listenUsers();
        $scope.listenMessages();
        $scope.status.loading = false;
        if( !nbd_live_chat.is_admin ) {
            $scope.frontendLayout.connecting = false;
            $scope.frontendLayout.joined = true;
            $scope.frontendLayout.endChatSession = false;
        }
        $scope.updateChatApp();
    };
    $scope.getUser = function( userId, userData, callback ){
        if ( userData.id ) {
            $status = ( userData.is_mod && nbd_live_chat.is_admin ) ? 'offline' : 'online';
            $scope.ref.user.child('status').set( $status );
            $scope.ref.user.child('logged').set(true);
            $scope.ref.user.child('share_design').set(false);
            $scope.ref.user.child('connection').set('');
            ['ip', 'avatar', 'current_page'].forEach(function(val){
                $scope.ref.user.child( val ).set( $scope.user[val] );
            });
            $scope.user.name = $scope.user.name || userData.name;
            $scope.user.conversation_id = userData.conversation_id;
            $scope.user.chat_with = userData.chat_with ? userData.chat_with : '';
            $scope.user.connection = '';

            $scope.frontendLayout.email = userData.email;
            $scope.frontendLayout.validEmail = true;
            $scope.frontendLayout.name = userData.name;

            $scope.startConnect();
        } else {
            if( nbd_live_chat.user.logged ){
                $scope.addUser( userId );
            }else{
                $scope.frontendLayout.showLoginFrom = true;
                $scope.listenMode();
                if( $scope.status.busyMode ){
                    $scope.frontendLayout.listenMode = $interval(function(){
                        $scope.listenMode();
                        if( !$scope.status.busyMode ){
                            $interval.cancel( $scope.frontendLayout.listenMode );
                            $scope.frontendLayout.listenMode = undefined;
                        }
                    }, 1e4);
                }
            }
        }
        if( typeof callback == 'function' ) callback();
    };
    $scope.addUser = function( userId ){
        userId = userId || $scope.user.id;
        if( !nbd_live_chat.user.logged && !$scope.frontendLayout.validEmail ) return;
        var session = $scope.ref.sessions.push({
            user_id: userId,
            create_at: firebase.database.ServerValue.TIMESTAMP
        });

        var data = {
            id : userId,
            conversation_id: session.key,
            last_online: '',
            logged: true,
            status: ( $scope.user.is_mod && nbd_live_chat.is_admin ) ? 'offline' : 'online',
            name: $scope.user.name || $scope.frontendLayout.name,
            email: $scope.user.email || $scope.frontendLayout.email,
            ip: $scope.user.ip,
            current_page: $scope.user.current_page,
            avatar: $scope.user.avatar,
            is_mod: $scope.user.is_mod,
            share_design: false,
            chat_with: '',
            connection: ''
        };

        $scope.ref.user.set(data, function (error) {
            if (!error) {
                $scope.user.conversation_id = session.key;
                $scope.user.connection = '';
                $scope.user.share_design = false;
                $scope.frontendLayout.showLoginFrom = false;

                $scope.startConnect();
            }else{
                console.log(error);
            }
        });
    };
    $scope.getUserData = function( userId, callback ){
        $scope.ref.users.child(userId).once('value', function (snap) {
            var user = snap.val();
            if( typeof callback == 'function' ) callback(user);
        });
    };
    $scope.cleanUserData = function( user_id ){
        var user_ref = $scope.ref.users.child(user_id);
        user_ref.once('value', function (snap) {
            var user = snap.val();

            $scope.ref.messages.once('value', function (msg_snap) {
                var msgs = msg_snap.val();

                if ( msgs ) {
                    angular.forEach(msgs, function (msg_id, msg) {
                        if ( msg.user_id === user_id) {
                            $scope.ref.messages.child( msg_id ).remove();
                        }
                    });
                }
            });

            user_ref.remove();
        });
    };
    $scope.toggleStatus = function( status ){
        $scope.adminLayout.showStatusOpt = false;
        if( status == 'online' ){
            $scope.online();
        }else{
            $scope.offline();
        }
    };
    $scope.online = function(){
        $scope.user.status = 'online';
        $scope.user.connection = '';
        if( $scope.ref.user ){
            $scope.ref.user.child('status').set('online');
            if( $scope.user.is_mod ){
                $scope.status.activeMod++;
                $scope.ref.active_mod.set( $scope.status.activeMod );
            }
            $scope.playSound('online');
        }
    };
    $scope.offline = function(){
        $scope.user.status = 'offline';
        $scope.user.connection = '';
        $scope.status.conversationUser = {};
        if( $scope.ref.user ){
            $scope.ref.user.child('status').set('offline');
            $scope.ref.user.child('last_online').set(firebase.database.ServerValue.TIMESTAMP);
            if( $scope.user.is_mod ) {
                $scope.status.activeMod--;
                $scope.ref.active_mod.set( $scope.status.activeMod );

                if( $scope.user.chat_with ){
                    $scope.ref.users.child($scope.user.chat_with + '/chat_with').set('');
                }
            }
            $scope.playSound('offline');
        }
    };
    $scope.listenUsers = function(){
        $scope.ref.users.once('value', function (snap) {
            var users = snap.val();
            function updateUsers( users ){
                $scope._users = $scope.users;
                $scope.users = [];

                angular.forEach(users, function(user, user_id){
                    if( !nbd_live_chat.is_admin && user.id == $scope.user.id ){
                        $scope.user.chat_with = user.chat_with;
                    }
                });

                angular.forEach(users, function(user, user_id){
                    if( user.logged ){
                        if( user.logged ) {
                            if( nbd_live_chat.is_admin ){
                                angular.forEach($scope._users, function(_user){
                                    if( _user.id == user_id ){
                                        user.last_msg = _user.last_msg ? _user.last_msg : '';
                                        user.last_msg_time = _user.last_msg_time ? _user.last_msg_time : '';
                                    }
                                });
                            }
                            $scope.users.push( user );
                        }
                    }else{
                        if( nbd_live_chat.is_admin && $scope.user.connection == user.conversation_id ){
                            $scope.disconnectCurrentConversation( true );
                        }
                    }

                    if( !nbd_live_chat.is_admin && user.id == $scope.user.chat_with && !user.logged ){
                        $scope.ref.user.child('chat_with').set('');
                    }
                });
                if( nbd_live_chat.is_admin ) $scope.updateConversationUser();

                $scope.updateActiveMod();
                $scope.getChatMode();
                $scope.updateChatApp();
            }
            if ( users !== null ) {
                updateUsers( users );

                $scope.ref.users.on('value', function (snap) {
                    var users = snap.val();
                    if ( users !== null ) {
                        updateUsers( users );
                    }
                });
            }
        });
    };
    $scope.listenMode = function(){
        $scope.ref.users.once('value', function (snap) {
            var users = snap.val();
            $scope.users = [];
            if ( users !== null ) {
                $scope.status.activeMod = 0;
                angular.forEach(users, function(user, user_id){
                    if( user.is_mod && user.status == 'online' ) $scope.status.activeMod++;
                    if( user.logged ) $scope.users.push( user );
                });
                $scope.getChatMode();
                $scope.updateChatApp();
            }
        });
    };
    $scope.updateUserProp = function( user_id, prop, val ){
        angular.forEach( $scope.users, function(user){
            if( user.id == user_id ){
                user[prop] = val;
            }
        });
    };
    $scope.getUserProp = function( user_id, prop ){
        var val;
        angular.forEach( $scope.users, function(user){
            if( user.id == user_id ){
                val = user[prop];
            }
        });
        return val;
    };
    $scope.updateConversationUser = function(){
        if( $scope.status.conversationUser && $scope.status.conversationUser.id ){
            $scope.status.conversationUser = $scope.getConversationUser( $scope.status.conversationUser.id );
            if( $scope.status.conversationUser ){
                $scope.updateConversationDuration();
                if( !$scope.status.conversationUser.share_design ){
                    if( $scope.adminLayout.openSharedDesignPop && $scope.user.conversation_id ){
                        $scope.adminLayout.openSharedDesignPop = false;
                        alert( nbd_live_chat.langs.customer_stop_share_desgin );
                    }
                }
            }
        }
    };
    $scope.getConversationUser = function( user_id ){
        var _user;
        angular.forEach( $scope.users, function(user){
            if( user.id == user_id ){
                _user = user;
            }
        });
        return _user;
    };
    $scope.updateConversationDuration = function(){
        var conversation_id = $scope.status.conversationUser.conversation_id;
        if( conversation_id ){
            if( angular.isDefined( $scope.status.conversationUser.timer ) ) {
                $interval.cancel( $scope.status.conversationUser.timer );
                $scope.status.conversationUser.timer = undefined;
            }

            $scope.ref.sessions.child(conversation_id).once('value', function( snap ) {
                var conversation = snap.val();
                if( conversation ){
                    if( $scope.status.conversationUser ){
                        $scope.status.conversationUser.session_create_at = conversation.create_at;
                        $scope.status.conversationUser.timer = $interval(function(){
                            var now = new Date();
                            if( $scope.status.conversationUser ) $scope.status.conversationUser.chat_duration = $scope.getChatDuration( $scope.status.conversationUser.session_create_at, now.getTime() );
                        }, 1000);
                    }
                }
            });
        }
    };
    $scope.updateUserLastMessage = function( msg ){
        angular.forEach($scope.users, function(user){
            if( user.id == msg.user_id ){
                user.last_msg = $scope.truncateMessage( msg.content, 150, true );
                user.last_msg_time = $scope.getMsgTime( msg );
            }
        });
        $scope.updateChatApp();
    };
    $scope.listenMessages = function(){
        $scope.ref.messages.off();
        $scope.messages = [];
        if( !nbd_live_chat.is_admin ){
            $scope.messages.push({
                msg_id: 'greeting',
                time: '',
                content: nbd_live_chat.langs.greeting,
                origin_content: nbd_live_chat.langs.greeting,
                cssClass: 'outbound',
                avatar: $scope.frontendLayout.defaultAvatar
            });
        }

        $scope.ref.messages.once('value', function (snap) {
            var msgs = snap.val();
            if (msgs) {
                var last_msg_id;
                angular.forEach( msgs, function( msg, msg_id ){
                    if ( ( !nbd_live_chat.is_admin && $scope.user.conversation_id === msg.conversation_id ) || ( nbd_live_chat.is_admin && $scope.user.connection === msg.conversation_id ) ) {
                        $scope.addLocalMessage( msg, msg_id );
                    }

                    if( nbd_live_chat.is_admin && msg.user_id != $scope.user.id ){
                        $scope.updateUserLastMessage( msg );
                    }

                    last_msg_id = msg_id;
                } );
                $scope.adminLayout.loadingMsg = false;
                $scope.updateChatApp();
                $scope.listenNewMessage( last_msg_id );
            }else{
                $scope.adminLayout.loadingMsg = false;
                $scope.updateChatApp();
                $scope.listenNewMessage();
            }
        });
    };
    $scope.listenNewMessage = function( msg_id ){
        var msgs_ref = !msg_id ? $scope.ref.messages : $scope.ref.messages.startAt(null, msg_id);

        msgs_ref.on('child_added', function (new_snap) {
            var new_msg = new_snap.val(),
                new_snap_key = new_snap.key;
            if ( ( ( !nbd_live_chat.is_admin && $scope.user.conversation_id === new_msg.conversation_id ) || ( nbd_live_chat.is_admin && $scope.user.connection === new_msg.conversation_id ) ) && msg_id != new_snap_key ) {
                if( nbd_live_chat.is_admin && new_msg.user_id != $scope.user.id ){
                    $scope.notify( nbd_live_chat.langs.new_message_from + ' ' + new_msg.user_name, new_msg.content );
                    $scope.playSound('new-msg');
                }
                $scope.addLocalMessage( new_msg, new_snap_key );
            } else if( msg_id != new_snap_key ){
                var unread = $scope.getUserProp( new_msg.user_id, 'unreadMsg' );
                unread = Number.isInteger( unread ) ? unread : 0;
                $scope.updateUserProp( new_msg.user_id, 'unreadMsg', ++unread );
            }
            if( nbd_live_chat.is_admin && new_msg.user_id != $scope.user.id ){
                $scope.updateUserLastMessage( new_msg );
            }
        });

        var conversation_id = nbd_live_chat.is_admin ? $scope.user.connection : $scope.user.conversation_id;
        if( conversation_id != '' ){
            $scope.ref.sessions.child(conversation_id + '/typing').on('child_added', function (new_snap) {
                var key = new_snap.key, val = new_snap.val();
                if( key && key != $scope.user.id ){
                    $scope.status.partner_typing = true;
                    $scope.status.partner_name = val;
                    if( $scope.status.typing_hellip.length < 3 ){
                        $scope.status.typing_hellip += '.';
                    }else{
                        $scope.status.typing_hellip = '.';
                    }
                    $scope.updateChatApp();
                }
            });
            $scope.ref.sessions.child(conversation_id + '/typing').on('child_removed', function (new_snap) {
                var key = new_snap.key;
                if( key && key != $scope.user.id ){
                    $scope.status.partner_typing = false;
                    $scope.updateChatApp();
                }
            });
            $scope.ref.sessions.child(conversation_id + '/actions').on('child_added', function (new_snap) {
                var action = new_snap.val();
                if( action ){
                    $scope.updateLocalMessage( action.type, action.msg_id );
                }
            });
        }
    };
    $scope.getMsgTime = function( msg ){
        var now = new Date(),
        d = new Date( msg.create_at ),
        t = d.getHours() + ':' + (d.getMinutes() < 10 ? '0' : '') + d.getMinutes(),
        time = (d.toDateString() === now.toDateString()) ? t : t + ', ' + d.toLocaleDateString().replace(/\//g, '-');
        return time;
    };
    $scope.getChatDuration = function(start_time, now_time) {
        if (now_time === '' || start_time === '') {
            return '00:00:00'
        }

        var seconds = ((now_time - start_time) * 0.001) >> 0,
            minutes = seconds / 60 >> 0,
            hours = minutes / 60 >> 0;

        minutes = minutes % 60;
        seconds = seconds % 60;

        hours = (hours < 10) ? '0' + hours : hours;
        minutes = (minutes < 10) ? '0' + minutes : minutes;
        seconds = (seconds < 10) ? '0' + seconds : seconds;

        return hours + ':' + minutes + ':' + seconds;
    };
    $scope.addLocalMessage = function( msg, msg_id ){
        var time = $scope.getMsgTime( msg );
        content = $scope.sanitizeMessage( msg.content );
        $scope.messages.push({
            msg_id: msg_id,
            time: time,
            content: content,
            origin_content: msg.content,
            cssClass: ( msg.user_id == $scope.user.id || ( msg.is_mod && $scope.user.is_mod ) ) ? 'inbound' : 'outbound',
            avatar: msg.avatar != '' ? msg.avatar : $scope.frontendLayout.defaultAvatar
        });
        if( !nbd_live_chat.is_admin ){
            if( !$scope.frontendLayout.popupStatus && msg.is_mod ) $scope.frontendLayout.unreadMsg++;
        }
        $scope.updateChatApp();
        if( nbd_live_chat.is_admin ){
            $scope.updateMessagesScrollBar();
        }else if( !( $scope.status.busyMode && $scope.frontendLayout.firstTime ) ){
            $scope.updateMessagesScrollBar();
        }
    };
    $scope.updateLocalMessage = function( action, msg_id ){
        if( action == 'delete' ){
            var deleteIndex;
            $scope.messages.forEach(function(msg, index){
                if( msg.msg_id == msg_id ){
                    deleteIndex = index;
                }
            });
            if( typeof deleteIndex != 'undefined' ){
                $scope.messages.splice( deleteIndex, 1 );
            }
        }else{
            $scope.ref.messages.child( msg_id ).once('value', function(snap){
                var snap_msg = snap.val();
                $scope.messages.forEach(function(msg){
                    if( msg.msg_id == msg_id ){
                        msg.content = $scope.sanitizeMessage( snap_msg.content );
                        msg.origin_content = snap_msg.content;
                    }
                });
            });
        }
    };
    $scope.endChat = function(){
        if( nbd_live_chat.is_admin ){
            $scope.disconnectCurrentConversation();
        }else{
            $scope.frontendLayout.endChatSession = true;
            $scope.ref.user.child('/chat_with').set('');
            $scope.ref.users.off();
            $scope.ref.messages.off();
            $scope.ref.sessions.off();
        }
    };
    $scope.disconnectCurrentConversation = function( passive ){
        if( angular.isUndefined( passive ) ) $scope.ref.users.child($scope.user.chat_with + '/chat_with').set('');
        $scope.user.connection = '';
        if( angular.isDefined( $scope.status.conversationUser.timer ) ) {
            $interval.cancel( $scope.status.conversationUser.timer );
            $scope.status.conversationUser.timer = undefined;
        }
        $scope.status.conversationUser = {};
        NBCDESIGNMONITOR.destroyDesign();
        $scope.adminLayout.loadingDesign = false;
        $scope.adminLayout.totalStage = 1;
        $scope.adminLayout.openSharedDesignPop = false;
    };
    $scope.switchConversation = function( conversation_id, user_id ){
        if( $scope.user.connection == conversation_id ) return;
        
        $scope.adminLayout.loadingMsg = true;
        $scope.adminLayout.showAttach = false;
        $scope.ref.designs.off();
        NBCDESIGNMONITOR.destroyDesign();
        $scope.adminLayout.loadingDesign = false;
        $scope.adminLayout.totalStage = 1;
        $scope.adminLayout.openSharedDesignPop = false;

        if( $scope.user.connection != '' ){
            $scope.ref.sessions.child($scope.user.connection + '/typing').off();
            $scope.ref.sessions.child($scope.user.connection).off('child_added');
        }

        $scope.status.currentMsg = '';
        $scope.user.connection = conversation_id;
        $scope.user.chat_with = user_id;
        $scope.user.partner_name = '';
        $scope.user.partner_typing = false;
        $scope.updateUserProp( user_id, 'unreadMsg', 0 );
        $scope.status.conversationUser = $scope.getConversationUser( user_id );
        if( $scope.status.conversationUser ) $scope.updateConversationDuration();

        $scope.ref.user.child('connection').set(conversation_id);
        $scope.ref.users.child(user_id + '/chat_with').set($scope.user.id);

        $scope.ref.sessions.child(conversation_id + '/typing').remove();

        $scope.listenMessages();
    };
    $scope.updateTyping = function( user_id, typing ){
        angular.forEach($scope.users, function(user){
            if( user.user_id == user_id ){
                user.typing = typing;
            }
        });
    };
    $scope.updateActiveMod = function(){
        $scope.status.activeMod = 0;
        $scope.status.mods = [];
        angular.forEach($scope.users, function(user){
            if( user.is_mod && user.status == 'online' ) {
                $scope.status.activeMod++;
                $scope.status.mods.push( user );
            }
        });
        $scope.ref.active_mod.set( $scope.status.activeMod );
    };
    $scope.getChatMode = function(){
        $scope.status.busyMode = $scope.status.activeMod == 0 ? true : false;
        var guests = $scope.users.length - $scope.status.activeMod;
        if( $scope.frontendLayout.joined && !nbd_live_chat.is_admin ) guests -= 1;
        if( nbd_live_chat.max_guest != '' && guests >= parseInt( nbd_live_chat.max_guest ) ){
            $scope.status.busyMode = true;
        }
        if( !$scope.status.busyMode ) $scope.frontendLayout.firstTime = false;
    };
    $scope.pushMessage = function( content ){
        var conversation_id = nbd_live_chat.is_admin ? $scope.user.connection : $scope.user.conversation_id;
        if( $scope.ref.messages && conversation_id != '' ){
            if( angular.isUndefined( $scope.status.currentMsgKey ) ){
                $scope.ref.messages.push({
                    user_id: $scope.user.id,
                    conversation_id: conversation_id,
                    content: content,
                    create_at: firebase.database.ServerValue.TIMESTAMP,
                    avatar: $scope.user.avatar,
                    user_name: $scope.user.name,
                    is_mod: $scope.user.is_mod
                });
            }else{
                $scope.ref.messages.child( $scope.status.currentMsgKey + '/content' ).set( content );
                $scope.ref.sessions.child( conversation_id + '/actions' ).push({
                    type: 'edit',
                    msg_id: $scope.status.currentMsgKey
                });
            }
        }
        $scope.status.emoji.show = false;
        $scope.status.giphy.show = false;
        $scope.status.currentMsgKey = undefined;
        $scope.status.currentMsg = '';
    };
    $scope.initEditMsg = function( content, msg_id ){
        $scope.status.currentMsgKey = msg_id;
        $scope.status.currentMsg = content;
    };
    $scope.deleteMsg = function( msg_id ){
        $scope.ref.messages.child( msg_id ).remove();
        var conversation_id = nbd_live_chat.is_admin ? $scope.user.connection : $scope.user.conversation_id;
        $scope.ref.sessions.child( conversation_id + '/actions' ).push({
            type: 'delete',
            msg_id: msg_id
        });
    };
    $scope.updateUserConnection = function(){
        if ( !$scope.ref.user ) {
            return;
        }
        $scope.ref.connect.on('value', function (snap) {
            if ( snap.val() === true ) {
                var conversation_id = nbd_live_chat.is_admin ? $scope.user.connection : $scope.user.conversation_id;

                if( nbd_live_chat.is_admin || !$scope.user.is_mod ){
                    $scope.ref.user.child('status').onDisconnect().set('offline');
                    $scope.ref.user.child('logged').onDisconnect().set(false);
                    $scope.ref.user.child('last_online').onDisconnect().set(firebase.database.ServerValue.TIMESTAMP);
                    if( conversation_id != '' ) $scope.ref.sessions.child( conversation_id + '/typing/' + $scope.user.id ).onDisconnect().remove();
                }
            }
        });
    };
    $scope.sanitizeMessage = function( str ){
        var msg;

        var tagsToReplace = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;'
        };

        msg = str.replace(/[&<>]/g, function (i) {
            return tagsToReplace[i] || i;
        });

        pattern_line = /\n/gim;
        msg = msg.replace(pattern_line, '<br />');

        pattern_url = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
        msg = msg.replace(pattern_url, '<a href="$1" target="_blank">$1</a>');

        pattern_pseudo_url = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
        msg = msg.replace(pattern_pseudo_url, '$1<a href="http://$2" target="_blank">$2</a>');

        pattern_email = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
        msg = msg.replace(pattern_email, '<a href="mailto:$1">$1</a>');

        pattern_img = /(?:>)(https?:\/\/\S+(\.png|\.jpg|\.gif))/gim;
        msg = msg.replace(pattern_img, '><img src="$1" />');

        return msg;
    };
    $scope.truncateMessage = function truncate( str, n, useWordBoundary ){
        if (str.length <= n) { return str; }
        const subString = str.substr(0, n-1);
        return ( useWordBoundary ? subString.substr( 0, subString.lastIndexOf(" ") ) : subString ) + "&hellip;";
    };
    $scope.notify = function( title, msg, callback ){
        if ( !Notification ) return;

        title = $scope.truncateMessage( title, 45, true );
        msg = $scope.truncateMessage( msg, 180, true );

        function showNotify(){
            var notification = new Notification(title, {
                body: msg,
                icon: nbd_live_chat.assets_url + 'images/email.png'
            });

            if ( typeof callback == 'function' ){
                notification.onclick = function () {
                    callback();
                };
            }

            setTimeout(function () {
                notification.close();
            }, 4000);
        }

        if ( !( "Notification" in window ) ) {
            return;
        } else if ( Notification.permission === "granted" ) {
            showNotify();
        } else if ( Notification.permission !== 'denied' ) {
            Notification.requestPermission(function (permission) {
                if (!('permission' in Notification)) {
                    Notification.permission = permission;
                }
                if (permission === "granted") {
                    showNotify();
                }
            });
        }
    };
    $scope.purgeData = function( force ){
        var interval = force ? 0 : 3600;
        $scope.ref.users.once('value', function (snap) {
            var users = snap.val(), now = new Date();
            if ( users !== null ) {
                var conversations = [], delUsers = [], delConversations = [];

                angular.forEach(users, function(user, user_id){
                    var seconds = ((now.getTime() - user.last_online) * 0.001) >> 0;

                    if ( !user.logged && !user.is_mod && seconds >= interval ) {
                        conversations.push( user.conversation_id );
                        delUsers.push( user_id );
                        if( user.conversation_id ) delConversations.push( user.conversation_id );
                    }
                });

                angular.forEach(conversations, function(conversation_id){
                    $scope.clearConversationMessages( conversation_id );
                    $scope.clearConversationDesigns( conversation_id );
                });

                angular.forEach(delUsers, function(user_id){
                    $scope.ref.users.child( user_id ).remove();
                });

                angular.forEach(delConversations, function(conversation_id){
                    $scope.ref.sessions.child( conversation_id ).remove();
                });
            }
        });
    };
    $scope.clearConversationMessages = function( conversation_id ){
        $scope.ref.messages.once('value', function ( msgs_snap ) {
            var msgs = msgs_snap.val();
            if( msgs ){
                angular.forEach(msgs, function(msg, msg_id){
                    if( msg.conversation_id === conversation_id ) {
                        $scope.ref.messages.child( msg_id) .remove();
                    }
                });
            }
        });
    };
    $scope.clearConversationDesigns = function( conversation_id ){
        $scope.ref.designs.once('value', function ( designs_snap ) {
            var designs = designs_snap.val();
            if( designs ){
                angular.forEach(designs, function(design, design_id){
                    if( design.conversation_id === conversation_id ) {
                        $scope.ref.designs.child( design_id) .remove();
                    }
                });
            }
        });
    };
    $scope.checkNotify = function(){
        if (!("Notification" in window)) {
            return;
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {
                if (!('permission' in Notification)) {
                    Notification.permission = permission;
                }
            });
        }
    };
    $scope.initSounds = function(){
        ['connected', 'disconnected', 'online', 'offline', 'new-msg'].forEach(function(sound){
            var source = document.createElement('source'),
                filename = nbd_live_chat.assets_url + 'sounds/' + sound;

            $scope.status.sounds[sound] = document.createElement('audio');
            $scope.status.sounds[sound].setAttribute("muted", "muted");
            if ( $scope.status.sounds[sound].canPlayType( 'audio/mpeg;' ) ) {
                source.type = 'audio/mpeg';
                source.src = filename + '.mp3';
            } else {
                source.type = 'audio/ogg';
                source.src = filename + '.ogg';
            }
            $scope.status.sounds[sound].appendChild(source);
        });
    };
    $scope.playSound = function( sound ){
        if( nbd_live_chat.is_admin ) $scope.status.sounds[sound].play();
    };
    $scope.changeCurrentMsg = function( $event ){
        var conversation_id = nbd_live_chat.is_admin ? $scope.user.connection : $scope.user.conversation_id;
        if( !$event.shiftKey && $event.which == 13 && $scope.status.currentMsg != '' ){
            $scope.pushMessage( $scope.status.currentMsg );

            $scope.frontendLayout.textAreaCss = {}

            $scope.ref.sessions.child(conversation_id + '/typing/' + $scope.user.id).remove();
            return;
        }

        if( ['8', '9', '16', '17', '18', '91', '93', '224'].indexOf( $event.keyCode ) == -1 ){
            $scope.ref.sessions.child(conversation_id + '/typing/' + $scope.user.id).set($scope.user.name);
        }

        nbc_delay(function () {
            $scope.ref.sessions.child(conversation_id + '/typing/' + $scope.user.id).remove();
        }, 1300);

        var numOfLines = $scope.status.currentMsg.split(/\r\n|\r|\n/).length;
        $scope.frontendLayout.textAreaCss = {
            height: ( numOfLines * 20 + 30 ) + 'px'
        };
        $scope.updateScrollBar();
    };
    $scope.updateChatApp = function(){
        if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply(); 
    };
    $scope.updateScrollBar = function(){
        $timeout(function(){
            jQuery('.nbc-pf-scroll').nbcPerfectScrollbar('update');
        });
    };
    $scope.updateMessagesScrollBar = function(){
        jQuery('.nbc-pf-scroll').nbcPerfectScrollbar('update');
        $timeout(function(){
            jQuery('.nbc-panel-content.nbc-pf-scroll').scrollTop(jQuery('.nbc-panel-content.nbc-pf-scroll').prop( "scrollHeight" ));
            jQuery('.nbc-messages-inner.nbc-pf-scroll').scrollTop(jQuery('.nbc-messages-inner.nbc-pf-scroll').prop( "scrollHeight" ));
        }, 100);
    };
    $scope.initGiphy = function(){
        if( $scope.status.giphy.init ) return;
        NBDChatFactory.get('//api.giphy.com/v1/gifs/trending?api_key=' + nbd_live_chat.giphy_app_key + '&limit=10', function(data){
            if( data.data ){
                $scope.status.giphy.init = true;
                $scope.status.giphy.images = [];
                data.data.forEach(function(val){
                    $scope.status.giphy.images.push( { src: val.images.fixed_width.url, id: val.id } );
                });
            }
        });
    };
    $scope.searchGiphy = function(){
        var query = $scope.status.searchMedia.replace(/ /g, '+');
        NBDChatFactory.get('//api.giphy.com/v1/gifs/search?q=' + query + '&api_key=' + nbd_live_chat.giphy_app_key + '&limit=20', function(data){
            if( data.data ){
                $scope.status.giphy.init = true;
                $scope.status.giphy.images = [];
                data.data.forEach(function(val){
                    $scope.status.giphy.images.push( { src: val.images.fixed_width.url, id: val.id } );
                });
            }
        });
    };
    $scope.updateGiphySearch = function( $event ){
        if( $scope.status.searchMedia != '' ){
            if( $event.which == 13 ){
                $scope.searchGiphy();
            } else {
                nbc_delay(function () {
                    $scope.searchGiphy();
                }, 1e3);
            }
        }
    };
    $scope.updateEmojiSearch = function(){

    };
    $scope.initEmoji = function(){
        if( $scope.status.emoji.init ) return;
        $scope.status.emoji.categories.push({
            name: nbd_live_chat.langs.frequently_used,
            icons: [{"name":"thumbs up sign","char":"👍"},{"name":"thumbs down sign","char":"👎"},{"name":"loudly crying face","char":"😭"},{"name":"confused face","char":"😕"},{"name":"neutral face","char":"😐"},{"name":"smiling face with smiling eyes","char":"😊"},{"name":"smiling face with heart-shaped eyes","char":"😍"}]
        });
        NBDChatFactory.get('https://raw.githubusercontent.com/mediumhust/emoji.json/master/compact.json', function(data){
            if( data ){
                $scope.status.emoji.init = true;
                data.forEach(function(cat){
                    $scope.status.emoji.categories.push({
                        name: cat.name,
                        icons: cat.icons
                    });
                });
            }
        });
    };
    $scope.initHelper = function(){
        if( $scope.frontendLayout.faq.init ) return;
        $scope.frontendLayout.faq.loading = true;
        NBDChatFactory.post('nbf_get_live_chat_helper', {}, function(data){
            var res = JSON.parse( data );
            if( res.flag == 1 ){
                $scope.frontendLayout.faq.init = true;
                $scope.frontendLayout.faq.categories = res.categories;
                angular.forEach( $scope.frontendLayout.faq.categories, function(cat){
                    if( cat.id != 0 ){
                        var exist_parent_cat = false;
                        angular.forEach( $scope.frontendLayout.faq.categories, function(_cat){
                            if( _cat.id == cat.parent ){
                                exist_parent_cat = true;
                            }
                        });
                        if( !exist_parent_cat ){
                            cat.parent = 0;
                        }
                    }
                });
            }else{
                console.log('Fail to get helper!');
            }
            $scope.frontendLayout.faq.loading = false;
        });
    };
    $scope.browserFaqCat = function(cat, command){
        $scope.frontendLayout.faq.articleContent = '';
        $scope.frontendLayout.faq.search = '';

        if( angular.isUndefined( command ) ){
            $scope.frontendLayout.faq.currentCatId = cat.id;
            $scope.frontendLayout.faq.currentCat = cat;
            $scope.frontendLayout.faq.currentCatTitle = cat.title;
            if( cat.type == 'faq' ){
                $scope.frontendLayout.faq.loading = true;
                NBDChatFactory.post('nbf_get_faq_content', {fid: cat.id}, function(data){
                    var res = JSON.parse( data );
                    if( res.flag == 1 ){
                        $scope.frontendLayout.faq.articleContent = res.content;
                        $timeout(function(){
                            jQuery('.nbc-faq-article-content a').attr('target', '_blank');
                        });
                    }else{
                        console.log('Fail to get helper!');
                    }
                    $scope.frontendLayout.faq.loading = false;
                    $scope.updateScrollBar();
                });
            }
        }else{
            var parent_id = cat.parent;
            $scope.frontendLayout.faq.currentCatId = parent_id;
            angular.forEach( $scope.frontendLayout.faq.categories, function(_cat){
                if( _cat.id == parent_id ){
                    $scope.frontendLayout.faq.currentCat = _cat;
                    $scope.frontendLayout.faq.currentCatTitle = _cat.title;
                }
            });
            $scope.updateScrollBar();
        }
    };
    $scope.voteFaq = function(command){
        NBDChatFactory.post('nbf_vote_faq', {fid: $scope.frontendLayout.faq.currentCatId, type: command}, function(data){
            var res = JSON.parse( data );
            console.log(res);
        });
    };
    $scope.initChatApp = function(){
        ['ip', 'name', 'email', 'avatar', 'is_mod', 'current_page'].forEach(function(val){
            $scope.user[val] = nbd_live_chat.user[val];
        });
        if( nbd_live_chat.is_admin ) {
            $scope.checkNotify();
            $scope.initSounds();
            $scope.get_token( $scope.initApp );

            jQuery( '#nbc-macro-select' ).select2({
                placeholder: nbd_live_chat.langs.apply_macro,
                allowClear : true
            }).on('change', function () {
                var value = jQuery(this).val();
                if (value !== '' && value !== null) {
                    $scope.status.currentMsg = value;
                    $scope.pushMessage( $scope.status.currentMsg );
                }
            });

            jQuery(document).on('click', function( event ){
                var statusWrap = jQuery('.nbc-mod-status-options'),
                statusEl = jQuery('.nbc-mod-status');
                if ( ( statusWrap.has( event.target ).length == 0 && !statusWrap.is( event.target ) ) 
                     && ( statusEl.has( event.target ).length == 0 && !statusEl.is( event.target ) ) ){
                    $scope.adminLayout.showStatusOpt = false;
                    $scope.updateChatApp();
                }
            });

            jQuery(document).bind('keydown', function(e) {
                if( e.which == 27 ){
                    $scope.status.currentMsgKey = undefined;
                    $scope.status.currentMsg = '';
                }
            });
        } else {
            $scope.get_token( $scope.initApp );
        }

        /* FE */
        jQuery('.nbc-pf-scroll').nbcPerfectScrollbar();
        $scope.updateScrollBar();

        jQuery(document).on( 'nbd_pass_design_json', function(e, data){
            $scope.pushDesign( data );
        });

    };
    $scope.initChatApp();

    /* Frontend */
    $scope.frontendLayout = {
        activeNav: 'messages',
        popupStatus: false,
        bubbleSatus: true,
        connected: false,
        connecting: false,
        joined: false,
        firstTime: true,
        endChatSession: false,
        showLoginFrom: false,
        listenMode: undefined,
        textAreaCss: {},
        shareDesign: false,
        name: '',
        email: '',
        emailContent: '',
        validEmail: false,
        sentEmailSucess: false,
        defaultAvatar: nbd_live_chat.default_avatar,
        unreadMsg: 0,
        designs: [],
        faq: {
            init: false,
            categories: [],
            currentCat: {},
            currentCatId: 0,
            currentCatTitle: '',
            search: '',
            articleContent: '',
            loading: false
        }
    };
    $scope.toggleChatPopup = function(){
        $scope.frontendLayout.popupStatus = !$scope.frontendLayout.popupStatus;
        if( $scope.frontendLayout.popupStatus ) $scope.frontendLayout.unreadMsg = 0;
        $scope.frontendLayout.bubbleSatus = false;
    };
    $scope.activeTab = function( tab ){
        $scope.frontendLayout.activeNav = tab;
        if( tab == 'messages' ){
            $scope.updateMessagesScrollBar();
        }else{
            $scope.updateScrollBar();
        }
    };
    $scope.toggleShareDesign = function(){
        if( !nbd_live_chat.in_editor && !nbd_live_chat.trigger_outside_editor ){
            return;
        }
        var frame, win, doc;
        $scope.frontendLayout.shareDesign = !$scope.frontendLayout.shareDesign;
        if( $scope.frontendLayout.shareDesign ){
            if( $scope.ref.user ){
                $scope.ref.user.child('share_design').set(true);
            }
            jQuery( document ).triggerHandler( 'nbc_enable_share_design' );
            if( !nbd_live_chat.in_editor && nbd_live_chat.trigger_outside_editor ){
                frame = document.getElementById('onlinedesigner-designer'),
                win = frame.contentWindow,
                doc = frame.contentDocument ? frame.contentDocument: frame.contentWindow.document;
                win.jQuery( doc ).triggerHandler( 'nbc_enable_share_design' );
            }
        }else{
            if( $scope.ref.user ){
                $scope.ref.user.child('share_design').set(false);
            }
            jQuery( document ).triggerHandler( 'nbc_disable_share_design' );
            if( !nbd_live_chat.in_editor && nbd_live_chat.trigger_outside_editor ){
                frame = document.getElementById('onlinedesigner-designer'),
                win = frame.contentWindow,
                doc = frame.contentDocument ? frame.contentDocument: frame.contentWindow.document;
                win.jQuery( doc ).triggerHandler( 'nbc_disable_share_design' );
            }
        }
    };
    $scope.pushDesign = function( designData ){
        var design = JSON.stringify( designData );
        if( $scope.ref.designs ){
            var design = $scope.ref.designs.push({
                user_id: $scope.user.id,
                conversation_id: $scope.user.conversation_id,
                design: design,
                create_at: firebase.database.ServerValue.TIMESTAMP
            });
            $scope.frontendLayout.designs.push( design.key );
            if( $scope.frontendLayout.designs.length > 10 ){
                var first_design = $scope.frontendLayout.designs.shift();
                $scope.ref.designs.child( first_design ).remove();
            }
        }
    };
    $scope.validateMail = function( email ){
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    };
    $scope.validateLoginForm = function(){
        $scope.frontendLayout.validEmail = $scope.validateMail( $scope.frontendLayout.email );
    };
    $scope.resetMailForm = function(){
        $scope.frontendLayout.validEmail = true;
        $scope.frontendLayout.sentEmailSucess = false;
    };
    $scope.sendMail = function(){
        $scope.frontendLayout.validEmail = $scope.validateMail( $scope.frontendLayout.email );
        if( $scope.frontendLayout.validEmail && $scope.frontendLayout.emailContent != '' ){
            var sendData = {
                name: $scope.frontendLayout.name,
                email: $scope.frontendLayout.email,
                message: $scope.frontendLayout.emailContent
            };

            function _sendMail(){
                NBDChatFactory.post('nblc_send_mail', sendData, function(data){
                    var res = JSON.parse( data );
                    if( res.flag == 1 ){
                        $scope.frontendLayout.emailContent = '';
                        $scope.frontendLayout.sentEmailSucess = true;
                    }else{
                        console.log('Fail to send mail!');
                    }
                });
            }

            if( angular.isDefined( window.nbl_recaptcha ) ){
                grecaptcha.ready(function() {
                    grecaptcha.execute(window.nbl_recaptcha, {action: 'submit'}).then(function(token) {
                        sendData.token = token;
                        _sendMail();
                    });
                });
            }else{
                _sendMail();
            }
        }
    };
}).factory('NBDChatFactory', function($http){
    return {
        post : function(action, data, callback, progressCallback) {
            var formData = new FormData();
            formData.append("action", action);
            formData.append("nonce", nbd_live_chat.nonce);

            angular.forEach(data, function (value, key) {
                var keepDefault = [];
                if( typeof value != 'object' || _.includes(keepDefault, key) ){
                    formData.append(key, value);
                }else{
                    var keyName;
                    for ( var k in value ) {
                        if ( value.hasOwnProperty( k ) ) {
                            keyName = [key, '[', k, ']'].join('');
                            formData.append( keyName, value[k] );
                        }
                    }
                }
            });

            var config = {
                transformRequest: angular.identity,
                transformResponse: angular.identity,
                headers: {
                    'Content-Type': undefined
                }
            };

            $http.post(nbd_live_chat.ajax_url, formData, config).then(
                function(response) {
                    callback(response.data);
                },
                function(response) {
                    console.log(response);
                }
            );
        },
        get: function(url, callback) {
            $http.get(url).then(
                function success(response) {
                    callback( response.data );
                },
                function error(response) {
                    console.log(response);
                }
            );
        }
    }
}).filter('message_trusted', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}]).filter('userFilter', [function(){
    return function( input, userId ){
        var users = [];
        angular.forEach(input, function( user ){
            if( !(user.id == userId || user.is_mod || ( user.chat_with && user.chat_with != userId ) ) ){
                users.push( user );
            }
        });
        return users;
    };
}]).filter('faqFilter', [function(){
    return function( input, params ){
        var cats = [];
        angular.forEach(input, function( cat ){
            if( params.search != '' ){
                if( cat.title.toLowerCase().indexOf(params.search.toLowerCase()) >= 0 ){
                    cats.push( cat );
                }
            }else{
                if( cat.parent == params.currentCatId ){
                    cats.push( cat );
                }
            }
        });
        cats.sort(function(a, b){
            var type_a = a.type == 'cat' ? 1 : 0,
            type_b = b.type == 'cat' ? 1 : 0;
            if( type_a > type_b ) return -1;
            if( type_a < type_b ){
                return 1;
            }else{
                return a.id * 1 - b.id * 1;
            }
        });
        return cats;
    };
}]).directive("nbcPfScroll", function($timeout) {
    return {
        restrict: "A",
        link: function(scope, element) {
            $timeout(function(){
                jQuery(element).nbcPerfectScrollbar();
                if( jQuery(element).parent('.nbc-media-browser').find('.nbc-media-browser-search-input').length ){
                    jQuery(element).parent('.nbc-media-browser').find('.nbc-media-browser-search-input').on('keyup', function(){
                        $timeout(function(){
                            jQuery(element).nbcPerfectScrollbar('update');
                        }, 100);
                    });
                }
            })
        }
    };
});

jQuery(document).ready(function(){
    if( !nbd_live_chat.in_editor ){
        var appEl = document.getElementById('nbd-chat-app');
        angular.element(function() {
            angular.bootstrap(appEl, ['nbdChatApp']);
        });

        jQuery(document).on( 'trigger_live_chat_from_iframe', function(){
            jQuery('.design-monitor').removeClass('outside-editor');
            nbd_live_chat.trigger_outside_editor = true;
        });
    }
});