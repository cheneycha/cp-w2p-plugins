import React, { Component } from "react";
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from './style.css';
import { closeModal } from '../../actions/modal';

class Modal extends Component {
    close( e ){
        const { closeModal } = this.props;
        closeModal();
    }

    render(){
        const { isOpen, closing, content } = this.props;

        const backdropClasses = classNames(
            styles.modal,
            styles.animated,
            {[styles.active]: isOpen},
            {
                [styles.fadeIn]: isOpen,
                [styles.fadeOut]: closing,
            }
        );

        const contentClasses = classNames(
            styles['modal-inner'],
            styles.animated,
            {
                [styles.fadeIn]: isOpen,
                [styles.fadeOut]: closing,
            }
        );

        return(
            <div>
                <div className={backdropClasses} onClick={() => this.close()}>
                    <div className={contentClasses} onClick={e => e.stopPropagation()}>
                        {content || null}
                    </div>
                </div>
            </div>
        )
    }
}

Modal.propTypes = {
    isOpen: PropTypes.bool,
    closing: PropTypes.bool,
    content: PropTypes.any,
    timer: PropTypes.any,
    close: PropTypes.func,
    closeModal: PropTypes.func
};

const mapStateToProps = state => ({
    isOpen: state.modal.isOpen,
    closing: state.modal.closing,
    content: state.modal.content
});

const mapDispatchToProps = ( dispatch ) => ({
    closeModal: () => dispatch( closeModal() )
});

export default connect(mapStateToProps, mapDispatchToProps)(Modal);