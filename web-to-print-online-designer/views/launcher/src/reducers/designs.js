import {  
    GET_DESIGNS_SUCCESS, 
    GET_DESIGNS_FAILURE, 
    GET_DESIGNS_STARTED, 
    DELETE_DESIGNS,
    DESIGNS_PAGE_UNLOADED,
    GET_DESIGNS_ENDED,
    UPDATE_DESIGNS_CHECKED_ITEMS,
    TEMPORARY_TOGGLE_DESIGNS_STATUS
} from "../actionTypes";

const initialState = {
    loading: false,
    error: null,
    designs: [],
    all: 0,
    approved: 0,
    pending: 0,
    total: 0,
    totalpages: 0,
    checkedItems: []
}

export default function(state = initialState, action) {
    switch (action.type) {
        case GET_DESIGNS_STARTED:
            return {
                ...state,
                loading: true
            };
        case GET_DESIGNS_ENDED:
            return {
                ...state,
                loading: false
            };
        case GET_DESIGNS_SUCCESS:
            return {
                ...state,
                loading: false,
                error: null,
                designs: [...action.designs],
                all: action.all,
                approved: action.approved,
                pending: action.pending,
                total: action.total,
                totalpages: action.totalpages,
                checkedItems: []
            };
        case GET_DESIGNS_FAILURE:
            return {
                ...state,
                loading: false,
                error: action.payload.error
            };
        case UPDATE_DESIGNS_CHECKED_ITEMS:
            return {
                ...state,
                checkedItems: action.items
            };
        case TEMPORARY_TOGGLE_DESIGNS_STATUS: {
            const designs = JSON.parse(JSON.stringify(state.designs));
            designs.map( design => {
                if( design.id == action.design_id ){
                    design[action.propType] = action.value; 
                }
            });
            return {
                ...state,
                loading: true,
                designs
            };
        }
        case DELETE_DESIGNS: {
            const designs = JSON.parse(JSON.stringify(state.designs)),
            newDesigns = designs.filter( design => design.id != action.design_id );
            return {
                ...state,
                loading: false,
                designs: newDesigns
            };
        }
        case DESIGNS_PAGE_UNLOADED:
            return initialState;
        default:
            return state;
    }
}