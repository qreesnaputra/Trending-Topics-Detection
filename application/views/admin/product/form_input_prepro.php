<!DOCTYPE html>
<html lang="en">

<head>
	<?php $this->load->view("admin/_partials/head.php") ?>
	<script src="http://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>
	<link href="http://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet"/>

</head>

<body id="page-top">

	<?php $this->load->view("admin/_partials/navbar.php") ?>
	<div id="wrapper">

		<?php $this->load->view("admin/_partials/sidebar.php") ?>

		<div id="content-wrapper">

			<div class="container-fluid">

				<?php $this->load->view("admin/_partials/breadcrumb.php") ?>

				<!-- DataTables -->
				<?php
					if($this->session->flashdata('item')){
					$message = $this->session->flashdata('item');
					?>
					<div class="<?php echo $message['class']?>"><?php echo $message['message']; ?></div>
					<?php 
					}
				?>

				<div class="card mb-3">
					<div class="card-header">
						<form action="<?php echo base_url('index.php/admin/prepro/post') ?>" method="POST">
						<table>
							<tr>
								<td>Masukan tanggal awal : </td>
								<td><input type="date" name="start_date" placeholder="Date"></td>
								<td>
									<input type=”text” name='start_time' value='00:00:00'>
								</td>
							</tr>
							<tr>
								<td>Masukan tanggal akhir : </td>
								<td><input type="date" name="end_date" placeholder="Date"></td>
								<td><input type=”text” name='end_time' value='00:00:00'></td>
							</tr>
							<tr><td><button type="submit">submit</button></td></tr>
						</table>
						</form>
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