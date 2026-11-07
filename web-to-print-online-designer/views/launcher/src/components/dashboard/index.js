import React, { Component } from "react";
import { withRouter } from 'react-router-dom';
import { connect } from 'react-redux';

import { getSummary, getReport } from '../../actions/dashboard';
import Summary from './summary';
import Overview from './overview';
class Dashboard extends Component {

    componentDidMount() {
        const { getSummary, getReport, history } = this.props;

        getSummary();
        getReport();
        this.unlisten = history.listen((location, action) => {
            getSummary();
            getReport();
        });
    }

    componentWillUnmount() {
        this.props.onUnload();
        this.unlisten();
    }

    render() {
        return (
            <div id="dashboard-widgets" className="metabox-holder">
                <div id="postbox-container-1" className="postbox-container">
                    <div className="meta-box-sortables">
                        <Summary />
                    </div>
                </div>
                <div id="postbox-container-2" className="postbox-container">
                    <div className="meta-box-sortables">
                        <Overview />
                    </div>
                </div>
            </div>
        );
    }
}

const mapDispatchToProps = ( dispatch ) => ({
    getSummary: () => dispatch( getSummary() ),
    getReport: () => dispatch( getReport() ),
    onUnload: () => dispatch({ type: 'DASHBOARD_PAGE_UNLOADED' })
});

export default withRouter(connect(null, mapDispatchToProps)(Dashboard));