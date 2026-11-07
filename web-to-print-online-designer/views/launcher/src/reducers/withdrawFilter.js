import {
    SET_WITHDRAW_FILTER,
    WITHDRAW_PAGE_UNLOADED
} from "../actionTypes";

const initialState = {
    status: '',
    page: 1,
    per_page: 20,
    filter: '',
    defaultFilter: ''
};

const buildFilter = state => {
    let filter = `?per_page=${state.per_page}&page=${state.page}`;
    if( state.status != '' ){
        filter += `&status=${state.status}`;
    }
    return filter;
};

initialState.defaultFilter = buildFilter( initialState );

export default function(state = initialState, action) {
    switch (action.type) {
        case SET_WITHDRAW_FILTER: {
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
        case WITHDRAW_PAGE_UNLOADED:{
            return initialState
        }
        default: {
            return state;
        }
    }
};