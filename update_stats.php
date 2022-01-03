<?php

require 'functions.php';

$pontuacoes           = [];
$vencedores_rodadas   = [];
$rodadas_vencidas     = [];
$menor_pont_vencedor  = [ 'time' => '', 'pontos' => 10000, 'rodada' => 0 ];
$pontuacoes_por_time  = [];
$tabela_final         = [];
$tabela_por_mes       = [];
$lideres              = [];
$rodadas_na_lideranca = [];
$colocacao_geral      = [];
$mais_escalados       = [];
$times_mais_escalados = [];

$mais_escalados_pos       = [];
$times_mais_escalados_pos = [];

$mais_escalados_time       = [];
$times_mais_escalados_time = [];

$capitaes 		     = [];
$capitaes_por_time   = [];
$tabela_s_capitao    = [];

$quantos_jogadores = [];

$scouts_por_time = [];
$times_por_scout = [];


$times = [ 
	'13087463' => 'LAM BR',
	'391582'   => 'MIRANDEXTER F.R',
	'14457016' => 'FLAPOLINARIO',
	'25906150' => 'Santa Carupita S.C.',
	'14439154' => 'Timaço do Mumu',
	'14785157' => 'TimeThunder',
	'366857'   => 'VERRUGA-FC',
	'1414'     => 'ROSSINENSE',
	'28512436' => 'Prosbild FC',
	'28894191' => 'Nalucar FC',
	'19298986' => 'TellesFLA',
	'27496871' => 'NaSegundona',
	'28970970' => 'EC ENIGMA FLA',
];

$clubes = [
	'1'    => 'Desconhecido',
	'1371' => 'Cuiabá',
	'265'  => 'Bahia',
	'264'  => 'Corinthians',
	'262'  => 'Flamengo',
	'293'  => 'Athlético PR',
	'282'  => 'Atlético MG',
	'277'  => 'Santos',
	'284'  => 'Grêmio',
	'276'  => 'São Paulo',
	'280'  => 'Bragantino',
	'266'  => 'Fluminense',
	'327'  => 'América MG',
	'275'  => 'Palmeiras',
	'292'  => 'Sport',
	'373'  => 'Atlétigo GO',
	'315'  => 'Chapecoense',
	'285'  => 'Internacional',
	'354'  => 'Ceará',
	'356'  => 'Fortaleza',
	'286'  => 'Juventude',
];

$scouts = [
	'DS' => 'Desarmes',
	'FF' => 'Finalizações pra Fora',
	'I'  => 'Impedimentos',
	'FS' => 'Faltas Sofridas',
	'PI' => 'Passes Incompletos',
	'CA' => 'Cartões Amarelos',
	'FC' => 'Faltas Cometidas',
	'DE' => 'Defesas',
	'GS' => 'Gols Sofridos',
	'SG' => 'Saldo de Gols',
	'FD' => 'Finalizações Defendidas',
	'PC' => 'Pênaltis Cometidos',
	'G'  => 'Gols',
	'PP' => 'Pênaltis Perdidos',
	'A'  => 'Assistências',
	'FT' => 'Finalizações na Trave',
	'CV' => 'Cartões Vermelhos',
	'GC' => 'Gols Contra',
	'PS' => 'Pênaltis Sofridos',
	'DP' => 'Defesas de Pênalti',
	'TC' => 'Técnico',
];

$pontos_scouts = [
	'DS' => 1,
	'FF' => 0.8,
	'I'  => -0.5,
	'FS' => 0.5,
	'PI' => -0.1,
	'CA' => -2,
	'FC' => -0.5,
	'DE' => 1,
	'GS' => -1,
	'SG' => 5,
	'FD' => 1.2,
	'PC' => -1,
	'G'  => 8,
	'PP' => -4,
	'A'  => 5,
	'FT' => 3,
	'CV' => -5,
	'GC' => -5,
	'PS' => 1,
	'DP' => 7,
	'TC' => 1, // "Scout" Técnico => que não tem scouts
];

$posicoes = [ '', 'Goleiro', 'Lateral', 'Zagueiro', 'Meia', 'Atacante', 'Técnico' ];

for ( $i = 1; $i <= 6; $i++ ) {
	$mais_escalados_pos[ $posicoes[ $i ] ] = [];
	$times_mais_escalados_pos[ $posicoes[ $i ] ] = [];
}

// Mês de cada rodada - Para descobrir vencedores por mês
// Retirado de https://www.transfermarkt.com.br/campeonato-brasileiro-serie-a/gesamtspielplan/wettbewerb/BRA1/saison_id/2020
// APESAR DE 2020 na URL. está mostrando os dados de 2021!!
// Em 2022, buscar esses dados da API do Cartola que não está disponível hoje (28/12/21)
$mes_da_rodada = [ 5, 6, 6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 11, 11, 11, 11, 11, 11, 11, 11, 12, 12 ];

$mercado = callURL( 'https://api.cartolafc.globo.com/mercado/status' );

for ( $rodada = 1; $rodada <= $mercado->rodada_atual; $rodada++ ) {

	$colocacao_geral[ $rodada ] = [];

	print_r( 'Rodada ' . $rodada . PHP_EOL );

	foreach ( $times as $time_id => $nome ) {

		$rodada_time = load_info_time( $time_id, $rodada );

		$mais_escalados_time[ $nome ]       = empty( $mais_escalados_time[ $nome ] ) ? [] : $mais_escalados_time[ $nome ];
		$times_mais_escalados_time[ $nome ] = empty( $times_mais_escalados_time[ $nome ] ) ? [] : $times_mais_escalados_time[ $nome ];

		$pont_sem_capitao = 0;
		foreach( $rodada_time->atletas as $atleta ) {
			$pont_sem_capitao += $atleta->pontos_num;

			$mais_escalados[ $atleta->apelido ] = empty( $mais_escalados[ $atleta->apelido ] ) ? 1 : $mais_escalados[ $atleta->apelido ] + 1;
			$times_mais_escalados[ $clubes[ $atleta->clube_id ] ] = empty( $times_mais_escalados[ $clubes[ $atleta->clube_id ] ] ) ? 1 : $times_mais_escalados[ $clubes[ $atleta->clube_id ] ] + 1;

			$mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $atleta->apelido ] = empty( $mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $atleta->apelido ] ) ? 1 : $mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $atleta->apelido ] + 1;
			$times_mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $clubes[ $atleta->clube_id ] ] = empty( $times_mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $clubes[ $atleta->clube_id ] ] ) ? 1 : $times_mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $clubes[ $atleta->clube_id ] ] + 1;

			$mais_escalados_time[ $nome ][ $atleta->apelido ] = empty( $mais_escalados_time[ $nome ][ $atleta->apelido ] ) ? 1 : $mais_escalados_time[ $nome ][ $atleta->apelido ] + 1;
			$times_mais_escalados_time[ $nome ][ $clubes[ $atleta->clube_id ] ] = empty( $times_mais_escalados_time[ $nome ][ $clubes[ $atleta->clube_id ] ] ) ? 1 : $times_mais_escalados_time[ $nome ][ $clubes[ $atleta->clube_id ] ] + 1;

			if ( $rodada_time->capitao_id == $atleta->atleta_id ) {
				$capitaes[ $atleta->apelido ] = empty( $capitaes[ $atleta->apelido ] ) ? 1 : $capitaes[ $atleta->apelido ] + 1;
				$capitaes_por_time[ $nome ][ $atleta->apelido ] =  empty( $capitaes_por_time[ $nome ][ $atleta->apelido ] ) ? 1 : $capitaes_por_time[ $nome ][ $atleta->apelido ] + 1;
			}

			foreach ( $atleta->scout as $scout => $scout_value ) {
				$scouts_por_time[ $nome ][ $scout ] = empty( $scouts_por_time[ $nome ][ $scout ] ) ? $scout_value : $scouts_por_time[ $nome ][ $scout ] + $scout_value;
				$times_por_scout[ $scout ][ $nome ] = empty ( $times_por_scout[ $scout ][ $nome ] ) ? $scout_value : $times_por_scout[ $scout ][ $nome ] + $scout_value;
			}

			if ( $atleta->posicao_id == '6' ) { // tecnicos não tem scout
				$times_por_scout[ 'TC' ][ $nome ] = empty( $times_por_scout[ 'TC' ][ $nome ] ) ? $atleta->pontos_num : $times_por_scout[ 'TC' ][ $nome ] + $atleta->pontos_num;
			}
		}
		$pontos_rodada = round( $rodada_time->pontos, 2 );

		if ( ! empty( $rodada_time->reservas ) ) { 
			foreach( $rodada_time->reservas as $atleta ) {
				$mais_escalados[ $atleta->apelido ] = empty( $mais_escalados[ $atleta->apelido ] ) ? 1 : $mais_escalados[ $atleta->apelido ] + 1;

				$times_mais_escalados[ $clubes[ $atleta->clube_id ] ] = empty( $times_mais_escalados[ $clubes[ $atleta->clube_id ] ] ) ? 1 : $times_mais_escalados[ $clubes[ $atleta->clube_id ] ] + 1;

				$mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $atleta->apelido ] = empty( $mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $atleta->apelido ] ) ? 1 : $mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $atleta->apelido ] + 1;
				$times_mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $clubes[ $atleta->clube_id ] ] = empty( $times_mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $clubes[ $atleta->clube_id ] ] ) ? 1 : $times_mais_escalados_pos[ $posicoes[ $atleta->posicao_id ] ][ $clubes[ $atleta->clube_id ] ] + 1;

				$mais_escalados_time[ $nome ][ $atleta->apelido ] = empty( $mais_escalados_time[ $nome ][ $atleta->apelido ] ) ? 1 : $mais_escalados_time[ $nome ][ $atleta->apelido ] + 1;
				$times_mais_escalados_time[ $nome ][ $clubes[ $atleta->clube_id ] ] = empty( $times_mais_escalados_time[ $nome ][ $clubes[ $atleta->clube_id ] ] ) ? 1 : $times_mais_escalados_time[ $nome ][ $clubes[ $atleta->clube_id ] ] + 1;
			}
		}

		print_r( $nome . ' => ');
		print_r( round( $rodada_time->pontos, 2 ) . ' (' . round( $rodada_time->pontos_campeonato, 2 ) . ') ' . PHP_EOL );
		print_r( PHP_EOL );

		$tabela_s_capitao[ $nome ] = empty( $tabela_s_capitao[ $nome ] ) ? $pont_sem_capitao : $tabela_s_capitao[ $nome ] + $pont_sem_capitao;

		array_push( $pontuacoes, [
			'time'   => $nome,
			'pontos' => $pontos_rodada,
			'rodada' => $rodada,
		]);

		$tabela_por_mes[ $mes_da_rodada[ $rodada - 1 ] ] = empty( $tabela_por_mes[ $mes_da_rodada[ $rodada - 1 ] ] ) ? [] : $tabela_por_mes[ $mes_da_rodada[ $rodada - 1 ] ];
		$tabela_por_mes[ $mes_da_rodada[ $rodada - 1 ] ][ $nome ] = empty( $tabela_por_mes[ $mes_da_rodada[ $rodada - 1 ] ][ $nome ] ) ? $pontos_rodada : $tabela_por_mes[ $mes_da_rodada[ $rodada - 1 ] ][ $nome ] + $pontos_rodada;

		$colocacao_geral[ $rodada ][ $nome ] = round( $rodada_time->pontos_campeonato, 2 );

		if ( empty( $vencedores_rodadas[ $rodada ] ) || ( $pontos_rodada > $vencedores_rodadas[ $rodada ]['pontos'] ) ) {
			$vencedores_rodadas[ $rodada ] = [
				'time'   => $nome,
				'pontos' => $pontos_rodada,
			];
		}

		if ( empty( $pontuacoes_por_time[ $nome ] ) ) {
			$pontuacoes_por_time[ $nome ] = [ 'pontuacoes' => [] ];
		}
		array_push( $pontuacoes_por_time[ $nome ]['pontuacoes'], $pontos_rodada );

		if ( empty( $lideres[ $rodada ] ) || ( $rodada_time->pontos_campeonato > $lideres[ $rodada ]['pontos_campeonato'] ) ) {
			$lideres[ $rodada ] = [
				'time'              => $nome,
				'pontos_campeonato' => round( $rodada_time->pontos_campeonato, 2 ),
			];
		}
	}

	print_r( PHP_EOL );
}
//exit();

foreach ( $tabela_por_mes as $mes => $tabela ) {
	arsort( $tabela_por_mes[ $mes ] );
}
file_put_contents( 'stats/tabelas_por_mes.json', json_encode( $tabela_por_mes ) );
print_r( $tabela_por_mes );


// Maiores Pontuações
usort( $pontuacoes, function ( $a, $b ) {
	return ( $a['pontos'] < $b['pontos']);
});

$top_10_pontuacoes = array_slice( $pontuacoes, 0, 10 );
file_put_contents( 'stats/top_10_pontuacoes.json', json_encode( $top_10_pontuacoes ) );

print_r( 'MAIORES PONTUAÇÕES '. PHP_EOL );
print_r( '##################'. PHP_EOL );
for ( $i = 0; $i <= 29; $i++ ) {
	print_r( ($i+1) . '. ' . $pontuacoes[ $i ]['time'] . ' => ' . $pontuacoes[ $i ]['pontos'] . ' (' . $pontuacoes[ $i ]['rodada'] . 'a rodada)' . PHP_EOL );
}


print_r( PHP_EOL );
print_r( 'VENCEDORES POR RODADA' . PHP_EOL );
print_r( '#####################' . PHP_EOL );
for ( $i = 1; $i <= count( $vencedores_rodadas ); $i++ ) {
	print_r( $i. 'ª) '. $vencedores_rodadas[$i]['time'] . ' => ' . $vencedores_rodadas[$i]['pontos'] . PHP_EOL );

	if ( empty( $rodadas_vencidas[ $vencedores_rodadas[ $i ]['time'] ] ) ) {
		$rodadas_vencidas[ $vencedores_rodadas[ $i ]['time'] ] = 1;
	} else {
		$rodadas_vencidas[ $vencedores_rodadas[ $i ]['time'] ] = $rodadas_vencidas[ $vencedores_rodadas[ $i ]['time'] ] + 1;
	}

	if ( $menor_pont_vencedor['pontos'] > $vencedores_rodadas[$i]['pontos'] ) {
		$menor_pont_vencedor['time']   = $vencedores_rodadas[$i]['time'];
		$menor_pont_vencedor['pontos'] = $vencedores_rodadas[$i]['pontos'];
		$menor_pont_vencedor['rodada'] = $i;
  	}
}

arsort( $rodadas_vencidas );

print_r( PHP_EOL );
print_r( 'Quem venceu mais rodadas: ' );
print_r( PHP_EOL );
print_r( $rodadas_vencidas );
file_put_contents( 'stats/rodadas_vencidas.json', json_encode( $rodadas_vencidas ) );


print_r( PHP_EOL );
print_r( 'Menor pontuação para vencer uma rodada: ' . PHP_EOL );
print_r( $menor_pont_vencedor );
file_put_contents( 'stats/menor_pontuacao_vencedor.json', json_encode( $menor_pont_vencedor ) );

print_r( PHP_EOL );
$index = 0;
foreach ( $pontuacoes_por_time as $time => $dados ) {

	$tabela_final[ $index ] = [];
	$tabela_final[ $index ]['time'] = $time;
	$tabela_final[ $index ]['pontos'] = round( array_sum( $dados['pontuacoes'] ), 2 );
	$tabela_final[ $index ]['1o_turno'] = round( array_sum( array_slice( $dados['pontuacoes'], 0, 19 ) ), 2 ); // cada rodada tem 19 rodadas (length)
	$tabela_final[ $index ]['2o_turno'] = round( array_sum( array_slice( $dados['pontuacoes'], 19, 19 ) ), 2 );
	$tabela_final[ $index ]['max'] = max( $dados['pontuacoes'] );
	$tabela_final[ $index ]['min'] = min( $dados['pontuacoes'] );
	$tabela_final[ $index ]['avg'] = round( array_sum( $dados['pontuacoes'] ) / count( $dados['pontuacoes'] ), 2 );
	$tabela_final[ $index ]['maior_q_100'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 100; } ) );
	$tabela_final[ $index ]['entre_90_100'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 90 && $val < 100; } ) );
	$tabela_final[ $index ]['entre_80_90'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 80 && $val < 90; } ) );
	$tabela_final[ $index ]['entre_70_80'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 70 && $val < 80; } ) );
	$tabela_final[ $index ]['entre_60_70'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 60 && $val < 70; } ) );
	$tabela_final[ $index ]['entre_50_60'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 50 && $val < 60; } ) );
	$tabela_final[ $index ]['entre_40_50'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 40 && $val < 50; } ) );
	$tabela_final[ $index ]['entre_30_40'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 30 && $val < 40; } ) );
	$tabela_final[ $index ]['entre_20_30'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val >= 20 && $val < 30; } ) );
	$tabela_final[ $index ]['menor_q_20'] = count( array_filter( $dados['pontuacoes'], function( $val ) { return $val < 20; } ) );

	$index++;
}

// Tabela do Campeonato
usort( $tabela_final, function ( $a, $b ) {
	return ( $a['pontos'] < $b['pontos']);
});
print_r( $tabela_final );
file_put_contents( 'stats/tabela_final.json', json_encode( $tabela_final ) );

// Tabela 1º Turno
usort( $tabela_final, function ( $a, $b ) {
	return ( $a['1o_turno'] < $b['1o_turno']);
});
file_put_contents( 'stats/tabela_1o_turno.json', json_encode( $tabela_final ) );

// Tabela 2º Turno
usort( $tabela_final, function ( $a, $b ) {
	return ( $a['2o_turno'] < $b['2o_turno']);
});
file_put_contents( 'stats/tabela_2o_turno.json', json_encode( $tabela_final ) );

print_r( PHP_EOL );
print_r( 'Líderes por rodadas' . PHP_EOL );
for ( $i = 1; $i <= count( $lideres ); $i++ ) {
	print_r( $i. 'a) ' . $lideres[ $i ]['time'] . ' => ' . $lideres[ $i ]['pontos_campeonato'] . PHP_EOL );

	if ( empty( $rodadas_na_lideranca[ $lideres[ $i ]['time'] ] ) ) {
		$rodadas_na_lideranca[ $lideres[ $i ]['time'] ] = 1;
	} else {
		$rodadas_na_lideranca[ $lideres[ $i ]['time'] ] = $rodadas_na_lideranca[ $lideres[ $i ]['time'] ] + 1;
	}
}

print_r( PHP_EOL );
print_r( 'Rodadas na liderança: ' . PHP_EOL );
arsort( $rodadas_na_lideranca );
print_r( $rodadas_na_lideranca );
file_put_contents( 'stats/rodadas_na_lideranca.json', json_encode( $rodadas_na_lideranca ) );

print_r( PHP_EOL );

file_put_contents( 'stats/colocacao_por_rodada.json', json_encode( $colocacao_geral ) );

arsort( $mais_escalados );
print_r( $mais_escalados );
print_r( count( $mais_escalados ) . ' jogadores diferentes escalados' .PHP_EOL );

arsort( $times_mais_escalados );
print_r( $times_mais_escalados );

foreach( $mais_escalados_pos as $pos => $mais_escalados ) {
	arsort( $mais_escalados_pos[ $pos ] );
}
print_r( $mais_escalados_pos );

foreach( $times_mais_escalados_pos as $pos => $mais_escalados ) {
	arsort( $times_mais_escalados_pos[ $pos ] );
}
print_r( $times_mais_escalados_pos );

foreach( $mais_escalados_time as $time => $mais_escalados ) {
	arsort( $mais_escalados_time[ $time ] );	
	$quantos_jogadores[ $time ] = count( $mais_escalados );
}
print_r( $mais_escalados_time );

arsort( $quantos_jogadores );
print_r( $quantos_jogadores );

foreach( $times_mais_escalados_time as $time => $mais_escalados ) {
	arsort( $times_mais_escalados_time[ $time ] );
}
print_r( $times_mais_escalados_time );


arsort( $capitaes );
print_r( $capitaes );
print_r ( count( $capitaes ) );
print_r( ' capitães diferentes' . PHP_EOL );

foreach( $capitaes_por_time as $time => $capitaes ) {
	arsort( $capitaes_por_time[ $time ] );
	print_r ( count( $capitaes ) );
	print_r( ' capitães diferentes escalados por ' . $time . PHP_EOL );
}

print_r( $capitaes_por_time );

arsort( $tabela_s_capitao );
print_r( $tabela_s_capitao );

print_r( $scouts_por_time );

$total_por_scout = [];
foreach( $times_por_scout as $scout => $times ) {

	if ( $pontos_scouts[ $scout ] > 0 ) {
		arsort( $times_por_scout[ $scout ] );
	} else {
		asort( $times_por_scout[ $scout ] );
	}

	print_r( $scout . PHP_EOL . PHP_EOL );

	foreach ( $times_por_scout[ $scout ] as $time => $qtd ) {
		$times_por_scout[ $scout ][ $time ] = [ $qtd, $qtd * $pontos_scouts[ $scout ] ];
		print_r( $time . ' => ' . $qtd . ' / ' . $qtd * $pontos_scouts[ $scout ] . ' pontos' . PHP_EOL );
		$total_por_scout[ $time ] = empty( $total_por_scout[ $time ] ) ? $qtd * $pontos_scouts[ $scout ] : $total_por_scout[ $time ] + ( $qtd * $pontos_scouts[ $scout ] );
	}

	print_r( PHP_EOL );
}
// /print_r( $times_por_scout );

print_r( $total_por_scout );