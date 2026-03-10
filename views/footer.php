    </div><!-- /.main-content wrapper if used -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>

    <?php if (!empty($_SESSION['error_message'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: <?php echo json_encode($_SESSION['error_message']); ?>,
            confirmButtonColor: '#0d6efd'
        });
    </script>
    <?php unset($_SESSION['error_message']); endif; ?>

    <?php if (!empty($_SESSION['success_message'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: <?php echo json_encode($_SESSION['success_message']); ?>,
            confirmButtonColor: '#0d6efd'
        });
    </script>
    <?php unset($_SESSION['success_message']); endif; ?>
</body>
</html>
