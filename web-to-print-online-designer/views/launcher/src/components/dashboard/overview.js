import React, { useState } from "react";
import { connect } from 'react-redux';
import classNames from 'classnames';
import { Line } from 'react-chartjs-2';

import { getReport } from '../../actions/dashboard';
import styles from './overview.css';
import SectionLoading from '../sectionLoading';

function Overview ( props ) {
    const { loading, labels, datasets, getReport } = props;
    const [from, setFrom] = useState('');
    const [to, setTo] = useState('');
    const [reportBy, setReportBy] = useState('this_month');

    const getDate = (date) => date.getFullYear() + '-' + ( date.getMonth() + 1 ) + '-' + date.getDate();

    const setRange = ( e, type ) => {
        e.preventDefault();
        let date = new Date(),
        year = date.getFullYear(),
        month = date.getMonth(),
        _from, _to, last_month, firstDay, lastDay;
        switch(type){
            case 'year':
                _from = year + '-1-1';
                _to = year + '-12-31';
                break;
            case 'last_month':
                if( month == 0 ){
                    year -= 1;
                    last_month = 11;
                }else{
                    last_month = month - 1;
                }
                firstDay = new Date(year, last_month, 1);
                lastDay = new Date(date.getFullYear(), date.getMonth(), 0);
                _from = getDate( firstDay );
                _to = getDate( lastDay );
                break;
            case 'this_month':
                firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
                lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                _from = getDate( firstDay );
                _to = getDate( lastDay );
                break;
            case 'custom_range':
                _from = from;
                _to = to;
                break;
        }

        if( type != 'custom_range' ){
            setReportBy( type )
        }
        
        if( type != 'custom' ){
            getReport(_from, _to)
        }
    };

    return (
        <div className="postbox">
            <h2 className="hndle"><span>{nbdl.langs.overview}</span></h2>
            <div className="inside">
                {loading ? <SectionLoading /> : (
                <div className="main">
                    <div>
                        <a href="#" className={classNames(styles.range, {[styles.active]: reportBy == 'year'})} onClick={(e) => setRange(e, 'year')}>{nbdl.langs.year}</a> |&nbsp;
                        <a href="#" className={classNames(styles.range, {[styles.active]: reportBy == 'last_month'})} onClick={(e) => setRange(e, 'last_month')}>{nbdl.langs.last_month}</a> |&nbsp;
                        <a href="#" className={classNames(styles.range, {[styles.active]: reportBy == 'this_month'})} onClick={(e) => setRange(e, 'this_month')}>{nbdl.langs.this_month}</a> |&nbsp;
                        <a href="#" className={classNames(styles.range, {[styles.active]: reportBy == 'custom'})} onClick={(e) => setRange(e, 'custom')}>{nbdl.langs.custom}</a>
                        {reportBy == 'custom' && <div className={styles.time_range}>
                            {nbdl.langs.from} <input value={from} onChange={(e) => setFrom(e.target.value)} type="date"/>&nbsp;
                            {nbdl.langs.to} <input value={to} onChange={(e) => setTo(e.target.value)} type="date"/>&nbsp;
                            <button className="button" onClick={(e) => setRange(e, 'custom_range')}>{nbdl.langs.show}</button>
                        </div>}
                    </div>
                    <div className={styles.chart}>
                        <Line 
                            data={{labels, datasets}}
                            options={{
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    xAxes: [{
                                        type: 'time',
                                        scaleLabel: {
                                            display: false
                                        },
                                        gridLines: {
                                            display: false
                                        },
                                        ticks: {
                                            fontColor: '#aaa',
                                            fontSize: 11
                                        }
                                    }],
                                    yAxes: [{
                                        scaleLabel: {
                                            display: false
                                        },
                                        ticks: {
                                            fontColor: '#aaa'
                                        }
                                    }]
                                },
                                legend: {
                                    position: 'top',
                                    onClick: false
                                },
                                elements: {
                                    line: {
                                        tension: 0,
                                        borderWidth: 4
                                    },
                                    point: {
                                        radius: 5,
                                        borderWidth: 3,
                                        backgroundColor: '#fff',
                                        borderColor: '#fff'
                                    }
                                },
                                tooltips: {
                                    displayColors: false,
                                    callbacks: {
                                        label: function (tooltipItems, data) {
                                            let label = data.datasets[tooltipItems.datasetIndex].label || '';
                                            let customLabel = data.datasets[tooltipItems.datasetIndex].tooltipLabel || '';
                                            let prefix = data.datasets[tooltipItems.datasetIndex].tooltipPrefix || '';
                
                                            let tooltipLabel = customLabel ? customLabel + ': ' : label + ': ';
                
                                            tooltipLabel += prefix + tooltipItems.yLabel;
                
                                            return tooltipLabel;
                                        }
                                    }
                                }
                            }}
                        />
                    </div>
                </div>)}
            </div>
        </div>
    );
}

const mapStateToProps = state => ({
    labels: state.overview.labels,
    datasets: state.overview.datasets,
    loading: state.overview.loading,
    error: state.overview.error
});

const mapDispatchToProps = ( dispatch ) => ({
    getReport: (from, to) => dispatch( getReport(from, to) )
});

export default connect(mapStateToProps, mapDispatchToProps)(Overview);