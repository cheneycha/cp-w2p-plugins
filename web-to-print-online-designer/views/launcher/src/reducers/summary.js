import {
    DASHBOARD_PAGE_UNLOADED,
    GET_SUMMARY_STARTED,
    GET_SUMMARY_SUCCESS,
    GET_SUMMARY_FAILURE
} from "../actionTypes";

const initialState = {
    loading: false,
    error: null,
    designers: {},
    designs: {},
    sales: {},
    withdraw: {}
}

export default function(state = initialState, action) {
    switch (action.type) {
        case GET_SUMMARY_STARTED:
            return {
                ...state,
                loading: true
            };
        case GET_SUMMARY_SUCCESS:
            return {
                ...state,
                loading: false,
                error: null,
                designers: action.designers,
                designs: action.designs,
                withdraw: action.withdraw,
                sales: action.sales
            };
        case GET_SUMMARY_FAILURE:
            return {
                ...state,
                loading: false,
                error: action.payload.error
            };
        case DASHBOARD_PAGE_UNLOADED:
            return initialState;
        default:
            return state;
    }
}