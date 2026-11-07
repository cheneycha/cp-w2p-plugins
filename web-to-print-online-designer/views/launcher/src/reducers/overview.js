import {
    DASHBOARD_PAGE_UNLOADED,
    GET_REPORT_STARTED,
    GET_REPORT_SUCCESS,
    GET_REPORT_FAILURE
} from "../actionTypes";

const initialState = {
    loading: false,
    error: null,
    labels: [],
    datasets: []
}

export default function(state = initialState, action) {
    switch (action.type) {
        case GET_REPORT_STARTED:
            return {
                ...state,
                loading: true
            };
        case GET_REPORT_SUCCESS:
            return {
                ...state,
                loading: false,
                error: null,
                labels: action.labels,
                datasets: action.datasets
            };
        case GET_REPORT_FAILURE:
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