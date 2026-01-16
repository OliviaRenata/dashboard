<?php session_start();
if (isset($_SESSION['logado'])) {
    require "core/mysql.php";
    require "bin/funcoes.bin.php";
    require "version.php";
    $pageid = "dashboard";
    $pagenum = "1";
    require "core/controleDePermissoes.php"; 

    // Criar instância da classe correta
    $localsql = new Localsql(); // ajuste para o nome real da sua classe

    // Buscar dados do vendedor estrela
    $vendedorEstrela = $localsql->getVendedorEstrela($idempresa, $pdom);
    
    // Buscar totais do dia
    $totaisDia = $localsql->getTotaisVendasDia($idempresa, $pdom);
    
    // Buscar indicadores dos vendedores
    $indicadoresVendedores = $localsql->getIndicadoresVendedores($idempresa, $pdom);
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
        
        /* Estilos para o dashboard de vendedores */
        .dashboard-vendedores {
            margin-top: 2rem;
        }
        
        .table-vendedores {
            font-size: 0.9rem;
        }
        
        .table-vendedores th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }
        
        .badge-perfomance {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .card-vendedor {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .card-vendedor:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }
        
        .card-vendedor .card-header {
            background: linear-gradient(135deg, #0099ff 0%, #0078cc 100%);;
            color: white;
            border-bottom: none;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
 
        .ranking-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        
        /* Novos estilos para gráficos lado a lado */
        .graficos-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 2rem;
        }
        
        .grafico-card {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .grafico-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }
        
        .grafico-header {
            background: linear-gradient(135deg, #0099ff 0%, #0078cc 100%);
            color: white;
            padding: 14px 18px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        
        .grafico-header i {
            margin-right: 8px;
            font-size: 1.2rem;
        }
        
        .grafico-body {
            padding: 20px;
            height: 300px;
            position: relative;
        }
        
        @media (max-width: 992px) {
            .graficos-container {
                flex-direction: column;
            }
            
            .grafico-card {
                min-width: 100%;
            }
        }
        
        /* Estilos para gráficos específicos */
        .grafico-largo {
            flex: 2;
            min-width: 500px;
        }
        
        .grafico-alto {
            height: 400px;
        }
        
        .grafico-body-alto .grafico-body {
            height: 350px;
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
<div id="content" class="container-fluid pt-4 px-4">

    <!-- Company Header -->
    <div class="company-header">
        <div class="d-flex justify-content-between align-items-center">

            <!-- Lado esquerdo -->
            <div>
                <h3><?= $idempresa ?> - <?= $apelido ?></h3>

                <div class="user-info-company">
                    <i class="fas fa-user me-2"></i>
                    <?= $_SESSION['idlogin']; ?>.<?= $_SESSION['nome']; ?>
                </div>
            </div>

            <!-- Lado direito -->
            <div class="d-flex align-items-center gap-3">

                <!-- Vendedor Estrela -->
                <div class="card-stat border card-vendedor-estrela">
                    <i class="fa-solid fa-star stat-icon text-warning"></i>
                    <div class="stat-content text-end">
                        <p class="stat-label mb-1">Vendedor Estrela</p>
                        <h6 id="nomevendedor" class="stat-value mb-0">
                            <?= htmlspecialchars($vendedorEstrela['nome_vendedor'] ?? 'Nenhum') ?>
                        </h6>
                    </div>
                </div>

                <!-- Ícone da empresa -->
                <i class="fas fa-building company-icon"></i>

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


                
                <!-- Gráficos lado a lado -->
                <div class="graficos-container">
                    <!-- Gráfico 1: Evolução Vendas por Vendedor -->
                    <div class="grafico-card grafico-largo border">
                        <div class="grafico-header">
                            <i class="bi bi-graph-up"></i> Evolução Vendas por Vendedor (Últimos 10 dias)
                        </div>
                        <div class="grafico-body">
                            <canvas id="graficoVendedores"></canvas>
                        </div>
                    </div>
                    
                    <!-- Gráfico 2: Evolução Geral de Vendas -->
                    <div class="grafico-card border">
                        <div class="grafico-header">
                            <i class="bi bi-graph-up"></i> Evolução Geral (Últimos 10 dias)
                        </div>
                        <div class="grafico-body">
                            <canvas id="graficoVendas10Dias"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard de Vendedores -->
<div class="dashboard-vendedores mt-4  border">

    <!-- Top 5 Vendedores - Mais Vendas -->
   <div class="row mb-4 justify-content-center">
        <div class="col-10">
            <div class="card shadow-sm card-vendedor">
                <div class="card-header">
                    <i class="fas fa-trophy me-2"></i>
                    Top 5 Vendedores - Mais Vendas (Últimos 30 dias)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-vendedores mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Vendedor</th>
                                    <th class="text-end">Vendas</th>
                                    <th class="text-end">Valor Total</th>
                                    <th class="text-end">Comissão</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaTopLucro">
                                <tr>
                                    <td colspan="5" class="text-center">Carregando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Vendedores - Mais Desconto -->
    <div class="row mb-4 justify-content-center">
        <div class="col-10">
            <div class="card shadow-sm card-vendedor">
                <div class="card-header">
                    <i class="fas fa-percent me-2"></i>
                    Top 5 Vendedores - Mais Desconto
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-vendedores mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Vendedor</th>
                                    <th class="text-end">Qtd Vendas</th>
                                    <th class="text-end">Total Desconto</th>
                                    <th class="text-end">% Médio</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaTopDesconto">
                                <tr>
                                    <td colspan="5" class="text-center">Carregando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
 
                 </div>
                </div>
 
   
                 </div>
                </div>
                    
                    <!-- Metas e Performance -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <i class="fas fa-bullseye me-2"></i>
                                    Metas e Performance dos Vendedores (Este Mês)
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="tabelaMetas">
                                            <thead>
                                                <tr>
                                                    <th>Vendedor</th>
                                                    <th class="text-center">Vendas (Mês)</th>
                                                    <th class="text-center">Valor Total</th>
                                                    <th class="text-center">Ticket Médio</th>
                                                    <th class="text-center">Comissão Total</th>
                                                    <th class="text-center">Performance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dados serão preenchidos via JavaScript -->
                                                <tr><td colspan="6" class="text-center">Carregando...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Alerts Section -->


        <?php require "home/home.back.to.top.php" ?>
        <?php require "core/importacoes_js.php"; ?>
        <?php require "footer.php"; ?>
<script>
    // ===================================================
    // FUNÇÕES DE FORMATAÇÃO
    // ===================================================
    function formatarNumero(num) {
        return new Intl.NumberFormat('pt-BR').format(num || 0);
    }

    function formatarBRL(valor) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor || 0);
    }

    function formatarPercentual(valor) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'percent',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format((valor || 0) / 100);
    }

    // ===================================================
    // FUNÇÕES PARA BUSCAR DADOS
    // ===================================================
    
    // Vendas do dia (APENAS COMISSÃO E DESCONTO)
    async function updateVendasDia() {
        try {
            const response = await fetch('core/dashboardVendedores.php?acao=totais_dia');
            if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
            const data = await response.json();

            if (data) {
                // Atualiza apenas os elementos que existem no HTML
                if (document.getElementById('comissaoDia')) {
                    document.getElementById('comissaoDia').textContent = formatarBRL(data.vlr_comissao);
                }
                if (document.getElementById('descontoDia')) {
                    document.getElementById('descontoDia').textContent = formatarBRL(data.vlr_desconto);
                }
            }
        } catch (error) {
            console.error('Erro ao buscar vendas do dia:', error);
        }
    }

    // Vendedor estrela (APENAS NOME E COMISSÃO)
    async function updateAlertasvendedor() {
        try {
            const response = await fetch('core/dashboardVendedores.php?acao=vendedor_estrela');
            if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
            const data = await response.json();

            if (data) {
                // Atualiza apenas os elementos que existem no HTML
                if (document.getElementById('nomevendedor')) {
                    document.getElementById('nomevendedor').textContent = data.nome_vendedor || 'Nenhum';
                }
                if (document.getElementById('lucrovendasvendedor')) {
                    document.getElementById('lucrovendasvendedor').textContent = formatarBRL(data.valor_comissao || 0);
                }
            }
        } catch (error) {
            console.error('Erro ao buscar vendedor estrela:', error);
        }
    }

    // Top vendedores
    async function carregarTopVendedores() {
        try {
            const [responseLucro, responseDesconto, responseMetas] = await Promise.all([
                fetch('core/dashboardVendedores.php?acao=mais_lucro&limit=5'),
                fetch('core/dashboardVendedores.php?acao=mais_desconto&limit=5'),
                fetch('core/dashboardVendedores.php?acao=metas')
            ]);

            if (responseLucro.ok) {
                const dataLucro = await responseLucro.json();
                atualizarTopLucro(dataLucro);
            }

            if (responseDesconto.ok) {
                const dataDesconto = await responseDesconto.json();
                atualizarTopDesconto(dataDesconto);
            }

            if (responseMetas.ok) {
                const dataMetas = await responseMetas.json();
                atualizarMetas(dataMetas);
            }
        } catch (error) {
            console.error('Erro ao carregar top vendedores:', error);
        }
    }

    // REMOVIDA: Função updateDadosEstoque - elementos não existem mais

function atualizarTopLucro(vendedores) {
    const tbody = document.getElementById('tabelaTopLucro');
    if (!tbody) return;

    if (!Array.isArray(vendedores) || vendedores.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Nenhuma venda registrada
                </td>
            </tr>
        `;
        return;
    }

tbody.innerHTML = '';

vendedores.forEach((vendedor, index) => {
    const row = document.createElement('tr');

    row.innerHTML = `
        <td class="text-center">
            <span class="ranking-number">${index + 1}</span>
        </td>

        <td class="vendedor-col">
            <span class="vendedor-nome" title="${vendedor.nome_vendedor || ''}">
                <strong>${vendedor.nome_vendedor || 'Sem nome'}</strong>
            </span>
        </td>
            <td class="text-end fw-bold">
                ${formatarNumero(vendedor.qtd_vendas || 0)}
            </td>
            <td class="text-end text-success fw-bold">
                ${formatarBRL(vendedor.valor_total || 0)}
            </td>
            <td class="text-end text-primary">
                ${formatarBRL(vendedor.valor_comissao || 0)}
            </td>
        `;
        tbody.appendChild(row);
    });
}

    function atualizarTopDesconto(vendedores) {
        const tbody = document.getElementById('tabelaTopDesconto');
        if (!tbody) return;

        if (!vendedores || vendedores.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhuma venda registrada</td></tr>';
            return;
        }

tbody.innerHTML = '';

vendedores.forEach((vendedor, index) => {
    const row = document.createElement('tr');

    row.innerHTML = `
        <td class="text-center">
            <span class="ranking-number">${index + 1}</span>
        </td>

        <td class="vendedor-col">
            <span class="vendedor-nome" title="${vendedor.nome_vendedor || ''}">
                ${vendedor.nome_vendedor || 'Sem nome'}
            </span>
        </td>
                <td class="text-end">${formatarNumero(vendedor.qtd_vendas || 0)}</td>
                <td class="text-end text-danger fw-bold">${formatarBRL(vendedor.total_desconto || 0)}</td>
                <td class="text-end">${formatarPercentual(vendedor.media_percentual || 0)}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function atualizarMetas(metas) {
        const tbody = document.querySelector('#tabelaMetas tbody');
        if (!tbody) return;

        if (!metas || metas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhuma venda registrada</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        const maxValor = Math.max(...metas.map(m => m.valor_total_mes || 0));

        metas.forEach(meta => {
            const porcentagem = maxValor > 0 ? (meta.valor_total_mes / maxValor) * 100 : 0;

            const row = document.createElement('tr');
            row.innerHTML = `
<td>
    <strong class="vendedor-nome">
        ${meta.nome_vendedor || 'Sem vendedor'}
    </strong>
</td>

                <td class="text-center">${formatarNumero(meta.qtd_vendas_mes || 0)}</td>
                <td class="text-center fw-bold">${formatarBRL(meta.valor_total_mes || 0)}</td>
                <td class="text-center">${formatarBRL(meta.ticket_medio || 0)}</td>
                <td class="text-center text-primary">${formatarBRL(meta.total_comissao || 0)}</td>
                <td>
                    <div class="progress progress-vendedor">
                        <div class="progress-bar bg-success" 
                             role="progressbar" 
                             style="width: ${porcentagem}%"
                             aria-valuenow="${porcentagem}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted">${porcentagem.toFixed(1)}% do líder</small>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // ===================================================
    // FUNÇÕES PARA GRÁFICOS
    // ===================================================
    function mostrarMensagemGraficoVazio(canvasId, mensagem) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Destruir gráfico anterior se existir
        if (canvas._chartInstance) {
            canvas._chartInstance.destroy();
            canvas._chartInstance = null;
        }

        ctx.font = '14px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(mensagem, canvas.width / 2, canvas.height / 2);
    }

    async function carregarGraficoVendedores() {
        const canvas = document.getElementById('graficoVendedores');
        if (!canvas) {
            console.error('Canvas #graficoVendedores não encontrado');
            return;
        }

        try {
            const resp = await fetch('core/dashboardVendedores.php?acao=evolucao_vendedores');
            if (!resp.ok) throw new Error(`Erro HTTP: ${resp.status} - ${resp.statusText}`);
            
            const data = await resp.json();

            // Verifica se há dados
            if (!data || Object.keys(data).length === 0) {
                mostrarMensagemGraficoVazio('graficoVendedores', 'Nenhuma venda registrada nos últimos 10 dias');
                return;
            }

            // Labels dos últimos 10 dias
            const labels = [];
            const today = new Date();
            for (let i = 9; i >= 0; i--) {
                const d = new Date(today);
                d.setDate(today.getDate() - i);
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                labels.push(`${yyyy}-${mm}-${dd}`);
            }

            // Preparar datasets
            const datasets = Object.values(data).map((vendedor, idx) => {
                const valores = labels.map(d => vendedor.vendas[d] || 0);
                const hue = (idx * 47) % 360; // cores distintas por vendedor

                return {
                    label: vendedor.nome || 'Sem nome',
                    data: valores,
                    borderColor: `hsl(${hue}, 65%, 40%)`,
                    backgroundColor: `hsla(${hue}, 65%, 40%, 0.12)`,
                    fill: false,
                    tension: 0.25,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    borderWidth: 2
                };
            });

            // Destruir gráfico anterior se existir
            if (canvas._chartInstance) {
                canvas._chartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            canvas._chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.map(d => {
                        const [ano, mes, dia] = d.split('-');
                        return `${dia}/${mes}`;
                    }),
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { 
                        mode: 'index', 
                        intersect: false 
                    },
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { 
                                boxWidth: 12, 
                                padding: 10,
                                font: { size: 11 } 
                            } 
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctxItem) => {
                                    const valor = ctxItem.parsed.y;
                                    return `${ctxItem.dataset.label}: R$ ${valor.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })}`;
                                }
                            }
                        },
                        title: { 
                            display: true, 
                            text: 'Vendas por Vendedor (últimos 10 dias)',
                            font: { size: 14 }
                        }
                    },
                    scales: {
                        y: {
                            title: { display: true, text: 'Valor (R$)' },
                            beginAtZero: true,
                            ticks: { callback: function(value) { return formatarBRL(value); } },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            title: { display: true, text: 'Data' },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        }
                    }
                }
            });

        } catch (err) {
            console.error('Erro ao carregar gráfico de vendedores:', err);
            mostrarMensagemGraficoVazio('graficoVendedores', 'Erro ao carregar dados');
        }
    }

    async function carregarGraficoGeral() {
        const canvas = document.getElementById('graficoVendas10Dias');
        if (!canvas) {
            console.error('Canvas #graficoVendas10Dias não encontrado');
            return;
        }

        try {
            const resp = await fetch('core/dashboardVendedores.php?acao=grafico_10_dias');
            if (!resp.ok) throw new Error(`Erro HTTP: ${resp.status} - ${resp.statusText}`);
            
            const data = await resp.json();
            
            if (!Array.isArray(data)) {
                throw new Error('Resposta inválida da API');
            }

            if (data.length === 0) {
                mostrarMensagemGraficoVazio('graficoVendas10Dias', 'Nenhuma venda registrada nos últimos 10 dias');
                return;
            }

            // Ordenar por data crescente para o gráfico
            data.sort((a, b) => new Date(a.data) - new Date(b.data));
            
            const labels = data.map(item => {
                const [ano, mes, dia] = item.data.split('-');
                return `${dia}/${mes}`;
            });
            
            const vlrTotalData = data.map(item => parseFloat(item.vlr_total) || 0);
            const descontoData = data.map(item => parseFloat(item.vlr_desconto) || 0);
            const numVendasData = data.map(item => parseInt(item.num_vendas) || 0);

            // Destruir gráfico anterior se existir
            if (canvas._chartInstance) {
                canvas._chartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            canvas._chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Valor Total (R$)',
                            data: vlrTotalData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Desconto Total (R$)',
                            data: descontoData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2
                        },
                        {
                            label: 'Nº de Vendas',
                            data: numVendasData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y2',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    stacked: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Evolução de Vendas, Descontos e Quantidade (10 dias)',
                            font: {
                                size: 14
                            }
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
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y2: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Número de Vendas'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    }
                }
            });

        } catch (err) {
            console.error('Erro ao carregar gráfico geral:', err);
            mostrarMensagemGraficoVazio('graficoVendas10Dias', 'Erro ao carregar dados');
        }
    }

    // ===================================================
    // INICIALIZAÇÃO
    // ===================================================
    document.addEventListener('DOMContentLoaded', () => {
        // Carregar dados iniciais (SEM updateDadosEstoque)
        const carregamentosIniciais = [
            updateVendasDia(),
            updateAlertasvendedor(),
            carregarTopVendedores(),
            carregarGraficoVendedores(),
            carregarGraficoGeral()
        ];

        // Tratar erros individuais sem parar a execução
        Promise.allSettled(carregamentosIniciais).then(results => {
            results.forEach((result, index) => {
                if (result.status === 'rejected') {
                    console.warn(`Falha ao carregar dados ${index}:`, result.reason);
                }
            });
        });

        // Atualizações periódicas (SEM estoque)
        setInterval(updateVendasDia, 30000);    // 30 segundos
        setInterval(updateAlertasvendedor, 60000); // 1 minuto
        setInterval(carregarTopVendedores, 120000); // 2 minutos
        setInterval(carregarGraficoVendedores, 300000); // 5 minutos
        setInterval(carregarGraficoGeral, 300000); // 5 minutos

        // Animações para cards
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.card-stat').forEach(card => observer.observe(card));
    });

    // Expor funções para possível uso externo
    window.dashboardVendedores = {
        updateVendasDia,
        updateAlertasvendedor,
        carregarTopVendedores,
        carregarGraficoVendedores,
        carregarGraficoGeral,
        formatarNumero,
        formatarBRL,
        formatarPercentual
    };
</script>
<?php
} else {
    header('Location:signin');
}
?>

 </body>
 </html>
