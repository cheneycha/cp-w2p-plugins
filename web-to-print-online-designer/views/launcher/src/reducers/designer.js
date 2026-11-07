import { 
    GET_DESIGNER_STARTED,
    GET_DESIGNER_FAILURE,
    GET_DESIGNER_SUCCESS,
    DESIGNER_PAGE_UNLOADED,
    CHANGE_DESIGNER_INFO,
    UPDATE_DESIGNER_STARTED,
    UPDATE_DESIGNER_SUCCESS,
    UPDATE_DESIGNER_FAILURE,
    GET_DESIGNER_STATS_STARTED,
    GET_DESIGNER_STATS_SUCCESS,
    GET_DESIGNER_STATS_FAILURE
} from "../actionTypes";

const initialState = {
    infos: {},
    stats: {},
    loading: true,
    error: null,
    statsLoading: true,
    statsError: null
}

export default function(state = initialState, action) {
    switch (action.type) {
        case GET_DESIGNER_STARTED:
        case UPDATE_DESIGNER_STARTED:
            return {
                ...state,
                loading: true
            };
        case UPDATE_DESIGNER_STARTED:
            return {
                ...state,
                loading: true
            };
        case GET_DESIGNER_FAILURE:
        case UPDATE_DESIGNER_FAILURE:
            return {
                ...state,
                loading: false,
                error: action.payload.error
            };
        case GET_DESIGNER_SUCCESS:
        case UPDATE_DESIGNER_SUCCESS:
            return {
                ...state,
                loading: false,
                error: null,
                infos: { ...action.infos }
            }
        case CHANGE_DESIGNER_INFO:
            return {
                ...state,
                infos: { ...state.infos, ...action.infos }
            }
        case GET_DESIGNER_STATS_STARTED:
            return {
                ...state,
                statsLoading: true
            };
        case GET_DESIGNER_STATS_FAILURE:
            return {
                ...state,
                statsLoading: false,
                statsError: action.payload.error
            };
        case GET_DESIGNER_STATS_SUCCESS:
            return {
                ...state,
                statsLoading: false,
                statsError: null,
                stats: { ...action.stats }
            }
        case DESIGNER_PAGE_UNLOADED:
            return initialState;
        default:
            return state;
    }
}