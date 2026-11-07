import React, { Component } from "react";
import styles from './stats.css';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

class Stats extends Component {
    getCommision( infos ){
        let commision = '0';
        if( infos ){
            commision = infos.artist_commission_display;
        }
        return {__html: commision};
    }
    render(){
        const { infos, stats, loading, statsLoading } = this.props;
        return (
            <div className={styles.wrap}>
                {statsLoading ? (
                    <div className={styles.loading}>
                        <img src={`${nbdl.assets_images_url}spinner.svg`} />
                    </div>
                ) :
                (<React.Fragment>
                    <div className={styles.stat_summary}>
                        <h3>{nbdl.langs.designs}</h3>
                        <ul>
                            <li className={styles.total_designs}>
                                <span className={styles.count}>{stats.designs.total}</span>
                                <span className={styles.subhead}>{nbdl.langs.total_designs}</span>
                            </li>
                            <li className={styles.publish_designs}>
                                <span className={styles.count}>{stats.designs.publish}</span>
                                <span className={styles.subhead}>{nbdl.langs.publish_designs}</span>
                            </li>
                        </ul>
                    </div>
                    <div className={styles.stat_summary}>
                        <h3>{nbdl.langs.revenue}</h3>
                        <ul>
                            <li className={styles.designs_sold}>
                                <span className={styles.count}>{stats.revenue.sold}</span>
                                <span className={styles.subhead}>{nbdl.langs.designs_sold}</span>
                            </li>
                            <li className={styles.total_earning}>
                                <span className={styles.count} dangerouslySetInnerHTML={{__html: stats.revenue.earning}}></span>
                                <span className={styles.subhead}>{nbdl.langs.total_earning}</span>
                            </li>
                            <li className={styles.current_balance}>
                                <span className={styles.count} dangerouslySetInnerHTML={{__html: stats.revenue.balance}}></span>
                                <span className={styles.subhead}>{nbdl.langs.current_balance}</span>
                            </li>
                        </ul>
                    </div>
                    <div className={styles.stat_summary}>
                        <h3>{nbdl.langs.others}</h3>
                        <ul>
                            <li className={styles.registered_since}>
                                <span className={styles.count}>{loading ? '00/00/0000' : infos.registered}</span>
                                <span className={styles.subhead}>{nbdl.langs.registered_since}</span>
                            </li>
                            <li className={styles.designer_commission}>
                                <span className={styles.count} dangerouslySetInnerHTML={this.getCommision( infos )}></span>
                                <span className={styles.subhead}>{nbdl.langs.designer_commission}</span>
                            </li>
                        </ul>
                    </div>
                </React.Fragment>)}
            </div>
        );
    }
}

Stats.propTypes = {
    stats: PropTypes.object,
    infos: PropTypes.object,
    loading: PropTypes.bool,
    statsLoading: PropTypes.bool
};

const mapStateToProps = state => ({
    stats: state.designer.stats,
    infos: state.designer.infos,
    statsLoading: state.designer.statsLoading,
    loading: state.designer.loading
});

export default connect( mapStateToProps )(Stats);