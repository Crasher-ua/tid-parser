const jsdom = require('jsdom').jsdom;
const doc = jsdom();
const window = doc.defaultView;
const jQuery = require('jquery')(window);

module.exports = function TidParser(html) {
    const dom = jQuery(html);
    const box = dom.find('#mainContentForPage');

    const mainData = getMainData(box);
    if (!mainData) {
        return false;
    }

    const additionalData = getAdditionalData(box);
    const catalogId = getCatalogId(box)

    const parsedData = Object.assign(mainData, {catalogId}, additionalData);
    log(parsedData);
    return parsedData;
}

function getMainData(box) {
    const headerBlock = box.find('h1');
    const artistNameBlock = box.find('#trackDetailArtistName');

    if (!headerBlock || !artistNameBlock) {
        return false;
    }

    const title = getBlockTrimmedText(headerBlock);
    const artist = getBlockTrimmedText(artistNameBlock);

    return {title, artist};
}

function getAdditionalData(box) {
    const genre = getBlockTrimmedTextBySelector(box, '#trackDetailGenreName');
    const label = getBlockTrimmedTextBySelector(box, '#trackDetailRecordlabelName');
    const date = getBlockTrimmedTextBySelector(box, '#trackDetailReleaseDate');

    return {genre, label, date};
}

function getCatalogId(box) {
    const catalogIdBlock = box.find('#trackDetailCatalogueNo');

    return catalogIdBlock
        ? catalogIdBlock.text().trim()
        : '';
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
