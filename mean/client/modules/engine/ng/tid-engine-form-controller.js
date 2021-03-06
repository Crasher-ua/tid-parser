export default function TidEngineFormController($interval, tidEngineResource) {
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

        //TODO: remove
        console.log(currentMode); //eslint-disable-line no-console, no-undef

        if (!isAnyButtonActive()) {
            vm.currentOffset = 0;
        } else {
            isModeChanged = true;
        }
    }

    function onCleanLogClick() {
        vm.logRows.length = 0;
    }

    function resetTime() {
        vm.lastTime = vm.timeText;
        vm.timeText = 0;
        vm.lastTimeVisible = true;
    }

    function logInfo(str) {
        vm.logRows.unshift(str);
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

        if (vm.maxEmptyRequests && emptyRequestsAmount === vm.maxEmptyRequests && vm.modeIncrementalActive) {
            onModeRecheckClick();
            logInfo(`Dropped to recheck holes mode, because limit=${vm.maxEmptyRequests} reached`);
        }

        resetTime();
        fetchTidData();
    }

    function fetchTidData() {
        isModeChanged = false;
        vm.requestsSentAmount++;

        tidEngineResource.checkList(currentMode, vm.currentOffset)
            .then(({data}) => {
                vm.successNumber += data.successNumber;

                emptyRequestsAmount = data.successNumber
                    ? 0
                    : emptyRequestsAmount + 1;

                const successList = joinList(data.successUrls);
                const allList = joinList(data.allUrls);
                logInfo(`${data.successNumber} (${successList} / ${allList}), max=${data.max}`);

                if (data.offsetDelta) {
                    vm.currentOffset += data.offsetDelta - 1;
                }

                returnValue();
            });
    }

    function joinList(list) {
        if (!list.length) {
            return '';
        }

        const urls = list.split(',');

        const fullUrls = urls.map((url) => {
            const id = /\/(\d+)\.html/.exec(url)[1];
            return `<a href="${url}">${id}</a>`;
        });

        return fullUrls.join(', ');
    }
}
