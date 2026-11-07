import React, { Component } from "react";
import styles from './leftSide.css';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import  classNames from 'classnames';
import { openModal, setModalContent } from '../../actions/modal';
import SendMailForm from '../modal/sendMailForm';
class LeftSideInView extends Component {
    sendMail(){
        const { openModal, setModalContent, infos } = this.props;
        setModalContent(<SendMailForm email={infos.email} designerId={infos.id} />);
        openModal();
    }
    render(){
        
        const { infos } = this.props;

        return (
            <div className={styles.profile}>
                <div className={styles['profile-icon']} >
                    <div className={styles['upload-image']}>
                        <img className={styles.avatar} src={infos.gravatar} />
                    </div>
                </div>
                <div>
                    <div className={classNames(styles['account-info'], styles['view'])}>
                        <h2>{infos.artist_name}</h2>
                    </div>
                    <div className={classNames(styles['account-info'], styles['view'])}>
                        <button className={classNames("button", "button-primary", styles['send-email'])} onClick={() => this.sendMail()}>
                            <span className="dashicons dashicons-email"></span>
                            {nbdl.langs.send_email}
                        </button>
                        <button className="button">
                            <span className={classNames("dashicons", {"dashicons-yes-alt": infos.enabled}, {"dashicons-dismiss": !infos.enabled})}></span>
                            {infos.enabled ? nbdl.langs.enabled : nbdl.langs.disabled}
                        </button>
                    </div>
                </div>
            </div>
        )
    }
}

LeftSideInView.propTypes = {
    infos: PropTypes.object,
    sendMail: PropTypes.func,
    openModal: PropTypes.func,
    setModalContent: PropTypes.func
};

const mapStateToProps = state => ({
    infos: state.designer.infos
});

const mapDispatchToProps = ( dispatch, ownProps ) => ({
    openModal: () => dispatch( openModal() ),
    setModalContent: content => dispatch( setModalContent( content ) )
});

export default connect(mapStateToProps, mapDispatchToProps)(LeftSideInView);