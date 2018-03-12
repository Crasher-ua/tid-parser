const express = require('express');
const app = express();
const bodyParser = require('body-parser');
const engineRoutes = require('./engine-routes');

app.use(bodyParser.urlencoded({extended: true}));
app.use(bodyParser.json());

const port = process.env.PORT || 8080;
const router = express.Router();

engineRoutes(router);

app.use('/api', router);
app.listen(port);

console.log(`Server application started on port ${port}`);

