module.exports = function(router) {
    router.get('/', (req, res) => res.json({message: 'I am working!'}));
};