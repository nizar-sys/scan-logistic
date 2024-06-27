<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="ZXing for JS">

    <title>Scan Resi Pengiriman</title>

    <link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null"
        href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null"
        href="https://unpkg.com/normalize.css@8.0.0/normalize.css">
    <link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null"
        href="https://unpkg.com/milligram@1.3.0/dist/milligram.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('/assets/css//snackbar.min.css') }}">
    <script src="{{ asset('/assets/js/snackbar.min.js') }}"></script> --}}
    <style>
        .nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 1.5rem;
            height: 60px;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .logo {
            color: aliceblue;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 1.25rem;
            margin-left: 12px;
            cursor: pointer;
        }

        .fas {
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
        }

        .list-nav-bar {
            list-style: none;
            text-transform: uppercase;
            display: flex;
            gap: 20px;
        }

        .list-item {
            display: flex;
            align-items: center;
            margin-left: 0.5rem;
            margin-top: 3rem;
        }

        .list-item a {
            cursor: pointer;
            font-size: 1.25rem;
            text-decoration: none;
            color: #fff;
            text-align: center;
            margin-left: 0.5rem;
            letter-spacing: 0.1rem;
        }

        .list-item a:hover {
            color: #a0a0a0;
        }

        .burger-menu {
            display: none;
        }

        .main-content {
            text-align: center;
            margin-top: 25vh;
        }

        .main-content h1 {
            color: #fff;
            font-size: 3.5rem;
        }

        .list-item.active a {
            color: #9b4dca;
        }

        @media screen and (max-width: 768px) {

            .list-item a {
                font-size: 0.875rem;
            }

            .logo {
                font-size: 0.875rem;
            }
        }

        @media screen and (max-width: 578px) {

            .list-item a {
                font-size: 1rem;

            }

            .list-nav-bar.active {
                right: 0;
            }

            .list-nav-bar {
                display: flex;
                position: fixed;
                right: -100%;
                top: 60px;
                width: 35%;
                background-color: rgba(0, 0, 0, 0.2);
                text-align: center;
                flex-direction: column;
                transition: 0.7s;
                gap: 18px;
                border-radius: 0 0 10px 10px;
            }

            .burger-menu {
                display: block;
                cursor: pointer;
            }
        }

        .flex-container {
            display: flex;
            align-items: center;
            max-width: 600px;
            margin-top: 0px;
            gap: 10px;
        }

        .flex-item {
            flex: 1;
        }

        .flex-item.wide {
            flex: 2;
        }

        .flex-item select {
            width: 100%;
        }

        .plus-button,
        .minus-button {
            flex: 0 0 auto;
            height: fit-content;
        }

        .dropdown-group {
            margin-bottom: 0px;
        }
    </style>
</head>

<body>
    <main class="wrapper" style="padding-top:2em">

        <section class="container" id="demo-content">
            <nav class="nav-bar">
                <div class="icon-nav">
                    <i class="fas fa-moon"></i>
                    <span class="logo">Scan Resi Pengiriman</span>
                </div>

                <ul class="list-nav-bar">
                    <li class="list-item"><a href="{{ route('scan.index', []) }}">Scan Invoice</a></li>
                    <li class="list-item active"><a href="{{ route('scan-pengiriman.index', []) }}">Scan Pengiriman</a></li>
                </ul>
                <div class="fas burger-menu" id="burger-menu">&#9776;</div>
            </nav>

            <h1 class="title" style="margin-top: 2rem">Scan Resi Pengiriman</h1>

            <div>
                <video id="video" width="330" height="300" style="border: 1px solid gray"></video>
            </div>

            <div id="sourceSelectPanel" style="display:none">
                <label for="sourceSelect">Pilih Kamera:</label>
                <select id="sourceSelect" style="max-width:400px">
                </select>
            </div>

            <label>Tanggal:</label>
            <input type="text" id="date" style="max-width:400px" name="date" value="@date(now())"
                disabled readonly>

            <label>Nomor Resi:</label>
            <pre><code id="result"></code></pre>

            <div>
                <a class="button" id="startButton">Start</a>
                <a class="button" id="resetButton">Reset</a>
            </div>
        </section>

    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
    <script type="text/javascript">
        window.addEventListener('load', function() {
            const hamburguer = document.getElementById("burger-menu")
            const navMenu = document.querySelector(".list-nav-bar")
            const urlRoot = "https://dowear.dimas.co.id";


            hamburguer.addEventListener("click", () => {
                navMenu.classList.toggle("active")
            })

            let selectedDeviceId;
            const codeReader = new ZXing.BrowserMultiFormatReader();

            let groupId = 1;

            function startScanning() {
                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        handleResult(result.text);

                        // stop scanning
                        handleReset();
                    }
                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        handleError(err);
                    }
                });
                console.log(`Started continuous decode from camera with id ${selectedDeviceId}`)
            }

            function handleResult(text) {
                document.getElementById('result').textContent = text;
                // buat ajax request ke server
                $.ajax({
                    url: urlRoot + "/scan",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tracking_number: text,
                    },
                    success: function(response) {
                        showSnackbar(response.message, '#28a745');

                        // audio notifikasi
                        var audio = new Audio(urlRoot + "assets/sound/beep.mp3");
                        audio.play();

                        navigator.vibrate = navigator.vibrate || navigator.webkitVibrate ||
                            navigator.mozVibrate || navigator.msVibrate;

                        if (navigator.vibrate) {
                            navigator.vibrate([100, 50, 100, 50, 100]);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            showSnackbar('Nomor resi sudah terdaftar', '#dc3545', 'error');

                            // audio notifikasi
                            var audio = new Audio(urlRoot + "assets/sound/tetot.mp3");
                            audio.play();

                            navigator.vibrate = navigator.vibrate || navigator.webkitVibrate ||
                                navigator.mozVibrate || navigator.msVibrate;

                            if (navigator.vibrate) {
                                navigator.vibrate([100, 50, 100, 50, 100]);
                            }
                        } else {
                            showSnackbar('Terjadi kesalahan, silahkan coba lagi ' + xhr.status + ' ' +
                                xhr.statusText, '#dc3545', 'error');
                        }
                    }
                });
            }

            function handleError(error) {
                console.error(error);
                document.getElementById('result').textContent = 'No QR code found.';
            }

            function showSnackbar(message, backgroundColor, icon = 'success') {
                Swal.fire({
                    title: message,
                    showConfirmButton: false,
                    timer: 2000,
                    icon: icon,
                });
            }

            function handleReset() {
                codeReader.reset()
                document.getElementById('result').textContent = '';
                console.log('Reset.')
            }

            codeReader.listVideoInputDevices()
                .then((videoInputDevices) => {
                    const sourceSelect = document.getElementById('sourceSelect');
                    let selectedDeviceId = videoInputDevices[0].deviceId;

                    if (videoInputDevices.length >= 1) {
                        videoInputDevices.forEach((element) => {
                            const sourceOption = document.createElement('option');
                            sourceOption.text = element.label;
                            sourceOption.value = element.deviceId;
                            sourceSelect.appendChild(sourceOption);

                            // Check if the device label includes 'rear' or 'back' to identify the rear camera
                            if (element.label.toLowerCase().includes('rear') || element.label
                                .toLowerCase().includes('back') || element.label
                                .toLowerCase().includes('belakang')) {
                                selectedDeviceId = element.deviceId;
                                sourceSelect.value = selectedDeviceId;
                            }
                        });

                        sourceSelect.onchange = () => {
                            selectedDeviceId = sourceSelect.value;
                        };

                        const sourceSelectPanel = document.getElementById('sourceSelectPanel');
                        sourceSelectPanel.style.display = 'block';
                    }
                    codeReader.startDecoding(selectedDeviceId);
                })
                .catch((err) => {
                    console.error(err);
                });


            document.getElementById('startButton').addEventListener('click', startScanning);

            document.getElementById('resetButton').addEventListener('click', () => {
                handleReset()
            })
        })
    </script>
</body>

</html>
