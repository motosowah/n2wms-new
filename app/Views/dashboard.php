<?= $this->extend('layouts/n2wms_layout') ?>

<?= $this->section('title') ?>
Dashboard - N2WMS New | Warehouse Management System
<?= $this->endSection() ?>

<?= $this->section('nav') ?>
Dashboard

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Welcome to the Dashboard!</h2>
<p>This is the main dashboard content.</p>
<pre><?php
    $username = session()->get('username');
    echo $username;
    // print_r(session());

    // print_r($_SERVER);
?></pre>
<?= $this->endSection() ?>
