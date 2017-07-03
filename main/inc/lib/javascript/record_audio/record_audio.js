/* For licensing terms, see /license.txt */
window.RecordAudio = (function () {
    function useRecordRTC(rtcInfo, fileName) {
        $(rtcInfo.blockId).show();

        var mediaConstraints = {audio: true},
            recordRTC = null,
            btnStart = $(rtcInfo.btnStartId),
            btnPause = $(rtcInfo.btnPauseId),
            btnPlay = $(rtcInfo.btnPlayId),
            btnStop = $(rtcInfo.btnStopId),
            btnSave = $(rtcInfo.btnSaveId),
            tagAudio = $(rtcInfo.plyrPreviewId);

        btnStart.on('click', function () {
            if (!fileName) {
                fileName = $('#audio-title-rtc').val();

                if (!$.trim(fileName)) {
                    return;
                }
            }

            navigator.getUserMedia = navigator.getUserMedia || navigator.mozGetUserMedia || navigator.webkitGetUserMedia;

            function successCallback(stream) {
                recordRTC = RecordRTC(stream, {
                    numberOfAudioChannels: 1,
                    type: 'audio'
                });
                recordRTC.startRecording();

                $('#audio-title-rtc').prop('readonly', true);
                btnSave.prop('disabled', true).addClass('hidden');
                btnStop.prop('disabled', false).removeClass('hidden');
                btnStart.prop('disabled', true).addClass('hidden');
                btnPause.prop('disabled', false).removeClass('hidden');
                tagAudio.removeClass('show').addClass('hidden');
            }

            function errorCallback(error) {
                alert(error.message);
            }

            if (navigator.getUserMedia) {
                navigator.getUserMedia(mediaConstraints, successCallback, errorCallback);
            } else if (navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia(mediaConstraints)
                    .then(successCallback)
                    .error(errorCallback);
            }
        });

        btnPause.on('click', function () {
            if (!recordRTC) {
                return;
            }

            btnPause.prop('disabled', true).addClass('hidden');
            btnPlay.prop('disabled', false).removeClass('hidden');
            btnStop.prop('disabled', true).addClass('hidden');
            recordRTC.pauseRecording();
        });

        btnPlay.on('click', function () {
            if (!recordRTC) {
                return;
            }

            btnPlay.prop('disabled', true).addClass('hidden');
            btnPause.prop('disabled', false).removeClass('hidden');
            btnStop.prop('disabled', false).removeClass('hidden');
            recordRTC.resumeRecording();
        });

        btnStop.on('click', function () {
            if (!recordRTC) {
                return;
            }

            recordRTC.stopRecording(function (audioURL) {
                btnStart.prop('disabled', false).removeClass('hidden');
                btnPause.prop('disabled', true).addClass('hidden');
                btnStop.prop('disabled', true).addClass('hidden');
                btnSave.prop('disabled', false).removeClass('hidden');

                tagAudio
                    .removeClass('hidden')
                    .addClass('show')
                    .prop('src', audioURL);
            });
        });

        btnSave.on('click', function () {
            if (!recordRTC) {
                return;
            }

            var recordedBlob = recordRTC.getBlob();

            if (!recordedBlob) {
                return;
            }

            var fileExtension = '.' + recordedBlob.type.split('/')[1];

            var formData = new FormData();
            formData.append('audio_blob', recordedBlob, fileName + fileExtension);
            formData.append('audio_dir', rtcInfo.directory);

            $.ajax({
                url: _p.web_ajax + 'record_audio_rtc.ajax.php',
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST'
            }).then(function (fileUrl) {
                if (!fileUrl) {
                    return;
                }

                btnSave.prop('disabled', true).addClass('hidden');
                btnStop.prop('disabled', true).addClass('hidden');
                btnStart.prop('disabled', false).removeClass('hidden');

                if ($('#audio-title-rtc').length) {
                    $('#audio-title-rtc').prop('readonly', false);

                    window.location.reload();
                }
            });
        });
    }

    function useWami(wamiInfo, fileName) {
        $(wamiInfo.blockId).show();

        if (!fileName) {
            $('#btn-activate-wami').on('click', function (e) {
                e.preventDefault();

                fileName = $('#audio-title-wami').val();

                if (!$.trim(fileName)) {
                    return;
                }

                $('#audio-title-wami').prop('readonly', true);
                $(this).prop('disabled', true);

                Wami.setup({
                    id: wamiInfo.containerId,
                    onReady : setupGUI,
                    swfUrl: _p.web_lib + 'wami-recorder/Wami.swf'
                });
            });
        } else {
            Wami.setup({
                id: wamiInfo.containerId,
                onReady: setupGUI,
                swfUrl: _p.web_lib + 'wami-recorder/Wami.swf'
            });
        }

        function setupGUI() {
            var gui = new Wami.GUI({
                id: wamiInfo.containerId,
                singleButton: true,
                recordUrl: _p.web_ajax + 'record_audio_wami.ajax.php?' + $.param({
                    waminame: fileName + '.wav',
                    wamidir: wamiInfo.directory,
                    wamiuserid: wamiInfo.userId
                }),
                buttonUrl: _p.web_lib + 'wami-recorder/buttons.png',
                buttonNoUrl: _p.web_img + 'blank.gif'
            });

            gui.setPlayEnabled(false);
        }
    }

    return {
        init: function (rtcInfo, wamiInfo, fileName) {
            $(rtcInfo.blockId + ', ' + wamiInfo.blockId).hide();

            var webRTCIsEnabled = navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.getUserMedia ||
                navigator.mediaDevices.getUserMedia;

            if (webRTCIsEnabled) {
                useRecordRTC(rtcInfo, fileName);

                return;
            }

            useWami(wamiInfo, fileName);
        }
    }
})();