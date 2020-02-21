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
						<a href="<?php echo site_url('admin/products/add') ?>"><i class="fas fa-plus"></i> Add New</a>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
								<thead>
									<tr>
										<th>CaseFolding</th>
										<th>Cleansing</th>
										<th>Tokenizing</th>
										<th>Stopword</th>
										<th>Singkatan</th>
									</tr>
								</thead>
								<tbody>								
									<?php for($i=0;$i < count($crawling["casefolding"]) ; $i++): ?>
									<tr>
										<td width="150">
											<?php echo $crawling["casefolding"][$i] ?>
										</td>
										<td width="150">
											<?php echo $crawling["cleansing"][$i] ?>
										</td>
										<td width="150">
											<?php echo implode(', ',$crawling["tokenizing"][$i]) ?>
										</td>
										<td width="150">
											<?php echo $crawling["stopword"][$i] ?>
										</td>
										<td width="150">
											<?php echo $crawling["singkatan"][$i] ?>
										</td>
									</tr>
									<?php endfor; ?>

								</tbody>
							</table>
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