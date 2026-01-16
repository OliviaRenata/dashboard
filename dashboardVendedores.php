<?php
session_start();
require "mysql.php";
// error_reporting(E_ALL); // opcional em produção, útil em dev

// Instancia a classe Localsql
$localsql = new Localsql();

// Verifica se o usuário está logado e se a empresa está definida
if (!isset($_SESSION['logado']) || !isset($_SESSION['idempresa'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$idempresa = $_SESSION['idempresa'];

// Define cabeçalho JSON
header('Content-Type: application/json');

// Função para enviar resposta JSON e sair
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

try {
    // Ação recebida via GET
    $acao = $_GET['acao'] ?? null;

    switch ($acao) {

case 'evolucao_vendedores':
    // Busca apenas os dados reais do banco
    $dados = $localsql->getEvolucaoVendedores10Dias($idempresa, $pdom);

    // Retorna JSON diretamente
    jsonResponse($dados);
    break;


        case 'mais_lucro':
            $limit = (int)($_GET['limit'] ?? 5);
            $dados = $localsql->getVendedoresMaisLucro($idempresa, $limit, $pdom);
            jsonResponse($dados);
            break;

        case 'vendedor_estrela':
            $dados = $localsql->getVendedorEstrela($idempresa, $pdom);
            jsonResponse($dados);
            break;

        case 'mais_desconto':
            $limit = (int)($_GET['limit'] ?? 5);
            $dados = $localsql->getVendedoresMaisDesconto($idempresa, $limit, $pdom);
            jsonResponse($dados);
            break;

        case 'metas':
            $dados = $localsql->getMetasVendedores($idempresa, $pdom);
            jsonResponse($dados);
            break;

        case 'totais_dia':
            $dados = $localsql->getTotaisVendasDia($idempresa, $pdom);
            jsonResponse($dados);
            break;

        case 'grafico_10_dias':
            $dados = $localsql->getDadosGrafico10Dias($idempresa, $pdom);
            jsonResponse($dados);
            break;

        case 'vendas_periodo':
            $idvendedor = $_GET['idvendedor'] ?? null;
            $dias = (int)($_GET['dias'] ?? 30);
            if ($idvendedor) {
                $dados = $localsql->getVendasVendedorPorPeriodo($idempresa, $idvendedor, $dias, $pdom);
                jsonResponse($dados);
            } else {
                jsonResponse(['error' => 'ID do vendedor não especificado'], 400);
            }
            break;

        case 'indicadores':
            $dados = $localsql->getIndicadoresVendedores($idempresa, $pdom);
            jsonResponse($dados);
            break;

        case 'vendedores_ativos':
            $searchterm = $_GET['search'] ?? '';
            $dados = $localsql->buscaVendedoresAtivosComVendas($idempresa, $searchterm, $pdom);
            jsonResponse($dados);
            break;

        case 'vendedor_por_id':
            $idvendedor = $_GET['idvendedor'] ?? null;
            if ($idvendedor) {
                $dados = $localsql->buscaVendedorPorIdComEstatisticas($idvendedor, $idempresa, $pdom);
                jsonResponse($dados);
            } else {
                jsonResponse(['error' => 'ID do vendedor não especificado'], 400);
            }
            break;

        default:
            // Sem ação ou ação não reconhecida: retorna estatísticas principais
            $response = [
                'top_5_lucro'     => $localsql->getVendedoresMaisLucro($idempresa, 5, $pdom) ?? [],
                'top_5_desconto'  => $localsql->getVendedoresMaisDesconto($idempresa, 5, $pdom) ?? [],
                'metas'           => $localsql->getMetasVendedores($idempresa, $pdom) ?? [],
                'evolucao'        => $localsql->getEvolucaoVendedores10Dias($idempresa, $pdom) ?? [],
                'totais_dia'      => $localsql->getTotaisVendasDia($idempresa, $pdom) ?? [],
                'indicadores'     => $localsql->getIndicadoresVendedores($idempresa, $pdom) ?? []
            ];
            jsonResponse($response);
            break;
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
