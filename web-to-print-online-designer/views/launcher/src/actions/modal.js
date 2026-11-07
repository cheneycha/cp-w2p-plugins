import { 
    OPEN_MODAL,
    CLOSE_MODAL,
    MODAL_CLOSING,
    SET_MODAL_CONTENT
} from "../actionTypes";

export const openModal = () => ({
    type: OPEN_MODAL
});

export const _closeModal = () => ({
    type: CLOSE_MODAL
});

export const closingModal = () => ({
    type: MODAL_CLOSING
});

export const setModalContent = content => ({
    type: SET_MODAL_CONTENT,
    content
})

export const closeModal = () => {
    return ( dispatch, getState ) => {
        
        if (window.closeModalTimer) {
            clearTimeout(window.closeModalTimer);
            window.closeModalTimer = null;
        }

        dispatch( closingModal() );

        window.closeModalTimer = setTimeout(() => {
            dispatch( _closeModal() );
            dispatch( setModalContent( null ) );
        }, 600);
    }
}