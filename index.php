<?php

/** INSTRUÇÕES
 *  Temos um pdf com um resultado de um concurso em anexo;
 *  Precisamos de um script em php que leia esse anexo e retorne um csv, xls com os dados dos aprovados.
 *  Vale atentar aos resultados para candidatos com deficiência, etc.;
 *  Utilize expressões regulares;
 *  Deve ser feito com PHP.
 */

include 'vendor/autoload.php';

$arquivoPDF = 'ED_6__2019__DPDF_DEFENSOR_RES_PROVISORIO_OBJETIVA.pdf';
$parser = new \Smalot\PdfParser\Parser();

//Extraindo texto do arquivo PDF
$pdf = $parser->parseFile($arquivoPDF);
$texto = $pdf->getText();

$expressao = "/ *?(\d+), *?([ \w]+), *?(\d+), *?(\d+\.\d+)/";

//Convertendo de NO-BREAK SPACE codepoint para espaço
$texto =   str_replace("\xc2\xa0", " ", $texto);

//Extraindo dados
preg_match_all(
    $expressao,
    $texto,
    $dados,
    PREG_PATTERN_ORDER
);

//Exportando para CSV
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=dados_dos_aprovados.csv');
header('Content-Transfer-Encoding: binary');
header('Pragma: no-cache');
$out = fopen('php://output','b');

fputcsv($out, array('N. Inscrição', 'Nome do candidato ', 'N. de acertos', 'Nota provisória') );
$iMax = count($dados[0]);

for ($i = 0; $i < $iMax; $i++){

    fputcsv($out, array((int)$dados[1][$i], trim($dados[2][$i]), (int)$dados[3][$i], number_format($dados[4][$i], 2, '.', '')));
}
fclose($out);

