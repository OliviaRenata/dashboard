<?php session_start();
// print_r($_SESSION); 
if (isset($_SESSION['logado'])) {
    require "core/mysql.php";
    require "bin/funcoes.bin.php";
    require "version.php";
    $pageid = "dashboard";
    $pagenum = "1";
    require "core/controleDePermissoes.php"; 
?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <?php require "core/header.php"; ?>

    <style>
        :root {
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            --transition-speed: 0.3s;
            --card-height: 120px;
        }

        .company-header {
            background: linear-gradient(135deg, #009cff 0%, #0078cc 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 156, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .company-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            transform: rotate(45deg);
        }

        .company-header h3 {
            color: white;
            font-weight: 700;
            font-size: 2.2rem;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }

        .company-icon {
            position: absolute;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.3);
            z-index: 1;
        }

        .user-welcome {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f4ff 100%);
            border: 1px solid #009cff;
            border-radius: 15px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            box-shadow: 0 5px 20px rgba(0, 156, 255, 0.1);
        }

        .user-welcome h2 {
            color: #009cff;
            font-weight: 600;
            font-size: 1.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-icon {
            background: linear-gradient(135deg, #009cff, #0078cc);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 156, 255, 0.3);
        }

        .welcome-text {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
            font-style: italic;
        }

        /* CARDS UNIFORMES E RESPONSIVOS */
        .stats-container {
            margin-top: 2rem;
        }

        .card-stat {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all var(--transition-speed) ease;
            position: relative;
            z-index: 1;
            height: var(--card-height);
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            box-shadow: var(--card-shadow);
            background: white;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow) !important;
        }

        .card-stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            z-index: -1;
        }

        .stat-icon {
            font-size: 2.2rem;
            opacity: 0.8;
            transition: all var(--transition-speed) ease;
            min-width: 50px;
            text-align: center;
        }

        .card-stat:hover .stat-icon {
            transform: scale(1.1);
            opacity: 1;
        }

        .stat-content {
            flex: 1;
            text-align: right;
            min-width: 0;
            /* Permite que o texto quebre corretamente */
        }

        .stat-label {
            font-size: 0.75rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
            font-weight: 600;
            line-height: 1.2;
            color: #6c757d;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #2c3e50;
            line-height: 1.2;
            margin: 0;
        }

        .click-text {
            position: absolute;
            right: 0.2rem;
            font-size: 0.65rem;
            color: #009cff !important;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-weight: bold;
            opacity: 0;
            transform: translateY(10px);
            transition: all var(--transition-speed) ease;
        }

        .card-stat:hover .click-text {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 156, 255, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(0, 156, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(0, 156, 255, 0);
            }
        }

        /* ANIMAÇÕES */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .company-header,
        .user-welcome {
            animation: fadeInUp 0.6s ease-out;
        }

        .user-welcome {
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }

        .dashboard-section {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* RESPONSIVIDADE AVANÇADA */
        @media (max-width: 1400px) {
            :root {
                --card-height: 110px;
            }

            .stat-icon {
                font-size: 2rem;
                min-width: 45px;
            }

            .stat-value {
                font-size: 1.3rem;
            }

            .stat-label {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 1200px) {
            :root {
                --card-height: 100px;
            }

            .card-stat {
                padding: 0.875rem 1rem;
            }

            .stat-icon {
                font-size: 1.8rem;
                min-width: 40px;
            }

            .stat-value {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 992px) {
            .company-header {
                padding: 1.5rem;
            }

            .company-header h3 {
                font-size: 1.8rem;
            }

            .company-icon {
                font-size: 2.5rem;
                right: 1.5rem;
            }

            .user-welcome {
                padding: 1.25rem;
            }

            .user-welcome h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            :root {
                --card-height: 90px;
            }

            .company-header {
                padding: 1.2rem;
                border-radius: 15px;
                margin-bottom: 1rem;
            }

            .company-header h3 {
                font-size: 1.5rem;
                max-width: 70%;
            }

            .company-icon {
                font-size: 2rem;
                right: 1rem;
            }

            .user-welcome {
                padding: 1rem;
                border-radius: 12px;
            }

            .user-welcome h2 {
                font-size: 1.3rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .user-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .card-stat {
                padding: 0.75rem;
            }

            .stat-icon {
                font-size: 1.6rem;
                min-width: 35px;
            }

            .stat-value {
                font-size: 1.1rem;
            }

            .stat-label {
                font-size: 0.65rem;
            }

            .click-text {
                font-size: 0.6rem;
                right: 6px;
                bottom: 6px;
            }
        }

        @media (max-width: 576px) {
            :root {
                --card-height: 85px;
            }

            .company-header h3 {
                font-size: 1.2rem;
            }

            .user-welcome h2 {
                font-size: 1.1rem;
            }

            .card-stat {
                padding: 0.625rem;
            }

            .stat-icon {
                font-size: 1.4rem;
                min-width: 30px;
            }

            .stat-value {
                font-size: 1rem;
            }

            .stat-label {
                font-size: 0.6rem;
                letter-spacing: 0.3px;
            }

            .click-text {
                display: none;
                /* Esconde em telas muito pequenas */
            }

            /* Garante que o ícone permaneça redondo */
            .user-icon {
                border-radius: 50% !important;
                aspect-ratio: 1/1;
            }
        }

        @media (max-width: 400px) {
            :root {
                --card-height: 80px;
            }

            .stat-icon {
                font-size: 1.2rem;
            }

            .stat-value {
                font-size: 0.9rem;
            }

            .stat-label {
                font-size: 0.55rem;
            }
        }

        /* Grid system adjustments */
        .row.g-4>[class*="col-"] {
            display: flex;
            flex-direction: column;
        }

        .row.g-4>[class*="col-"]>.card-stat {
            flex: 1;
        }

        /* Ajustes específicos para textos longos */
        .text-long-break {
            word-break: break-word;
            hyphens: auto;
        }

        .user-info-company {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 0.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .user-info-company i {
            opacity: 0.8;
        }

        /* Ajustes responsivos para o user-info-company */
        @media (max-width: 992px) {
            .user-info-company {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 768px) {
            .user-info-company {
                font-size: 0.8rem;
                margin-top: 0.25rem;
            }
        }

        @media (max-width: 576px) {
            .user-info-company {
                font-size: 0.75rem;
            }
        }

        .company-header {
            background: linear-gradient(135deg, #009cff 0%, #0078cc 100%);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 156, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .company-header .d-flex {
            position: relative;
            z-index: 2;
        }
    </style>
  <style>
    :root {
      --cor-primaria: #0099ff;
      --cor-fundo: #f5f7fa;
      --cor-card: #ffffff;
      --sombra: rgba(0, 0, 0, 0.08);
      --texto-titulo: #222;
      --texto-normal: #555;
    }

    /* body {
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      background: var(--cor-fundo);
      margin: 0;
      padding: 40px 20px;
      color: var(--texto-normal);
    } */

    h2 {
      text-align: center;
      color: var(--texto-titulo);
      font-size: 1.8rem;
      margin-bottom: 35px;
      letter-spacing: 0.5px;
    }


  .card {
    width: 90%;
    max-width: 1000px; /* era 1000px, agora mais largo */
    margin: 20px auto;
    border-radius: 12px;
    background: var(--cor-card);
    box-shadow: 0 4px 20px var(--sombra);
    overflow: hidden;
    transition: all 0.3s ease;
  }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px var(--sombra);
    }

    .card-header {
      background: var(--cor-primaria);
      color: #fff;
      padding: 14px 18px;
      font-weight: 600;
      font-size: 1rem;
      display: flex;
      align-items: center;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    .card-header i {
      margin-right: 8px;
      font-size: 1.2rem;
    }

  .card-body {
    padding: 25px;
    height: 500px; /* 🔹 aumenta altura padrão dos gráficos */
  }

  canvas {
    width: 100% !important;
    height: 100% !important;
    max-height: 600px;
  }

  /* 🔹 Ajuste para telas grandes: os gráficos ainda maiores */
  @media (min-width: 100px) {
    .card {
      max-width: 1200px;
    }
    .card-body {
      height: 550px;
    }
  }

  /* 🔹 Ajuste para mobile */
  @media (max-width: 768px) {
    .card {
      width: 100%;
      max-width: 100%;
      margin: 10px 0;
    }

    .card-body {
      padding: 15px;
      height: 350px;
    }

      h2 {
        font-size: 1.4rem;
      }
    }
  </style>
    <body>
        <div class="container-fluid position-relative bg-white d-flex p-0">
            <!-- Spinner Start -->
            <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <!-- Sidebar Start -->
            <?php require "home/home.sidebar.php" ?>
            <!-- Sidebar End -->
            <!-- Topbar Start -->
            <?php require "home/home.topbar.php" ?>
            <!-- Topbar End -->

            <!-- content Start -->
            <div id="content" class="container-fluid pt-4 px-4 ">

                                                    <!-- Company Header -->
                                                    <div class="company-header">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h3><?= $idempresa ?> - <?= $apelido ?></h3>
                                                                <!-- Usuário menor e na mesma cor -->
                                                                <div class="user-info-company">
                                                                    <i class="fas fa-user me-2"></i> <?php echo $_SESSION['idlogin']; ?>.<?php echo $_SESSION['nome']; ?>
                                                                </div>
                                                            </div>
                                                            <i class="fas fa-building company-icon"></i>
                                                        </div>
                                                    </div>

                                                    <!-- Stats Cards -->
                                                    <div class="stats-container mb-3">
                                                        <div class="row <?= $mobile == "true" ? "g-2" : "g-3" ?>">
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border ">
                                                                    <i class="fa-solid fa-cart-shopping stat-icon text-primary"></i>
                                                                    <div class="stat-content">
                                                                        <p class="stat-label">Vendas/Dia</p>
                                                                        <h6 id="numVendas" class="stat-value">...</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa fa-solid fa-dollar-sign stat-icon text-primary"></i>
                                                                    <div class="stat-content">
                                                                        <p class="stat-label">Valor Bruto</p>
                                                                        <h6 id="brutoDia" class="stat-value">...</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa fa-chart-area stat-icon text-primary"></i>
                                                                    <div class="stat-content">
                                                                        <p class="stat-label mb-1">Custo</p>
                                                                        <h3 id="custoDia" class="stat-value mb-0">...</h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa-solid fa-arrow-trend-up stat-icon text-primary"></i>
                                                                    <div class="stat-content">
                                                                        <p class="stat-label mb-1">Lucro</p>
                                                                        <h3 id="lucroDia" class="stat-value mb-0">...</h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php
                                                    $safeKey5 = htmlspecialchars(sha1($_SERVER['REMOTE_ADDR'] . "5"), ENT_QUOTES, 'UTF-8');
                                                    $safeKey6 = htmlspecialchars(sha1($_SERVER['REMOTE_ADDR'] . "6"), ENT_QUOTES, 'UTF-8');
                                                    $safeKey8 = htmlspecialchars(sha1($_SERVER['REMOTE_ADDR'] . "8"), ENT_QUOTES, 'UTF-8');
                                                    $safeKey9 = htmlspecialchars(sha1($_SERVER['REMOTE_ADDR'] . "9"), ENT_QUOTES, 'UTF-8');
                                                    $safeKey10 = htmlspecialchars(sha1($_SERVER['REMOTE_ADDR'] . "10"), ENT_QUOTES, 'UTF-8');
                                                    $safeKey11 = htmlspecialchars(sha1($_SERVER['REMOTE_ADDR'] . "11"), ENT_QUOTES, 'UTF-8');
                                                    ?>

                                                    <!-- Alerts vendedor estrela -->
                                                    <div class="container-fluid pt-4 px-0 dashboard-section" style="animation-delay: 0.2s">
                                                        <div class="row <?= $mobile == "true" ? "g-2" : "g-3" ?>">
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa-solid fa-star stat-icon text-warning"></i>
                                                                    <div class="stat-content ">
                                                                        <p class="stat-label text-long-break">Vendedor Estrela</p>
                                                                        <h6 id="nomevendedor" class="stat-value">...</h6>
                                                                    </div>
                              
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa-solid fa-receipt stat-icon text-warning"></i>
                                                                    <div class="stat-content ">
                                                                        <p class="stat-label text-long-break">Qtd Vendas</p>
                                                                        <h6 id="qtdvendasvendedor" class="stat-value">...</h6>
                                                                    </div>
                                                             
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa-solid fa-circle-dollar-to-slot stat-icon text-warning"></i>
                                                                    <div class="stat-content ">
                                                                        <p class="stat-label text-long-break">Vlr Total De Vendas</p>
                                                                        <h6 id="vendastotalvendedor" class="stat-value">...</h6>
                                                                    </div>
                                                                   
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa-solid fa-sack-dollar stat-icon text-warning"></i>
                                                                    <div class="stat-content ">
                                                                        <p class="stat-label text-long-break">Lucro Total Vendedor</p>
                                                                        <h6 id="lucrovendasvendedor" class="stat-value">...</h6>
                                                                    </div>
                                                                 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Inventory Alerts Section -->
                                                    <div class="container-fluid pt-4 px-0 dashboard-section" style="animation-delay: 0.4s">
                                                        <div class="row <?= $mobile == "true" ? "g-2" : "g-3" ?>">
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa-solid fa-triangle-exclamation stat-icon text-danger"></i>
                                                                    <div class="stat-content ">
                                                                        <p class="stat-label text-long-break">Produtos S/ Estoque</p>
                                                                        <h6 id="prodSemEst" class="stat-value">...</h6>
                                                                    </div>
                                                                
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa fa-solid fa-dollar-sign stat-icon text-danger"></i>
                                                                    <div class="stat-content">
                                                                        <p class="stat-label text-long-break">Produtos S/ Custo</p>
                                                                        <h6 id="prodSemCusto" class="stat-value">...</h6>
                                                                    </div>
                                                                   
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="bi bi-tag-fill stat-icon text-danger"></i>
                                                                    <div class="stat-content ">
                                                                        <p class="stat-label text-long-break">Produtos S/ Preço</p>
                                                                        <h6 id="prodSemPreco" class="stat-value">...</h6>
                                                                    </div>
                                                                                                                                    
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-xl-3">
                                                                <div class="card-stat border">
                                                                    <i class="fa-solid fa-barcode stat-icon text-danger"></i>
                                                                    <div class="stat-content">
                                                                        <p class="stat-label text-long-break">Produtos S/ EAN</p>
                                                                        <h6 id="prodSemCodBarras" class="stat-value">...</h6>
                                                                    </div>
                                                                   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <!-- coloque este bloco onde deseja que o gráfico apareça (ex: dentro do container do dashboard) -->
                                            <div class="card shadow-sm mt-4">
                                            <div class="card-header bg-primary text-white">
                                                <i class="bi bi-graph-up"></i> Evolução Vendas por Vendedor (Últimos 10 dias)
                                            </div>
                                            <div class="card-body" style="height:420px">
                                                <canvas id="graficoVendedores" style="width:100%; height:100%"></canvas>
                                            </div>
                                            </div>

                                            <head>
                                                <meta charset="UTF-8">
                                                <title>Evolução de Vendas - Últimos 10 Dias</title>
                                                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


                                                <h2></h2>

                                                <div class="card">
                                                    <div class="card-header">
                                                    <i class="bi bi-graph-up"></i> Evolução De Vendas (Últimos 10 dias)
                                                    </div>
                                                    <div class="card-body">
                                                    <canvas id="graficoVendas10Dias"></canvas>
                                                    </div>
                                                </div>
                                                


        <?php require "home/home.back.to.top.php" ?>
        <?php require "core/importacoes_js.php"; ?>
        <?php require "footer.php"; ?>

        <script>
            // Sales Dashboard Data
            async function updateVendasDia() {
                try {
                    const response = await fetch('core/dashboard.php');
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();

                    document.getElementById('brutoDia').textContent = data.vlr_total;
                    document.getElementById('numVendas').textContent = data.num_vendas;
                    document.getElementById('custoDia').textContent = data.custo_total;
                    document.getElementById('lucroDia').textContent = data.lucro_total;
                } catch (error) {
                    console.error('Error fetching sales data:', error);
                }
            }

            // Alerts vendedor Estrela
        
            async function updateAlertasvendedor() {
    try {
       const response = await fetch('core/dashboardAlertasVendEstrela.php');
        if (!response.ok) throw new Error('Network response was not ok');
        
        const data = await response.json();
        document.getElementById('nomevendedor').textContent = data.nomevendedor ;
        document.getElementById('qtdvendasvendedor').textContent = formatarNumero(data.qtdvendasvendedor);
        document.getElementById('vendastotalvendedor').textContent = formatarBRL(data.vendastotalvendedor);
        document.getElementById('lucrovendasvendedor').textContent = formatarBRL(data.lucrovendasvendedor);
        
    } catch (error) {
        console.error('Error fetching Vendedor Estrela data:', error);
     
    }
}

            // Inventory Data
            async function updateDadosEstoque() {
                try {
                    const response = await fetch('bin/dashboardEstoque.php');
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();

                    document.getElementById('prodSemEst').textContent = data.produtosSemSALDO;
                    document.getElementById('prodSemCusto').textContent = data.produtosSemCUSTO;
                    document.getElementById('prodSemPreco').textContent = data.produtosSemPRECO;
                    document.getElementById('prodSemCodBarras').textContent = data.produtosSemCODBAR;
                } catch (error) {
                    console.error('Error fetching inventory data:', error);
                }
            }

            // Initialize all data fetchers
            document.addEventListener('DOMContentLoaded', () => {
                // Initial load
                updateVendasDia();
                updateAlertasvendedor();
                updateDadosEstoque();

                // Set intervals for updates
                setInterval(updateVendasDia, 10000); // Update sales every 10 seconds
                setInterval(updateAlertasvendedor, 60000); // Update alerts every minute
                setInterval(updateDadosEstoque, 60000); // Update inventory every minute

                // Add animation to cards when they come into view
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                        }
                    });
                }, {
                    threshold: 0.1
                });

                document.querySelectorAll('.card-stat').forEach(card => {
                    observer.observe(card);
                });
            });
        </script>


<!-- coloque este bloco onde deseja que o gráfico apareça (ex: dentro do container do dashboard) -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
        function formatarDataBrasileira(dataISO) {
  if (!dataISO) return "";
  const partes = dataISO.split("-");
  if (partes.length !== 3) return dataISO;
  const [ano, mes, dia] = partes;
  return `${dia}/${mes}/${ano}`;
}

    
document.addEventListener('DOMContentLoaded', () => {
  carregarGraficoVendedores();
});

function getUltimosNDiasLabels(n = 10) {
  const arr = [];
  const today = new Date();
  // cria array com N dias (ordenado asc: mais antigo -> mais recente)
  for (let i = n - 1; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(today.getDate() - i);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    arr.push(`${yyyy}-${mm}-${dd}`);
  }
  return arr;
}

async function carregarGraficoVendedores() {
  try {
    const resp = await fetch('core/dashboard.php?acao=vendedores10dias');
    if (!resp.ok) throw new Error('Erro ao buscar dados: ' + resp.status);
    const data = await resp.json();

    console.log('Dados recebidos (vendedores10dias):', data); // debug: verifique formato no console

    if (!Array.isArray(data) || data.length === 0) {
      console.warn('Sem dados para os últimos 10 dias.');
      // opcional: limpar canvas ou mostrar mensagem
      return;
    }

    // labels fixas: últimos 10 dias (YYYY-MM-DD)
    const labels = getUltimosNDiasLabels(10);

    // Agrupa dados por vendedor
    // estrutura: { 'Nome Vendedor': { '2025-10-25': 123.45, ... }, ... }
    const vendedoresMap = {};
    data.forEach(row => {
      const nome = (row.nome_vendedor || row.NOME || 'Sem Vendedor').trim();
      const dataDia = row.data; // espera "YYYY-MM-DD"
      const valor = parseFloat(row.vlr_vendido ?? row.VLR_VENDIDO ?? 0) || 0;
      if (!vendedoresMap[nome]) vendedoresMap[nome] = {};
      vendedoresMap[nome][dataDia] = (vendedoresMap[nome][dataDia] || 0) + valor;
    });

    // monta datasets (um por vendedor), preenchendo zeros para datas faltantes
    const vendedores = Object.keys(vendedoresMap);
    const datasets = vendedores.map((nome, idx) => {
      const valores = labels.map(d => vendedoresMap[nome][d] || 0);
      // gerar cor HSL variável por índice
      const hue = (idx * 47) % 360; // passo para diversificar cores
      const border = `hsl(${hue}, 65%, 40%)`;
      const background = `hsla(${hue}, 65%, 40%, 0.12)`;
      return {
        label: nome,
        data: valores,
        borderColor: border,
        backgroundColor: background,
        fill: false,
        tension: 0.25,
        pointRadius: 3,
        pointHoverRadius: 6
      };
    });

    // destroi gráfico anterior se houver
    const canvas = document.getElementById('graficoVendedores');
    if (!canvas) {
      console.error('Canvas #graficoVendedores não encontrado no DOM.');
      return;
    }

    // guarda o gráfico na propriedade do elemento para permitir destruição
    if (canvas._chartInstance) {
      canvas._chartInstance.destroy();
    }

    const ctx = canvas.getContext('2d');
    canvas._chartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } },
          tooltip: {
            callbacks: {
              label: ctxItem => {
                const v = ctxItem.parsed.y;
                // formatar como BRL
                return ctxItem.dataset.label + ': R$ ' + Number(v).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
              }
            }
          },
          title: { display: true, text: 'Vendas por Vendedor (últimos 10 dias)' }
        },
        scales: {
          y: {
            title: { display: true, text: 'Valor (R$)' },
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return Number(value).toLocaleString('pt-BR', {minimumFractionDigits: 0});
              }
            }
          },
          x: {
            title: { display: true, text: 'Data' }
          }
        }
      }
    });

  } catch (err) {
    console.error('Erro ao carregar gráfico de vendedores:', err);
  }
}
</script>

  <script>
        function formatarDataBrasileira(dataISO) {
  if (!dataISO) return "";
  const partes = dataISO.split("-");
  if (partes.length !== 3) return dataISO;
  const [ano, mes, dia] = partes;
  return `${dia}/${mes}/${ano}`;
}
    async function carregarGrafico() {
      try {
        const response = await fetch('core/dashboard.php?acao=10dias');
        if (!response.ok) throw new Error('Erro na resposta da rede.');

        const data = await response.json();

        const labels = data.map(item => formatarDataBrasileira(item.data));
        const vlrTotalData = data.map(item => parseFloat(item.vlr_total));
        const custoData = data.map(item => parseFloat(item.custo_total));
        const numVendasData = data.map(item => parseInt(item.num_vendas));

        const ctx = document.getElementById('graficoVendas10Dias').getContext('2d');

        new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [
              {
                label: 'Valor Total (R$)',
                data: vlrTotalData,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3,
                fill: true
              },
              {
                label: 'Custo Total (R$)',
                data: custoData,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.3,
                fill: true
              },
              {
                label: 'Nº de Vendas',
                data: numVendasData,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3,
                fill: true,
                yAxisID: 'y2'
              }
            ]
          },
          options: {
            responsive: true,
            interaction: {
              mode: 'index',
              intersect: false
            },
            stacked: false,
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Evolução de Vendas, Custos e Quantidade (10 dias)'
              }
            },
            scales: {
              y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                  display: true,
                  text: 'Valores (R$)'
                }
              },
              y2: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                  drawOnChartArea: false
                },
                title: {
                  display: true,
                  text: 'Número de Vendas'
                }
              }
            }
          }
        });

      } catch (error) {
        console.error('Erro ao carregar gráfico:', error);
      }
    }

    carregarGrafico();
  </script>



<?php
} else {
    header('Location:signin');
}
?>

 </body>
 </html>
