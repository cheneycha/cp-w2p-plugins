import {
    GET_WITHDRAW_SUCCESS,
    GET_WITHDRAW_FAILURE,
    GET_WITHDRAW_STARTED,
    DELETE_WITHDRAW,
    WITHDRAW_PAGE_UNLOADED,
    GET_WITHDRAW_ENDED,
    UPDATE_WITHDRAW_CHECKED_ITEMS,
    TEMPORARY_TOGGLE_WITHDRAW_STATUS
} from "../actionTypes";

const initialState = {
    loading: false,
    error: null,
    withdraws: [],
    cancelled: 0,
    approved: 0,
    pending: 0,
    total: 0,
    totalpages: 0,
    checkedItems: []
};

export default function(state = initialState, action) {
    switch (action.type) {
        case GET_WITHDRAW_STARTED:
            return {
                ...state,
                loading: true
            };
        case GET_WITHDRAW_SUCCESS:
            return {
                ...state,
                loading: false,
                error: null,
                withdraws: [...action.withdraws],
                cancelled: action.cancelled,
                approved: action.approved,
                pending: action.pending,
                total: action.total,
                totalpages: action.totalpages,
                checkedItems: []
            };
        case GET_WITHDRAW_FAILURE:
            return {
                ...state,
                loading: false,
                error: action.payload.error
            };
        case GET_WITHDRAW_ENDED:
            return {
                ...state,
                loading: false
            };
        case UPDATE_WITHDRAW_CHECKED_ITEMS:
            return {
                ...state,
                checkedItems: action.items
            };
        case TEMPORARY_TOGGLE_WITHDRAW_STATUS: {
            const withdraws = JSON.parse(JSON.stringify(state.withdraws));
            withdraws.map( withdraw => {
                if( withdraw.id == action.withdraw_id ){
                    withdraw[action.propType] = action.value; 
                }
            });
            return {
                ...state,
                loading: true,
                withdraws
            };
        }
        case DELETE_WITHDRAW: {
            const withdraws = JSON.parse(JSON.stringify(state.withdraws)),
            newWithdraws = withdraws.filter( withdraw => withdraw.id != action.withdraw_id);
            return {
                ...state,
                loading: false,
                withdraws: newWithdraws
            };
        }
        case WITHDRAW_PAGE_UNLOADED:
            return initialState;
        default:
            return state;
    }
}