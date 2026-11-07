export const uploadMedia = ( width, height, callback ) => {
    var fileFrame;

    if ( fileFrame ) {
        fileFrame.open();
        return;
    }

    const fileStatesOptions = {
        library: wp.media.query({ type: 'image' }),
        multiple: false,
        title: nbdl.langs.select_crop_image,
        priority: 20,
        filterable: 'uploaded',
        autoSelect: true,
        suggestedWidth: parseInt( width ),
        suggestedHeight: parseInt( height )
    };

    const cropControl = {
        id: "control-id",
        params: {
            width: parseInt( width ),
            height: parseInt( height ),
            flex_width: false,
            flex_height: false
        }
    }

    cropControl.mustBeCropped = function (flexW, flexH, dstW, dstH, imgW, imgH) {
        if (true === flexW && true === flexH) {
            return false;
        }

        if (true === flexW && dstH === imgH) {
            return false;
        }

        if (true === flexH && dstW === imgW) {
            return false;
        }

        if (dstW === imgW && dstH === imgH) {
            return false;
        }

        if (imgW <= dstW) {
            return false;
        }

        return true;
    }

    const calculateImageSelectOptions = (attachment, controller) => {
        let xInit = parseInt( width );
        let yInit = parseInt( height );
        let flexWidth = false;
        let flexHeight = false;

        let ratio, xImg, yImg, realHeight, realWidth, imgSelectOptions;

        realWidth = attachment.get('width');
        realHeight = attachment.get('height');

        let control = controller.get('control');
        controller.set('canSkipCrop', !control.mustBeCropped(flexWidth, flexHeight, xInit, yInit, realWidth, realHeight));

        ratio = xInit / yInit;
        xImg = realWidth;
        yImg = realHeight;

        if (xImg / yImg > ratio) {
            yInit = yImg;
            xInit = yInit * ratio;
        } else {
            xInit = xImg;
            yInit = xInit / ratio;
        }

        imgSelectOptions = {
            handles: true,
            keys: true,
            instance: true,
            persistent: true,
            imageWidth: realWidth,
            imageHeight: realHeight,
            x1: 0,
            y1: 0,
            x2: xInit,
            y2: yInit
        };

        if (flexHeight === false && flexWidth === false) {
            imgSelectOptions.aspectRatio = xInit + ':' + yInit;
        }
        if (flexHeight === false) {
            imgSelectOptions.maxHeight = yInit;
        }
        if (flexWidth === false) {
            imgSelectOptions.maxWidth = xInit;
        }

        return imgSelectOptions;
    }

    const fileStates = [new wp.media.controller.Library(fileStatesOptions), new wp.media.controller.CustomizeImageCropper({
        imgSelectOptions: calculateImageSelectOptions,
        control: cropControl
    })];

    const mediaOptions = {
        title: nbdl.langs.select_image,
        button: {
            text: nbdl.langs.select_image,
            close: false
        },
        multiple: false
    };

    
    mediaOptions.states = fileStates;

    fileFrame = wp.media(mediaOptions);

    fileFrame.on('select', () => {
        fileFrame.setState('cropper');
    });

    fileFrame.on('cropped', croppedImage => {
        callback(croppedImage);
        fileFrame = null;
    });

    fileFrame.on('skippedcrop', () => {
        const selection = fileFrame.state().get('selection');

        const files = selection.map(attachment => {
            return attachment.toJSON();
        });

        const file = files.pop();

        callback(file);

        fileFrame = null;
    });

    fileFrame.on('close', () => {
        fileFrame = null;
    });

    fileFrame.open();
}

export const addUrlParam = ( url, param, value ) => {
    const _url = new URL( url ),
    query_string = _url.search,
    search_params = new URLSearchParams(query_string);
    search_params.append( param, value );
    _url.search = search_params.toString();
    return _url.toString();
}