//TODO: move $http to service/resource
export default function TidEngineFormController($interval, $http) {
    const vm = this;
    let currentMode;
    let isModeChanged = false;
    let emptyRequestsAmount = 0;

    Object.assign(vm, {
        currentStatus: 'standby',
        currentOffset: 0,
        timeText: 0,
        lastTimeVisible: false,
        logRows: [],
        requestsSentAmount: 0,
        successNumber: 0,
        maxEmptyRequests: 10,
        logSize: 100,
        waitTime: 100,

        init,
        onStartClick,
        onStopClick,
        onFireClick,
        onModeIncrementalClick,
        onModeRecheckClick,
        onCleanLogClick
    });

    function init() {
        $interval(handleEachSecond, 1000);
        onModeIncrementalClick();
    }

    function handleEachSecond() {
        incrementTimeIfNeeded();
        triggerReturnValueIfNeeded();
    }

    function incrementTimeIfNeeded() {
        vm.timeText = isAnyButtonActive()
            ? vm.timeText + 1
            : 0;
    }

    function isAnyButtonActive() {
        const buttonsActivity = [vm.startActive, vm.fireActive, vm.stopActive];
        return buttonsActivity.some(Boolean);
    }

    function triggerReturnValueIfNeeded() {
        if (vm.timeText > vm.waitTime) {
            returnValue();
        }
    }

    function onStartClick() {
        fetchTidData();

        vm.startActive = true;
        vm.stopDisabled = false;
        vm.fireDisabled = true;
        vm.currentStatus = 'ongoing';
    }

    function onStopClick() {
        vm.stopActive = true;
        vm.startActive = false;
        vm.currentStatus = 'stopped, waiting response';
    }

    function onFireClick() {
        fetchTidData();

        vm.fireActive = true;
        vm.startDisabled = true;
        vm.currentStatus = 'fired, waiting response';
    }

    function onModeIncrementalClick() {
        vm.modeIncrementalActive = true;
        vm.modeRecheckActive = false;
        changeMode('incremental');
    }

    function onModeRecheckClick() {
        vm.modeRecheckActive = true;
        vm.modeIncrementalActive = false;
        changeMode('recheck');
    }

    function changeMode(newMode) {
        currentMode = newMode;
        console.log(currentMode); //TODO: remove

        if (!isAnyButtonActive()) {
            vm.currentOffset = 0;
        } else {
            isModeChanged = true;
        }
    }

    function onCleanLogClick() {
        logRows.length = 0;
    }

    function resetTime(){
        vm.lastTime = vm.timeText;
        vm.timeText = 0;
        vm.lastTimeVisible = true;
    }

    function logInfo(str){
        logRows.unshift(str);
    }

    function returnValue() {
        if (isModeChanged) {
            vm.currentOffset = 0;
            isModeChanged = false;
        } else {
            vm.currentOffset++;
        }

        if (vm.fireActive) {
            vm.fireActive = false;
            vm.startDisabled = false;
            resetTime();
            vm.currentStatus = 'fired, got response';
            return;
        }

        if (vm.stopActive) {
            vm.stopActive = false;
            vm.stopDisabled = true;
            vm.fireDisabled = false;
            resetTime();
            vm.currentStatus = 'stopped, standby';
            return;
        }

        if (vm.maxEmptyRequests && emptyRequestsAmount == vm.maxEmptyRequests && vm.modeIncrementalActive){
            onModeRecheckClick();
            logInfo(`Dropped to recheck holes mode, because limit=${vm.maxEmptyRequests} reached`);
        }

        resetTime();
        fetchTidData();
    }

    function fetchTidData(){
        isModeChanged = false;
        vm.requestsSentAmount++;

        const data = {
            mode: currentMode,
            //drop: $('#scan-drop').val(),
            offset: vm.currentOffset
        };
        $http.get('core.php', data)
            .then(({data}) => {
                //TODO: remove try/catch
                try {
                    vm.successNumber += data.success_number;

                    if (data.success_number) {
                        emptyRequestsAmount = 0;
                    } else {
                        emptyRequestsAmount++;
                    }

                    const successList = list(data.success_urls);
                    const allList = list(data.all_urls);
                    logInfo(`${data.success_number} (${successList} / ${allList}), max=${data.max}`);

                    if (data.offset_delta) {
                        vm.currentOffset += data.offset_delta - 1;
                    }
                } catch(e) {
                    console.log('error', e, data);
                    logInfo('error, logged to console');
                }

                returnValue();
            });
    }

    function list(list){
        if (!list.length) {
            return '';
        }

        let urls = list.split(',');

        const fullUrls = urls.map((url) => {
            const id = /\/(\d+)\.html/.exec(url)[1];
            return `<a href="${url}">${id}</a>`;
        });

        return fullUrls.join(', ');
    }
}
