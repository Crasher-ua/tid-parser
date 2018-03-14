module.exports = DbApi();

//TODO: remove mocks
function DbApi() {
    return {
        getTableData,
        recheckData,
        saveRelease,
        getRowsData,
        getReleaseToGenres,
        getStatsCacheData,
        getStatsNumberData,
        getIndexData
    };

    function getTableData() {
        const oldSqlRequest = `SELECT *
            , IF(YEAR(STR_TO_DATE('date' '%M %d,%Y')) > YEAR(CURDATE()), YEAR(STR_TO_DATE('date', '%M %d,%Y')), DATE_FORMAT(STR_TO_DATE('date', '%M %d,%Y'), '%d/%m')) AS date_simple
            , IF(DATEDIFF(CURDATE(), 'added') < 2, 1, 0) AS recent
            FROM tid WHERE (genre = 'Drum & Bass' OR genre = 'Dubstep')
            AND YEAR(STR_TO_DATE('date', '%M %d,%Y')) > 2014
            AND DATEDIFF(CURDATE(), STR_TO_DATE('date', '%M %d,%Y')) <= 1
            ORDER BY genre, STR_TO_DATE('date', '%M %d,%Y')
        `;

        return someDb.get(oldSqlRequest);
    }

    function recheckData(ids) {
        return [
            {id: 123},
            {id: 456}
        ];

        /*const idsList = ids.join();
        const oldSqlRequest = `SELECT id FROM tid WHERE id IN (${idsList})`;

        return someDb.get(oldSqlRequest);*/
    }

    function saveRelease(id, title, artist, genre, label, date, catalog_id) {
        return true;
        /*const oldSqlRequest = `
            INSERT INTO tid(id, title, artist, genre, label, `date`, catalog_id, added)
            VALUES (${id}, ${title}, ${artist}, ${genre}, ${label}, ${date}, ${catalog_id}, NOW())
        `;

        return someDb.get(oldSqlRequest);*/
    }

    function getRowsData() {
        return {
            min: 13,
            max: 21,
            rows: 30
        };

        /*const oldSqlRequest = `SELECT COUNT(*) AS rows, MIN(id) AS min, MAX(id) AS max FROM tid`;
        return someDb.get(oldSqlRequest);*/
    }

    function getReleaseToGenres(limit_days, our_genres_tid) {
        const maxDate = limit_days + 1;
        const genres = our_genres_tid.join('\' OR genre=\'');

        const oldSqlRequest = `
            SELECT * FROM tid WHERE "
            DATEDIFF(CURDATE(), 'added') < ${maxDate}
            AND (genre = '${genres}')
            AND YEAR(STR_TO_DATE('date', '%M %d,%Y')) > 2014
            ORDER BY genre, STR_TO_DATE('date', '%M %d,%Y') DESC
        `;

        return someDb.get(oldSqlRequest);
    }

    function getStatsCacheData() {
        const oldSqlRequest = `SELECT id, genre FROM tid ORDER BY id`;
        return someDb.get(oldSqlRequest);
    }

    function getStatsNumberData() {
        const oldSqlRequest = `SELECT COUNT(*) AS rows, MAX(id) AS max FROM tid`;
        return someDb.get(oldSqlRequest);
    }

    function getIndexData() {
        const oldSqlRequest = `
            SELECT *,
            IF (DATEDIFF(CURDATE(),'added') < 2,1,0)
            AS recent FROM tid
            WHERE (genre = 'Drum & Bass' OR genre = 'Dubstep' OR genre = 'Breaks')
            AND YEAR(STR_TO_DATE('date','%M %d,%Y')) > 2014
            AND DATEDIFF(CURDATE(), STR_TO_DATE('date', '%M %d,%Y')) <= 1
            ORDER BY genre, STR_TO_DATE('date', '%M %d,%Y') DESC
        `;
        return someDb.get(oldSqlRequest);
    }
}
