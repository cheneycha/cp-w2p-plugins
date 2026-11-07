import { 
    OPEN_MODAL,
    CLOSE_MODAL,
    MODAL_CLOSING,
    SET_MODAL_CONTENT
} from "../actionTypes";

const initialState = {
    isOpen: false,
    closing: false,
    content: null
}

export default function(state = initialState, action) {
    switch (action.type) {
        case OPEN_MODAL:
            return {
                ...state,
                isOpen: true
            };
        case MODAL_CLOSING:
            return {
                ...state,
                closing: true
            };
        case CLOSE_MODAL:
            return {
                ...state,
                isOpen: false,
                closing: false
            };
        case SET_MODAL_CONTENT:
            return {
                ...state,
                content: action.content
            };
        default:
            return state;
    }
}