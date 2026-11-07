import {
    SET_DESIGNS_FILTER,
    DESIGNS_PAGE_UNLOADED
} from "../actionTypes";

const initialState = {
    status: '',
    page: 1,
    per_page: 20,
    filter: '',
    defaultFilter: '',
    user_id: '',
    product_id: ''
};

const buildFilter = state => {
    let filter = `?per_page=${state.per_page}&page=${state.page}`;
    if( state.status != '' ){
        filter += `&status=${state.status}`;
    }
    if( state.user_id != '' ){
        filter += `&user_id=${state.user_id}`;
    }
    if( state.product_id != '' ){
        filter += `&product_id=${state.product_id}`;
    }
    return filter;
};

initialState.defaultFilter = buildFilter( initialState );

export default function(state = initialState, action) {
    switch (action.type) {
        case SET_DESIGNS_FILTER: {
            const newState = {
                ...state,
                ...action.filters
            };
            newState.filter = buildFilter( newState );
            return {
                ...state,
                ...newState
            };
        }
        case DESIGNS_PAGE_UNLOADED:{
            return initialState
        }
        default: {
            return state;
        }
    }
};