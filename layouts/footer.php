<script>
  const sessionUserId = <?php echo json_encode(Session::get('user_id')); ?>;
  const userRole = <?php echo json_encode(Session::get('user_role')); ?>;
</script>
<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/jquery-3.7.1.min.js"></script>
<script src="./assets/js/moment.js"></script>
<script src="./assets/js/script.js"></script>

</body>

</html>