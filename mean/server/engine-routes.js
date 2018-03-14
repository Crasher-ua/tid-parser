const DbApi = require('./db-api');

module.exports = function(router) {
    //TODO: make it working, not only compilable
    router.get('/check-list', ({query}, res) => {
        const range = 10;
        const isSimpleType = false;

        const {offset, mode} = query;

        const error = validateQueryParameters(mode, offset);
        if (error) {
            return res.json({error});
        }

        const result = DbApi.getRowsData();

        if (!result) {
            return res.json({error: 'error: empty result'});
        }

        const {min, max, rows: rowsNumber} = result;
        // min = Math.min(min, fileGetContents('min.txt'));

        let urls = [];
        let offsetDelta = range;

        // if (saveMin)) filePutContents('min.txt', min);

        if (mode === 'incremental') {
            urls = listUrls(max + 1 + offset, range);
            //start = max + 1 + offset;
            //for (i = 0; i < incrementalSteps; i++) urls.push(url(start + i));
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
            //start = max - 1 - offset;
            //for (i = 0; i < range; i++) urls.push(url(start - i));
        }

        const successUrls = [];

        //TODO: check isSimpleType if we need it
        if (isSimpleType) {
            urls.forEach((url) => {
                //TODO: remove try/catch
                try {
                    if (saveRelease(getDomFromUrl($url), idFromUrl($url))) {
                        successUrls.push(url);
                    }
                } catch(error) {
                    console.log('catched error:', error);
                }
            });
        } else {
            urls.forEach((url) => {
                const {data, statusCode} = requestHtmlContent(url);
                if (statusCode !== 200) {
                    return;
                }

                if (saveRelease(getDomFromString(data), idFromUrl(url))) {
                    successUrls.push(url);
                }
            });
        }

        return res.json({
            successNumber: successUrls.length,
            successUrls: successUrls.join(','),
            allUrls: urls.join(','),
            offsetDelta,
            max
        });
    });

    function requestHtmlContent(url) {
        //TODO: remove mock
        const data = `<html>
            <body>
                <div>smth here</div>
            </body>
        </html>`;

        return {statusCode: 200, data};
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
        const box = html.getElement('#mainContentForPage', 0);

        if (box('h1', 0) !== null && box('#trackDetailArtistName', 0) !== null) {
            let title = box('h1', 0).getPlainText();
            let artist = box('#trackDetailArtistName', 0).getPlainText();
            let genre = box('#trackDetailGenreName', 0).getPlainText();
            let label = box('#trackDetailRecordlabelName', 0).getPlainText();
            let date = box('#trackDetailReleaseDate', 0).getPlainText();
            let catalogId = box('#trackDetailCatalogueNo', 0);

            if(!catalogId) {
                catalogId = '';
            } else {
                catalogId = catalogId.getPlainText();
            }

            title = `'${mysqlRealEscapeString(title.trim())}'`;
            artist = `'${mysqlRealEscapeString(artist.trim())}'`;
            genre = `'${mysqlRealEscapeString(genre.trim())}'`;
            label = `'${mysqlRealEscapeString(label.trim())}'`;
            date = `'${mysqlRealEscapeString(date.trim())}'`;
            catalogId = `'${mysqlRealEscapeString(catalogId.trim())}'`;

            const result = DbApi.saveRelease(id, title, artist, genre, label, date, catalogId);
            return result !== false;
        }

        return false;
    }

    function validateQueryParameters(mode, offset) {
        if (!mode) {
            return 'error: no mode';
        }

        //if (!drop) return 'error: no drop';

        if (!offset) {
            return 'error: no offset';
        }

        //if (drop < 0) return 'error: bad drop';

        if (offset < 0) {
            return 'error: bad offset';
        }

        if (!['recheck', 'incremental'].includes(mode)) {
            return 'error: bad mode';
        }
    }

    function getDomFromUrl(url) {
        return getElement();
    }

    function getDomFromString(url) {
        return getElement();
    }

    function getElement() {
        const box = function() {
            return getElement();
        };

        return Object.assign(box, {
            getElement,
            getPlainText: () => ''
        });
    }

    function mysqlRealEscapeString(str) {
        return str;
    }
};
