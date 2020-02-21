<!DOCTYPE html>
<html lang="en">

  <head>
    <?php $this->load->view('admin/_partials/head.php') ?>
  </head>

  <body id="page-top">

    <!-- Navbar -->
    <?php $this->load->view('admin/_partials/navbar.php') ?>

    <div id="wrapper">

      <!-- Sidebar -->
      <?php $this->load->view('admin/_partials/sidebar.php') ?>

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Breadcrumbs-->
          <?php // $this->load->view('breadcrumb.php') ?>

          <h2 style="font-size:3rem;text-align:center;position: absolute;top: 50%; left: 50%;margin-right: -50%;transform: translate(-50%, -50%);"> Text Mining<br/>Tools Deteksi Trending Topic</h2>
          <!-- Area Chart Example-->
          <!-- <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-chart-area"></i>
              Area Chart Example</div>
            <div class="card-body">
              <canvas id="myAreaChart" width="100%" height="30"></canvas>
            </div>
            <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
          </div> -->

          <!-- DataTables Example -->
          <!-- <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Data Table Example</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Position</th>
                      <th>Office</th>
                      <th>Age</th>
                      <th>Start date</th>
                      <th>Salary</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Name</th>
                      <th>Position</th>
                      <th>Office</th>
                      <th>Age</th>
                      <th>Start date</th>
                      <th>Salary</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <tr>
                      <td>Tiger Nixon</td>
                      <td>System Architect</td>
                      <td>Edinburgh</td>
                      <td>61</td>
                      <td>2011/04/25</td>
                      <td>$320,800</td>
                    </tr>
                    <tr>
                      <td>Garrett Winters</td>
                      <td>Accountant</td>
                      <td>Tokyo</td>
                      <td>63</td>
                      <td>2011/07/25</td>
                      <td>$170,750</td>
                    </tr>
                    <tr>
                      <td>Ashton Cox</td>
                      <td>Junior Technical Author</td>
                      <td>San Francisco</td>
                      <td>66</td>
                      <td>2009/01/12</td>
                      <td>$86,000</td>
                    </tr>
                    <tr>
                      <td>Cedric Kelly</td>
                      <td>Senior Javascript Developer</td>
                      <td>Edinburgh</td>
                      <td>22</td>
                      <td>2012/03/29</td>
                      <td>$433,060</td>
                    </tr>
                    <tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
          </div> -->

        </div>
        <!-- /.container-fluid -->

        <!-- Sticky Footer -->
        <?php $this->load->view('admin/_partials/footer.php') ?>

      </div>
      <!-- /.content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Scroll to Top Button-->
    <?php $this->load->view('admin/_partials/scrolltop.php') ?>

    <!-- Logout Modal-->
    <?php $this->load->view('admin/_partials/modal.php') ?>

	<!-- Bootstrap core JavaScript-->
  <?php $this->load->view('admin/_partials/js.php') ?>


  </body>

</html>