import React, { Component } from "react";
import { connect } from 'react-redux';
import Filter from './filter';

class Filters extends Component {
    render(){
        const { total, totalApproved, totalPending, totalCancelled, list } = this.props;
        return (
            <div>
                <ul className="subsubsub" style={{marginBottom: '15px'}}>
                    {list != 'withdraws' && <Filter filter="all" count={total} list={list} >{nbdl.langs.all}</Filter>}
                    <Filter filter="approved" count={totalApproved} list={list} >{nbdl.langs.approved}</Filter>
                    <Filter filter="pending" count={totalPending} list={list} last={list == 'withdraws' ? false : true} >{nbdl.langs.pending}</Filter>
                    {list == 'withdraws' && <Filter filter="cancelled" count={totalCancelled} list={list} last={true} >{nbdl.langs.cancelled}</Filter>}
                </ul>
            </div>
        );
    }
}

const mapStateToProps = (state, ownProps) => ({
    total: state[ownProps.list].all,
    totalApproved: state[ownProps.list].approved,
    totalPending: state[ownProps.list].pending,
    totalCancelled: state[ownProps.list].cancelled
});

export default connect(mapStateToProps)(Filters);