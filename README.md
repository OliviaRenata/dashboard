Dashboard de Vendedores – README
Descrição

Este projeto é um Dashboard de Vendedores desenvolvido em PHP + MySQL, com foco em exibir métricas de vendas, evolução e comissões de cada vendedor da empresa. Ele permite acompanhar quantidade de vendas, valor total e comissões em tempo real, oferecendo insights sobre desempenho individual e coletivo da equipe de vendas.

O dashboard foi desenvolvido para integração interna de empresas, utilizando dados de tabelas de faturamento (movsaida0, movsaida4) e comissões (movcomissao2, movcomissao3, movcomissao4).

Funcionalidades

Visualização dos top vendedores por:

Total de vendas

Total de comissão

Quantidade de vendas realizadas

Evolução das vendas nos últimos 10 dias

Indicadores de desempenho:

Meta diária, mensal e anual

Comissão por forma de pagamento, seção e item

Filtro por período e vendedor

Suporte a empresas múltiplas via idempresa

Dashboard responsivo e com endpoints JSON para consumo em front-end

Estrutura de Tabelas e Colunas

O dashboard utiliza os seguintes bancos/tabelas:

1. movsaida0

IDFATENT → ID da venda/faturamento

IDEMPRESA → ID da empresa

VLRTOTAL → Valor total da venda

DTCAD → Data da venda

CANCELADA → Filtra vendas canceladas

2. movsaida4

IDFATENT → ID da venda

IDVENDEDOR → ID do vendedor

3. movcomissao2 (Comissão por forma de pagamento)

idvendedor → Vendedor

idfatent → ID da venda

vlr_dinheiro, vlr_pix, vlr_cartao_deb, vlr_cartao_cre, vlr_duplicata, vlr_cheque → Valores da comissão

status → 1 = Ativo

dtcad → Data do lançamento

4. movcomissao3 (Comissão por seção)

idvendedor, idfatent, vlr_comissao, status (1 = pago, 2 = pendente), dtcad

5. movcomissao4 (Comissão por item)

idvendedor, idfatent, vlr_comissao, status (1 = pago, 2 = pendente), dtcad

6. cadpessoa0 (Cadastro de vendedores)

IDPESSOA → ID do vendedor

NOME → Nome do vendedor
