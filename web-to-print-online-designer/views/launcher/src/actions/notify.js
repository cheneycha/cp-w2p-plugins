import { 
    NOTIFICATION_SUCCESS,
    NOTIFICATION_FAILURE,
    NOTIFICATION_HIDDEN
} from "../actionTypes";

export const notifySuccess = message => ({
    type: NOTIFICATION_SUCCESS,
    message
});

export const notifyFailure = error => ({
    type: NOTIFICATION_FAILURE,
    message: error
});

export const hideNotify = () => ({
    type: NOTIFICATION_HIDDEN
});