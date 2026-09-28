<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Legal / Institutional Content Routes
|--------------------------------------------------------------------------
|
| Rotas públicas (sem auth) que servem o conteúdo de páginas legais
| estáticas. Mantidas como closures com o conteúdo hardcoded, já que se
| trata de texto raramente alterado — editar aqui evita deploy do
| frontend, que já traz o mesmo conteúdo como fallback local.
|
*/

Route::get('termos-e-condicoes', function () {
    return response()->json([
        'title' => 'Condições Gerais de Contratação da Plataforma Cervagelada',
        'last_updated' => '2026-09-23',
        'sections' => [
            [
                'title' => '1. Definições',
                'content' => 'Os termos e as expressões abaixo, quando iniciados em letra maiúscula nestas Condições Gerais ou em quaisquer documentos anexos ou vinculados a este instrumento, terão os significados que lhes for indicados na Lista de Definições.<br><br>'
                    .'<strong>"Bebida Alcoólica":</strong> qualquer produto líquido fermentado, destilado, retificado ou obtido por mistura, destinado ao consumo humano, que contenha graduação alcoólica superior a 0,5% (meio por cento) em volume a 20º Celsius, abrangendo cervejas artesanais, chopes, vinhos, destilados, licores e bebidas mistas reguladas pelo Ministério da Agricultura e Pecuária (MAPA) e pela Agência Nacional de Vigilância Sanitária (ANVISA).<br><br>'
                    .'<strong>"Produto Restrito":</strong> qualquer bem de consumo cujo fornecimento, oferta, venda, publicidade ou entrega dependa de autorizações específicas, registros especiais perante autoridades públicas ou que possua restrição legal de comercialização e consumo baseada em faixa etária, nos termos do Estatuto da Criança e do Adolescente (Lei nº 8.069/1990), da Lei nº 13.106/2015 e da Lei nº 9.294/1996.<br><br>'
                    .'<strong>"IFC (Intervenção no Comércio de Bebidas)":</strong> registro ou autorização administrativa disciplinada pela legislação federal, em especial pelas normas de controle e fiscalização tributária e regulatória do comércio de bebidas, inclusive no âmbito da Lei nº 4.737/1965 e dos atos normativos expedidos pela Receita Federal do Brasil e órgãos setoriais competentes.<br><br>'
                    .'<strong>"Documentação Regulatória":</strong> conjunto de licenças sanitárias expedidas por órgãos municipais ou estaduais de Vigilância Sanitária, alvarás de funcionamento, registros de estabelecimentos e de produtos perante o MAPA, autorizações da ANVISA, habilitações de Inscrição Estadual, certificados de regularidade fiscal e sanitária, bem como comprovações de rastreabilidade de lotes exigidas pela legislação em vigor.<br><br>'
                    .'<strong>"Estabelecimento":</strong> sociedade empresária, empresário individual ou produtor rural formalizado (abrangendo cervejarias artesanais, distribuidoras de bebidas, adegas e lojas correlatas) devidamente cadastrado e aprovado para ofertar seus produtos aos Usuários por meio da Plataforma Cervagelada.<br><br>'
                    .'<strong>"Plataforma Cervagelada":</strong> conjunto de softwares, aplicações web, aplicativos móveis, infraestrutura tecnológica e interfaces operados pela CERVAGELADA para intermediação de pedidos entre Estabelecimentos e Usuários.<br><br>'
                    .'<strong>"Usuário":</strong> pessoa natural plenamente capaz que se cadastra na Plataforma Cervagelada com o propósito de adquirir produtos comercializados pelos Estabelecimentos.<br><br>'
                    .'<strong>"Pedido":</strong> solicitação de compra de produtos formulada pelo Usuário por intermédio da Plataforma e direcionada ao Estabelecimento.<br><br>'
                    .'<strong>"Sistema de Pagamentos":</strong> solução tecnológica e financeira integrada à Plataforma, fornecida por instituição de pagamento devidamente autorizada pelo Banco Central do Brasil, responsável pela captura, processamento, liquidação e repasse dos fluxos financeiros decorrentes dos Pedidos.',
            ],
            [
                'title' => '2. Objeto',
                'content' => 'O Contrato tem por objeto:<br><br>'
                    .'a) a disponibilização, pela Cervagelada, de infraestrutura tecnológica para facilitar a intermediação entre o Estabelecimento e os consumidores finais, para fins de comercialização de alimentos, bebidas e demais itens previamente autorizados, sem que a Cervagelada participe diretamente da operação de venda, observadas, contudo, as responsabilidades que lhe sejam atribuídas pela legislação aplicável; e<br><br>'
                    .'b) o licenciamento não exclusivo do uso dos Softwares pelo Cervagelada ao Estabelecimento, nos termos deste contrato e das políticas internas da plataforma, vedada sua reprodução, engenharia reversa, compartilhamento com terceiros ou utilização para fins diversos daqueles previstos nesta avença.',
            ],
            [
                'title' => '3. Obrigações do Estabelecimento',
                'content' => '3.1. O Estabelecimento será o único responsável pela execução/coleta e pela correção dos Pedidos feitos de forma inadequada ou incompleta, bem como pela completa observância de todas e quaisquer normas aplicáveis a suas atividades, incluindo, sem limitação, as normas sanitárias, e pela emissão de nota fiscal, recibo ou documento equivalente para os Clientes Finais com relação aos Pedidos, sendo o Estabelecimento Comercial o único responsável pela relação de consumo decorrente do Pedido, inclusive quanto à qualidade, integridade, entrega e conformidade dos Produtos comercializados.<br><br>'
                    .'3.2. O Estabelecimento reconhece que somente poderão ser disponibilizados Produtos que tenham sido previamente autorizados pelo Cervagelada, sendo certo que essa autorização não poderá ser entendida como homologação da qualidade do Produto, permanecendo o Estabelecimento integralmente responsável pela segurança, adequação e conformidade dos produtos autorizados.<br><br>'
                    .'3.2.1. A Cervagelada poderá, a seu exclusivo critério e comprometendo-se a comunicar ao Estabelecimento com antecedência mínima razoável, salvo em casos de urgência, risco à segurança ou descumprimento legal, posteriormente revisitar a autorização dos Produtos e quais compõem a lista de Produtos Restritos.<br><br>'
                    .'3.3. O Estabelecimento declara, sob pena de restrição de acesso à Plataforma, que é pessoa jurídica formalmente constituída e regularmente estabelecida, única e exclusivamente responsável por cumprir e observar todos os requisitos legais e infralegais, fiscais e sanitários para abertura e desenvolvimento contínuo e pleno de sua atividade, comprometendo-se a atuar em plena regularidade perante todos os órgãos, agências e autoridades públicas, durante toda a sua permanência na Plataforma Cervagelada e na comercialização dos seus produtos através dela. Compromete-se, ainda, a manter todas as informações requisitadas pelo Cervagelada atualizadas devendo, em caso de qualquer alteração, informar ao Cervagelada imediatamente, comprometendo-se a manter o Cervagelada indene de quaisquer penalidades impostas em razão da não observação nos termos firmados nesta cláusula.<br><br>'
                    .'3.4. Caso os Pedidos envolvam algum Produto Perecível, o Estabelecimento obriga-se a garantir o armazenamento e transporte adequado dos produtos perecíveis, zelando por sua integridade física e condições de consumo, sob pena de responsabilidade integral por danos causados a clientes finais ou à plataforma, garantindo o armazenamento em temperatura adequada e a preservação da embalagem para que a mesma não amasse, estufe, enferruje, trinque, rasgue, apresente furos e/ou vazamentos ou qualquer outro tipo de defeito que interfira na integridade dos Produtos Perecíveis, impedindo o seu consumo.<br><br>'
                    .'3.5. O estabelecimento se obriga, sob as penas da lei, a não realizar a venda de qualquer tipo de produto que apresente vício ou esteja em desacordo com as normas de consumo, sob pena de imediata suspensão e responsabilização por perdas e danos.<br><br>'
                    .'3.6. O Estabelecimento declara, para todos os fins de direito, que no exercício de suas atividades comerciais, bem como na utilização da Plataforma Cervagelada, não pratica, nem praticará, qualquer ato que constitua violação a direitos de terceiros, sejam estes direitos de natureza contratual, patrimonial, intelectual, autoral, industrial, de imagem, honra, privacidade, personalidade, concorrencial ou qualquer outro amparado pela legislação nacional ou internacional aplicável, reconhecendo que tal responsabilidade é exclusiva do Estabelecimento, sem que haja qualquer obrigação de verificação prévia pela Plataforma Cervagelada.<br><br>'
                    .'3.6.1. O Estabelecimento declara possuir todas as licenças, autorizações, permissões e direitos necessários à comercialização, uso e divulgação de produtos, marcas, logotipos, embalagens, nomes comerciais, conteúdos, imagens, textos, áudios, vídeos e demais materiais que venha a disponibilizar ou utilizar na Plataforma, responsabilizando-se integralmente pela sua origem, veracidade, regularidade, titularidade, não contrafação e conformidade com a legislação vigente.<br><br>'
                    .'3.6.2. O Estabelecimento obriga-se, ainda, a não carregar, inserir, divulgar ou promover na Plataforma quaisquer conteúdos ou produtos que:<br><br>'
                    .'a) infrinjam direitos autorais, marcas, patentes, desenhos industriais, segredos comerciais ou quaisquer outros direitos de propriedade intelectual;<br>'
                    .'b) estejam sujeitos a contratos de exclusividade, cessão ou licenciamento que impeçam sua comercialização ou divulgação nos termos em que forem apresentados;<br>'
                    .'c) contenham materiais protegidos por sigilo legal, judicial ou contratual, sem autorização expressa do titular;<br>'
                    .'d) induzam à prática de atos ilícitos, discriminatórios, abusivos, imorais ou que contrariem a ordem pública;<br>'
                    .'e) infrinjam qualquer disposição legal, regulamentar ou contratual vigente no ordenamento jurídico brasileiro ou estrangeiro, conforme aplicável.<br><br>'
                    .'3.6.2.1. O descumprimento das obrigações acima poderá ensejar a suspensão ou exclusão do estabelecimento da Plataforma, sem prejuízo das demais medidas legais cabíveis.<br><br>'
                    .'3.6.3. O Estabelecimento reconhece que será o único e exclusivo responsável por quaisquer prejuízos, danos, encargos, despesas, perdas ou reclamações de terceiros que venham a ser formuladas contra o Cervagelada em decorrência da violação das obrigações aqui assumidas, obrigando-se a indenizar e manter o Cervagelada integralmente isento e indene, a qualquer tempo, inclusive em sede judicial ou administrativa, arcando com todos os custos, honorários advocatícios e despesas decorrentes.<br><br>'
                    .'3.7. O Estabelecimento se obriga a responder em até 5 (cinco) dias, salvo nos casos em que, pela sua natureza, não possam aguardar 5 (cinco) dias, a todos os pedidos de esclarecimentos realizados pelo Cervagelada. O descumprimento dessa disposição sujeitará o Estabelecimento às sanções cabíveis.<br><br>'
                    .'3.7.1. Esclarecimentos e demais comunicações pelo Estabelecimento sobre Pedidos em andamento deverão ser imediatos. O descumprimento dessa disposição poderá dar ensejo ao cancelamento do Pedido em andamento. Na ocorrência de 3 (três) ou mais cancelamentos decorrentes do descumprimento desta cláusula dentro de um prazo de 7 (sete) dias, o Estabelecimento ficará sujeito às sanções contratuais cabíveis, por exemplo, mas não se limitando a, incluindo advertência, suspensão temporária ou exclusão definitiva da Plataforma, conforme a gravidade da infração.',
            ],
            [
                'title' => '4. Ausência de Exclusividade',
                'content' => '4.1. O Estabelecimento declara, por meio da assinatura no Contrato, que está ciente e concorda que o Cervagelada poderá prestar os serviços objeto deste Contrato a quaisquer outros estabelecimentos, ainda que estes sejam, direta ou indiretamente, concorrentes do Estabelecimento. De igual modo, o Cervagelada está ciente e concorda que o Estabelecimento poderá expor e vender os seus produtos por quaisquer outros meios, ainda que concorrente do Cervagelada.<br><br>'
                    .'4.1.1. A ausência de exclusividade permitirá à Cervagelada negociar condições comerciais distintas na sua relação com os Estabelecimentos Comerciais, conforme seus critérios internos.',
            ],
            [
                'title' => '5. Meios de Pagamento e Repasse',
                'content' => '5.1. O Cervagelada fornecerá ao Estabelecimento, diretamente ou por terceiros, tecnologia para recebimentos e gestão de pagamentos realizados por Clientes Finais através de cartões de débito, crédito, Pix, boleto bancário, entre outros, por meio de sistema de pagamento online integrado à Plataforma Cervagelada ("Sistema de Pagamentos Cervagelada"). O Estabelecimento está ciente de que para utilização da Plataforma Cervagelada é necessário utilizar o Sistema de Pagamentos Cervagelada.<br><br>'
                    .'5.1.1. O Estabelecimento concorda que o Cervagelada disponibilizará aos Clientes Finais o Sistema de Pagamento Cervagelada integrado à Plataforma, o qual poderá ser fornecido, total ou parcialmente, diretamente pelo Cervagelada ou por terceiros contratados. O Estabelecimento outorga ao Cervagelada poderes para contratar, em seu nome, todos e quaisquer serviços necessários ou úteis à disponibilização e manutenção do Sistema de Pagamentos da Plataforma.<br><br>'
                    .'5.1.2. As transações realizadas por meio da Plataforma Cervagelada serão liquidadas eletronicamente pelo Sistema de Pagamentos Cervagelada. Os meios de pagamento do Sistema de Pagamentos Cervagelada poderão ser alterados a qualquer tempo, a exclusivo critério da Plataforma.<br><br>'
                    .'5.1.3. O Estabelecimento autoriza expressamente o Cervagelada ou quaisquer terceiros por ele contratado a receber dos Clientes Finais, em nome do Estabelecimento, o valor total dos Pedidos e a repassar este valor para o Estabelecimento, no prazo e nas condições previstas nesta cláusula.<br><br>'
                    .'5.2. Os Pedidos pagos por meio do Sistema de Pagamentos Cervagelada serão repassados ao Estabelecimento no prazo e na forma previstos nesta cláusula.<br><br>'
                    .'5.3. Em caso de pagamento realizado por meio dos sistemas de pagamento da Plataforma, eventuais estornos ou chargebacks realizados pelos Clientes Finais serão assumidos inicialmente pelo Cervagelada, devendo os valores correspondentes aos respectivos Pedidos serem incluídos nos Repasses, salvo nas hipóteses previstas neste Contrato.<br><br>'
                    .'5.3.1. Caso seja comprovado que o Estabelecimento foi responsável direto ou indireto pelo estorno, ou tenha dado causa ao chargeback (inclusive por não entrega, contestação do consumidor, duplicidade, suspeita de fraude, entre outros), o Cervagelada se reserva o direito de:<br><br>'
                    .'a) descontar os valores estornados de repasses futuros;<br>'
                    .'b) reter valores quando verificado risco de inadimplemento, insolvência, encerramento de atividades ou índice atípico de chargebacks;<br>'
                    .'c) suspender repasses por até 180 (cento e oitenta) dias em caso de suspeita de fraude;<br>'
                    .'d) reter, a título de garantia e limitadamente ao valor efetivamente contestado ou objeto de chargeback, os montantes correspondentes aos Pedidos em apuração, pelo prazo máximo de 30 (trinta) dias corridos, contados da abertura da apuração, prorrogável por uma única vez por igual período quando persista necessidade fundamentada de investigação, obrigando-se a Cervagelada a liberar imediatamente os valores não alcançados pela contestação e os remanescentes ao término de apuração, mantida a comunicação fundamentada ao Estabelecimento;<br>'
                    .'e) aplicar as penalidades previstas neste Contrato e em suas Políticas correlatas;<br>'
                    .'f) cobrar perdas e danos eventualmente incorridas;<br>'
                    .'g) efetuar compensação cruzada com outros créditos eventualmente devidos ao Estabelecimento Comercial;<br>'
                    .'h) exigir restituição dos valores com atualização pelo IGP-M/FGV e juros de 1% ao mês.<br><br>'
                    .'5.3.2. O Estabelecimento poderá apresentar documentos comprobatórios quanto à regularidade da transação contestada no prazo de até 5 (cinco) dias corridos do recebimento da notificação, sendo certo que o envio dos documentos não garante a reversão do estorno.<br><br>'
                    .'5.4. O Estabelecimento compromete-se a não rejeitar e/ou cancelar os Pedidos nos quais os Clientes Finais optem pela utilização do sistema de pagamentos da Plataforma Cervagelada.<br><br>'
                    .'5.5. O Cervagelada poderá, mediante notificação, solicitar documentos que comprovem as vendas dos Produtos, conforme previsto neste Contrato e demais políticas aplicáveis.<br><br>'
                    .'5.6. Repasse pelo Cervagelada: O Cervagelada consolidará os Repasses referentes a Pedidos recebidos pelo Estabelecimento em 1 (uma) "Semana", assim entendido os dias compreendidos entre uma segunda-feira e o domingo subsequente ("Semana de Competência").<br><br>'
                    .'5.6.1. O Cervagelada fará 1 (um) único Repasse semanal, às quartas-feiras, dentro de quatro semanas contadas a partir do fechamento da Semana de Competência.<br><br>'
                    .'5.6.2. O Cervagelada consolidará os Repasses referentes a Pedidos recebidos pelo Estabelecimento na Semana de Competência ficando o Cervagelada desde já autorizado a reter do Repasse em questão os valores da Remuneração devida pelo Estabelecimento ao Cervagelada.<br><br>'
                    .'5.6.3. O pagamento será realizado exclusivamente para conta bancária de titularidade do Estabelecimento, cujo CNPJ coincida com o cadastrado na Plataforma. Tal pagamento será considerado como quitação integral, salvo nos casos de estorno ou chargeback.<br><br>'
                    .'5.6.3.1. Na hipótese de o Estabelecimento desejar alterar a sua conta bancária informada no Contrato, deverá informar, por meio dos Softwares disponibilizados, os dados da nova conta bancária com antecedência mínima de 15 (quinze) dias, sob pena de o Repasse em questão ser feito pelo Cervagelada para a conta bancária anteriormente indicada pelo Estabelecimento.<br><br>'
                    .'5.6.4. O valor do Repasse será calculado conforme a fórmula: Repasse = VP – VC, onde VP é o valor total dos Pedidos pagos por meio do Sistema de Pagamento da Plataforma na Semana de Competência e VC é o valor total da Remuneração devida pelo Estabelecimento ao Cervagelada.<br><br>'
                    .'5.6.5. Caso o valor de VC seja superior ao de VP, a diferença será descontada do próximo Repasse. Se isso não for possível, o Cervagelada poderá emitir boleto com vencimento em 7 (sete) dias.<br><br>'
                    .'5.6.6. Na hipótese de a quarta-feira cair em feriado bancário regional e/ou nacional, o pagamento será realizado no primeiro dia útil subsequente.<br><br>'
                    .'5.6.7. O Cervagelada fica autorizado a descontar dos Repasses os valores de estornos e penalidades previstos nestas Condições Gerais, conforme cláusula 5.3.1.<br><br>'
                    .'5.7. O Estabelecimento poderá acompanhar, por meio da Plataforma de Integração, as informações relativas aos Repasses, podendo questioná-las no prazo de até 7 (sete) dias após seu recebimento, mediante exposição fundamentada. O silêncio no prazo será considerado concordância plena e irretratável, sem prejuízo do direito de pleitear correção de erro material devidamente comprovado.<br><br>'
                    .'5.8. Caso o Estabelecimento atrase o pagamento de quaisquer valores devidos ao Cervagelada ou o Cervagelada atrase o pagamento do Repasse devido ao Estabelecimento, no todo ou em parte, a Parte inocente fará jus ao recebimento de multa moratória equivalente a 1% (um por cento) do valor em atraso e juros moratórios de 1% (um por cento) ao mês.<br><br>'
                    .'5.9. Caso o atraso do pagamento dos valores devidos ao Cervagelada perdure por 10 (dez) dias corridos, o Cervagelada poderá suspender os acessos à Loja Virtual e todas as demais atividades relacionadas ao Contrato, até que o Estabelecimento efetue o pagamento dos valores pendentes. Caso o atraso persista por mais de 15 (quinze) dias corridos, o Cervagelada poderá, a seu exclusivo critério, rescindir o Contrato e adotar todas as medidas necessárias à defesa dos seus interesses.',
            ],
            [
                'title' => '6. Informações do Estabelecimento',
                'content' => '6.1. O Estabelecimento é o único responsável por todas e quaisquer informações a respeito de suas atividades e Produtos que venham a ser por ele disponibilizadas ao Cervagelada e aos Clientes Finais ("Informações do Estabelecimento"), e compromete-se a mantê-las, a todo tempo atualizadas e em estrita observância à legislação aplicável.<br><br>'
                    .'6.2. O acesso do Estabelecimento aos Softwares será realizado por meio de nome de usuário e senha de uso pessoal e intransferível. O Estabelecimento reconhece que será o único responsável por todo e qualquer acesso por meio do seu nome de usuário e da sua senha, devendo notificar imediatamente a Cervagelada em caso de acesso indevido ou suspeita de vazamento de credenciais.<br><br>'
                    .'6.3. O Estabelecimento não poderá disponibilizar aos Clientes Finais, por meio do(s) Pedido(s) e outros canais de comunicação disponibilizados pelo Cervagelada, os seus números de telefone e/ou os seus endereços virtuais de outros canais de entrega, sejam eles de titularidade do Estabelecimento ou de terceiros.<br><br>'
                    .'6.4. É vedada ao Estabelecimento a utilização ou o compartilhamento dos dados dos Clientes Finais, obtidos por ocasião do(s) Pedido(s), para a divulgação dos canais de entrega supramencionados e/ou o compartilhamento de quaisquer informações para qualquer outro fim que não tenha relação com o(s) Pedido(s) efetuado(s) pelos Clientes Finais dentro da Plataforma. O uso indevido pode configurar violação à LGPD, com responsabilização civil e, se aplicável, criminal.<br><br>'
                    .'6.5. O Estabelecimento deverá preparar para entrega ou retirada todos os Pedidos dos Clientes Finais. É vedado ao Estabelecimento utilizar embalagens de concorrentes do Cervagelada para embalar os Produtos adquiridos pelos Clientes Finais.',
            ],
            [
                'title' => '7. Pedidos',
                'content' => '7.1. O Cervagelada será responsável, exclusivamente, pela intermediação entre o Estabelecimento e os Clientes Finais, por meio da Plataforma Cervagelada.<br><br>'
                    .'7.1.1. O Cervagelada, na máxima extensão permitida por lei e independentemente do Plano de Contratação escolhido no Formulário, não responderá perante o Estabelecimento por danos indiretos, lucros cessantes ou perdas de oportunidade que não lhe sejam diretamente imputáveis. Esta limitação não alcança responsabilidades que, por lei, não possam ser excluídas ou restringidas, nem prejuízos decorrentes de dolo ou culpa quando legalmente aplicável.<br><br>'
                    .'7.2. O Estabelecimento será o único responsável (i) pela perfeita execução dos Pedidos e pelos Pedidos que tenham sido preparados de forma inadequada, incompleta ou em desconformidade com o que foi solicitado pelos Clientes Finais, (ii) pelas informações contidas na Loja Virtual (tais como horário de funcionamento, fotos, descrição e valor dos produtos disponibilizados no Cardápio) e (iii) pela emissão e entrega de nota/cupom fiscal ou documento equivalente para os Clientes Finais com relação aos Pedidos.<br><br>'
                    .'7.3. Os Pedidos serão recebidos pelo Estabelecimento por meio dos Softwares, cabendo ao Estabelecimento aceitá-los, atualizar o status, rejeitá-los ou cancelá-los também por meio dos Softwares, sendo vedado ao Estabelecimento rejeitar e/ou cancelar mais do que 10% (dez por cento) dos Pedidos por ele recebidos dentro de cada mês. Nesta hipótese, o Cervagelada reserva-se o direito de rescindir o Contrato, caso o Estabelecimento não apresente justificativas apropriadas para os cancelamentos.<br><br>'
                    .'7.3.1. Não será aceita como justificativa apropriada a falta de insumos para a confecção dos Pedidos pelo Estabelecimento, cabendo ao Estabelecimento, na falta de insumos, manter o seu Cardápio atualizado apenas com os itens disponíveis para venda aos Clientes Finais.<br><br>'
                    .'7.3.2. Caso o Pedido fuja dos padrões de consumo, o Estabelecimento deverá imediatamente entrar em contato com o Cervagelada para que as partes possam, em comum acordo, decidir quais medidas serão tomadas. Caso o Estabelecimento não consiga entrar em contato com o Cervagelada, o Pedido deverá ser rejeitado. Os Pedidos rejeitados nos termos desta cláusula não serão contabilizados para fins da cláusula 7.3.<br><br>'
                    .'7.4. O Estabelecimento se compromete a não realizar a execução, coleta e/ou entrega aos Clientes Finais dos Pedidos que houverem sido por eles cancelados, independentemente do motivo do cancelamento, sob pena de arcar integralmente com os custos decorrentes desta ação, inclusive com possibilidade de retenção do Repasse.<br><br>'
                    .'7.5. O Estabelecimento deverá elaborar e embalar as refeições, bebidas e/ou demais produtos dos Pedidos dos Clientes Finais adequadamente, observando-se, ainda, o disposto na cláusula 3.4.<br><br>'
                    .'7.6. Caso o Cervagelada concorde com a integração entre os Softwares e os sistemas utilizados pelo Estabelecimento para o recebimento dos Pedidos, esta integração deverá ser regulada por meio de instrumento específico. Nesta hipótese, o Cervagelada permanecerá indene de todas e quaisquer divergências, falhas e problemas técnicos decorrentes da integração de Software.<br><br>'
                    .'7.7. O Estabelecimento assume, em caráter irrevogável, irretratável e irreversível, a obrigação de manter a Cervagelada a todo tempo livre e indene de todas e quaisquer perdas, danos e demandas que a Cervagelada eventualmente venha a sofrer de Clientes Finais ou quaisquer outros terceiros em decorrência da execução e entrega dos Pedidos, da violação do Contrato pelo Estabelecimento ou de qualquer legislação aplicável.',
            ],
            [
                'title' => '8. Entrega dos Pedidos',
                'content' => '8.1. O Estabelecimento concorda que a entrega dos Pedidos deverá ser feita pelo próprio Estabelecimento.<br><br>'
                    .'8.2. O Cervagelada será responsável apenas pela intermediação entre os Clientes Finais e o Estabelecimento na Plataforma Cervagelada, de modo que o Estabelecimento será o único responsável pela entrega dos Pedidos, independentemente de os serviços de entrega serem prestados diretamente pelo Estabelecimento ou por terceiro contratado por ele.<br><br>'
                    .'8.2.1. Os entregadores utilizados pelo Estabelecimento, empregados, autônomos ou parceiros, mantêm relação exclusivamente jurídica com o Estabelecimento, que será o único responsável pela contratação, remuneração, encargos trabalhistas, previdenciários e fiscais, seguros e eventuais indenizações decorrentes de acidentes ou danos durante as entregas, não existindo qualquer vínculo, subordinação ou responsabilidade da Cervagelada em relação a eles.<br><br>'
                    .'8.2.2. O Estabelecimento obrigará seus entregadores a, no ato da entrega de Produtos com restrição etária, verificar a maioridade do recebedor mediante documento oficial com foto, recusando a entrega quando não comprovada, e a observar as normas de trânsito, sanitárias e de segurança aplicáveis.<br><br>'
                    .'8.2.3. Qualquer reclamação, ação ou procedimento envolvendo entregadores será conduzido e suportado exclusivamente pelo Estabelecimento, que manterá a Cervagelada indene nos termos da Cláusula 15.<br><br>'
                    .'8.3. A Loja Virtual do Estabelecimento será exibida aos Clientes Finais exclusivamente por meio dos aplicativos e interfaces digitais da Plataforma Cervagelada, desde que estejam localizados dentro do raio geográfico de atuação delimitado pela Plataforma.<br><br>'
                    .'8.3.1. O raio de exibição da Loja Virtual será determinado conforme a categoria do Estabelecimento, nos seguintes termos:<br><br>'
                    .'a) Distribuidoras de Bebidas: até 5 (cinco) quilômetros de distância a partir da localização da Loja cadastrada na Plataforma;<br>'
                    .'b) Cervejarias Artesanais: até 15 (quinze) quilômetros de distância;<br>'
                    .'c) Lojas Âncoras: conforme o limite geográfico de atuação previamente estipulado pela própria Loja Âncora e aceito pelo Cervagelada.<br><br>'
                    .'8.3.1.1. A definição e a gestão do raio de atuação são de competência exclusiva do Cervagelada, podendo ser revistas, ajustadas ou ampliadas a qualquer tempo, mediante critérios técnicos, comerciais ou operacionais, desde que respeitados os princípios da razoabilidade, da boa-fé e da não discriminação injustificada entre Estabelecimentos de mesma categoria.<br><br>'
                    .'8.3.2. O Estabelecimento reconhece e concorda que a exibição de sua Loja Virtual estará condicionada à ativação dos recursos de geolocalização pelos Clientes Finais e à compatibilidade técnica dos dispositivos utilizados, não sendo o Cervagelada responsável por falhas externas, indisponibilidades ou limitações tecnológicas dos aparelhos dos usuários.<br><br>'
                    .'8.4. O Estabelecimento deverá (i) definir o valor da taxa de entrega a ser cobrada dos Clientes Finais; e (ii) informar ao Cervagelada a ocorrência de quaisquer eventualidades relativas aos Pedidos que prejudiquem e/ou impossibilitem a realização das entregas.',
            ],
            [
                'title' => '9. Política Tributária',
                'content' => '9.1. O Estabelecimento compromete-se a manter sua Regularidade Fiscal sob pena de ser excluído da Plataforma Cervagelada em caso de qualquer afronta à legislação tributária a que esteja submetido.<br><br>'
                    .'9.2. As Partes são contratantes independentes, sendo cada uma delas responsável pelo adimplemento das obrigações que a legislação tributária lhes atribui.<br><br>'
                    .'9.3. O Estabelecimento se responsabiliza pelo recolhimento tempestivo de todos os tributos decorrentes de suas atividades empresariais, bem como pelos débitos trabalhistas e previdenciários referentes aos colaboradores envolvidos.<br><br>'
                    .'9.4. Se, durante a vigência do presente Contrato, for criado um tributo ou modificada a alíquota de qualquer dos tributos já existentes, os valores a serem pagos pelo Estabelecimento serão revisados de modo a refletirem tal modificação.<br><br>'
                    .'9.5. O Estabelecimento se compromete a efetuar o recolhimento dos tributos que, por força de lei, devam ser retidos considerando o serviço prestado pelo Cervagelada.<br><br>'
                    .'9.6. Será de responsabilidade do Estabelecimento realizar a devida retenção e o posterior recolhimento do Imposto de Renda Retido na Fonte ("IRRF"), incidente nas operações relacionadas aos Serviços de Intermediação, nos termos do artigo 718 do Decreto nº 9.580/18.<br><br>'
                    .'9.7. Caberá ao Estabelecimento informar à Receita Federal do Brasil os valores do IRRF recolhidos em nome do Cervagelada, mediante o devido preenchimento e entrega da Declaração do Imposto sobre a Renda Retido na Fonte (DIRF).<br><br>'
                    .'9.8. O Cervagelada, quando atua na qualidade de prestador do serviço de intermediação e recebedor do montante global da operação, deverá repassar ao Estabelecimento o valor relativo à venda, acrescido do valor correspondente ao IRRF.<br><br>'
                    .'9.9. Nos casos em que a legislação municipal específica que o Imposto Sobre Serviços (ISS) deverá ser retido na fonte, aplicar-se-á o disposto nas cláusulas acima.<br><br>'
                    .'9.10. É de inteira responsabilidade do Estabelecimento o recolhimento do Imposto sobre Circulação de Mercadorias e Serviços (ICMS) e de outros tributos que incidam sobre as operações de compra e venda de mercadorias.<br><br>'
                    .'9.11. O Estabelecimento deverá emitir Nota Fiscal ou outro documento fiscal equivalente correspondente a cada venda realizada por meio da Plataforma, devendo anexar cópia impressa à mercadoria entregue.<br><br>'
                    .'9.11.1. A Cervagelada poderá, a seu critério, solicitar a qualquer momento a comprovação da emissão das respectivas notas fiscais.<br><br>'
                    .'9.11.2. O Estabelecimento compromete-se a enviar à Cervagelada, por meio eletrônico e sempre que solicitado, cópia digital das Notas Fiscais emitidas, no prazo máximo de 24 (vinte e quatro) horas da emissão.<br><br>'
                    .'9.12. O Estabelecimento reconhece e declara ser o único responsável pelo recolhimento de todos os tributos, diretos ou indiretos, incidentes sobre as operações de venda realizadas por meio da Plataforma Cervagelada.<br><br>'
                    .'9.12.1. O Cervagelada não se responsabiliza, em nenhuma hipótese, por erros, omissões ou descumprimentos fiscais por parte do Estabelecimento.<br><br>'
                    .'9.13. O Estabelecimento deverá manter-se regularmente inscrito no CNPJ, com situação cadastral ativa, e com todos os requisitos fiscais obrigatórios para o exercício de sua atividade, inclusive, se aplicável, regularidade perante o regime do Simples Nacional.<br><br>'
                    .'9.14. Na hipótese de cancelamento de pedidos ou devoluções que impliquem obrigação de estorno fiscal, o Estabelecimento compromete-se a realizar os devidos ajustes nos documentos fiscais emitidos, observando rigorosamente a legislação aplicável.<br><br>'
                    .'9.15. O Cervagelada poderá reter do valor do Repasse o montante correspondente às autuações fiscais eventualmente recebidas pela ausência de pagamento dos tributos dos Estabelecimentos.<br><br>'
                    .'9.15.1. Na hipótese de a Cervagelada ser autuado, responsabilizado solidariamente ou compelido a realizar o recolhimento de tributos devidos pelo Estabelecimento, este compromete-se a reembolsar integralmente todos os valores pagos, inclusive multas, juros, honorários advocatícios e demais encargos, no prazo máximo de 10 (dez) dias corridos a contar da notificação enviada pela Cervagelada.<br><br>'
                    .'9.16. Se, durante a vigência do presente Contrato, for criada obrigação de retenção de tributos inerente às operações por este intermediadas, ficará o Cervagelada autorizado a reter os valores em nome do Estabelecimento.<br><br>'
                    .'9.17. As Partes reconhecem que a Cervagelada atua exclusivamente como plataforma digital de intermediação de negócios entre o Estabelecimento Comercial e os Clientes Finais, não possuindo a condição de instituição financeira, instituição de pagamento ou sociedade autorizada pelo Banco Central do Brasil a capturar, custodiar ou movimentar recursos de terceiros por conta própria. A captura, o processamento e a liquidação financeira das transações serão realizados por instituição de pagamento devidamente autorizada pelo Banco Central do Brasil, contratada pela Cervagelada nos termos da Lei nº 12.865/2013, mediante modelo de split de pagamentos.<br><br>'
                    .'9.18. A Cervagelada poderá, a seu exclusivo critério, suspender temporariamente a exibição da Loja Virtual do Estabelecimento Comercial na Plataforma em caso de indícios de irregularidade fiscal, inconsistência cadastral junto à Receita Federal ou órgãos estaduais/municipais, ou ainda diante de notificações oriundas de entes públicos que possam comprometer a regularidade jurídica da operação intermediada.',
            ],
            [
                'title' => '10. Propriedade Intelectual',
                'content' => '10.1. Todos e quaisquer direitos de propriedade intelectual ou industrial relativos à Plataforma Cervagelada e/ou aos Softwares pertencem única e exclusivamente ao Cervagelada. Em nenhuma hipótese, o Contrato implica transferência, no todo ou em parte, de qualquer direito de propriedade intelectual ou industrial pelo Cervagelada para o Estabelecimento.<br><br>'
                    .'10.2. O Estabelecimento se compromete a (i) utilizar a Plataforma Cervagelada e os Softwares de acordo com as suas finalidades e exigências técnicas; (ii) disponibilizar meios adequados para a implantação e a utilização dos Softwares; (iii) responsabilizar-se legalmente por quaisquer dados e informações que venham a ser armazenados pelo Estabelecimento nos Softwares; (iv) não fazer ou distribuir quaisquer cópias dos Softwares; (v) não alterar, combinar, adaptar, traduzir, decodificar, fazer ou solicitar a terceiros engenharia reversa dos Softwares; (vi) não criar trabalhos deles derivados ou solicitar que terceiros o façam; e (vii) não ceder, licenciar, sublicenciar ou de qualquer outra forma dispor dos Softwares.<br><br>'
                    .'10.3. Caso o Estabelecimento deseje veicular quaisquer sinais distintivos do Cervagelada em seus estabelecimentos, no Cardápio ou em qualquer outro material de divulgação, deverá obter a prévia autorização por escrito do Cervagelada e somente poderá fazê-lo de acordo com a orientação do Cervagelada.<br><br>'
                    .'10.4. O Estabelecimento outorga à Cervagelada, de forma gratuita, não exclusiva, pelo prazo de vigência deste Contrato, autorização para utilização dos seus Sinais Distintivos na Plataforma Cervagelada, bem como em materiais promocionais, institucionais, publicitários e de marketing, físicos e/ou digitais, exclusivamente para fins relacionados à divulgação e à execução do objeto deste Contrato.<br><br>'
                    .'10.4.1. O Estabelecimento declara ser o único e exclusivo titular ou possuir a devida autorização de uso dos titulares dos direitos da propriedade intelectual sobre os Sinais Distintivos, reconhecendo que o Cervagelada poderá solicitar a comprovação de referida titularidade ou autorização.<br><br>'
                    .'10.4.2. O Estabelecimento será o único responsável por eventuais prejuízos financeiros decorrentes de violação de direitos da propriedade intelectual pelo uso dos Sinais Distintivos, podendo o Cervagelada descontar o prejuízo diretamente dos Repasses do Estabelecimento.<br><br>'
                    .'10.4.3. O Estabelecimento declara-se ciente de que o Cervagelada poderá rescindir o Contrato caso o Cervagelada venha a ser acionado judicialmente, por ordem judicial para cessação de uso de marca ou por notificação com a devida comprovação de uso indevido de marca por terceiros, se o Estabelecimento não apresentar as devidas comprovações para refutar e solucionar os apontamentos.<br><br>'
                    .'10.5. O Estabelecimento Comercial se compromete a não reproduzir, replicar ou explorar economicamente, direta ou indiretamente, o modelo de negócios, os fluxos operacionais, a arquitetura de funcionalidades ou a identidade visual da Plataforma Cervagelada, sob pena de responsabilidade civil por concorrência desleal, sem prejuízo da rescisão imediata do Contrato.<br><br>'
                    .'10.6. É vedado ao Estabelecimento Comercial utilizar quaisquer mecanismos automatizados para coleta de dados da Plataforma Cervagelada, incluindo, mas não se limitando a, web crawlers, bots ou sistemas de scraping, sendo tal prática considerada violação grave da propriedade intelectual e passível de sanções civis e criminais.<br><br>'
                    .'10.7. A Cervagelada não será responsabilizada por danos decorrentes de conteúdo gerado, disponibilizado ou exibido por terceiros na Loja Virtual do Estabelecimento, incluindo textos, imagens, vídeos, descrições de produtos, avaliações e comentários, cabendo ao Estabelecimento, como fornecedor e único responsável por tal conteúdo, a verificação prévia de sua licitude, nos termos do art. 19 da Lei nº 12.965/2014.',
            ],
            [
                'title' => '11. Inadimplemento',
                'content' => '11.1. O Estabelecimento reconhece e concorda que, em caso de descumprimento do Contrato, estará sujeito às seguintes penalidades, a serem determinadas e aplicadas a exclusivo critério do Cervagelada, conforme as características particulares de cada caso: (a) rebaixamento da posição ocupada pelo Estabelecimento na lista de Estabelecimentos constante na Plataforma Cervagelada, por período de 1 (um) a 30 (trinta) dias; (b) desativação da Loja Virtual pelo período de 1 (um) a 30 (trinta) dias; (c) desativação da modalidade do Pagamento Offline, no caso de atrasos no pagamento da Remuneração; (d) suspensão do Repasse, no caso de descumprimento do disposto nas Políticas; e (e) rescisão do Contrato.<br><br>'
                    .'As penalidades previstas serão aplicadas observando-se a gravidade da infração, a reincidência, o dano causado a Clientes Finais, a terceiros ou à Cervagelada e a boa-fé do Estabelecimento, adotando-se sempre a medida menos gravosa suficiente para a finalidade pretendida. Antes da aplicação de qualquer penalidade, exceto nos casos de risco iminente à segurança dos Clientes Finais, à saúde pública ou de suspeita fundada de fraude, a Cervagelada notificará o Estabelecimento, que terá o prazo de 5 (cinco) dias úteis para apresentar defesa ou esclarecimentos.<br><br>'
                    .'11.2. Sem prejuízo do direito da Cervagelada de imediatamente aplicar ao Estabelecimento as penalidades previstas acima, no caso de uma das Partes tornar-se inadimplente no tocante a uma ou mais de suas obrigações, a outra Parte poderá comunicá-la para que, no prazo atribuído na comunicação, o qual não poderá ser inferior a 5 (cinco) dias úteis, a Parte inadimplente sane e/ou esclareça tal inadimplemento. Se a Parte inadimplente não o fizer, a outra Parte poderá resolver imediatamente o Contrato.<br><br>'
                    .'11.2.1. Sem prejuízo das penalidades previstas nesta Cláusula, o descumprimento de qualquer obrigação contratual pelo Estabelecimento Comercial poderá acarretar a aplicação de multa compensatória equivalente a 10% (dez por cento) da média dos valores mensais faturados pelo Estabelecimento Comercial na Plataforma nos últimos 3 (três) meses, ou, caso inferior, sobre o valor mínimo de R$ 5.000,00 (cinco mil reais), prevalecendo sempre o menor dos valores, sem prejuízo do direito de a Cervagelada pleitear perdas e danos comprovadamente superiores, na forma do art. 416, parágrafo único, do Código Civil.<br><br>'
                    .'11.3. O Cervagelada poderá suspender, sem aviso prévio, a Loja Virtual do Estabelecimento na Plataforma, de forma preventiva, nos casos de suspeita de fraude, uso indevido de marca, denúncia por órgão de defesa do consumidor ou autoridade pública, ou qualquer situação que possa comprometer a imagem da Plataforma ou colocar em risco os Clientes Finais, até a apuração completa dos fatos. No primeiro dia útil seguinte à suspensão, a Cervagelada comunicará o Estabelecimento, por escrito, dos fundamentos da medida, abrindo prazo de 5 (cinco) dias úteis para manifestação.<br><br>'
                    .'11.4. A reincidência em condutas que violem este Contrato, as Políticas da Plataforma e/ou as normas legais aplicáveis poderá ensejar, a critério exclusivo da Cervagelada, a exclusão definitiva do Estabelecimento da Plataforma, independentemente de notificação prévia.',
            ],
            [
                'title' => '12. Políticas Cervagelada',
                'content' => '12.1. Ao cumprir as obrigações previstas no Contrato, o Estabelecimento, seus funcionários, agentes e representantes devem respeitar, plenamente, todas as leis aplicáveis sobre anticorrupção, antissuborno, antiterrorismo, antiboicote, antilavagem de dinheiro e de sanções econômicas e de defesa da concorrência, incluindo, mas não limitado à Lei nº 12.846/2013.<br><br>'
                    .'12.2. O Estabelecimento declara que tomou ciência, não violará as disposições e que está de acordo com as seguintes políticas que serão disponibilizadas na Plataforma de Integração: (i) Código de Ética e Conduta do Cervagelada; (ii) Política de Conteúdo; (iii) Política de Propriedade Intelectual — declarando ciência de que essas políticas poderão ser atualizadas periodicamente, obrigando-se a consultá-las e cumpri-las.<br><br>'
                    .'12.3. O Estabelecimento se compromete a fornecer, no prazo máximo de 30 (trinta) dias, documentos no formato original e de forma organizada, bem como esclarecimentos, quando solicitado, seja para cadastro, seja para fins de auditoria. Compromete-se também a manter livros contábeis precisos, completos e registros apurados durante a vigência do Contrato e por um período adicional de 5 (cinco) anos após o seu término.<br><br>'
                    .'12.4. O não fornecimento dos documentos resultará na suspensão do Repasse até o efetivo fornecimento dos documentos solicitados pelo Cervagelada.<br><br>'
                    .'12.5. O Estabelecimento deverá informar ativamente o Cervagelada sobre ações ou recursos relacionados a processos com alegações de corrupção, lavagem de dinheiro ou competição limitada, assim como investigações e medidas coercitivas decorrentes de violação de lei.<br><br>'
                    .'12.6. O Estabelecimento se compromete a manter o Cervagelada livre e indene de todo e qualquer dano ou perda, incluindo multa, custo, obrigação de reparação de danos, taxas, juros, honorários advocatícios ou outras responsabilidades, incluindo as criminais, imputadas ao Cervagelada a partir de investigação ou procedimento judicial ou administrativo originado a partir de qualquer ação ou omissão do Estabelecimento.<br><br>'
                    .'12.7. O descumprimento desta cláusula garante ao Cervagelada a faculdade de rescindir o Contrato.',
            ],
            [
                'title' => '13. Promoções e Novos Produtos',
                'content' => '13.1. O Estabelecimento está ciente que o Cervagelada poderá realizar promoções e/ou campanhas de marketing e incentivo de vendas dentro da Plataforma Cervagelada ("Promoções"). Caso o Estabelecimento opte por participar das Promoções, deverá aceitar o Regulamento da promoção, sendo que a não aceitação impedirá a participação.<br><br>'
                    .'13.2. O Cervagelada poderá oferecer novos produtos e serviços ao Estabelecimento ("Novos Produtos"), informando os termos e condições sobre o Novo Produto no momento de sua oferta; a não aceitação da totalidade das Condições do Novo Produto impedirá o Estabelecimento de contratá-lo.<br><br>'
                    .'13.3. O Estabelecimento concorda que todos e quaisquer descontos e promoções oferecidas em outros canais de delivery por ele utilizados deverão ser oferecidas também aos Clientes Finais na Plataforma, não sendo considerada violação a oferta de promoções exclusivas no canal de delivery próprio ou loja física do Estabelecimento. A Cervagelada poderá solicitar a qualquer momento provas de equidade de preços em canais concorrentes, sob pena de suspensão da participação em promoções.<br><br>'
                    .'A participação do Estabelecimento nas Promoções realizadas pela Cervagelada é opcional e condicionada à oferta de condições de preço e de disponibilidade na Plataforma que não sejam menos favoráveis ao Cliente Final do que as condições ofertadas em quaisquer outros canais de venda do Estabelecimento. O Estabelecimento permanece livre para praticar preços e promoções distintas em seus canais próprios, loja física ou outros aplicativos quando não participante de Promoções subsidiadas pela Cervagelada.<br><br>'
                    .'13.4. A Cervagelada poderá, a seu exclusivo critério, subsidiar parcialmente promoções, mediante aceite prévio e por escrito do Estabelecimento.',
            ],
            [
                'title' => '14. Privacidade e Proteção de Dados Pessoais',
                'content' => '14.1. Ao cumprir as obrigações previstas no Contrato com a Cervagelada, as partes obrigam-se a respeitar, fielmente, a legislação aplicável à proteção de dados pessoais, como a Lei Geral de Proteção de Dados (Lei federal nº 13.709/2018), o Marco Civil da Internet (Lei federal nº 12.965/2014) e seu Decreto regulamentador nº 8.771/2016, e a garantir a privacidade dos Clientes Finais e demais dados acessados ou disponibilizados para a execução do objeto do presente Contrato.<br><br>'
                    .'14.2. Em conformidade com o objeto previsto no Contrato, o Estabelecimento poderá ter acesso a dados que identifiquem ou permitam a identificação de indivíduos ("Dados Pessoais") em atividades de acesso, utilização, coleta, processamento, armazenamento, eliminação, dentre outras, para fins de execução deste Contrato ("Tratamento de Dados Pessoais").<br><br>'
                    .'14.2.1. As atividades de Tratamento de Dados Pessoais serão autorizadas ao Estabelecimento desde que limitadas ao estritamente necessário para a execução do Contrato e tão somente durante a sua vigência.<br><br>'
                    .'14.2.2. Fica vedada a utilização dos Dados Pessoais para atender quaisquer outras finalidades além daquelas necessárias para a execução do objeto deste Contrato.<br><br>'
                    .'14.2.3. Fica vedado ao Estabelecimento transferir, no todo ou em parte, os Dados Pessoais compartilhados pelo Cervagelada para quaisquer terceiros que não estejam diretamente relacionados com a execução do Contrato, ainda que de forma agregada e/ou anônima.<br><br>'
                    .'14.3. O Estabelecimento declara estar ciente e autoriza a Cervagelada a compartilhar os dados e informações de sua operação na Cervagelada com empresas do grupo econômico do Cervagelada e/ou parceiros comerciais, para fins de oferta de produtos e serviços financeiros, observando-se a base legal própria prevista no art. 7º da Lei nº 13.709/2018, os direitos previstos no art. 18 da mesma lei, e que as empresas parceiras serão as únicas responsáveis pela qualidade dos serviços ou produtos financeiros oferecidos.<br><br>'
                    .'14.3.1. A revogação do consentimento ou a oposição ao compartilhamento, quando aplicável, não acarretará rescisão do Contrato nem prejuízo ao Estabelecimento, limitando-se seus efeitos à suspensão do compartilhamento para a finalidade correspondente.<br><br>'
                    .'14.4. O Estabelecimento deverá valer-se de técnicas de segurança como criptografia, hardening, controle de acesso, dupla autenticação, monitoramento e testes de segurança frequentes, dentre outros métodos de proteção condizentes com as melhores práticas do setor.<br><br>'
                    .'14.5. De acordo com as orientações do Cervagelada, o Estabelecimento deverá promover a exclusão definitiva de quaisquer Dados Pessoais que lhe foram transmitidos por força do Contrato, durante ou após finda a vigência do Contrato.<br><br>'
                    .'14.6. Durante a vigência do Contrato, será facultado ao Cervagelada, a seu exclusivo critério, realizar auditorias nos documentos ou no ambiente de controle de segurança da informação do Estabelecimento para verificar as medidas e controles de proteção de dados pessoais e segurança da informação aplicados.<br><br>'
                    .'14.7. O Estabelecimento obriga-se a notificar a Cervagelada, no prazo máximo de 24 (vinte e quatro) horas contadas da ciência, por meio do e-mail lojas@cervagelada.com.br, acerca de qualquer incidente de segurança que resulte em vazamento, perda, alteração ou acesso não autorizado a dados pessoais relacionados a este Contrato, bem como de qualquer violação da legislação de proteção de dados de que tenha ciência, inclusive violações acidentais ou culposas.',
            ],
            [
                'title' => '15. Responsabilidade Geral do Estabelecimento',
                'content' => '15.1. Sem prejuízo do disposto neste Contrato em outras passagens, o Estabelecimento será o único e exclusivo responsável pelo cumprimento integral de todas as suas obrigações legais, regulatórias, contratuais e fiscais relacionadas à sua atuação na Plataforma Cervagelada. A responsabilidade do Estabelecimento será objetiva, independentemente da comprovação de dolo ou culpa, sempre que decorrer do descumprimento de suas obrigações legais ou contratuais.<br><br>'
                    .'15.2. O Estabelecimento compromete-se a indenizar, ressarcir, reembolsar e manter o Cervagelada, seus sócios, administradores, empregados e representantes integralmente indenes e livres de quaisquer ônus, prejuízos, condenações, perdas, custos, despesas (inclusive honorários advocatícios) ou responsabilidades de qualquer natureza, decorrentes de:<br><br>'
                    .'a) descumprimento, total ou parcial, de qualquer obrigação prevista neste Contrato ou nas Políticas vinculadas;<br>'
                    .'b) violação de qualquer norma legal ou regulatória aplicável à atividade do Estabelecimento;<br>'
                    .'c) fornecimento de informações falsas, incompletas ou enganosas;<br>'
                    .'d) reclamações, ações judiciais, procedimentos administrativos ou arbitrais promovidos por Clientes Finais, autoridades públicas, terceiros ou instituições financeiras;<br>'
                    .'e) falhas na entrega de Produtos, defeitos, vícios ou inobservância da legislação de defesa do consumidor;<br>'
                    .'f) práticas ilícitas, fraudulentas, enganosas ou lesivas praticadas por seus prepostos, Estabelecimentos ou colaboradores.<br><br>'
                    .'15.3. Caso o Cervagelada venha a ser incluído como parte em qualquer processo judicial, administrativo ou arbitral em razão de atos ou omissões atribuíveis ao Estabelecimento, este se compromete a assumir integralmente a defesa do Cervagelada, se juridicamente possível, reembolsar de forma imediata todos os valores pagos a qualquer título e cooperar com o Cervagelada fornecendo documentos, esclarecimentos e diligências necessárias.<br><br>'
                    .'15.4. As obrigações de indenização previstas nesta cláusula subsistirão à rescisão ou extinção deste Contrato, por qualquer motivo, enquanto perdurarem obrigações, responsabilidades ou riscos decorrentes da relação contratual aqui estabelecida.<br><br>'
                    .'15.5. A Cervagelada poderá reter valores de repasse ao Estabelecimento Comercial, total ou parcialmente, a fim de garantir a cobertura de riscos ou passivos oriundos de violação contratuais, legais ou reclamações de Clientes Finais, mediante comunicação prévia e justificada.',
            ],
            [
                'title' => '16. Alteração destas Condições Gerais',
                'content' => '16.1. O Estabelecimento reconhece e concorda que a Cervagelada poderá alterar estas Condições Gerais e/ou as Políticas a qualquer tempo, mediante o envio de notificação por e-mail ao endereço eletrônico indicado no cadastro do Estabelecimento, sendo este considerado meio válido e eficaz para comunicação independente de confirmação de aceite e leitura do mesmo:<br><br>'
                    .'(i) com antecedência mínima de 30 (trinta) dias da data de entrada em vigor, no caso de alterações que impliquem modificação substancial das obrigações do Estabelecimento ou que possam impactar de forma relevante a sua operação;<br><br>'
                    .'(ii) com antecedência mínima de 48h (quarenta e oito horas) em caso de alterações necessárias para adequação a normas legais ou regulatórias, ou de ajustes operacionais, técnicos ou de segurança que não acarretem ônus ou responsabilidade adicional relevante ao Estabelecimento.<br><br>'
                    .'16.1.1. Se a alteração tiver impacto substancial e adverso sobre o Estabelecimento nos termos do item (i) acima, e o Estabelecimento não concordar com a alteração, poderá apresentar notificação de objeção à Cervagelada no prazo de até 10 (dez) dias contados do recebimento da notificação. Recebida a objeção, as Partes envidarão esforços para chegar a um consenso. Não sendo possível, qualquer parte poderá rescindir o contrato mediante comunicação à outra Parte, sem penalidade.<br><br>'
                    .'16.1.2. O Estabelecimento reconhece e concorda que não terá o direito de apresentar objeção a qualquer alteração nestes Termos e Condições que o Cervagelada venha a implantar para o cumprimento de exigências legais ou regulatórias, hipótese em que períodos de notificação menores podem ser aplicados.<br><br>'
                    .'16.2. O não envio da notificação de objeção no prazo previsto no item 16.1.1 será considerado aceite do Estabelecimento quanto à alteração destas Condições Gerais, para todos os fins e efeitos.',
            ],
            [
                'title' => '17. Disposições Gerais',
                'content' => '17.1. A relação jurídica estabelecida entre as Partes é de prestação de serviços, de modo que o Contrato não estabelece relação de consumo, trabalho, representação comercial ou de qualquer outra natureza, sendo certo que as Partes são e permanecerão a todo tempo autônomas e independentes entre si.<br><br>'
                    .'17.2. Este Contrato é celebrado em caráter irrevogável, irretratável e irreversível, obrigando as Partes e seus sucessores, seja qual for o título da sucessão.<br><br>'
                    .'17.3. As disposições contidas neste Contrato representam a totalidade dos entendimentos mantidos entre as Partes relativamente aos assuntos de que ele trata, superando todos e quaisquer entendimentos anteriores, verbais ou escritos.<br><br>'
                    .'17.4. Todas as notificações, autorizações, consentimento e quaisquer outras comunicações referentes ao Contrato deverão ser enviadas aos destinatários nos endereços indicados no Formulário. As notificações serão consideradas efetuadas no dia em que forem recebidas.<br><br>'
                    .'17.5. Caso qualquer disposição deste Contrato se torne nula ou ineficaz, a validade ou eficácia das disposições restantes não será afetada, permanecendo em pleno vigor e efeito, e as Partes entrarão em negociações de boa-fé visando a substituir a disposição ineficaz por outra que atinja a finalidade e os efeitos desejados.<br><br>'
                    .'17.6. O fato de uma das Partes deixar de exigir a tempo o cumprimento de qualquer das disposições ou de quaisquer direitos relativos a este Contrato não será considerado uma renúncia a tais disposições, direitos ou faculdades, não constituirá novação e não afetará de qualquer forma a validade deste Contrato.<br><br>'
                    .'17.7. O Cervagelada adotará medidas razoáveis de verificação para confirmar, dentro da Plataforma, que o Cliente Final possui idade legal para a aquisição de bebidas alcoólicas ou de outros produtos sujeitos a restrição etária. Quando o Pedido contiver bebida alcoólica e a entrega estiver sob responsabilidade do Estabelecimento, este deverá adotar procedimento de conferência da maioridade do recebedor, inclusive mediante solicitação de documento oficial com foto quando necessário, recusando a entrega caso não seja possível comprovar a idade mínima exigida pela legislação aplicável.<br><br>'
                    .'17.8. O Estabelecimento reconhece e concorda que a Cervagelada poderá ceder e transferir os seus direitos e obrigações previstos no Contrato a quaisquer terceiros. A cessão, total ou parcial, dos direitos ou obrigações do Estabelecimento dependerá de anuência prévia e expressa da Cervagelada.<br><br>'
                    .'17.9. O Estabelecimento declara que está ciente e de acordo com que o Cervagelada preste os serviços objeto destas Condições Gerais a quaisquer outros estabelecimentos, ainda que estes sejam direta ou indiretamente concorrentes do Estabelecimento.<br><br>'
                    .'17.10. O Estabelecimento deverá manter sigilo absoluto sobre todas e quaisquer informações a respeito do Cervagelada e dos Clientes Finais a que tiver acesso em decorrência da contratação dos serviços previstos no Contrato, incluindo condições comerciais definidas no Formulário ("Informações Confidenciais"). A obrigação de confidencialidade permanecerá em vigor pelo prazo de 5 (cinco) anos após o término deste Contrato, independentemente do motivo da rescisão.<br><br>'
                    .'17.11. Fica terminantemente proibido para o Estabelecimento a adoção, direta ou indireta, ou a permissão de qualquer forma de trabalho infantil, salvo nas situações expressamente permitidas nos estritos limites da legislação trabalhista em vigor ou na Lei nº 8.069/1990, sob pena de imediata rescisão deste Contrato.<br><br>'
                    .'17.12. Caso o Estabelecimento tenha assinado o Contrato por meio de qualquer ferramenta eletrônica, especialmente com uso de certificação digital no padrão ICP-Brasil ou através do GOV.BR, o documento será considerado válido e eficaz para todos os fins de direito.<br><br>'
                    .'17.13. O presente Contrato será regido e interpretado de acordo com as leis da República Federativa do Brasil. As Partes elegem o foro da Comarca de Pinhais, Estado do Paraná, com a exclusão de qualquer outro foro, por mais privilegiado que seja, para dirimir quaisquer controvérsias ou litígios decorrentes ou relativos a este Contrato.<br><br>'
                    .'17.14. A eventual tolerância de qualquer das Partes quanto ao descumprimento de cláusulas ou condições previstas neste Contrato não será considerada novação ou renúncia de direito, sendo considerada ato de mera liberalidade.',
            ],
        ],
    ]);
});

Route::get('como-comprar', function () {
    return response()->json([
        'title' => 'Como Comprar no Cervagelada',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => 'Um Guia Rápido',
                'content' => 'Comprar suas bebidas favoritas no Cervagelada é fácil e rápido! Siga estes passos simples.',
            ],
            [
                'title' => '1. Cadastre-se',
                'content' => 'No canto superior direito da tela, clique em "Cadastrar-se".<br><br>'
                    .'Preencha o formulário com seus dados pessoais: nome completo, e-mail, número de telefone, data de nascimento e endereço de entrega completo.<br><br>'
                    .'Crie uma senha segura para sua conta.<br><br>'
                    .'Leia e aceite os Termos e Condições de Uso e a Política de Privacidade.<br><br>'
                    .'Clique em "Cadastrar" para criar sua conta.',
            ],
            [
                'title' => '2. Escolha sua Loja',
                'content' => 'Após o cadastro, você poderá navegar pelas diversas lojas (Cervejarias Artesanais e Distribuidoras de Bebidas) disponíveis na sua região.<br><br>'
                    .'Utilize os filtros por tipo de bebida, marca, localização ou outros critérios para encontrar a loja que mais lhe agrada.<br><br>'
                    .'Você também pode buscar diretamente pelo nome da loja na barra de pesquisa.',
            ],
            [
                'title' => '3. Selecione seus Produtos',
                'content' => 'Ao acessar a página de uma loja, você poderá visualizar todos os produtos disponíveis.<br><br>'
                    .'Clique nos produtos para ver mais detalhes, como descrição, preço e informações adicionais.<br><br>'
                    .'Selecione a quantidade desejada de cada produto e clique em "Adicionar ao Carrinho".<br><br>'
                    .'Você pode continuar navegando por outras lojas e adicionando mais produtos ao seu carrinho.',
            ],
            [
                'title' => '4. Finalize sua Compra',
                'content' => 'Quando terminar de escolher seus produtos, clique no ícone do carrinho no canto superior direito da tela.<br><br>'
                    .'Revise os itens adicionados, a quantidade e os preços.<br><br>'
                    .'Informe ou confirme seu endereço de entrega.<br><br>'
                    .'Escolha a forma de pagamento de sua preferência entre as opções disponíveis.<br><br>'
                    .'Confira o valor total do seu pedido, incluindo a taxa de entrega (se houver).<br><br>'
                    .'Clique em "Finalizar Pedido" para concluir sua compra.',
            ],
            [
                'title' => '5. Acompanhe seu Pedido',
                'content' => 'Após a confirmação do pagamento, você receberá um e-mail com os detalhes do seu pedido.<br><br>'
                    .'Você poderá acompanhar o status da sua entrega na seção "Meus Pedidos" da sua conta.<br><br>'
                    .'Pronto! Agora é só aguardar a entrega das suas bebidas geladas no conforto da sua casa. Boas compras no Cervagelada!',
            ],
        ],
    ]);
});

Route::get('formas-pagamento', function () {
    return response()->json([
        'title' => 'Formas de Pagamento',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => 'Escolha a Sua Forma de Pagamento',
                'content' => 'Pensando na sua comodidade, o Cervagelada oferece opções de pagamento fáceis e seguras para você garantir suas bebidas favoritas sem complicação! O valor total da sua compra, incluindo a entrega, aparece de forma clara para você conferir antes de finalizar o pedido.',
            ],
            [
                'title' => 'Cartão de Crédito',
                'content' => 'Use seu cartão de crédito das principais bandeiras e parcele suas compras (verifique as condições de cada loja).',
            ],
            [
                'title' => 'Pix',
                'content' => 'Pague na hora, com toda a segurança e a rapidez que só o Pix oferece!<br><br>'
                    .'Selecione a forma de pagamento que mais combina com você e finalize seu pedido. Em breve, suas bebidas estarão a caminho para refrescar seus momentos. Saúde!',
            ],
        ],
    ]);
});

Route::get('envios-frete', function () {
    return response()->json([
        'title' => 'Envios / Frete',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => 'Pedidos em Distribuidoras de Bebidas',
                'content' => 'O prazo estimado de entrega será informado antes da conclusão da compra e poderá variar de acordo com a localização, disponibilidade do estabelecimento, condições climáticas, volume de pedidos e operação logística. Quando houver indicação de tempo médio de entrega, ela será apresentada como estimativa, e não como garantia de prazo.',
            ],
            [
                'title' => 'Pedidos em Cervejarias Artesanais',
                'content' => 'Para pedidos de Cervejarias Artesanais, o prazo estimado de entrega será informado antes da conclusão da compra e poderá variar conforme disponibilidade de produção e estoque, localização, condições climáticas, volume de pedidos e operação logística.',
            ],
            [
                'title' => 'Demais lojas, livros, acessórios, ingressos, cursos e demais',
                'content' => 'As lojas parceiras são 100% responsáveis pelo devido despacho conforme o pedido recebido. Para as demais lojas do Cervagelada, produtos físicos (ex.: churrasqueira ou livros) são despachados por transportadoras para envio de longa distância, com acompanhamento via comunicados por e-mail. Para cursos e eventos, a entrega será realizada conforme a política das lojas, utilizando meios logísticos como e-mail e WhatsApp.',
            ],
        ],
    ]);
});

Route::get('perguntas-frequentes', function () {
    return response()->json([
        'title' => 'Perguntas Frequentes',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => '1. Sobre o Cervagelada',
                'content' => '<strong>O que é o Cervagelada?</strong><br>'
                    .'O Cervagelada é um marketplace online que conecta você às melhores Cervejarias Artesanais e Distribuidoras de Bebidas da sua região. Através da nossa plataforma, você pode explorar uma seleção diversificada de cervejas artesanais exclusivas e outras bebidas, realizar seus pedidos de forma rápida e prática, e recebê-los no conforto do seu lar.<br><br>'
                    .'<strong>Em quais cidades o Cervagelada opera?</strong><br>'
                    .'Estamos trabalhando continuamente para expandir nossa área de atuação. Consulte a disponibilidade para a sua região informando seu CEP na Plataforma.<br><br>'
                    .'<strong>Como posso entrar em contato com o Cervagelada?</strong><br>'
                    .'E-mail: faleconosco@cervagelada.com.br — Telefone/WhatsApp: (41) 9 8855-1173.',
            ],
            [
                'title' => '2. Realização de Pedidos',
                'content' => '<strong>Como efetuo um pedido no Cervagelada?</strong><br>'
                    .'Navegue em nossa plataforma (website ou aplicativo) e explore as opções de cervejas artesanais e outras bebidas disponíveis. Utilize os filtros e categorias para encontrar seus produtos desejados. Adicione os produtos ao carrinho, revise-o e selecione "Finalizar Pedido". Informe seu endereço de entrega, escolha a forma de pagamento, confirme o pedido e aguarde a notificação de confirmação.<br><br>'
                    .'<strong>É necessário possuir uma conta para realizar um pedido?</strong><br>'
                    .'Sim, para realizar pedidos no Cervagelada é necessário criar uma conta. Isso nos permite armazenar seu histórico de pedidos, informações de entrega e preferências, otimizando suas futuras compras.<br><br>'
                    .'<strong>Posso modificar ou cancelar meu pedido após a finalização?</strong><br>'
                    .'A possibilidade de alteração ou cancelamento depende do status do seu pedido. Caso o pedido ainda não tenha sido processado para entrega, entre em contato com nossa equipe de suporte o mais breve possível para verificar a viabilidade da sua solicitação.<br><br>'
                    .'<strong>Existe um valor mínimo para realizar um pedido?</strong><br>'
                    .'O valor mínimo, quando aplicável, é sempre exibido em seu carrinho de compras antes da conclusão do pedido.<br><br>'
                    .'<strong>Como serei notificado sobre a confirmação do meu pedido?</strong><br>'
                    .'Após a conclusão do seu pedido, você receberá uma confirmação por e-mail contendo os detalhes da sua compra e um código de rastreamento (se aplicável). Você também poderá acompanhar o status do seu pedido na seção "Meus Pedidos" da sua conta.<br><br>'
                    .'<strong>O que ocorre se um produto se tornar indisponível após a realização do meu pedido?</strong><br>'
                    .'Nossa equipe entrará em contato para oferecer alternativas, como a substituição por um item similar, a remoção do item do pedido ou o cancelamento parcial ou total da compra.',
            ],
            [
                'title' => '3. Entrega dos Pedidos',
                'content' => '<strong>Qual a área de cobertura para entregas do Cervagelada?</strong><br>'
                    .'Você poderá confirmar se seu endereço está dentro da nossa área de cobertura ao inserir seu CEP durante o processo de finalização da compra.<br><br>'
                    .'<strong>Qual o prazo estimado para a entrega do meu pedido?</strong><br>'
                    .'O prazo de entrega pode variar de acordo com sua localização, o horário da realização do pedido e a disponibilidade do parceiro responsável pela entrega (Cervejaria ou Distribuidora). O prazo estimado será informado durante a finalização do seu pedido.<br><br>'
                    .'<strong>Qual o valor da taxa de entrega?</strong><br>'
                    .'A taxa de entrega pode variar dependendo da distância do endereço de entrega e do parceiro responsável. O valor será claramente indicado em seu carrinho de compras antes da confirmação do pedido. Em algumas promoções, a entrega poderá ser gratuita para pedidos acima de um determinado valor.<br><br>'
                    .'<strong>Posso agendar um horário específico para a entrega do meu pedido?</strong><br>'
                    .'No momento, não oferecemos a opção de agendamento de horário para a entrega. As entregas são realizadas dentro do prazo estimado informado no momento da compra.<br><br>'
                    .'<strong>Como posso acompanhar o status da entrega do meu pedido?</strong><br>'
                    .'Assim que seu pedido for despachado para entrega, você receberá um link de rastreamento por e-mail (caso o parceiro de entrega ofereça este serviço). Você também poderá verificar o status da entrega na seção "Meus Pedidos" de sua conta.<br><br>'
                    .'<strong>O que devo fazer caso meu pedido não seja entregue dentro do prazo previsto?</strong><br>'
                    .'Entre em contato com nossa equipe de suporte para que possamos verificar a situação junto ao parceiro de entrega.<br><br>'
                    .'<strong>Qual o procedimento caso eu receba meu pedido incompleto ou danificado?</strong><br>'
                    .'Entre em contato com nossa equipe de suporte após o recebimento, enviando fotos dos produtos danificados e detalhes da ocorrência. Faremos o possível para solucionar a questão prontamente.<br><br>'
                    .'<strong>Quem será o responsável pela entrega do meu pedido?</strong><br>'
                    .'A entrega do seu pedido poderá ser realizada diretamente pela Cervejaria Artesanal, pela Distribuidora de Bebidas parceira ou por um serviço de entrega terceirizado, a depender da sua localização e dos produtos selecionados.',
            ],
            [
                'title' => '4. Produtos Disponíveis',
                'content' => '<strong>Quais tipos de bebidas posso encontrar no Cervagelada?</strong><br>'
                    .'No Cervagelada, você encontrará uma vasta seleção de cervejas em geral, inclusive artesanais de diversas cervejarias, além de outras bebidas como refrigerantes, sucos, vinhos, destilados, cachaças, energéticos, entre outras.<br><br>'
                    .'<strong>Onde posso obter mais informações sobre uma cerveja artesanal específica?</strong><br>'
                    .'Na página de cada produto, você encontrará informações detalhadas sobre o estilo da cerveja, a cervejaria produtora, os ingredientes utilizados, o teor alcoólico, as notas de degustação e outras informações relevantes.<br><br>'
                    .'<strong>Os produtos comercializados no Cervagelada são originais?</strong><br>'
                    .'Sim, o Cervagelada estabelece parcerias apenas com Cervejarias Artesanais e Distribuidoras de Bebidas que garantem a autenticidade e a alta qualidade de seus produtos.<br><br>'
                    .'<strong>Posso avaliar os produtos que adquiri?</strong><br>'
                    .'Sim, após o recebimento do seu pedido, você terá a oportunidade de avaliar os produtos e sua experiência de compra na seção "Meus Pedidos" de sua conta.',
            ],
            [
                'title' => '5. Formas de Pagamento',
                'content' => '<strong>Quais são as formas de pagamento aceitas no Cervagelada?</strong><br>'
                    .'Aceitamos cartão de crédito e Pix. As formas de pagamento disponíveis para cada loja são exibidas durante o processo de compra.<br><br>'
                    .'<strong>O processo de pagamento online é seguro?</strong><br>'
                    .'Sim, implementamos tecnologias de segurança avançadas para assegurar a proteção de seus dados financeiros durante o processo de pagamento online. As transações são criptografadas e processadas por plataformas de pagamento seguras.<br><br>'
                    .'<strong>Posso utilizar dois cartões diferentes para pagar um único pedido?</strong><br>'
                    .'No momento, nossa plataforma não oferece a funcionalidade de pagamento com dois cartões distintos para o mesmo pedido.<br><br>'
                    .'<strong>O que devo fazer se meu pagamento for recusado?</strong><br>'
                    .'Verifique se os dados do cartão inseridos estão corretos e se há saldo disponível. Você também pode tentar utilizar outra forma de pagamento ou entrar em contato com a administradora do seu cartão.<br><br>'
                    .'<strong>Como funciona o processo de reembolso em caso de cancelamento ou problema com o pedido?</strong><br>'
                    .'O reembolso será processado utilizando a mesma forma de pagamento utilizada na compra original. O prazo para o crédito ser efetuado em sua conta pode variar de acordo com a sua operadora de cartão ou instituição bancária.',
            ],
            [
                'title' => '6. Gerenciamento da Sua Conta',
                'content' => '<strong>Como posso criar uma conta no Cervagelada?</strong><br>'
                    .'Você pode criar sua conta clicando em "Cadastrar-se" na página inicial da nossa plataforma e seguindo as instruções apresentadas.<br><br>'
                    .'<strong>Como posso alterar minhas informações cadastrais?</strong><br>'
                    .'Você pode modificar seus dados cadastrais (endereço, número de telefone, e-mail, etc.) acessando a seção "Minha Conta" e editando as informações desejadas.<br><br>'
                    .'<strong>Esqueci minha senha. Como posso recuperá-la?</strong><br>'
                    .'Na página de login, clique na opção "Esqueci minha senha" e siga as instruções para redefinir sua senha. Você receberá um e-mail contendo um link para a criação de uma nova senha.<br><br>'
                    .'<strong>Como posso solicitar a exclusão da minha conta no Cervagelada?</strong><br>'
                    .'Entre em contato com nossa equipe de suporte através dos canais de atendimento disponíveis.',
            ],
            [
                'title' => '7. Para Cervejarias Artesanais e Distribuidoras Interessadas em Parceria',
                'content' => '<strong>Como posso me tornar um parceiro do Cervagelada?</strong><br>'
                    .'Se você representa uma Cervejaria Artesanal ou Distribuidora de Bebidas e tem interesse em estabelecer uma parceria com o Cervagelada, entre em contato conosco através do e-mail comercial@cervagelada.com.br.<br><br>'
                    .'<strong>Quais são os critérios para se tornar um parceiro do Cervagelada?</strong><br>'
                    .'Os critérios para parceria incluem a posse de todas as licenças e registros necessários para operação, a garantia da qualidade dos produtos, a capacidade de atendimento e entrega (para distribuidores), e o alinhamento com os valores e princípios do Cervagelada. Veja mais detalhes na página "Como Vender no Cervagelada".',
            ],
        ],
    ]);
});

Route::get('regras-do-site', function () {
    return response()->json([
        'title' => 'Termos e Condições de Uso — Marketplace Cervagelada',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => '1. Sobre a Plataforma',
                'content' => 'Bem-vindo(a) ao marketplace online Cervagelada ("Plataforma"), operado por M2T Tecnologia Ltda, com sede na Av. Camilo Di Lellis, 1065, Sala OutBox, Centro - 83323-000 - Pinhais - PR, CNPJ 57.774.206/0001-96 ("Cervagelada").<br><br>'
                    .'Estes Termos e Condições de Uso ("Termos") regem o acesso e a utilização da Plataforma Cervagelada por você, seja como Usuário Comprador ou como Parceiro Vendedor. Ao acessar ou utilizar a Plataforma, você concorda integralmente com estes Termos.<br><br>'
                    .'O Cervagelada é uma plataforma que conecta Usuários Compradores a Parceiros Vendedores de bebidas. O Cervagelada não é o proprietário, vendedor ou distribuidor dos produtos anunciados na Plataforma. Os Parceiros Vendedores são responsáveis pelos produtos, pelas informações disponibilizadas, pelo processamento dos pedidos e pelas entregas sob sua responsabilidade, sem prejuízo das responsabilidades atribuídas ao Cervagelada pela legislação aplicável.',
            ],
            [
                'title' => '2. Aceitação e Modificações dos Termos',
                'content' => 'A utilização da Plataforma implica a sua total aceitação e concordância com estes Termos. O Cervagelada reserva-se o direito de modificar estes Termos a qualquer momento, sem aviso prévio. As alterações entrarão em vigor imediatamente após a sua publicação na Plataforma. É de sua responsabilidade revisar periodicamente estes Termos para estar ciente de quaisquer modificações. O uso continuado da Plataforma após a publicação de alterações constituirá sua aceitação dos novos Termos.',
            ],
            [
                'title' => '3. Regras para Usuários Compradores',
                'content' => '<strong>3.1. Cadastro e Conta:</strong> Para realizar pedidos, é necessário criar uma conta, fornecendo informações precisas, completas e atualizadas. Você é responsável pela confidencialidade de sua senha e por todas as atividades em sua conta.<br><br>'
                    .'<strong>3.2. Uso da Plataforma:</strong> Utilize a Plataforma para fins lícitos e de acordo com estes Termos. É proibido qualquer uso que viole leis, direitos de terceiros ou a moral e os bons costumes.<br><br>'
                    .'<strong>3.3. Pedidos e Compras:</strong> Ao realizar um pedido, você está celebrando uma transação diretamente com o Parceiro Vendedor. O Cervagelada atua apenas como um facilitador da comunicação e do processo de compra.<br><br>'
                    .'<strong>3.4. Preços e Pagamentos:</strong> Os preços dos produtos são definidos pelos Parceiros Vendedores e podem ser alterados conforme as condições apresentadas na Plataforma.<br><br>'
                    .'<strong>3.5. Entrega:</strong> A entrega dos produtos poderá ser realizada pelo Parceiro Vendedor ou por serviço de entrega indicado na Plataforma. O Parceiro responsável pela entrega informará a área de cobertura, os prazos e as taxas aplicáveis.<br><br>'
                    .'<strong>3.6. Idade Mínima:</strong> A venda de bebidas alcoólicas é permitida exclusivamente a pessoas maiores de 18 anos, com confirmação obrigatória da data de nascimento e bloqueio automático de menores no fluxo de registro. O Cervagelada e/ou o Parceiro Vendedor poderão realizar procedimentos de verificação de idade durante a compra e na entrega, inclusive mediante solicitação de documento oficial com foto. A entrega poderá ser recusada caso não seja possível comprovar a maioridade do recebedor, com cancelamento do pedido e reembolso integral. A venda de bebidas alcoólicas a menores de 18 anos é proibida por lei (Art. 63 do ECA e Lei 13.106/2015).<br><br>'
                    .'<strong>3.7. Avaliações e Comentários:</strong> Você poderá avaliar os produtos e a experiência de compra, fornecendo informações honestas e respeitosas. O Cervagelada reserva-se o direito de remover avaliações que violem estes Termos.',
            ],
            [
                'title' => '4. Regras para Parceiros Vendedores',
                'content' => '<strong>4.1. Cadastro e Conta:</strong> Para anunciar e vender na Plataforma, é necessário realizar um cadastro específico como Parceiro Vendedor, incluindo todas as licenças e registros necessários para a venda de bebidas.<br><br>'
                    .'<strong>4.2. Responsabilidade pelos Produtos:</strong> Você é o único responsável pela qualidade, segurança, legalidade e descrição precisa dos produtos que anuncia na Plataforma.<br><br>'
                    .'<strong>4.3. Preços e Pagamentos:</strong> Você é responsável por definir os preços dos seus produtos. O Cervagelada poderá cobrar uma taxa ou comissão sobre as vendas realizadas, conforme acordo específico entre as partes.<br><br>'
                    .'<strong>4.4. Processamento de Pedidos e Entrega:</strong> Você é o único responsável por processar os pedidos e por realizar a entrega dos produtos, seguindo todas as leis e regulamentos aplicáveis.<br><br>'
                    .'<strong>4.5. Conformidade com a Lei:</strong> Você garante que suas atividades na Plataforma estão em conformidade com todas as leis aplicáveis, incluindo a legislação referente à venda de bebidas alcoólicas e a Lei Geral de Proteção de Dados (LGPD).',
            ],
            [
                'title' => '5. Propriedade Intelectual',
                'content' => 'A Plataforma e todo o seu conteúdo (exceto o conteúdo fornecido pelos Parceiros Vendedores) são de propriedade exclusiva do Cervagelada ou de seus licenciadores, sendo proibida qualquer reprodução, distribuição, modificação ou utilização não autorizada.<br><br>'
                    .'Ao anunciar produtos na Plataforma, você concede ao Cervagelada uma licença não exclusiva, gratuita, perpétua e mundial para utilizar, reproduzir, exibir e divulgar as informações e imagens dos seus produtos na Plataforma e em materiais promocionais.',
            ],
            [
                'title' => '6. Limitação de Responsabilidade',
                'content' => 'O Cervagelada atua como plataforma de intermediação, conectando Usuários Compradores e Parceiros Vendedores. Cada participante responderá pelos atos, serviços e obrigações sob sua responsabilidade, sem prejuízo dos direitos e responsabilidades que não possam ser afastados pela legislação de defesa do consumidor.<br><br>'
                    .'A Plataforma é fornecida "no estado em que se encontra" e "conforme a disponibilidade". O Cervagelada não garante que a Plataforma estará sempre disponível, livre de erros ou interrupções.<br><br>'
                    .'O Cervagelada adota dever de cuidado na curadoria de seus parceiros, incluindo verificação prévia de CNPJ ativo, CNAE compatível, além de monitoramento contínuo de reclamações de consumidores e bloqueio de parceiros reincidentes. O vendedor é sempre identificado em cada anúncio e na finalização da compra (razão social, CNPJ, endereço e canal de atendimento), em conformidade com o Decreto 7.962/2013.',
            ],
            [
                'title' => '7. Privacidade e Proteção de Dados',
                'content' => 'O Cervagelada coleta e trata dados pessoais em conformidade com a Lei Geral de Proteção de Dados (LGPD) e com a nossa Política de Privacidade. Ao utilizar a Plataforma, você declara ter lido e concordado com a nossa Política de Privacidade.',
            ],
            [
                'title' => '8. Rescisão',
                'content' => 'O Cervagelada poderá, a seu exclusivo critério, suspender ou encerrar o acesso de qualquer Usuário ou Parceiro Vendedor à Plataforma, a qualquer momento, em caso de violação destes Termos, uso indevido da plataforma ou motivos comerciais justificáveis, mediante aviso prévio, salvo em hipóteses de grave violação legal ou contratual, quando a medida poderá ser imediata. Em pedidos pagos e não entregues, os valores serão integralmente reembolsados.',
            ],
            [
                'title' => '9. Lei Aplicável e Foro',
                'content' => 'Estes Termos serão regidos e interpretados de acordo com as leis da República Federativa do Brasil. Fica eleito o foro da Comarca de Pinhais, Paraná, sem prejuízo da competência do Juizado Especial Cível, como o único competente para dirimir quaisquer dúvidas ou litígios oriundos destes Termos.',
            ],
            [
                'title' => '10. Contato',
                'content' => 'Em caso de dúvidas ou necessidade de informações adicionais sobre estes Termos, entre em contato conosco:<br><br>'
                    .'E-mail: faleconosco@cervagelada.com.br<br>'
                    .'Endereço: Av. Camilo Di Lellis, 1065, Sala OutBox, Centro - 83323-000 - Pinhais - PR<br>'
                    .'WhatsApp: (41) 9 8855-1173',
            ],
        ],
    ]);
});

Route::get('trocas-e-devolucoes', function () {
    return response()->json([
        'title' => 'Política de Trocas e Devoluções',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => 'Sobre esta Política',
                'content' => 'Esta política descreve como funcionam as trocas e devoluções de bebidas (alcoólicas e não alcoólicas) compradas no marketplace online Cervagelada. Ela é baseada no Código de Defesa do Consumidor do Brasil.<br><br>'
                    .'Importante: no Cervagelada, a compra é realizada com Cervejarias Artesanais, Distribuidoras e demais Lojas Parceiras. O vendedor é responsável pelos produtos, informações, preparação do pedido e atendimento das solicitações de troca ou devolução sob sua responsabilidade, sem prejuízo dos direitos do consumidor e das responsabilidades atribuídas ao Cervagelada pela legislação aplicável.',
            ],
            [
                'title' => '1. Condições Gerais para Troca ou Devolução',
                'content' => '1.1. Para pedir uma troca ou devolução, utilize o canal de comunicação disponível na Plataforma para contatar o vendedor responsável (WhatsApp, telefone, chat ou e-mail). Se houver dificuldade na solução, o suporte do Cervagelada poderá auxiliar na intermediação do atendimento.<br><br>'
                    .'1.2. Reclamação por vício: para produtos não duráveis, como bebidas, o prazo legal para reclamar de vícios aparentes ou de fácil constatação é de 30 dias, contado da entrega. Para produtos duráveis, o prazo legal é de 90 dias, sem prejuízo das regras aplicáveis aos vícios ocultos.<br><br>'
                    .'1.3. Direito de arrependimento em compras online: nas hipóteses previstas na legislação aplicável, o consumidor poderá exercer o direito de arrependimento no prazo de 7 dias, contado do recebimento do produto.<br><br>'
                    .'1.4. Para que a troca ou devolução seja aceita, os produtos precisam estar: na embalagem original, sem sinais de abertura (exceto se o problema for um defeito); sem sinais de uso ou consumo (exceto se o problema for um defeito); com todos os acessórios, manuais e etiquetas (se houver); com a nota fiscal ou comprovante de compra.<br><br>'
                    .'1.5. Guarde os produtos e as bebidas na temperatura correta desde que você as recebe. Problemas causados por armazenamento incorreto não serão de responsabilidade do vendedor.',
            ],
            [
                'title' => '2. Como Solicitar a Troca ou Devolução',
                'content' => '2.1. Acesse a seção "Meus Pedidos" na sua conta Cervagelada e encontre o pedido da bebida que você quer trocar ou devolver.<br><br>'
                    .'2.2. Entre em contato direto com o vendedor responsável pela venda, usando os canais de comunicação na plataforma.<br><br>'
                    .'2.3. Na sua mensagem, informe: o número do pedido; qual produto você quer trocar ou devolver; o motivo da troca ou devolução; e, se possível, envie fotos ou vídeos mostrando o problema.<br><br>'
                    .'2.4. O vendedor vai analisar sua solicitação e, se tudo estiver certo, vai te dar as instruções para a troca ou devolução.',
            ],
            [
                'title' => '3. O Que o Vendedor Pode Oferecer',
                'content' => 'Depois de receber sua solicitação e o produto (se precisar enviar de volta), o vendedor vai verificar se tudo está de acordo com esta política.<br><br>'
                    .'<strong>Troca por defeito:</strong> o vendedor pode oferecer o mesmo produto (se houver estoque), outro produto de mesmo valor (se houver estoque) ou o seu dinheiro de volta.<br><br>'
                    .'<strong>Entrega errada:</strong> se você recebeu um produto diferente do que pediu, o vendedor poderá enviar o produto correto ou realizar o reembolso, conforme o caso.',
            ],
            [
                'title' => '4. Condições Específicas para Bebidas de Consumo Imediato',
                'content' => '4.1. Para pedidos de "entrega para consumo imediato" (bebidas que você pretende consumir logo, como cervejas geladas), qualquer problema visível (embalagem danificada, bebida errada) deve ser comunicado ao vendedor no mesmo dia da entrega, de preferência na hora em que você receber ou em até 2 horas depois do recebimento do produto.<br><br>'
                    .'4.2. Use os canais de contato da plataforma para informar o problema e, se puder, envie fotos ou vídeos.<br><br>'
                    .'4.3. A comunicação rápida de problemas visíveis facilita a solução do atendimento. Contudo, os prazos operacionais sugeridos nesta seção não afastam os prazos e direitos assegurados ao consumidor pela legislação aplicável, inclusive nas hipóteses de vício oculto.',
            ],
            [
                'title' => '5. Como Enviar o Produto para Troca ou Devolução',
                'content' => '5.1. O vendedor vai te dizer como enviar o produto de volta. Geralmente, você terá que levar o produto aos Correios ou outra transportadora indicada, ou esperar que a transportadora retire o produto no seu endereço (se o vendedor oferecer essa opção).<br><br>'
                    .'5.2. O vendedor vai te informar sobre quem paga o frete da devolução ou troca, seguindo as leis (geralmente, o vendedor paga se for por defeito).',
            ],
            [
                'title' => '6. Reembolso do Seu Dinheiro',
                'content' => '6.1. O vendedor vai devolver o seu dinheiro depois de receber e analisar o produto devolvido ou depois de confirmar o cancelamento da compra por arrependimento.<br><br>'
                    .'6.2. O reembolso será feito da mesma forma que você pagou: estorno no cartão de crédito (o tempo para o dinheiro voltar depende da administradora do cartão); depósito ou transferência bancária; ou crédito na sua conta Cervagelada (se o vendedor oferecer essa opção).<br><br>'
                    .'6.3. O vendedor vai te informar o prazo para o reembolso, seguindo as leis.',
            ],
            [
                'title' => '7. Bebidas que Não Podem Ser Trocadas ou Devolvidas',
                'content' => '7.1. Alguns produtos podem estar sujeitos a condições específicas de devolução em razão de sua natureza, personalização, perecibilidade ou condições sanitárias. Essas limitações serão avaliadas caso a caso e não afastam direitos assegurados pela legislação aplicável.<br><br>'
                    .'7.2. O vendedor pode se recusar a trocar ou devolver a bebida se ela não estiver de acordo com esta política.',
            ],
            [
                'title' => '8. O Que o Cervagelada Faz',
                'content' => '8.1. O Cervagelada atua como plataforma de intermediação. O vendedor é responsável pelo tratamento das solicitações relativas aos produtos comercializados por ele, e o Cervagelada poderá auxiliar na comunicação e no atendimento, sem prejuízo das responsabilidades previstas na legislação aplicável.<br><br>'
                    .'8.2. Se você tiver problemas para falar com o vendedor ou para resolver a troca ou devolução, você pode entrar em contato com o suporte do Cervagelada para que possamos ajudar a facilitar a comunicação e buscar uma solução.',
            ],
            [
                'title' => '9. Contato',
                'content' => 'Para pedir uma troca ou devolução, fale direto com o vendedor pela plataforma. Se precisar de ajuda para falar com o vendedor, entre em contato com o suporte do Cervagelada:<br><br>'
                    .'E-mail: faleconosco@cervagelada.com.br<br>'
                    .'Telefone/WhatsApp: (41) 9 8855-1173',
            ],
        ],
    ]);
});

Route::get('quem-somos', function () {
    return response()->json([
        'title' => 'Quem Somos',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => 'A gente está sempre por perto',
                'content' => 'Olá, a gente está muito feliz de ter você aqui perto e, pra ficar melhor da gente se apresentar, vamos por tópicos. E lembre-se: a gente está sempre por perto. Olha aí quem somos nós...',
            ],
            [
                'title' => 'Visão',
                'content' => 'Ser o maior "hub virtual" do mercado cervejeiro do mundo.',
            ],
            [
                'title' => 'Missão',
                'content' => 'Desenvolver todo o macroambiente que envolve o atendimento ao mercado cervejeiro, desde a produção até a reciclagem, gerando dezenas de milhares de empregos diretos e/ou indiretos, satisfação, orgulho e renda para todo o ecossistema.',
            ],
            [
                'title' => 'Nossos Valores',
                'content' => '<strong>Gente</strong> — Nossa gente é uma união totalmente isenta de qualquer tipo de rótulo, porque somos todos iguais.<br><br>'
                    .'<strong>Inovação</strong> — Tudo pode e deve ser questionado buscando a melhoria contínua, sempre com o objetivo de beneficiar a todos os envolvidos, em qualquer fase da empresa.<br><br>'
                    .'<strong>Transparência</strong> — A gente faz sempre tudo de forma transparente, assim todos sabem onde estamos e onde queremos chegar.<br><br>'
                    .'<strong>Sociedade</strong> — A gente gosta de gente por perto; promovemos integração constante dentro e fora da empresa com nossos funcionários, seus familiares e toda a comunidade na qual estamos inseridos.<br><br>'
                    .'<strong>Paixão</strong> — A gente é apaixonado pela nossa empresa, nossas soluções e, principalmente, pela nossa gente.<br><br>'
                    .'<strong>Liderança</strong> — Liderar é a arte de inspirar a todos para terem confiança suficiente de que são capazes de fazer, alcançando os melhores resultados profissionais e pessoais.<br><br>'
                    .'<strong>Credibilidade</strong> — A gente transpira credibilidade; nossa honestidade em tudo o que fazemos deve ser reconhecida pela comunidade dentro e fora da empresa.<br><br>'
                    .'<strong>Ética</strong> — A gente atua sempre dentro de todas as regras da sociedade e dos governos onde atuamos, respeitando todas as leis vigentes e culturas locais.<br><br>'
                    .'<strong>Comunicação</strong> — A comunicação efetiva começa com a escuta ativa.<br><br>'
                    .'<strong>Qualidade</strong> — Qualidade é o que temos e fazemos. Ajustamos sempre que necessário para melhorar nossos produtos e serviços.<br><br>'
                    .'<strong>Meio Ambiente</strong> — Nossa vida depende do meio ambiente; a gente contribui e age o tempo todo pela sua preservação.<br><br>'
                    .'<strong>Fé</strong> — A gente tem fé, acredita na fé e respeita toda e qualquer manifestação de fé.<br><br>'
                    .'<strong>Gratidão</strong> — A gente é movido pela gratidão. Agradecemos a tudo e a todos, somos gratos pela nossa gente e pelo nosso meio ambiente.<br><br>'
                    .'<strong>Metas e Objetivos</strong> — A gente é focado no alcance de nossas metas e objetivos, gerando perpetuidade dos nossos negócios e do nosso ecossistema.<br><br>'
                    .'<strong>Conhecimento</strong> — A gente vai continuar buscando mais conhecimento e sempre compartilhando o que sabemos.',
            ],
        ],
    ]);
});

Route::get('como-vender', function () {
    return response()->json([
        'title' => 'Como Vender no Cervagelada',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => 'Prezados(as) Parceiros(as)',
                'content' => 'O Cervagelada está comprometido em construir um marketplace de bebidas de alta qualidade, proporcionando a melhor experiência para nossos clientes. Para isso, buscamos estabelecer parcerias sólidas e duradouras com Cervejarias Artesanais e Distribuidoras de Bebidas que compartilhem nosso compromisso com a excelência.<br><br>'
                    .'Se você tem um CNPJ com CNAE de Cervejaria Artesanal ou Distribuidora de Bebidas, verifique abaixo os requisitos para ser nosso parceiro. Um detalhe importante: só temos parceria com CNPJs com no mínimo 12 meses de atividade. Mande um e-mail para <strong>comercial@cervagelada.com.br</strong>.',
            ],
            [
                'title' => 'Requisitos para Cervejarias Artesanais Parceiras',
                'content' => '<strong>Compromisso com a Qualidade do Produto</strong><br>'
                    .'Regularização e conformidade: possuir todas as licenças e registros sanitários (federal, estadual e municipal) para produção e comercialização de cerveja artesanal, sempre atualizados.<br>'
                    .'Excelência na produção: implementar e manter rigorosos padrões de boas práticas de fabricação (BPF).<br>'
                    .'Controle de qualidade rigoroso em matérias-primas, no processo produtivo e no produto final.<br>'
                    .'Embalagem e rotulagem adequadas, em conformidade com a legislação vigente.<br>'
                    .'Armazenamento responsável, protegendo os produtos de fatores que comprometam sua qualidade.<br><br>'
                    .'<strong>Compromisso com a Parceria Cervagelada</strong><br>'
                    .'Capacidade produtiva e de estoque para atender à demanda da plataforma, com potencial de crescimento.<br>'
                    .'Confiabilidade e cumprimento de prazos de entrega, com fluxo de fornecimento consistente.<br>'
                    .'Comunicação eficaz e ágil com o Cervagelada e os clientes.<br>'
                    .'Flexibilidade e proatividade para colaborar com as iniciativas da Cervagelada.<br>'
                    .'Alinhamento de valores com a satisfação do cliente, a qualidade dos produtos e a ética nos negócios.<br>'
                    .'Disponibilidade para integrar seus sistemas e processos com a plataforma da Cervagelada.',
            ],
            [
                'title' => 'Requisitos para Distribuidoras de Bebidas Parceiras',
                'content' => '<strong>Compromisso com a Qualidade na Distribuição</strong><br>'
                    .'Regularização e conformidade: possuir todas as licenças e registros necessários para a distribuição e comercialização de bebidas, sempre atualizados.<br>'
                    .'Infraestrutura de armazenamento adequada, incluindo controle de temperatura quando necessário.<br>'
                    .'Transporte seguro e eficiente, com veículos adequados para proteção contra danos e variações de temperatura.<br>'
                    .'Rastreabilidade dos produtos, com sistema que permita identificar origem e destino de cada lote.<br>'
                    .'Manuseio e conservação cuidadosos, com colaboradores capacitados.<br><br>'
                    .'<strong>Compromisso com a Parceria Cervagelada</strong><br>'
                    .'Ampla área de cobertura de distribuição, atendendo às necessidades da Cervagelada e de seus clientes.<br>'
                    .'Logística eficaz e ágil, com entregas rápidas, seguras e dentro dos prazos.<br>'
                    .'Experiência comprovada na distribuição de bebidas, preferencialmente incluindo cervejas artesanais.<br>'
                    .'Bom relacionamento com diversas cervejarias, facilitando o acesso a um portfólio variado.<br>'
                    .'Comunicação transparente e proativa sobre informações relevantes da distribuição.<br>'
                    .'Flexibilidade e capacidade de solução de eventuais problemas de entrega.<br>'
                    .'Alinhamento de valores com a satisfação do cliente, a qualidade dos serviços e a ética nos negócios.<br>'
                    .'Disponibilidade para integrar seus sistemas e processos com a plataforma da Cervagelada.',
            ],
            [
                'title' => 'Próximos Passos',
                'content' => 'Acreditamos que, ao estabelecermos estes requisitos mínimos, construiremos uma rede de parceiros de excelência, capaz de oferecer aos nossos clientes a melhor experiência no mercado de cervejas artesanais e outras bebidas. Estamos à disposição para quaisquer esclarecimentos e ansiosos para construir uma parceria de sucesso com você!<br><br>'
                    .'Atenciosamente, Equipe Cervagelada.',
            ],
        ],
    ]);
});

Route::get('politica-de-privacidade', function () {
    return response()->json([
        'title' => 'Política de Privacidade',
        'last_updated' => '2025-04-01',
        'sections' => [
            [
                'title' => '1. Introdução',
                'content' => 'Esta Política de Privacidade descreve como a M2T Tecnologia Ltda ("Cervagelada", "nós" ou "nosso"), com sede na Av. Camilo Di Lellis, 1065, Sala OutBox, Centro - 83323-000 - Pinhais - PR, CNPJ 57.774.206/0001-96, coleta, utiliza, compartilha e protege as informações pessoais dos usuários que acessam e utilizam nosso marketplace disponível em www.cervagelada.com.br e seu aplicativo ("Plataforma"). Para fins desta Política e da LGPD, a M2T Tecnologia Ltda atua como controladora dos dados pessoais quando define as finalidades e os meios essenciais do tratamento realizado pela Plataforma.<br><br>'
                    .'Ao utilizar a Plataforma Cervagelada, você concorda com os termos desta Política de Privacidade. Caso não concorde com algum dos termos aqui apresentados, você não deve utilizar nossa Plataforma.',
            ],
            [
                'title' => '2. Quais Dados Pessoais Coletamos',
                'content' => '<strong>Dados de cadastro:</strong> nome completo, e-mail, telefone, data de nascimento, endereço de entrega, CPF (em alguns casos), informações de pagamento e senha de acesso.<br><br>'
                    .'<strong>Dados de utilização da Plataforma:</strong> histórico de pedidos e compras, produtos visualizados e pesquisados, avaliações e comentários, interações com o suporte, preferências de produtos e categorias, e informações de localização (com o seu consentimento).<br><br>'
                    .'<strong>Dados do dispositivo e de navegação:</strong> endereço IP, tipo de dispositivo, sistema operacional, informações do navegador, dados de acesso (data e hora), páginas visitadas e cookies.<br><br>'
                    .'<strong>Dados de Parceiros (Cervejarias e Distribuidoras):</strong> razão social, CNPJ, endereço comercial, dados de contato do representante, informações bancárias, informações sobre produtos e estoque, e documentação legal e sanitária.<br><br>'
                    .'<strong>Dados de redes sociais:</strong> caso você opte por se conectar à nossa Plataforma através de uma rede social, poderemos coletar informações do seu perfil, de acordo com as configurações de privacidade definidas por você.',
            ],
            [
                'title' => '3. Como Utilizamos Seus Dados Pessoais',
                'content' => '<strong>Fornecer e operar a Plataforma:</strong> criar e gerenciar sua conta, processar pedidos, intermediar sua relação com os parceiros, notificar sobre o status do pedido, processar pagamentos e reembolsos e prestar suporte ao cliente.<br><br>'
                    .'<strong>Personalizar sua experiência:</strong> recomendar produtos e ofertas com base em suas preferências e histórico de compras.<br><br>'
                    .'<strong>Comunicação:</strong> enviar e-mails e notificações sobre pedidos, promoções e novidades (com o seu consentimento, quando aplicável) e responder às suas dúvidas.<br><br>'
                    .'<strong>Melhoria da Plataforma:</strong> realizar análises estatísticas e desenvolver novas funcionalidades e serviços.<br><br>'
                    .'<strong>Marketing e publicidade:</strong> enviar publicidade direcionada com base em seus interesses (com o seu consentimento, quando aplicável).<br><br>'
                    .'<strong>Segurança e prevenção de fraudes:</strong> verificar sua identidade e prevenir atividades fraudulentas ou ilegais.<br><br>'
                    .'<strong>Obrigações legais e regulatórias:</strong> cumprir obrigações legais, como emissão de notas fiscais e atendimento a ordens judiciais.',
            ],
            [
                'title' => '4. Como Compartilhamos Seus Dados Pessoais',
                'content' => '<strong>Parceiros (Cervejarias e Distribuidoras):</strong> compartilhamos seus dados de contato e informações do pedido com os parceiros responsáveis por fornecer e entregar os produtos que você solicitou.<br><br>'
                    .'<strong>Prestadores de serviços:</strong> contratamos empresas para processamento de pagamentos, análise de dados, envio de e-mails, marketing, suporte ao cliente e infraestrutura, obrigadas a proteger seus dados.<br><br>'
                    .'<strong>Autoridades públicas:</strong> em cumprimento de obrigações legais, regulatórias ou ordens judiciais.<br><br>'
                    .'<strong>Parceiros de marketing:</strong> dados anonimizados ou pseudonimizados, para fins de publicidade direcionada (com o seu consentimento, quando aplicável).<br><br>'
                    .'<strong>Em caso de transação empresarial:</strong> fusão, aquisição, venda de ativos ou outra transação empresarial pode implicar a transferência de seus dados para a empresa adquirente.',
            ],
            [
                'title' => '5. Cookies e Outras Tecnologias de Rastreamento',
                'content' => 'Utilizamos cookies e outras tecnologias de rastreamento (como pixels e web beacons) para coletar informações sobre sua atividade de navegação na nossa Plataforma. Esses dados nos ajudam a melhorar a sua experiência, personalizar conteúdo e anúncios, analisar o tráfego do site e entender de onde nossos usuários vêm.<br><br>'
                    .'Você pode controlar o uso de cookies através das configurações do seu navegador. No entanto, desabilitar alguns cookies pode afetar a funcionalidade da nossa Plataforma.',
            ],
            [
                'title' => '6. Segurança dos Seus Dados Pessoais',
                'content' => 'Implementamos medidas de segurança técnicas e organizacionais adequadas para proteger seus dados pessoais contra acesso não autorizado, uso indevido, alteração, divulgação ou destruição, incluindo criptografia, firewalls, controles de acesso e treinamento de nossos colaboradores.<br><br>'
                    .'No entanto, é importante lembrar que nenhuma medida de segurança é completamente infalível. Portanto, não podemos garantir a segurança absoluta dos seus dados pessoais.',
            ],
            [
                'title' => '7. Retenção dos Seus Dados Pessoais',
                'content' => 'Nós reteremos seus dados pessoais pelo tempo necessário para cumprir as finalidades para as quais foram coletados, incluindo o cumprimento de obrigações legais, regulatórias, contratuais ou para o exercício regular de direitos.',
            ],
            [
                'title' => '8. Seus Direitos em Relação aos Seus Dados Pessoais',
                'content' => 'Em conformidade com a LGPD, você tem os seguintes direitos: acesso, retificação, eliminação, oposição, portabilidade, revogação do consentimento, informação sobre o compartilhamento e revisão de decisões automatizadas.<br><br>'
                    .'Para exercer qualquer um desses direitos, entre em contato conosco pelos canais indicados na seção "Contato" abaixo. A solicitação será analisada de acordo com a LGPD e demais normas aplicáveis, podendo ser necessária a confirmação da identidade do titular.',
            ],
            [
                'title' => '9. Privacidade de Crianças e Adolescentes',
                'content' => 'Nossa Plataforma não se destina à compra de bebidas alcoólicas por menores de 18 anos. A comercialização de bebidas alcoólicas é restrita a maiores de 18 anos. Caso sejam identificados dados ou contas utilizados em desacordo com essa regra, poderão ser adotadas medidas de verificação, bloqueio ou exclusão, observada a legislação aplicável.',
            ],
            [
                'title' => '10. Alterações a Esta Política de Privacidade',
                'content' => 'Podemos atualizar esta Política de Privacidade periodicamente para refletir mudanças em nossas práticas de privacidade ou em decorrência de alterações na legislação. A versão mais recente estará sempre disponível em nossa Plataforma, com a data da última atualização.',
            ],
            [
                'title' => '11. Contato',
                'content' => 'Se você tiver alguma dúvida ou preocupação sobre esta Política de Privacidade ou sobre o tratamento dos seus dados pessoais, entre em contato conosco:<br><br>'
                    .'E-mail: faleconosco@cervagelada.com.br<br>'
                    .'Endereço: Av. Camilo Di Lellis, 1065, Sala OutBox, Centro - 83323-000 - Pinhais - PR<br>'
                    .'Telefone: (41) 9 8855-1173',
            ],
        ],
    ]);
});
