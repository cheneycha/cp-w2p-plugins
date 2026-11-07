import React, { Component } from "react";
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import classNames from 'classnames';

import styles from './summary.css';
import SectionLoading from '../sectionLoading';

class Summary extends Component {

    render() {
        const { loading, designs, designers, withdraw, sales } = this.props;

        return (
            <div className="postbox">
                <h2 className="hndle"><span>{nbdl.langs.at_a_glance}</span></h2>
                <div className="_inside">
                    {loading ? <SectionLoading /> : (
                    <div className={styles.boxes}>
                        <div className={styles.box}>
                            <span className={classNames("dashicons", "dashicons-format-gallery", styles.box_icon)}></span>
                            <div className={styles.info}>
                                <div className={styles.value}>
                                    {designs.this_period ? designs.this_period : designs.this_month} {nbdl.langs.designs}
                                </div>
                                <div className={styles.detail}>
                                    {designs.this_period ? nbdl.langs.created_this_period : nbdl.langs.created_this_month} <span className={designs.class}>{designs.percent}%</span>
                                </div>
                            </div>
                        </div>
                        <div className={styles.box}>
                            <span className={classNames("dashicons", "dashicons-images-alt2", styles.awaiting, styles.box_icon)}></span>
                            <div className={styles.info}>
                                <div className={styles.value}>
                                    {designs.pending_designs} {nbdl.langs.designs}
                                </div>
                                <div className={styles.detail}>
                                    {nbdl.langs.awaiting_approval}
                                </div>
                            </div>
                        </div>
                        <div className={styles.box}>
                            <span className={classNames("dashicons", "dashicons-businessman", styles.box_icon)}></span>
                            <div className={styles.info}>
                                <div className={styles.value}>
                                    {designers.this_period ? designers.this_period : designers.this_month} {nbdl.langs.designers}
                                </div>
                                <div className={styles.detail}>
                                    {designers.this_period ? nbdl.langs.signup_this_period : nbdl.langs.signup_this_month} <span className={designers.class}>{designers.percent}%</span>
                                </div>
                            </div>
                        </div>
                        <div className={styles.box}>
                            <span className={classNames("dashicons", "dashicons-id", styles.awaiting, styles.box_icon)}></span>
                            <div className={styles.info}>
                                <div className={styles.value}>
                                    {designers.inactive} {nbdl.langs.designers}
                                </div>
                                <div className={styles.detail}>
                                    {nbdl.langs.awaiting_approval}
                                </div>
                            </div>
                        </div>
                        <div className={styles.box}>
                            <span className={classNames("dashicons", "dashicons-cart", styles.box_icon)}></span>
                            <div className={styles.info}>
                                <div className={styles.value}>
                                    {sales.this_period ? sales.this_period : sales.this_month} {nbdl.langs.designs}
                                </div>
                                <div className={styles.detail}>
                                    {sales.this_period ? nbdl.langs.sold_this_period : nbdl.langs.sold_this_month} <span className={sales.class}>{sales.percent}%</span>
                                </div>
                            </div>
                        </div>
                        <div className={styles.box}>
                            <span className={classNames("dashicons", "dashicons-tickets-alt", styles.awaiting, styles.box_icon)}></span>
                            <div className={styles.info}>
                                <div className={styles.value}>
                                    {withdraw.pending} {nbdl.langs.withdrawals}
                                </div>
                                <div className={styles.detail}>
                                    {nbdl.langs.awaiting_approval}
                                </div>
                            </div>
                        </div>
                    </div>)}
                </div>
            </div>
        );
    }
}

Summary.propTypes = {
    loading: PropTypes.bool.isRequired,
    error: PropTypes.string,
    designs: PropTypes.object,
    designers: PropTypes.object,
    withdraw: PropTypes.object,
    sales: PropTypes.object,
    history: PropTypes.object
};

const mapStateToProps = state => ({
    designs: state.summary.designs,
    designers: state.summary.designers,
    withdraw: state.summary.withdraw,
    sales: state.summary.sales,
    loading: state.summary.loading,
    error: state.summary.error
});

export default connect(mapStateToProps)(Summary);