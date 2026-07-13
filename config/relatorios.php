<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Taxa da plataforma
    |--------------------------------------------------------------------------
    |
    | Percentual (0-1) retido pela plataforma sobre a receita bruta de cada
    | loja no relatório financeiro. Ex.: 0.10 = 10%.
    |
    */
    'taxa_plataforma' => (float) env('RELATORIOS_TAXA_PLATAFORMA', 0.10),

];
