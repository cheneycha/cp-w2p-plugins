import React from "react";
import ReactDOM from "react-dom";
import { HashRouter as Router } from 'react-router-dom';
import App from "./components/App.js";
import { Provider } from 'react-redux';
import configureStore from './configureStore';

const store = configureStore();

ReactDOM.render(
    <Provider store={store} >
        <Router>
            <App />
        </Router>
    </Provider>
, document.getElementById("app"));