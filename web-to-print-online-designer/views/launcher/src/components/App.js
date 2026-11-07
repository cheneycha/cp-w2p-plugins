import React from "react";
import { Route } from 'react-router-dom';
import Designers from './designers';
import Designer from './designer';
import Withdraw from './withdraw';
import Dashboard from './dashboard';
import Designs from './designs';
import NavBarLink from './nav';
import Modal from './modal';
import Notification from './notification';
import styles from './app.css';

const App = () => {
    return (
        <div className={styles.wrap}>
            <h1>{nbdl.langs.designer_launcher}</h1>
            <h2 className="nav-tab-wrapper">
                <NavBarLink exact={true} to="/" >{nbdl.langs.dashboard}</NavBarLink>
                <NavBarLink to="/designs" >{nbdl.langs.designs}</NavBarLink>
                <NavBarLink to="/designers" >{nbdl.langs.designers}</NavBarLink>
                <NavBarLink to="/withdraws?status=pending">{nbdl.langs.withdraw}</NavBarLink>
            </h2>
            <div>
                <Route exact path="/" component={Dashboard} />
                <Route path="/designers" component={Designers} />
                <Route path="/designer/:id/:edit?"
                    render={props => <Designer key={props.match.params.edit} {...props} />} />
                <Route path="/withdraws" component={Withdraw} />
                <Route path="/designs" component={Designs} />
            </div>
            <Modal />
            <Notification />
        </div>
    );
}

export default App;