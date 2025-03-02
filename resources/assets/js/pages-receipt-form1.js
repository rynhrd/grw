/**
 * Form Basic Inputs - Improved Version
 */

'use strict';

// Cache DOM elements
const elements = {
  loadingSpinner: document.getElementById('loading-spinner'),
  productSelect: document.getElementById('product_id'),
  ccMotorSelect: document.getElementById('cc_motor'),
  ccMotorInput: document.getElementById('checkPrice'),
  tipeSelect: document.getElementById('tipeSelect'),
  tipeMotor: document.getElementById('tipe_motor'),
  region: document.getElementById('region'),
  number: document.getElementById('number'),
  series: document.getElementById('series'),
  payment: document.getElementById('payment'),
  makeReceipt: document.getElementById('makereceipt'),
  confirmQrisPayment: document.getElementById('confirmQrisPayment')
};

// API endpoints
const API = {
  TIPE_MOTOR: '/get-tipemotor',
  PRODUCTS: '/get-products',
  CC_MOTOR: '/get-ccmotor',
  ADD_RECEIPT: '/post-addreceipt',
  ALL_RECEIPTS: '/get-allreceipt'
};

/**
 * Fetch data with automatic retry for server errors
 * @param {string} url - API endpoint
 * @param {Object} options - Fetch options
 * @param {number} maxRetries - Maximum number of retries
 * @param {number} delay - Delay between retries in ms
 * @returns {Promise} - Promise with response data
 */
function fetchWithRetry(url, options = {}, maxRetries = 3, delay = 5000) {
  let attempt = 0;

  if (elements.loadingSpinner) {
    elements.loadingSpinner.style.display = 'block';
  }

  function tryFetch(resolve, reject) {
    fetch(url, options)
      .then(response => {
        if (!response.ok) {
          if (response.status === 500 && attempt < maxRetries) {
            console.warn(`Error 500 terjadi, mencoba lagi (${attempt + 1})...`);
            attempt++;
            setTimeout(() => tryFetch(resolve, reject), delay);
            return;
          }
          throw new Error(`Request gagal dengan status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (elements.loadingSpinner) {
          elements.loadingSpinner.style.display = 'none';
        }
        resolve(data);
      })
      .catch(error => {
        console.error('Fetch error:', error);
        if (elements.loadingSpinner) {
          elements.loadingSpinner.style.display = 'none';
        }
        reject(error);
      });
  }

  return new Promise(tryFetch);
}

/**
 * Initialize toastr notification settings
 */
function initToastr() {
  toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: false,
    positionClass: 'toast-top-right',
    preventDuplicates: false,
    showDuration: '300',
    hideDuration: '1000',
    timeOut: '5000',
    extendedTimeOut: '1000',
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
  };
}

/**
 * Initialize motor type dropdown
 */
function initTipeMotor() {
  if (!elements.tipeSelect) return;

  fetchWithRetry(API.TIPE_MOTOR)
    .then(data => {
      console.log('Data berhasil didapatkan:', data);
      const options = data.map(tipemotor => `<option value="${tipemotor.code}">${tipemotor.name}</option>`);

      elements.tipeSelect.innerHTML = `<option value=""></option>${options.join('')}`;
    })
    .catch(error => console.error('Gagal mendapatkan data:', error));
}

/**
 * Initialize product dropdown
 */
function initProducts() {
  if (!elements.productSelect) return;

  fetchWithRetry(API.PRODUCTS)
    .then(data => {
      // Check if data exists and is an array before mapping
      if (data && Array.isArray(data)) {
        const options = data.map(product => `<option value="${product.deskripsi}">${product.deskripsi}</option>`);
        elements.productSelect.innerHTML = `<option value=""></option>${options.join('')}`;
      } else {
        // Handle empty or invalid data
        elements.productSelect.innerHTML = `<option value="">No products available</option>`;
        console.warn('No valid product data returned from server');
      }
    })
    .catch(error => {
      console.error('Gagal mendapatkan data produk:', error);
      // Show user-friendly message in the dropdown
      elements.productSelect.innerHTML = `<option value="">Error loading products</option>`;
    });
}

/**
 * Setup product change watcher
 */
function setupProductWatcher() {
  if (!elements.productSelect || !elements.ccMotorSelect) return;

  let lastValue = elements.productSelect.value;
  const watchProductChange = () => {
    if (elements.productSelect.value !== lastValue) {
      lastValue = elements.productSelect.value;

      fetchWithRetry(`${API.CC_MOTOR}?desc=${elements.productSelect.value}`).then(data => {
        let options = '<option value=""></option>';

        if (data.products && data.products.length > 0) {
          options += data.products
            .map(product => `<option value="${product.product}">${product.cc_motor}</option>`)
            .join('');
        } else {
          options = '<option value=""></option>';
        }

        elements.ccMotorSelect.innerHTML = options;
      });
    }

    // Use requestAnimationFrame for better performance than setInterval
    setTimeout(watchProductChange, 1000);
  };

  watchProductChange();
}

/**
 * Setup CC motor change watcher
 */
function setupCCMotorWatcher() {
  if (!elements.ccMotorSelect || !elements.ccMotorInput) return;

  let lastValue = elements.ccMotorSelect.value;
  const watchCCMotorChange = () => {
    if (elements.ccMotorSelect.value !== lastValue) {
      lastValue = elements.ccMotorSelect.value;

      fetchWithRetry(`${API.CC_MOTOR}?type=${elements.ccMotorSelect.value}`).then(data => {
        elements.ccMotorInput.value = data.price > 0 ? data.price : '';
      });
    }

    // Use requestAnimationFrame for better performance than setInterval
    setTimeout(watchCCMotorChange, 1000);
  };

  watchCCMotorChange();
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
  // Tipe motor change
  if (elements.tipeMotor) {
    $(elements.tipeMotor).on('change', function () {
      const fields = [elements.productSelect, elements.ccMotorSelect, elements.ccMotorInput, elements.payment];

      fields.forEach(field => {
        if (field) field.value = '';
      });
    });
  }

  // Make receipt button
  if (elements.makeReceipt) {
    elements.makeReceipt.addEventListener('click', function () {
      handleMakeReceipt();
    });
  }
}

/**
 * Handle receipt creation
 */
function handleMakeReceipt() {
  // Get form values
  const formValues = {
    region: elements.region?.value || '',
    number: elements.number?.value || '',
    series: elements.series?.value || '',
    tipe_motor: elements.tipeMotor?.value || '',
    product_id: elements.productSelect?.value || '',
    cc_motor: elements.ccMotorSelect?.value || '',
    checkPrice: elements.ccMotorInput?.value || '',
    payment: elements.payment?.value || ''
  };

  // Validate form
  const isValid = Object.values(formValues).every(value => value !== '');
  if (!isValid) {
    toastr.error('Harap lengkapi semua data!', 'Gagal');
    return;
  }

  // Handle QRIS payment
  if (formValues.payment === 'qris') {
    const qrisModal = new bootstrap.Modal(document.getElementById('qrisModal'));
    qrisModal.show();

    if (elements.confirmQrisPayment) {
      // Use one-time event listener to prevent multiple bindings
      const handleConfirm = function () {
        qrisModal.hide();
        sendDataToServer(formValues);
        elements.confirmQrisPayment.removeEventListener('click', handleConfirm);
      };

      elements.confirmQrisPayment.addEventListener('click', handleConfirm);
    }
  } else {
    sendDataToServer(formValues);
  }
}

function refreshDataTable() {
  const dt_responsive_table = $('.dt-responsive');
  if (dt_responsive_table.length && $.fn.DataTable.isDataTable('.dt-responsive')) {
    dt_responsive_table.DataTable().ajax.reload();
  } else {
    // If table doesn't exist, initialize it
    initDataTable();
  }
}

/**
 * Send form data to server
 * @param {Object} formData - Form data to send
 */
function sendDataToServer(formData) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  fetch(API.ADD_RECEIPT, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(formData)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        toastr.success(`Plat Nomor: ${formData.region}${formData.number}${formData.series}`, 'Berhasil ditambahkan');

        // Reset form fields
        const fields = [
          elements.region,
          elements.number,
          elements.series,
          elements.tipeMotor,
          elements.productSelect,
          elements.ccMotorSelect,
          elements.ccMotorInput,
          elements.payment
        ];

        fields.forEach(field => {
          if (field) field.value = '';
        });

        // Refresh datatable after successful submission
        refreshDataTable();
      } else {
        toastr.error('Terjadi kesalahan: ' + data.message);
      }
    })
    .catch(error => {
      toastr.error('Terjadi kesalahan saat mengirim data');
      console.error('Error:', error);
    });
}

/**
 * Initialize DataTable
 */
function initDataTable() {
  const dt_responsive_table = $('.dt-responsive');

  if (!dt_responsive_table.length) return;

  const dt_responsive = dt_responsive_table.DataTable({
    paging: false,
    ordering: false,
    pageLength: 10,
    scrollX: true,
    ajax: {
      url: API.ALL_RECEIPTS,
      type: 'GET',
      dataType: 'json',
      dataSrc: function (json) {
        if (!json || json.error) {
          toastr.error('Failed to load table data');
          return [];
        }
        return json.data || [];
      },
      error: function (xhr, status, error) {
        console.error('DataTable error:', error);
        toastr.error('Error loading table data. Please try refreshing.');
      }
    },
    columns: [
      { data: '', defaultContent: '' },
      { data: 'created_at', defaultContent: '' },
      { data: 'uuid', defaultContent: '' },
      { data: 'nopol', defaultContent: '' },
      { data: 'merek_motor', defaultContent: '' },
      { data: 'price_list_deskripsi', defaultContent: '' },
      { data: 'price_list_product', defaultContent: '' },
      { data: 'cc_motor', defaultContent: '' },
      { data: 'price', defaultContent: '' }
    ],
    columnDefs: [
      { targets: '_all', className: 'text-nowrap text-center' },
      {
        className: 'control',
        orderable: false,
        targets: 0,
        searchable: false,
        render: function () {
          return '';
        }
      },
      {
        targets: -1,
        render: function (data, type, full) {
          const paymentType = full['payment'];
          const statusMap = {
            cash: { title: 'Cash', class: 'bg-label-primary' },
            qris: { title: 'Qris', class: 'bg-label-success' }
          };

          if (!statusMap[paymentType]) {
            return data;
          }

          return `<span class="badge ${statusMap[paymentType].class}">${statusMap[paymentType].title}</span>`;
        }
      },
      {
        targets: 1,
        render: function (data) {
          if (!data) return '-';
          const date = new Date(data);
          return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
          }).format(date);
        }
      }
    ],
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
            const data = row.data();
            return 'Informasi Detail: ' + data['uuid'];
          }
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          const data = columns
            .filter(col => col.title !== '')
            .map(
              col =>
                `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                <td>${col.title}:</td>
                <td>${col.data}</td>
              </tr>`
            )
            .join('');

          return data ? $('<table class="table"/><tbody />').append(data) : false;
        }
      }
    }
  });

  dt_responsive_table
    .closest('.dataTables_wrapper')
    .find('.col-md-6:first')
    .append(
      `<button id="manual-refresh" class="btn btn-sm btn-outline-primary ms-2" title="Refresh Data">
        <i class="ti ti-refresh"></i> Refresh
      </button>`
    );

  // Add event listener to manual refresh button
  $('#manual-refresh').on('click', function () {
    refreshDataTable();
    toastr.info('Memuat ulang data...', 'Refresh');
  });
}

/**
 * Initialize the application
 */
function init() {
  document.addEventListener('DOMContentLoaded', function () {
    initToastr();
    initTipeMotor();
    initProducts();
    setupProductWatcher();
    setupCCMotorWatcher();
    setupEventListeners();
    initDataTable();
  });
}

// Start the application
init();
