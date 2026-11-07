import { 
    SET_DESIGNERS_FILTER,
    DESIGNERS_PAGE_UNLOADED
 } from "../actionTypes";

const initialState = {
    status: '',
    order: 'desc',
    orderby: 'registered',
    page: 1,
    per_page: 20,
    search: '',
    filter: '',
    defaultFilter: ''
};

const buildFilter = state => {
    let filter = `?per_page=${state.per_page}&page=${state.page}`;
    if( state.status != '' ){
        filter += `&status=${state.status}`;
    }
    if( state.orderby != '' ){
        filter += `&orderby=${state.orderby}&order=${state.order}`;
    }
    return filter;
};

initialState.defaultFilter = buildFilter( initialState );

export default function(state = initialState, action) {
    switch (action.type) {
        case SET_DESIGNERS_FILTER: {
            const newState = {
                ...state,
                ...action.filters
            };
            if( action.filterType == 'sort' ){
                newState.order = newState.order == 'asc' ? 'desc' : 'asc';
            }
            newState.filter = buildFilter( newState );
            return {
                ...state,
                ...newState
            };
        }
        case DESIGNERS_PAGE_UNLOADED:{
            return initialState
        }
        default: {
            return state;
        }
    }
}