import React, { useState } from "react";
import { connect } from 'react-redux';
import styles from './form.css';
import { closeModal } from '../../actions/modal';
import { sendMail } from '../../actions/designer';

function sendMailForm ( props ) {
    const { designerId, email, closeModal, sendMail } = props;
    const [subject, setSubject] = useState('');
    const [message, setMessage] = useState('');

    const _sendMail = () => {
        sendMail( designerId, subject, message );
        closeModal();
    }
    return(
        <div className={styles.modal_content}>
            <div className={styles.header}>
                {nbdl.langs.send_email}
                <button className={styles.close} onClick={() => props.closeModal()}></button>
            </div>
            <div className={styles.body}>
                <div className={styles.form_row}>
                    <label>{nbdl.langs.to}</label>
                    <input type="text" readOnly value={email} />
                </div>
                <div className={styles.form_row}>
                    <label>{nbdl.langs.subject}</label>
                    <input type="text"  value={subject} onChange={(e) => setSubject(e.target.value)} />
                </div>
                <div className={styles.form_row}>
                    <label>{nbdl.langs.message}</label>
                    <textarea onChange={(e) => setMessage(e.target.value)} value={message} rows="3">
                    </textarea>
                </div>
            </div>
            <div className={styles.footer}>
                <button className="button button-primary" onClick={_sendMail}>{nbdl.langs.send_email}</button>
            </div>
        </div>
    )
}

const mapDispatchToProps = ( dispatch ) => ({
    closeModal: () => dispatch( closeModal() ),
    sendMail: ( designerId, subject, message ) => dispatch( sendMail( designerId, subject, message ) ),
});

export default connect(null, mapDispatchToProps)(sendMailForm);