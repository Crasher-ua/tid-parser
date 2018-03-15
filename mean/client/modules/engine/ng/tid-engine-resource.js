export default function TidEngineResource($http) {
    return {
        checkList
    };

    function checkList(mode, offset) {
        const params = {mode, offset};
        return $http.get('http://localhost:8080/api/check-list', {params})
            .then(({data}) => data);
    }
}
