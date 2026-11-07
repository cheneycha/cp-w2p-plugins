import React, { useState } from "react";
import { connect } from 'react-redux';
import styles from './form.css';
import { closeModal } from '../../actions/modal';
import { toggleStatusAction } from '../../actions/withdraws';

function changeWithdrawNoteForm ( props ) {
    const { withdraw, closeModal, toggleStatusAction } = props;
    const [note, setNote] = useState(withdraw.note);

    const updateNote = () => {
        toggleStatusAction( withdraw.id, 'note', note );
        closeModal();
    }
    return(
        <div className={styles.modal_content}>
            <div className={styles.header}>
                {nbdl.langs.update_note}
                <button className={styles.close} onClick={() => props.closeModal()}></button>
            </div>
            <div className={styles.body}>
                <div className={styles.form_row}>
                    <textarea onChange={(e) => setNote(e.target.value)} value={note} rows="3">
                    </textarea>
                </div>
            </div>
            <div className={styles.footer}>
                <button className="button button-primary" onClick={updateNote}>{nbdl.langs.update_note}</button>
            </div>
        </div>
    )
}

const mapDispatchToProps = ( dispatch ) => ({
    closeModal: () => dispatch( closeModal() ),
    toggleStatusAction: ( withdraw_id, type, value ) => dispatch( toggleStatusAction( withdraw_id, type, value ) ),
});

export default connect(null, mapDispatchToProps)(changeWithdrawNoteForm);