<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('title') ?>
Mobile WMS New
<?= $this->endSection() ?>

<!-- page content start -->
<?= $this->section('content') ?>

<style>
body{
  background-color: #ededed;
}

.card-panel {
  height: 180px;       /* adjust as needed */
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>

<!--
<div class="container">

  <div class="row">
    <div class="col s6">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Palletizing</p>
      </div>
    </div>

    <div class="col s6">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Palletizing</p>
      </div>
    </div>

  </div>

</div>
-->

  <div class="row">
    <div class="col s6">
      <a href="/mobile/palletizing">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Palletizing</p>
      </div>
      </a>
    </div>

    <div class="col s6">
      <a href="/mobile/staging">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Staging</p>
      </div>
      </a>
    </div>

    <div class="col s6">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Moving</p>
      </div>
    </div>

    <div class="col s6">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Splitting</p>
      </div>
    </div>

    <div class="col s6">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Delivery</p>
      </div>
    </div>

    <div class="col s6">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Outbound</p>
      </div>
    </div>

    <div class="col s6">
      <div class="card-panel">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Stock</p>
      </div>
    </div>

  </div>

  <div class="row">
    <!-- Palletizing -->
    <div class="col s6 menu-tile">
      <a href="#page2" class="center-align">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Palletizing</p>
      </a>
    </div>
  </div>

  <div class="row">
    <!-- Tile 1 -->
    <div class="col s6">
      <a href="#page1" class="menu-tile center-align">
        <i class="material-icons large green-text">home</i>
        <p>Home</p>
      </a>
    </div>

    <!-- Tile 2 -->
    <div class="col s6">
      <a href="#page2" class="menu-tile center-align">
        <i class="material-icons large blue-text">shopping_cart</i>
        <p>Orders</p>
      </a>
    </div>

    <!-- Tile 3 -->
    <div class="col s6">
      <a href="#page3" class="menu-tile center-align">
        <i class="material-icons large red-text">people</i>
        <p>Users</p>
      </a>
    </div>

    <!-- Tile 4 -->
    <div class="col s6">
      <a href="#page4" class="menu-tile center-align">
        <i class="material-icons large orange-text">settings</i>
        <p>Settings</p>
      </a>
    </div>

    <!-- Tile 5 -->
    <div class="col s6">
      <a href="#page5" class="menu-tile center-align">
        <i class="material-icons large purple-text">assessment</i>
        <p>Reports</p>
      </a>
    </div>

    <!-- Tile 6 -->
    <div class="col s6">
      <a href="#page6" class="menu-tile center-align">
        <i class="material-icons large teal-text">help</i>
        <p>Help</p>
      </a>
    </div>
  </div>
</div>


<div class="row">
  <div class="col s12 m6">
    <h4>Main Page</h4>
  </div>

  <?php
    print_r(session()->get());
  ?>
</div>

<script src="/assets/js/jquery.min.js"></script>
<script>

$(document).ready(function(){
  console.log('jquery loaded');

});


</script>

<?= $this->endSection() ?>
<!-- page content end -->