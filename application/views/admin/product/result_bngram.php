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
						<!-- <a href="<?php echo site_url('admin/products/add') ?>"><i class="fas fa-plus"></i> Add New</a> -->
					</div>
					<div class="card-body">

						<div class="table-responsive">
							<table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
							<thead>
									<tr>
										<th>time slot</th>
										<th>bngram</th>
										<th>df(i)</th>
										<th>t</th>
										<th>boost</th>
										<th>dfidf</th>
									</tr>
								</thead>
								<tbody>								
									<?php foreach($list_bngram as $bngram): ?>
									<tr>
										<td width="150">
											<?php echo $bngram[1] ?>
										</td>
										<td width="300">
											<?php 
												foreach($bngram[0] as $dt){
													echo ($dt);
													echo ('</br>');
												} 
											?>
										</td>
										<td width="150">
											<?php 
												foreach($bngram[2] as $dt){
													echo ($dt);
													echo ('</br>');
												} 
											?>
										</td>
										<td width="150">
											<?php
											echo ($t); 
											?>
										</td>
										<td width="150">
											<?php
											foreach($bngram[3] as $dt){
												echo($dt);
												echo ('</br>');

											}
											?>
										</td>
										<td width="150">
											<?php
											foreach($bngram[4] as $dt){
												echo($dt);
												echo ('</br>');

											}
											?>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>

						<div class="table-responsive">
							<table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
							<thead>
									<tr>
										<th>Topic Trending</th>
										<th>Cluster Trending</th>
										<th>Relevant Tweet</th>
									</tr>
							</thead>
								<tbody>								
									<tr>
										<td width="150">
											<?php echo $hasil_akhir ?>
										</td>
										<td width="150">
											<?php echo $cluster_trending ?>
										</td>
										<td width="150">
											<?php echo $relevant_tweet ?>
										</td>
									</tr>
								</tbody>
							</table>
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