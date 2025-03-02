/**
 * Form Basic Inputs
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const productSelect = document.getElementById('product_id');
  const tipeSelect = document.getElementById('tipe_motor');
  const ccMotorSelect = document.getElementById('cc_motor');
  const ccMotorInput = document.getElementById('checkPrice');

  // Load produk
  if (productSelect) {
    fetch('/get-products') // Ambil data dari route Laravel
      .then(response => response.json())
      .then(data => {
        let options = '<option value=""></option>';
        data.forEach(product => {
          options += `<option value="${product.deskripsi}">${product.deskripsi}</option>`;
        });
        productSelect.innerHTML = options; // Update dropdown dengan data produk
      });
  }

  // Load tipe motor
  if (tipeSelect) {
    fetch('/get-tipemotor') // Ambil data dari route Laravel
      .then(response => response.json())
      .then(data => {
        let options = '<option value=""></option>';
        data.forEach(tipemotor => {
          options += `<option value="${tipemotor.code}">${tipemotor.name}</option>`;
        });
        tipeSelect.innerHTML = options; // Perbaiki kesalahan sebelumnya
      });
  }

  if (productSelect) {
    productSelect.addEventListener('change', function () {
      const desc = this.value;
      console.log(desc);
      if (desc) {
        fetch(`/get-ccmotor?desc=${desc}`)
          .then(response => response.json())
          .then(data => {
            let options = '<option value=""></option>';
            if (data.products && data.products.length > 0) {
              data.products.forEach(product => {
                options += `<option value="${product.product}">${product.cc_motor}</option>`;
              });
            } else {
              options = '<option value="">-- Tidak Ada Data --</option>';
            }
            ccMotorSelect.innerHTML = options; // Update select option
          });
      } else {
        ccMotorSelect.innerHTML = '<option value=""></option>';
      }
    });
  }

  if (ccMotorSelect) {
    ccMotorSelect.addEventListener('change', function () {
      const type = this.value;
      console.log(type);
      if (type) {
        fetch(`/get-ccmotor?type=${type}`)
          .then(response => response.json())
          .then(data => {
            if (data.price > 0) {
              ccMotorInput.value = data.price;
            } else {
              ccMotorInput.value = '';
            }
          });
      } else {
        ccMotorInput.value = '';
      }
    });
  }

  $('#makereceipt').on('click', function () {
    var region = $('#region').val();
    var number = $('#number').val();
    var series = $('#series').val();
    var tipe_motor = $('#tipe_motor').val();
    var product_id = $('#product_id').val();
    var cc_motor = $('#cc_motor').val();
    var checkPrice = $('#checkPrice').val();
    var payment = $('#payment').val();

    // Validasi input tidak boleh kosong
    if (!region || !number || !series || !tipe_motor || !product_id || !cc_motor || !checkPrice || !payment) {
      alert('Harap lengkapi semua data sebelum mengirim.');
      return;
    }

    // Data yang akan dikirim ke server
    var formData = {
      region: region,
      number: number,
      series: series,
      tipe_motor: tipe_motor,
      product_id: product_id,
      cc_motor: cc_motor,
      checkPrice: checkPrice,
      payment: payment
    };

    // AJAX POST ke Laravel Controller
    $.ajax({
      url: '/post-addreceipt',
      type: 'POST',
      data: formData,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ambil CSRF Token
      },
      success: function (response) {
        if (response.success) {
          alert('✅ Data berhasil disimpan!');
          $('#region, #number, #series, #tipe_motor, #product_id, #cc_motor, #checkPrice, #payment').val('');
        } else {
          alert('❌ Terjadi kesalahan: ' + response.message);
        }
      },
      error: function (xhr) {
        alert('⚠️ Gagal mengirim data! ' + xhr.responseText);
      }
    });
  });
});

// Responsive Table
// --------------------------------------------------------------------
$(function () {
  var dt_responsive_table = $('.dt-responsive');

  // Responsive Table
  // --------------------------------------------------------------------

  if (dt_responsive_table.length) {
    var dt_responsive = dt_responsive_table.DataTable({
      paging: false,
      ordering: false,
      pageLength: 10,
      scrollX: true,
      ajax: '/get-allreceipt',
      columns: [
        { data: '' },
        { data: 'created_at' },
        { data: 'uuid' },
        { data: 'nopol' },
        { data: 'merek_motor' },
        { data: 'price_list_deskripsi' },
        { data: 'price_list_product' },
        { data: 'cc_motor' },
        { data: 'price' }
      ],
      columnDefs: [
        { targets: '_all', className: 'text-nowrap text-center' },
        {
          className: 'control',
          orderable: false,
          targets: 0,
          searchable: false,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // Label
          targets: -1,
          render: function (data, type, full, meta) {
            var $status_number = full['payment'];
            var $status = {
              cash: { title: 'Cash', class: 'bg-label-primary' },
              qris: { title: 'Qris', class: ' bg-label-success' }
            };
            if (typeof $status[$status_number] === 'undefined') {
              return data;
            }
            return (
              '<span class="badge ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>'
            );
          }
        },
        {
          targets: 1, // Kolom 'created_at'
          render: function (data, type, full, meta) {
            if (!data) return '-';

            // Konversi data ke format Date
            let date = new Date(data);

            // Format tanggal menjadi "3 Maret 2025"
            return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
          }
        }
      ],
      // scrollX: false,
      destroy: true,
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      language: {
        paginate: {
          next: '<i class="ti ti-chevron-right ti-sm"></i>',
          previous: '<i class="ti ti-chevron-left ti-sm"></i>'
        }
      },
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Informasi Detail: ' + data['uuid'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
  }
});
