import {  
    GET_DESIGNERS_SUCCESS, 
    GET_DESIGNERS_FAILURE, 
    GET_DESIGNERS_STARTED, 
    DESIGNERS_PAGE_UNLOADED,
    UPDATE_CHECKED_ITEMS,
    TEMPORARY_TOGGLE_STATUS,
    GET_DESIGNERS_ENDED,
    BATCH_UPDATE_DESIGNERS_SUCCESS
} from "../actionTypes";

const initialState = {
    loading: false,
    error: null,
    designers: [],
    all: 0,
    approved: 0,
    pending: 0,
    total: 0,
    totalpages: 0,
    checkedItems: []
};

export default function(state = initialState, action) {
    switch (action.type) {
        case GET_DESIGNERS_STARTED:
            return {
                ...state,
                loading: true
            };
        case GET_DESIGNERS_ENDED:
            return {
                ...state,
                loading: false
            };
        case GET_DESIGNERS_SUCCESS:
            return {
                ...state,
                loading: false,
                error: null,
                designers: [...action.designers],
                all: action.all,
                approved: action.approved,
                pending: action.pending,
                total: action.total,
                totalpages: action.totalpages,
                checkedItems: []
            };
        case GET_DESIGNERS_FAILURE:
            return {
                ...state,
                loading: false,
                error: action.payload.error
            };
        case UPDATE_CHECKED_ITEMS: 
            return {
                ...state,
                checkedItems: action.items
            };
        case TEMPORARY_TOGGLE_STATUS: {
            const designers = JSON.parse(JSON.stringify(state.designers));
            designers.map( designer => {
                if( designer.id == action.designer_id ){
                    designer[action.statusType] = !designer[action.statusType];
                }
            });
            return {
                ...state,
                designers
            };
        }    
        case BATCH_UPDATE_DESIGNERS_SUCCESS: {
            const designers = JSON.parse(JSON.stringify(state.designers));
            designers.map( designer => {
                action.payload.designers.map( _designer => {
                    if( designer.id == _designer.id ){
                        designer.enabled = action.payload.enabled;
                    }
                });
            });
            return {
                ...state,
                designers
            };
        }
        case DESIGNERS_PAGE_UNLOADED:
            return initialState;
        default:
            return state;
    }
};
