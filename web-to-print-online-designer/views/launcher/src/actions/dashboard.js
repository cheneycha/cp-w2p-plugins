import {
    GET_SUMMARY_STARTED,
    GET_SUMMARY_SUCCESS,
    GET_SUMMARY_FAILURE,
    GET_REPORT_STARTED,
    GET_REPORT_SUCCESS,
    GET_REPORT_FAILURE
} from "../actionTypes";

import axios from 'axios';
import { headers } from './util';

/* ---------Get Summary--------- */

export const getSummary = () => {
    return ( dispatch, getState ) => {
        dispatch(getSummaryStarted());

        axios.get(nbdl.rest_url + 'report/summary', { params:{}, headers: headers })
        .then(res => {
            dispatch(getSummarySuccess(
                res.data.designers,
                res.data.designs,
                res.data.withdraw,
                res.data.sales
            ));
        })
        .catch(err => {
            dispatch(getSummaryFailure(err.message));
        });
    }
};

const getSummarySuccess = ( designers, designs, withdraw, sales ) => ({
    type: GET_SUMMARY_SUCCESS,
    designers,
    designs,
    withdraw,
    sales
});

const getSummaryStarted = () => ({
    type: GET_SUMMARY_STARTED
});

const getSummaryFailure = error => ({
    type: GET_SUMMARY_FAILURE,
    payload: {
        error
    }
});

/* ---------Get Report--------- */

export const getReport = (from, to) => {
    return ( dispatch, getState ) => {
        dispatch(getReportStarted());

        let params = {};
        if( typeof from != 'undefined' && typeof to != 'undefined' ){
            params = {
                from,
                to
            }
        }

        axios.get(nbdl.rest_url + 'report/overview', { params, headers })
        .then(res => {
            dispatch(getReportSuccess(
                res.data.labels,
                res.data.datasets
            ));
        })
        .catch(err => {
            dispatch(getReportFailure(err.message));
        });
    }
};

const getReportSuccess = ( labels, datasets ) => ({
    type: GET_REPORT_SUCCESS,
    labels,
    datasets
});

const getReportStarted = () => ({
    type: GET_REPORT_STARTED
});

const getReportFailure = error => ({
    type: GET_REPORT_FAILURE,
    payload: {
        error
    }
});