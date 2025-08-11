<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title><?= $title?></title>
  
  <!-- Tell the browser to be responsive to screen width -->
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
  <meta name="viewport" content="width=device-width" />

  <!-- Favicons -->
  <link rel="icon" href="<?=base_url()?>assets/img/favicon.jpg" type="image/jpg" sizes="16x16">
  
  <!-- Font Awesome -->
  <link href="<?=base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" />
  
  <!--  Material Dashboard CSS    -->
  <link href="<?=base_url()?>assets/css/material-dashboard.css" rel="stylesheet" />

  <!--     Fonts and icons     -->
  <link href="<?=base_url()?>assets/bootstrap/plugins/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <link href="<?=base_url()?>assets/css/google-roboto-300-700.css" rel="stylesheet" />

  <!-- Datatables -->
  <link rel="stylesheet" href="<?=base_url()?>assets/bootstrap/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
  
  <style>
    label.col-form-label {color:black;}
  </style>
	<?php 
if (isset($extracss)) {
    echo view($extracss);
}
	?>
</head>
<body>
    <div class="wrapper">
