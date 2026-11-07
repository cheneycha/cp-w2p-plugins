import { 
    GET_DESIGNER_STARTED,
    GET_DESIGNER_FAILURE,
    GET_DESIGNER_SUCCESS,
    CHANGE_DESIGNER_INFO,
    UPDATE_DESIGNER_STARTED,
    UPDATE_DESIGNER_SUCCESS,
    UPDATE_DESIGNER_FAILURE,
    GET_DESIGNER_STATS_STARTED,
    GET_DESIGNER_STATS_SUCCESS,
    GET_DESIGNER_STATS_FAILURE
} from "../actionTypes";

import axios from 'axios';
import { headers } from './util';
import { notifySuccess, notifyFailure, hideNotify } from './notify';

export const getDesigner = id => {
    return ( dispatch, getState ) => {

        dispatch(getDesignerStarted());

        axios.get(nbdl.rest_url + `designers/${id}`, { params:{}, headers: headers })
        .then(res => {
            dispatch(getDesignerSuccess(res.data ));
        })
        .catch(err => {
            dispatch(getDesignerFailure(err.message));
        });
    }
}

const getDesignerStarted = () => ({
    type: GET_DESIGNER_STARTED
});

const getDesignerFailure = error => ({
    type: GET_DESIGNER_FAILURE,
    payload: {
        error
    }
});

const getDesignerSuccess = infos => ({
    type: GET_DESIGNER_SUCCESS,
    infos
});

/* Get designer stats */

export const getDesignerStats = id => {
    return ( dispatch, getState ) => {

        dispatch(getDesignerStatsStarted());

        axios.get(nbdl.rest_url + `designers/${id}/stats`, { params:{}, headers: headers })
        .then(res => {
            dispatch(getDesignerStatsSuccess(res.data ));
        })
        .catch(err => {
            dispatch(getDesignerStatsFailure(err.message));
        });
    }
}

const getDesignerStatsStarted = () => ({
    type: GET_DESIGNER_STATS_STARTED
});

const getDesignerStatsFailure = error => ({
    type: GET_DESIGNER_STATS_FAILURE,
    payload: {
        error
    }
});

const getDesignerStatsSuccess = stats => ({
    type: GET_DESIGNER_STATS_SUCCESS,
    stats
});

/* Change infos */

export const changeInfo = infos => ({
    type: CHANGE_DESIGNER_INFO,
    infos
})

export const updateInfos = history => {
    return ( dispatch, getState ) => {
        
        dispatch(updateDesignerStarted());

        const infos = getState().designer.infos;

        axios.put(nbdl.rest_url + `designers/${infos.id}`, infos, { headers: headers })
        .then(res => {
            dispatch(updateDesignerSuccess(res.data ));
            history.push( `/designer/${infos.id}` );
        })
        .catch(err => {
            dispatch(updateDesignerFailure(err.message));
        });
    }
}

const updateDesignerStarted = () => ({
    type: UPDATE_DESIGNER_STARTED
});

const updateDesignerFailure = error => ({
    type: UPDATE_DESIGNER_FAILURE,
    payload: {
        error
    }
});

const updateDesignerSuccess = infos => ({
    type: UPDATE_DESIGNER_SUCCESS,
    infos
})

/* Send email to designer */

export const sendMail = ( id, subject, message ) => {
    return ( dispatch, getState ) => {
        axios.post(nbdl.rest_url + `designers/${id}/email`, { id, message, subject }, { headers: headers })
        .then(res => {
            dispatch(notifySuccess(
                res.data.message
            ));
            setTimeout(() => dispatch( hideNotify() ), 2e3);
        })
        .catch(err => {
            dispatch(notifyFailure(err.message));
        });
    }
}