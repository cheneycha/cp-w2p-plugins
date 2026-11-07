import React, { Component } from "react";
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from './notification.css';

class Notification extends Component {
    render(){
        const { message, type, show } = this.props;
        return(
            <div className={styles['notifications']}>
                <span>
                    {show && <div className={classNames(styles['notification-wrapper'])}>
                        <div className={classNames(styles['notification'], styles[type])} >
                            <div className={styles['notification-title']}>{type == 'success' ? nbdl.langs.success_title : nbdl.langs.error_title}</div> 
                            <div className="notification-content">{message}</div>
                        </div>
                    </div>}
                </span>
            </div>
        );
    }
}

Notification.propTypes = {
    message: PropTypes.string,
    type: PropTypes.string,
    show: PropTypes.bool,
};

const mapStateToProps = state => ({
    message: state.notification.message,
    type: state.notification.type,
    show: state.notification.show
});

export default connect(mapStateToProps)(Notification);