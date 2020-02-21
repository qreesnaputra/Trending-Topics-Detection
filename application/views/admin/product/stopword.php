<!DOCTYPE html>
<html lang="en">

<head>
	<?php $this->load->view("admin/_partials/head.php") ?>
</head>

<body id="page-top">

	<?php $this->load->view("admin/_partials/navbar.php") ?>
	<div id="wrapper">

		<?php $this->load->view("admin/_partials/sidebar.php") ?>

		<div id="content-wrapper">

			<div class="container-fluid">

				<?php $this->load->view("admin/_partials/breadcrumb.php") ?>

				<!-- DataTables -->
				<div class="card mb-3">
					<div class="card-header">
						<form action="<?php echo base_url('index.php/admin/stopword/add') ?>" method="POST">
						<button type="add">Add</button>
						</form>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
									<thead>
										<tr>
											<th>Id</th>
											<th>Stopword</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($stopword as $stop): ?>
										<tr>
											<td width="150">
												<?php echo $stop->Id ?>
											</td>
											<td>
												<?php echo $stop->stopword ?>
											</td>
										</tr>
										<?php endforeach; ?>

									</tbody>
								</table>
							</div>
						</div>
					</div>	
				</div>

			</div>
			<!-- /.container-fluid -->

			<!-- Sticky Footer -->
			<?php $this->load->view("admin/_partials/footer.php") ?>

		</div>
		<!-- /.content-wrapper -->

	</div>
	<!-- /#wrapper -->


	<?php $this->load->view("admin/_partials/scrolltop.php") ?>
	<?php $this->load->view("admin/_partials/modal.php") ?>

	<?php $this->load->view("admin/_partials/js.php") ?>

    <script>
    function deleteConfirm(url){
	$('#btn-delete').attr('href', url);
	$('#deleteModal').modal();
}
</script>
</body>

</html>