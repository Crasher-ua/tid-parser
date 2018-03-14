const DbApi = require('./db-api');

module.exports = function(router) {
    router.get('/', (req, res) => res.json({message: 'I am working!'}));

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
        // $min=min($row['min'],intval(file_get_contents('min.txt')));

        let urls = [];
        let offsetDelta = range;

        // if(isset($save_min))file_put_contents('min.txt',$min);

        if (mode === 'incremental') {
            urls = listUrls(max + 1 + offset, range);
            /*$start=$max+1+$offset;for($i=0;$i<$incremental_steps;$i++)$urls[]=url($start+$i);*/
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
            /*$start=$max-1-$offset;for($i=0;$i<$range;$i++)$urls[]=url($start-$i);*/
        }

        /*
        if (mode === 'deepscan') {
            //забудь
            //$urls=list_urls($min-$range,$range);
            //$start=$min-1;for($i=0;$i<$range;$i++)$urls[]=url($start-$i);
            //$min-=$range;
            //$save_min=true;
        }
        */

        const successUrls = [];

        //TODO: check isSimpleType if we need it
        if (isSimpleType) {
            urls.forEach((url) => {
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
            success_number: successUrls.length,
            success_urls: successUrls.join(','),
            all_urls: urls.join(','),
            offset_delta: offsetDelta,
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
            let catalog_id = box('#trackDetailCatalogueNo', 0);

            if(!catalog_id) {
                catalog_id = '';
            } else {
                catalog_id = catalog_id.getPlainText();
            }

            title = `'${mysql_real_escape_string(title.trim())}'`;
            artist = `'${mysql_real_escape_string(artist.trim())}'`;
            genre = `'${mysql_real_escape_string(genre.trim())}'`;
            label = `'${mysql_real_escape_string(label.trim())}'`;
            date = `'${mysql_real_escape_string(date.trim())}'`;
            catalog_id = `'${mysql_real_escape_string(catalog_id.trim())}'`;

            const result = DbApi.saveRelease(id, title, artist, genre, label, date, catalog_id);
            return result !== false;
        }

        return false;
    }

    function validateQueryParameters(mode, offset) {
        if (!mode) {
            return 'error: no mode';
        }

        //if (!isset($_GET['drop'])) die('error: no drop');

        if (!offset) {
            return 'error: no offset';
        }

        //$drop = intval($_GET['drop']);
        //if($drop<0)       die('error: bad drop');

        if (offset < 0) {
            return 'error: bad offset';
        }

        //TODO: remove deepscan
        if (!['recheck', 'incremental', 'deepscan'].includes(mode)) {
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

    function mysql_real_escape_string(str) {
        return str;
    }
};
