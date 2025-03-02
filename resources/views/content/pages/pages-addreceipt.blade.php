@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Buat Orderan')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/toastr/toastr.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite('resources/assets/js/pages-receipt-form1.js')
@endsection

@section('content')
    <div class="row g-6">

        <!-- Form controls -->
        <div class="col-xxl-4 col-xl-4 col-lg-4">
            <div class="card h-100">
                <h5 class="card-header">Buat Pesanan</h5>
                <div class="card-body" id="addReceipt">
                    <div class="mb-4">
                        <label for="formValidationPlatRegion" class="form-label">Plat Nomor</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="text" id="region" maxlength="2" placeholder="AB"
                                    class="form-control text-uppercase text-center" required
                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '')"
                                    autocomplete="off">
                            </div>
                            <div class="col">
                                <input type="text" id="number" maxlength="4" placeholder="1234"
                                    class="form-control text-center" required
                                    oninput="this.value = this.value.replace(/\D/g, '')" autocomplete="off">
                            </div>
                            <div class="col">
                                <input type="text" id="series" maxlength="3" placeholder="XYZ"
                                    class="form-control text-uppercase text-center" required
                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '')"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="tipemotor" class="form-label">Merek Motor</label>
                        <select class="form-select" id="tipe_motor" aria-label="Default select example">
                            <option value=""></option>
                            <option value="hnd">Honda</option>
                            <option value="ymh">Yamaha</option>
                            <option value="szk">Suzuki</option>
                            <option value="kws">Kawasaki</option>
                            <option value="oth">Other</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="merekmotor" class="form-label">Pilih Produk</label>
                        <select class="form-select" id="product_id" aria-label="Default select example">
                            {{-- <option value=""></option>
                            <option value="Premium Wash">Premium Wash</option>
                            <option value="Reguler Wash">Reguler Wash</option> --}}
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="ccmotor" class="form-label">CC Motor</label>
                        <select class="form-select" id="cc_motor" aria-label="Default select example"></select>
                    </div>
                    <div class="mb-4">
                        <label for="exampleDataList" class="form-label">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control" id="checkPrice" name="formValidationPlatRegion"
                                placeholder="Total" aria-label="Total" aria-describedby="basic-addon11" disabled>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="merekmotor" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="payment" aria-label="Default select example">
                            <option value=""></option>
                            <option value="cash">Cash</option>
                            <option value="qris">Qris</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <button type="submit" id="makereceipt" class="btn btn-primary waves-effect waves-light">Buat
                            Pesanan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Sizing -->
        <div class="col-xxl-8 col-xl-8 col-lg-8">
            <!-- Responsive Datatable -->
            <div class="card">
                <h5 class="card-header">Tabel Orderan</h5>
                <div class="card-datatable table-responsive">
                    <table class="dt-responsive table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Tanggal</th>
                                <th>UUID</th>
                                <th>Nopol</th>
                                <th>Merek Kendaraan</th>
                                <th>Product</th>
                                <th>ID-Product</th>
                                <th>CC Kendaraan</th>
                                <th>Pembayaran</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!--/ Responsive Datatable -->
        </div>

    </div>

    <!-- Modal QRIS -->
    <div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrisModalLabel">Pembayaran QRIS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="assets/img/qris.jpeg" alt="QRIS Payment" class="img-fluid" id="qrisImage">
                    <p class="mt-3">Silakan scan QRIS untuk melanjutkan pembayaran.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmQrisPayment" class="btn btn-primary">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-4">
                            <label for="nameBasic" class="form-label">Name</label>
                            <input type="text" id="nameBasic" class="form-control" placeholder="Enter Name">
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col mb-0">
                            <label for="emailBasic" class="form-label">Email</label>
                            <input type="email" id="emailBasic" class="form-control" placeholder="xxxx@xxx.xx">
                        </div>
                        <div class="col mb-0">
                            <label for="dobBasic" class="form-label">DOB</label>
                            <input type="date" id="dobBasic" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div id="loading-spinner" style="display: none;">
        <p>Loading...</p>
    </div>

@endsection
