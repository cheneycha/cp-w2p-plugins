import { 
    NOTIFICATION_SUCCESS,
    NOTIFICATION_FAILURE,
    NOTIFICATION_HIDDEN
} from "../actionTypes";

const initialState = {
    message: '',
    type: 'success',
    show: false
}

export default function(state = initialState, action) {
    switch (action.type) {
        case NOTIFICATION_SUCCESS: {
            return {
                message: action.message,
                type: 'success',
                show: true
            };
        }
        case NOTIFICATION_FAILURE:{
            return {
                message: action.message,
                type: 'failure',
                show: true
            };
        }
        case NOTIFICATION_HIDDEN: 
            return initialState;
        default: {
            return state;
        }
    }
}