/* global require, process, console */
/* eslint-disable no-console */

require('babel-register');
require('babel-polyfill');

const express = require('express');
const app = express();
const bodyParser = require('body-parser');
const engineRoutes = require('./engine-routes');

app.use(bodyParser.urlencoded({extended: true}));
app.use(bodyParser.json());
app.use((req, res, next) => {
    res.setHeader('Access-Control-Allow-Origin', '*'); //TODO: only for localhost
    next();
});

const port = process.env.PORT || 8080;
const router = express.Router(); //eslint-disable-line new-cap

engineRoutes(router);

app.use('/api', router);
app.listen(port);

console.log(`Server application started on port ${port}`);

