<?php
class Localsql {

    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 1. Top vendedores por comissão (últimos 30 dias)
    public function getVendedoresMaisLucro($idempresa, $limit, $pdo) {
        $dataInicio = date('Y-m-d', strtotime('-29 days'));
        $dataFim    = date('Y-m-d');

        $sql = "
            SELECT 
                c.idvendedor AS IDVENDEDOR,
                COALESCE(p0.NOME,'Sem nome') AS nome_vendedor,
                COUNT(DISTINCT c.idfatent) AS qtd_vendas,
                SUM(c.valor_total) AS valor_total,
                SUM(c.total_comissao) AS valor_comissao
            FROM (
                SELECT 
                    m2.idvendedor,
                    m2.idfatent,
                    COALESCE(m0.VLRTOTAL,0) AS valor_total,
                    COALESCE(m2.vlr_dinheiro,0) + COALESCE(m2.vlr_pix,0) +
                    COALESCE(m2.vlr_cartao_deb,0) + COALESCE(m2.vlr_cartao_cre,0) +
                    COALESCE(m2.vlr_duplicata,0) + COALESCE(m2.vlr_cheque,0) +
                    COALESCE(mc3.total_secao,0) + COALESCE(mc4.total_item,0) AS total_comissao
                FROM movcomissao2 m2
                INNER JOIN movsaida0 m0
                    ON m0.IDFATENT = m2.idfatent
                   AND m0.IDEMPRESA = :idempresa
                   AND m0.DTCAD BETWEEN :dataInicio AND :dataFim
                   AND (m0.CANCELADA = 0 OR m0.CANCELADA IS NULL)
                LEFT JOIN (
                    SELECT idvendedor, idfatent, SUM(vlr_comissao) AS total_secao
                    FROM movcomissao3
                    WHERE status IN (1,2)
                    GROUP BY idvendedor, idfatent
                ) mc3 ON mc3.idvendedor = m2.idvendedor AND mc3.idfatent = m2.idfatent
                LEFT JOIN (
                    SELECT idvendedor, idfatent, SUM(vlr_comissao) AS total_item
                    FROM movcomissao4
                    WHERE status IN (1,2)
                    GROUP BY idvendedor, idfatent
                ) mc4 ON mc4.idvendedor = m2.idvendedor AND mc4.idfatent = m2.idfatent
                WHERE m2.status = 1
            ) c
            LEFT JOIN cadpessoa0 p0 ON p0.idpessoa = c.idvendedor
            GROUP BY c.idvendedor, p0.NOME
            ORDER BY valor_comissao DESC, valor_total DESC, qtd_vendas DESC
            LIMIT {$limit}
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->bindValue(':dataInicio', $dataInicio);
        $stmt->bindValue(':dataFim', $dataFim);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Evolução de vendas por vendedor últimos 10 dias
    public function getEvolucaoVendedores10Dias($idempresa, $pdo) {
        $sql = "
            SELECT v.idvendedor, COALESCE(p.NOME,'Sem nome') AS nome_vendedor,
                   DATE(m0.DTCAD) AS data_venda,
                   SUM(m0.VLRTOTAL) AS valor_total
            FROM movsaida0 m0
            INNER JOIN movsaida4 v ON v.IDFATENT = m0.IDFATENT
            LEFT JOIN cadpessoa0 p ON p.IDPESSOA = v.IDVENDEDOR
            WHERE m0.IDEMPRESA = :idempresa
              AND m0.DTCAD >= DATE_SUB(CURDATE(), INTERVAL 10 DAY)
              AND (m0.CANCELADA = 0 OR m0.CANCELADA IS NULL)
            GROUP BY v.idvendedor, DATE(m0.DTCAD)
            ORDER BY data_venda ASC, valor_total DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Vendedor estrela (maior comissão total)
    public function getVendedorEstrela($idempresa, $pdo) {
        $result = $this->getVendedoresMaisLucro($idempresa, 1, $pdo);
        return $result[0] ?? null;
    }

    // 4. Top vendedores por desconto (exemplo)
    public function getVendedoresMaisDesconto($idempresa, $limit, $pdo) {
        $sql = "
            SELECT v.IDVENDEDOR, COALESCE(p.NOME,'Sem nome') AS nome_vendedor,
                   SUM(m0.VLRDESCONTO) AS total_desconto
            FROM movsaida4 v
            INNER JOIN movsaida0 m0 ON m0.IDFATENT = v.IDFATENT
            LEFT JOIN cadpessoa0 p ON p.IDPESSOA = v.IDVENDEDOR
            WHERE m0.IDEMPRESA = :idempresa
              AND m0.DTCAD >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY v.IDVENDEDOR, p.NOME
            ORDER BY total_desconto DESC
            LIMIT {$limit}
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Metas de vendedores
    public function getMetasVendedores($idempresa, $pdo) {
        $sql = "SELECT * FROM metas_vendedores WHERE idempresa = :idempresa";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Totais de vendas do dia
    public function getTotaisVendasDia($idempresa, $pdo) {
        $sql = "
            SELECT SUM(VLRTOTAL) AS total_dia
            FROM movsaida0
            WHERE IDEMPRESA = :idempresa
              AND DTCAD = CURDATE()
              AND (CANCELADA = 0 OR CANCELADA IS NULL)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 7. Dados do gráfico últimos 10 dias
    public function getDadosGrafico10Dias($idempresa, $pdo) {
        return $this->getEvolucaoVendedores10Dias($idempresa, $pdo);
    }

    // 8. Vendas de um vendedor por período
    public function getVendasVendedorPorPeriodo($idempresa, $idvendedor, $dias, $pdo) {
        $sql = "
            SELECT m0.IDFATENT, m0.VLRTOTAL, m0.DTCAD
            FROM movsaida0 m0
            INNER JOIN movsaida4 v ON v.IDFATENT = m0.IDFATENT
            WHERE m0.IDEMPRESA = :idempresa
              AND v.IDVENDEDOR = :idvendedor
              AND m0.DTCAD >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
              AND (m0.CANCELADA = 0 OR m0.CANCELADA IS NULL)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
        $stmt->bindValue(':dias', $dias, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 9. Indicadores gerais
    public function getIndicadoresVendedores($idempresa, $pdo) {
        $sql = "
            SELECT 
                COUNT(DISTINCT v.IDVENDEDOR) AS total_vendedores,
                SUM(m0.VLRTOTAL) AS total_vendas
            FROM movsaida0 m0
            INNER JOIN movsaida4 v ON v.IDFATENT = m0.IDFATENT
            WHERE m0.IDEMPRESA = :idempresa
              AND (m0.CANCELADA = 0 OR m0.CANCELADA IS NULL)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 10. Buscar vendedores ativos com vendas
    public function buscaVendedoresAtivosComVendas($idempresa, $searchterm, $pdo) {
        $sql = "
            SELECT DISTINCT v.IDVENDEDOR, COALESCE(p.NOME,'Sem nome') AS nome_vendedor
            FROM movsaida4 v
            INNER JOIN movsaida0 m0 ON m0.IDFATENT = v.IDFATENT
            LEFT JOIN cadpessoa0 p ON p.IDPESSOA = v.IDVENDEDOR
            WHERE m0.IDEMPRESA = :idempresa
              AND (m0.CANCELADA = 0 OR m0.CANCELADA IS NULL)
              AND (p.NOME LIKE :search OR v.IDVENDEDOR LIKE :search)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->bindValue(':search', "%{$searchterm}%");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 11. Buscar vendedor por ID com estatísticas
    public function buscaVendedorPorIdComEstatisticas($idvendedor, $idempresa, $pdo) {
        $sql = "
            SELECT v.IDVENDEDOR, COALESCE(p.NOME,'Sem nome') AS nome_vendedor,
                   COUNT(DISTINCT m0.IDFATENT) AS qtd_vendas,
                   SUM(m0.VLRTOTAL) AS valor_total
            FROM movsaida4 v
            INNER JOIN movsaida0 m0 ON m0.IDFATENT = v.IDFATENT
            LEFT JOIN cadpessoa0 p ON p.IDPESSOA = v.IDVENDEDOR
            WHERE m0.IDEMPRESA = :idempresa
              AND v.IDVENDEDOR = :idvendedor
              AND (m0.CANCELADA = 0 OR m0.CANCELADA IS NULL)
            GROUP BY v.IDVENDEDOR, p.NOME
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idempresa', $idempresa, PDO::PARAM_INT);
        $stmt->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>
