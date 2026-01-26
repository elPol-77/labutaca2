<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - La Butaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/labutaca2_logo.ico') ?>">

    <style>
        body { background-color: #f4f6f9; min-height: 100vh; display: flex; }
        .sidebar { min-width: 250px; background: #343a40; color: white; min-height: 100vh; display: flex; flex-direction: column; }
        .sidebar a { color: #c2c7d0; text-decoration: none; padding: 12px 20px; display: block; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar a:hover, .sidebar a.active { background: #007bff; color: white; padding-left: 25px; transition: 0.3s; }
        .content { flex: 1; padding: 30px; overflow-y: auto; }
        .card-kpi { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: relative; overflow: hidden; }
        .kpi-icon { font-size: 4rem; opacity: 0.2; position: absolute; right: 10px; bottom: -10px; transform: rotate(-15deg); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center">
        <h4>La Butaca <span class="badge bg-danger" style="font-size:0.6em; vertical-align:top;">Panel</span></h4>
    </div>
    <a href="<?= base_url('admin') ?>"><i class="fa fa-tachometer-alt me-2"></i> Dashboard</a>
    <a href="<?= base_url('admin/peliculas') ?>"><i class="fa fa-film me-2"></i> Películas</a>
    <a href="<?= base_url('admin/series') ?>"><i class="fa fa-tv me-2"></i> Series</a>
    <a href="<?= base_url('admin/usuarios') ?>"><i class="fa fa-users me-2"></i> Usuarios</a>
    
    <div class="mt-auto">
        <a href="<?= base_url('/') ?>" target="_blank" class="bg-dark"><i class="fa fa-globe me-2"></i> Ir a la Web</a>
        <a href="<?= base_url('logout') ?>" class="text-danger bg-dark"><i class="fa fa-sign-out-alt me-2"></i> Cerrar Sesión</a>
    </div>

    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

</div>

<div class="content">