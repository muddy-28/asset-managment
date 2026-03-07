/**
 * Hospital Asset Management System - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // Initialize DataTables on .datatable tables
    // ==========================================
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
        jQuery('.datatable').each(function () {
            if (!jQuery.fn.DataTable.isDataTable(this)) {
                jQuery(this).DataTable({
                    responsive: true,
                    pageLength: 25,
                    language: {
                        search: '_INPUT_',
                        searchPlaceholder: 'Search...',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        paginate: {
                            first: '<i class="fas fa-angle-double-left"></i>',
                            last: '<i class="fas fa-angle-double-right"></i>',
                            next: '<i class="fas fa-angle-right"></i>',
                            previous: '<i class="fas fa-angle-left"></i>'
                        }
                    }
                });
            }
        });
    }

    // ==========================================
    // CSRF Token for AJAX Requests
    // ==========================================
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    if (typeof jQuery !== 'undefined') {
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function (xhr, settings) {
                if (settings.type && settings.type.toUpperCase() === 'POST' && settings.data) {
                    if (typeof settings.data === 'string' && settings.data.indexOf('csrf_token') === -1) {
                        settings.data += '&csrf_token=' + encodeURIComponent(csrfToken);
                    }
                }
            }
        });
    }

    // ==========================================
    // Confirm Delete with SweetAlert2
    // ==========================================
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var deleteUrl = this.getAttribute('href') || this.dataset.url;
            var itemName = this.dataset.name || 'this item';

            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to delete ' + itemName + '. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed && deleteUrl) {
                    // Submit via a form to include CSRF
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;

                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = 'csrf_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    var actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete';
                    form.appendChild(actionInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // ==========================================
    // Sidebar Toggle
    // ==========================================
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    var mainContent = document.getElementById('mainContent');
    var overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            var isMobile = window.innerWidth < 992;

            if (isMobile) {
                sidebar.classList.toggle('show');
                if (overlay) overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('expanded');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Close sidebar on mobile when clicking a link
    document.querySelectorAll('.sidebar-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
            }
        });
    });
});
