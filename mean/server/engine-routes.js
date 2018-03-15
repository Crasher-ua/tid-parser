const DbApi = require('./db-api');
const TidParser = require('./tid-parser');
const request = require('sync-request');

module.exports = function(router) {
    //TODO: make it working, not only compilable
    router.get('/check-list', ({query}, res) => {
        const range = 10;

        const offset = +query.offset;
        const {mode} = query;

        const error = validateQueryParameters(mode, offset);
        if (error) {
            return res.status(400).json({error});
        }

        const result = DbApi.getRowsData();

        if (!result) {
            return res.status(403).json({error: 'error: empty result'});
        }

        const {min, max, rows: rowsNumber} = result;

        let urls = [];
        let offsetDelta = range;

        if (mode === 'incremental') {
            //TODO: add ability to use incremental steps, not only list from A to B
            urls = listUrls(max + 1 + offset, range);
        }

        //TODO: check recheck
        if (mode === 'recheck') {
            let fromNumber = max - offset - range;

            while (urls.length < range) {
                const ids = listIds(fromNumber, range);
                fromNumber -= range;
                const result = DbApi.recheckData($ids);

                result.forEach(({id}) => {
                    const pos = ids.indexOf(id);

                    if (pos !== -1) {
                        ids.splice(pos, 1)
                    }
                });

                urls = [...urls, ...listUrlsFromIds(ids)]
                    .filter((element, id, arr) => arr.indexOf(element) === id);
            }

            offsetDelta = max - offset - fromNumber;
        }

        const successUrls = [];

        urls.forEach((url) => {
            const {body, statusCode} = fetchPage(url);

            if (statusCode !== 200) {
                return;
            }

            if (saveRelease(body, idFromUrl(url))) {
                successUrls.push(url);
            }
        });

        return res.json({
            successNumber: successUrls.length,
            successUrls: successUrls.join(','),
            allUrls: urls.join(','),
            offsetDelta,
            max
        });
    });

    function fetchPage(url) {
        const html = request('GET', url);

        return {
            statusCode: html.statusCode,
            body: html.body.toString('utf-8')
        };
    }

    function url(id) {
        return `http://www.trackitdown.net/track/artist/title/genre/${id}.html`;
    }

    function listIds(fromNumber, range) {
        const emptyArray = new Array(range).fill(1);
        return emptyArray.map((element, index) => fromNumber + index);
    }

    function listUrlsFromIds(ids) {
        return ids.map(url);
    }

    function listUrls(fromNumber, range) {
        return listUrlsFromIds(listIds(fromNumber, range));
    }

    function idFromUrl(url) {
        return /\/(\d+)\.html/.exec(url)[1];
    }

    function saveRelease(html, id) {
        const parsedData = TidParser(html);

        if (!parsedData) {
            return false;
        }

        const result = DbApi.saveRelease(Object.assign({id}, parsedData));
        return result !== false;
    }

    function validateQueryParameters(mode, offset) {
        if (!mode) {
            return 'error: no mode';
        }

        if (typeof offset !== 'number') {
            return 'error: no offset';
        }

        if (offset < 0) {
            return 'error: bad offset';
        }

        if (!['recheck', 'incremental'].includes(mode)) {
            return 'error: bad mode';
        }
    }
};
