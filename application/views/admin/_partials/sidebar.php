<!-- Sidebar -->
<ul class="sidebar navbar-nav">
    <li class="nav-item <?php echo $this->uri->segment(2) == '' ? 'active': '' ?>">
        <a class="nav-link" href="<?php echo site_url('admin') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span>
        </a>
    </li>
    <!-- <li class="nav-item dropdown <?php echo $this->uri->segment(2) == 'products' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Products</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/products/add') ?>">New Product</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/products') ?>">List Product</a>
        </div>
    </li> -->
    <li class="nav-item <?php echo $this->uri->segment(2) == '' ? 'active': '' ?>">
        <a class="nav-link" href="<?php echo base_url("index.php/admin/crawling") ?>">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Crawling</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo base_url("index.php/admin/prepro") ?>">
            <i class="fas fa-fw fa-users"></i>
            <span>Preprocessing</span></a>
    </li>
    <li class="nav-item dropdown <?php echo $this->uri->segment(2) == '' ? 'active': '' ?>">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Stop & Slang Word</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
            <a class="dropdown-item" href="<?php echo site_url('admin/stopword') ?>">Stopword</a>
            <a class="dropdown-item" href="<?php echo site_url('admin/slangword') ?>">Slangword</a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo base_url("index.php/admin/bngram") ?>">
            <i class="fas fa-fw fa-cog"></i>
            <span>BNgram</span></a>
    </li>
</ul>