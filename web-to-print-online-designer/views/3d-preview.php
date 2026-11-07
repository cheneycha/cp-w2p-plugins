<?php if (!defined('ABSPATH')) exit; 
    $product_id         = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0;
    $nbes_settings      = get_post_meta( $product_id, '_nbes_settings', true );
    $enable_3d_preview  = false;
    if( $nbes_settings ){
        $_nbes_settings = unserialize( $nbes_settings );
        if( isset( $_nbes_settings['td_preview'] ) && $_nbes_settings['td_preview'] == 1 && $_nbes_settings['td_folder_name'] != '' && $_nbes_settings['td_custom_mesh_name'] != '' ){
            $enable_3d_preview  = true;
        }
    }
    if( !$enable_3d_preview ){
        esc_html_e('Please enable 3D preview and fill 3D folder name and custom mesh name!', 'web-to-print-online-designer');
        die();
    }
    $model_url  = NBDESIGNER_DATA_URL . '/3d-models/' . $_nbes_settings['td_folder_name'] . '/' . $_nbes_settings['td_folder_name'] . '.gltf';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
        <title>3D - Preview</title>
        <style>
            html, body {
                height: 100%;
                margin: 0;
                border-radius: 4px;
                overflow: hidden;
            }
            #c {
                width: 100%;
                height: 100%;
                display: block;
            }
            #c:focus {
                outline: none;
            }
        </style>
    </head>
    <body>
        <canvas id="c" tabindex="1" ></canvas>
        <script type="module">
            import NBD3DPreview from '<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/3d/'; ?>preview.js';
            const settings = {
                meshName: '<?php echo $_nbes_settings['td_custom_mesh_name']; ?>'
            };

            const mouseEventHandler = makeSendPropertiesHandler([
                'ctrlKey',
                'metaKey',
                'shiftKey',
                'button',
                'clientX',
                'clientY',
                'pageX',
                'pageY',
                'pointerType',
            ]);
            const wheelEventHandlerImpl = makeSendPropertiesHandler([
                'deltaX',
                'deltaY',
            ]);
            const keydownEventHandler = makeSendPropertiesHandler([
                'ctrlKey',
                'metaKey',
                'shiftKey',
                'keyCode',
            ]);

            function wheelEventHandler(event, sendFn) {
                event.preventDefault();
                wheelEventHandlerImpl(event, sendFn);
            }

            function preventDefaultHandler(event) {
                event.preventDefault();
            }

            function copyProperties(src, properties, dst) {
                for (const name of properties) {
                    dst[name] = src[name];
                }
            }

            function makeSendPropertiesHandler(properties) {
                return function sendProperties(event, sendFn) {
                    const data = {type: event.type};

                    copyProperties(event, properties, data);
                    sendFn(data);
                };
            }

            function touchEventHandler(event, sendFn) {
                const touches = [];
                const data = {type: event.type, touches};
                for (let i = 0; i < event.touches.length; ++i) {
                    const touch = event.touches[i];
                    touches.push({
                        pageX: touch.pageX,
                        pageY: touch.pageY,
                    });
                }
                sendFn(data);
            }

            const orbitKeys = {
                '37': true,  // left
                '38': true,  // up
                '39': true,  // right
                '40': true,  // down
            };
            function filteredKeydownEventHandler(event, sendFn) {
                const {keyCode} = event;
                if (orbitKeys[keyCode]) {
                    event.preventDefault();
                    keydownEventHandler(event, sendFn);
                }
            }

            let nextProxyId = 0;
            class ElementProxy {
                constructor(element, worker, eventHandlers) {
                    this.id = nextProxyId++;
                    this.worker = worker;
                    const sendEvent = (data) => {
                        this.worker.postMessage({
                            type: 'event',
                            id: this.id,
                            data,
                        });
                    };

                    worker.postMessage({
                        type: 'makeProxy',
                        id: this.id,
                    });
                    sendSize();
                    for (const [eventName, handler] of Object.entries(eventHandlers)) {
                        element.addEventListener(eventName, function(event) {
                            handler(event, sendEvent);
                        });
                    }

                    function sendSize() {
                        const rect = element.getBoundingClientRect();
                        sendEvent({
                            type: 'size',
                            left: rect.left,
                            top: rect.top,
                            width: element.clientWidth,
                            height: element.clientHeight,
                        });
                    }

                    window.addEventListener('resize', sendSize);
                }
            }

            let worker;

            let preview, offscreencanvas = false;
            function _postMessage( msg ){
                window.parent.postMessage( msg );
            }
            function receiveMessage( event ){
                if( event.origin == window.location.origin ){
                    if( event.data[0] && event.data[0] == 'update_design' ){
                        if( offscreencanvas ){
                            worker.postMessage({
                                type: 'update_design',
                                design: event.data[1]
                            })
                        }else{
                            preview.update_design(event.data[1], 'on_main');
                        }
                    }
                }
            }

            function startWorker(canvas) {
                canvas.focus();
                const offscreen = canvas.transferControlToOffscreen();
                worker = new Worker('<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/3d/'; ?>worker.js', {type: 'module'});

                const eventHandlers = {
                    contextmenu: preventDefaultHandler,
                    mousedown: mouseEventHandler,
                    mousemove: mouseEventHandler,
                    mouseup: mouseEventHandler,
                    pointerdown: mouseEventHandler,
                    pointermove: mouseEventHandler,
                    pointerup: mouseEventHandler,
                    touchstart: touchEventHandler,
                    touchmove: touchEventHandler,
                    touchend: touchEventHandler,
                    wheel: wheelEventHandler,
                    keydown: filteredKeydownEventHandler,
                };
                const proxy = new ElementProxy(canvas, worker, eventHandlers);
                worker.postMessage({
                    type: 'start',
                    canvas: offscreen,
                    canvasId: proxy.id,
                    model: '<?php echo $model_url; ?>',
                    settings: settings
                }, [offscreen]);

                worker.onmessage = function(e) {
                    if(e.data == 'loaded_3d_model'){
                        _postMessage( "loaded_3d_model" );
                    }
                }
            }

            function startMainPage(canvas) {
                preview = new NBD3DPreview();
                preview.init({canvas, inputElement: canvas, model: '<?php echo $model_url; ?>', settings: settings, callback: _postMessage});
            }

            function main() {
                const canvas = document.querySelector('#c');
                if (canvas.transferControlToOffscreen) {
                    offscreencanvas = true;
                    startWorker(canvas);
                } else {
                    startMainPage(canvas);
                }
            }

            main();

            window.addEventListener("message", receiveMessage, false);
        </script>
    </body>
</html>