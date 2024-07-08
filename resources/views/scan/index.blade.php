<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="ZXing for JS">

    <title>Scan Invoice</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

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
            gap: 8px;
        }

        .flex-item {
            flex: 1;
        }

        .flex-item.wide {
            flex: 2;
        }

        .flex-item select {
            width: 100%;
            height: 28px;
            margin-top: 1rem;

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <main class="wrapper" style="padding-top:2em">

        <section class="container" id="demo-content">
            <nav class="nav-bar">
                <div class="icon-nav">
                    <i class="fas fa-moon"></i>
                    <span class="logo">Scan Invoice</span>
                </div>

                <ul class="list-nav-bar">
                    <li class="list-item active"><a href="{{ route('scan.index', []) }}">Scan Invoice</a></li>
                    <li class="list-item"><a href="{{ route('scan-pengiriman.index', []) }}">Scan Pengiriman</a></li>
                </ul>
                <div class="fas burger-menu" id="burger-menu">&#9776;</div>
            </nav>

            <h1 class="title" style="margin-top: 2rem">Scan Invoice</h1>

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

            <label>Marketplace:</label>
            <select name="marketplace" id="marketplace" style="max-width:400px">
                <option value="">Pilih Marketplace</option>
                @foreach (['tiktok', 'shopee', 'tokped', 'lazada', 'offline'] as $marketplace)
                    <option value="{{ $marketplace }}">{{ ucfirst($marketplace) }}</option>
                @endforeach
            </select>

            <div id="dropdownContainer">
                <label>Produk:</label>
                <div class="flex-container dropdown-group">
                    <!-- Nama Baju Dropdown -->
                    <div class="flex-item wide">
                        <select class="choose-product" height="100" name="product_id[]" id="product_id_1">
                            <option value="">Pilih Nama Baju</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ukuran Dropdown -->
                    <div class="flex-item">
                        <select name="size[]" id="size_1">
                            <option value="">Pilih Ukuran</option>
                        </select>
                    </div>

                    <!-- Quantity Dropdown -->
                    <div class="flex-item">
                        <select name="qty[]" id="qty_1">
                            <option value="">Pilih Qty</option>
                            @for ($i = 1; $i <= 50; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Plus Button -->
                    <button type="button" class="plus-button">+</button>
                </div>
            </div>

            <label>Nomor Resi:</label>
            <pre><code id="result"></code></pre>

            <div>
                <a class="button" id="startButton">Start</a>
                <a class="button" id="resetButton">Reset</a>
            </div>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
    <script type="text/javascript">
        window.addEventListener('load', function() {
            const hamburguer = document.getElementById("burger-menu")
            const navMenu = document.querySelector(".list-nav-bar")
            const urlRoot = "https://dowear.dimas.co.id";
            // const urlRoot = "{{ url('/') }}";

            hamburguer.addEventListener("click", () => {
                navMenu.classList.toggle("active")
            })

            let selectedDeviceId;
            const codeReader = new ZXing.BrowserMultiFormatReader();

            let groupId = 1;

            function addDropdownGroup() {
                codeReader.reset();
                groupId++;
                const container = document.getElementById('dropdownContainer');

                const group = document.createElement('div');
                group.className = 'flex-container dropdown-group';
                group.id = `group_${groupId}`;

                group.innerHTML = `
                    <div class="flex-item wide">
                        <select class="choose-product" height="100" name="product_id[]" id="product_id_${groupId}">
                            <option value="">Pilih Nama Baju</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-item">
                        <select name="size[]" id="size_${groupId}">
                            <option value="">Pilih Ukuran</option>
                        </select>
                    </div>
                    <div class="flex-item">
                        <select name="qty[]" id="qty_${groupId}">
                            <option value="">Pilih Qty</option>
                            @for ($i = 1; $i <= 50; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="button" class="minus-button" data-group-id="${groupId}">-</button>
                `;

                container.appendChild(group);

                $(`#product_id_${groupId}`).select2();

                $(`#product_id_${groupId}`).on('change', function() {
                    getSizeQty($(this).val(), groupId);
                });
            }

            function removeDropdownGroup(id) {
                codeReader.reset();
                const group = document.getElementById(id);
                group.parentNode.removeChild(group);
            }

            function startScanning() {
                var marketPlace = document.getElementById('marketplace').value;
                var products = [];

                if (!marketPlace) {
                    showSnackbar('Pilih marketplace terlebih dahulu', '#dc3545', 'error');
                    return;
                }

                for (let i = 1; i <= groupId; i++) {
                    if (!document.getElementById(`product_id_${i}`) || !document.getElementById(`size_${i}`) ||
                        !document.getElementById(`qty_${i}`)) {
                        continue;
                    }
                    var product_id = document.getElementById(`product_id_${i}`).value;
                    var size = document.getElementById(`size_${i}`).value;
                    var qty = document.getElementById(`qty_${i}`).value;

                    if (product_id && size && qty) {
                        products.push({
                            product_id,
                            size,
                            qty
                        });
                    }

                    if (!product_id || !size || !qty) {
                        showSnackbar('Lengkapi produk terlebih dahulu', '#dc3545', 'error');
                        return;
                    }
                }

                codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
                    if (result) {
                        handleResult(result.text, marketPlace, products);

                        // stop scanning
                        handleReset();
                    }
                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        handleError(err);
                    }
                });
                console.log(`Started continuous decode from camera with id ${selectedDeviceId}`)
            }

            function handleResult(text, marketplace, products) {
                document.getElementById('result').textContent = text;
                // buat ajax request ke server
                $.ajax({
                    url: urlRoot + "/scaninvoice",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tracking_number: text,
                        invoice_number: text,
                        marketplace,
                        products
                    },
                    success: function(response) {
                        showSnackbar(response.message, '#28a745');

                        // audio notifikasi
                        var audio = new Audio(urlRoot + "/assets/sound/beep.mp3");
                        audio.play();

                        navigator.vibrate = navigator.vibrate || navigator.webkitVibrate ||
                            navigator.mozVibrate || navigator.msVibrate;

                        if (navigator.vibrate) {
                            navigator.vibrate([100, 50, 100, 50, 100]);
                        }

                        handleReset();
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            showSnackbar('Nomor resi sudah terdaftar', '#dc3545', 'error');

                            // audio notifikasi
                            var audio = new Audio(urlRoot + "/assets/sound/tetot.mp3");
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

                        handleReset();
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
                document.getElementById('marketplace').value = '';
                for (let i = 1; i <= groupId; i++) {
                    if (document.getElementById(`product_id_${i}`) || document.getElementById(
                            `size_${i}`) || document.getElementById(`qty_${i}`)) {
                        // destroy select2
                        $(`#product_id_${i}`).select2('destroy');
                        $(`#product_id_${i}`).val('').select2();
                        document.getElementById(`product_id_${i}`).value = '';
                        document.getElementById(`size_${i}`).value = '';
                        document.getElementById(`qty_${i}`).value = '';
                    }
                }
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

            document.querySelector('.plus-button').addEventListener('click', addDropdownGroup);

            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('minus-button')) {
                    removeDropdownGroup(`group_${e.target.getAttribute('data-group-id')}`);
                }
            });

            function getSizeQty(productId, groupId) {
                var sizeDropdown = $(`#size_${groupId}`);
                $.ajax({
                    url: urlRoot + `/api/products/${productId}`,
                    type: "GET",
                    success: function(response) {
                        console.log(response);
                        sizeDropdown.empty();
                        sizeDropdown.append('<option value="">Pilih Ukuran</option>');
                        response.forEach(function(size) {
                            sizeDropdown.append(
                                `<option value="${size.size}">${size.size}</option>`);
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }
        });

        $(document).ready(function() {
            $('.choose-product').select2();

            $('.choose-product').on('change', function() {
                var productId = $(this).val();
                var groupId = $(this).attr('id').split('_')[2];
                // const urlRoot = "{{ url('/') }}";
                const urlRoot = "https://dowear.dimas.co.id";
                var sizeDropdown = $(`#size_${groupId}`);

                $.ajax({
                    url: urlRoot + `/api/products/${productId}`,
                    type: "GET",
                    success: function(response) {
                        sizeDropdown.empty();
                        sizeDropdown.append('<option value="">Pilih Ukuran</option>');
                        response.forEach(function(size) {
                            sizeDropdown.append(
                                `<option value="${size.size}">${size.size}</option>`);
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });
        });
    </script>
</body>

</html>
