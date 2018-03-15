const jsdom = require('jsdom').jsdom;
const doc = jsdom();
const window = doc.defaultView;
const jQuery = require('jquery')(window);

module.exports = function(html) {
    const dom = jQuery(html);
    const box = dom.find('#mainContentForPage');

    const headerBlock = box.find('h1');
    const artistNameBlock = box.find('#trackDetailArtistName');

    if (!headerBlock || !artistNameBlock) {
        return false;
    }

    const title = getBlockTrimmedText(headerBlock);
    const artist = getBlockTrimmedText(artistNameBlock);
    const genre = getBlockTrimmedTextBySelector(box, '#trackDetailGenreName');
    const label = getBlockTrimmedTextBySelector(box, '#trackDetailRecordlabelName');
    const date = getBlockTrimmedTextBySelector(box, '#trackDetailReleaseDate');

    const catalogIdBlock = box.find('#trackDetailCatalogueNo');

    const catalogId = catalogIdBlock
        ? catalogIdBlock.text().trim()
        : '';

    const parsedData = {title, artist, genre, label, date, catalogId};
    log(parsedData);
    return parsedData;
}

function getBlockTrimmedText(block) {
    return block.text().trim();
}

function getBlockTrimmedTextBySelector(source, selector) {
    return getBlockTrimmedText(source.find(selector));
}

function log(parsedData) {
    const strings = Object.entries(parsedData)
        .map(([key, value]) => `${key}: ${value}`);

    const data = strings.join('\n        ');
    console.log(`

        ${data}

    `);
}
