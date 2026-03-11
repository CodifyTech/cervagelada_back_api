<?php

namespace App\Domains\Produto\Seeders;

use App\Domains\Produto\Models\Produto;
use Illuminate\Database\Seeder;

class SkuCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $skus = $this->getSkus();
        $created = 0;
        $skipped = 0;

        foreach ($skus as $sku) {
            if (Produto::where('ean', $sku['ean'])->exists()) {
                $skipped++;

                continue;
            }

            Produto::create(array_merge($sku, [
                'status_aprovacao' => 'aprovado',
            ]));
            $created++;
        }

        $this->command?->info("SKU Catalog: {$created} created, {$skipped} skipped.");
    }

    private function getSkus(): array
    {
        return [
            // Ambev
            ['nome' => 'Brahma Chopp 350ml', 'marca' => 'Brahma', 'fabricante' => 'Ambev', 'ean' => '7891149010509', 'volume_ml' => 350, 'teor_alcoolico' => 4.8, 'descricao' => 'Cerveja Pilsen tipo Chopp, sabor suave e refrescante.'],
            ['nome' => 'Brahma Duplo Malte 350ml', 'marca' => 'Brahma', 'fabricante' => 'Ambev', 'ean' => '7891149108404', 'volume_ml' => 350, 'teor_alcoolico' => 4.7, 'descricao' => 'Cerveja com dois tipos de malte, sabor encorpado.'],
            ['nome' => 'Skol Pilsen 350ml', 'marca' => 'Skol', 'fabricante' => 'Ambev', 'ean' => '7891149100408', 'volume_ml' => 350, 'teor_alcoolico' => 4.7, 'descricao' => 'Cerveja Pilsen leve, redondinha e refrescante.'],
            ['nome' => 'Skol Puro Malte 350ml', 'marca' => 'Skol', 'fabricante' => 'Ambev', 'ean' => '7891149108107', 'volume_ml' => 350, 'teor_alcoolico' => 4.6, 'descricao' => 'Cerveja puro malte com sabor suave e equilibrado.'],
            ['nome' => 'Antarctica Pilsen 350ml', 'marca' => 'Antarctica', 'fabricante' => 'Ambev', 'ean' => '7891149010202', 'volume_ml' => 350, 'teor_alcoolico' => 4.9, 'descricao' => 'Cerveja Pilsen com sabor marcante e refrescante.'],
            ['nome' => 'Antarctica Original 600ml', 'marca' => 'Antarctica', 'fabricante' => 'Ambev', 'ean' => '7891149020508', 'volume_ml' => 600, 'teor_alcoolico' => 4.9, 'descricao' => 'A cerveja Original, tradição brasileira.'],
            ['nome' => 'Bohemia Pilsen 350ml', 'marca' => 'Bohemia', 'fabricante' => 'Ambev', 'ean' => '7891149103003', 'volume_ml' => 350, 'teor_alcoolico' => 4.7, 'descricao' => 'Cerveja Pilsen premium com sabor refinado.'],
            ['nome' => 'Wäls Trippel 600ml', 'marca' => 'Wäls', 'fabricante' => 'Ambev', 'ean' => '7898955700017', 'volume_ml' => 600, 'teor_alcoolico' => 9.2, 'descricao' => 'Belgian Tripel com complexidade aromática.'],
            ['nome' => 'Colorado Appia 600ml', 'marca' => 'Colorado', 'fabricante' => 'Ambev', 'ean' => '7898081740070', 'volume_ml' => 600, 'teor_alcoolico' => 5.5, 'descricao' => 'Witbier com mel de laranjeira, suave e aromática.'],
            ['nome' => 'Colorado Indica 600ml', 'marca' => 'Colorado', 'fabricante' => 'Ambev', 'ean' => '7898081740087', 'volume_ml' => 600, 'teor_alcoolico' => 7.0, 'descricao' => 'IPA com rapadura, amargor equilibrado e notas carameladas.'],
            ['nome' => 'Goose Island IPA 355ml', 'marca' => 'Goose Island', 'fabricante' => 'Ambev', 'ean' => '7891149106004', 'volume_ml' => 355, 'teor_alcoolico' => 5.9, 'descricao' => 'India Pale Ale com lúpulo cítrico e floral.'],
            ['nome' => 'Stella Artois 275ml', 'marca' => 'Stella Artois', 'fabricante' => 'Ambev', 'ean' => '7891149101207', 'volume_ml' => 275, 'teor_alcoolico' => 5.0, 'descricao' => 'Lager premium belga com sabor refinado.'],
            ['nome' => 'Budweiser 350ml', 'marca' => 'Budweiser', 'fabricante' => 'Ambev', 'ean' => '7891149104000', 'volume_ml' => 350, 'teor_alcoolico' => 5.0, 'descricao' => 'Lager americana com sabor suave e refrescante.'],
            ['nome' => 'Spaten Munich Helles 350ml', 'marca' => 'Spaten', 'fabricante' => 'Ambev', 'ean' => '7891149109203', 'volume_ml' => 350, 'teor_alcoolico' => 5.2, 'descricao' => 'Cerveja Munich Helles com tradição alemã.'],
            ['nome' => 'Beck\'s 330ml', 'marca' => 'Beck\'s', 'fabricante' => 'Ambev', 'ean' => '7891149107209', 'volume_ml' => 330, 'teor_alcoolico' => 5.0, 'descricao' => 'Cerveja alemã Pilsner puro malte.'],

            // Heineken Group
            ['nome' => 'Heineken 350ml', 'marca' => 'Heineken', 'fabricante' => 'Heineken', 'ean' => '7896045504503', 'volume_ml' => 350, 'teor_alcoolico' => 5.0, 'descricao' => 'Lager premium holandesa com sabor equilibrado.'],
            ['nome' => 'Heineken Long Neck 330ml', 'marca' => 'Heineken', 'fabricante' => 'Heineken', 'ean' => '7896045504206', 'volume_ml' => 330, 'teor_alcoolico' => 5.0, 'descricao' => 'Lager premium holandesa em garrafa long neck.'],
            ['nome' => 'Amstel Lager 350ml', 'marca' => 'Amstel', 'fabricante' => 'Heineken', 'ean' => '7896045505005', 'volume_ml' => 350, 'teor_alcoolico' => 4.5, 'descricao' => 'Cerveja Lager puro malte com sabor suave.'],
            ['nome' => 'Devassa Tropical Lager 350ml', 'marca' => 'Devassa', 'fabricante' => 'Heineken', 'ean' => '7896045505500', 'volume_ml' => 350, 'teor_alcoolico' => 4.5, 'descricao' => 'Tropical Lager com toque refrescante.'],
            ['nome' => 'Eisenbahn Pilsen 355ml', 'marca' => 'Eisenbahn', 'fabricante' => 'Heineken', 'ean' => '7897395000066', 'volume_ml' => 355, 'teor_alcoolico' => 4.8, 'descricao' => 'Pilsen com caráter maltado e lupulagem aromática.'],
            ['nome' => 'Eisenbahn Strong Golden Ale 355ml', 'marca' => 'Eisenbahn', 'fabricante' => 'Heineken', 'ean' => '7897395000073', 'volume_ml' => 355, 'teor_alcoolico' => 8.5, 'descricao' => 'Belgian Strong Golden Ale complexa e encorpada.'],
            ['nome' => 'Baden Baden Witbier 600ml', 'marca' => 'Baden Baden', 'fabricante' => 'Heineken', 'ean' => '7896045506200', 'volume_ml' => 600, 'teor_alcoolico' => 4.8, 'descricao' => 'Witbier com notas de cítricos e coentro.'],
            ['nome' => 'Lagunitas IPA 355ml', 'marca' => 'Lagunitas', 'fabricante' => 'Heineken', 'ean' => '7896045507003', 'volume_ml' => 355, 'teor_alcoolico' => 6.2, 'descricao' => 'IPA californiana com lupulagem intensa.'],
            ['nome' => 'Tiger 350ml', 'marca' => 'Tiger', 'fabricante' => 'Heineken', 'ean' => '7896045508000', 'volume_ml' => 350, 'teor_alcoolico' => 5.0, 'descricao' => 'Cerveja Lager asiática refrescante.'],

            // Grupo Petrópolis
            ['nome' => 'Itaipava Pilsen 350ml', 'marca' => 'Itaipava', 'fabricante' => 'Grupo Petrópolis', 'ean' => '7897395000110', 'volume_ml' => 350, 'teor_alcoolico' => 4.5, 'descricao' => 'Cerveja Pilsen popular com sabor leve.'],
            ['nome' => 'Itaipava Premium 355ml', 'marca' => 'Itaipava', 'fabricante' => 'Grupo Petrópolis', 'ean' => '7897395000127', 'volume_ml' => 355, 'teor_alcoolico' => 5.0, 'descricao' => 'Cerveja Premium Puro Malte com sabor encorpado.'],
            ['nome' => 'Petra Origem Puro Malte 600ml', 'marca' => 'Petra', 'fabricante' => 'Grupo Petrópolis', 'ean' => '7897395000134', 'volume_ml' => 600, 'teor_alcoolico' => 4.4, 'descricao' => 'Puro Malte com sabor equilibrado e suave.'],
            ['nome' => 'Petra Aurum 500ml', 'marca' => 'Petra', 'fabricante' => 'Grupo Petrópolis', 'ean' => '7897395000141', 'volume_ml' => 500, 'teor_alcoolico' => 4.7, 'descricao' => 'Lager premium com lúpulos nobres selecionados.'],
            ['nome' => 'Black Princess Gold 600ml', 'marca' => 'Black Princess', 'fabricante' => 'Grupo Petrópolis', 'ean' => '7897395000158', 'volume_ml' => 600, 'teor_alcoolico' => 4.7, 'descricao' => 'Premium lager com maltes especiais.'],
            ['nome' => 'Weltenburger Barock Dunkel 500ml', 'marca' => 'Weltenburger', 'fabricante' => 'Grupo Petrópolis', 'ean' => '7897395000165', 'volume_ml' => 500, 'teor_alcoolico' => 4.7, 'descricao' => 'Dunkel bávara da cervejaria mais antiga do mundo.'],

            // Artesanais Brasileiras
            ['nome' => 'Dádiva IPA 473ml', 'marca' => 'Dádiva', 'fabricante' => 'Cervejaria Dádiva', 'ean' => '7898955700100', 'volume_ml' => 473, 'teor_alcoolico' => 6.5, 'descricao' => 'IPA artesanal com lúpulos americanos e tropicais.'],
            ['nome' => 'Dádiva Pilsner 473ml', 'marca' => 'Dádiva', 'fabricante' => 'Cervejaria Dádiva', 'ean' => '7898955700117', 'volume_ml' => 473, 'teor_alcoolico' => 4.8, 'descricao' => 'Pilsner clássica com brilho e secura.'],
            ['nome' => 'Dogma Hop Lover 473ml', 'marca' => 'Dogma', 'fabricante' => 'Cervejaria Dogma', 'ean' => '7898955700200', 'volume_ml' => 473, 'teor_alcoolico' => 6.0, 'descricao' => 'Session IPA aromática para amantes de lúpulo.'],
            ['nome' => 'Dogma Belgian Blond 500ml', 'marca' => 'Dogma', 'fabricante' => 'Cervejaria Dogma', 'ean' => '7898955700217', 'volume_ml' => 500, 'teor_alcoolico' => 6.5, 'descricao' => 'Blonde Ale belga com notas frutadas e condimentadas.'],
            ['nome' => 'Hocus Pocus APA Cadabra 473ml', 'marca' => 'Hocus Pocus', 'fabricante' => 'Hocus Pocus', 'ean' => '7898955700300', 'volume_ml' => 473, 'teor_alcoolico' => 5.5, 'descricao' => 'American Pale Ale com cítricos e pinho.'],
            ['nome' => 'Hocus Pocus Interstellar 473ml', 'marca' => 'Hocus Pocus', 'fabricante' => 'Hocus Pocus', 'ean' => '7898955700317', 'volume_ml' => 473, 'teor_alcoolico' => 7.2, 'descricao' => 'IPA com lúpulos NZ, notas de maracujá e goiaba.'],
            ['nome' => 'Tupiniquim Juicy IPA 473ml', 'marca' => 'Tupiniquim', 'fabricante' => 'Cervejaria Tupiniquim', 'ean' => '7898955700400', 'volume_ml' => 473, 'teor_alcoolico' => 6.3, 'descricao' => 'Hazy IPA com tropical fruits e baixo amargor.'],
            ['nome' => 'Tupiniquim Weiss 500ml', 'marca' => 'Tupiniquim', 'fabricante' => 'Cervejaria Tupiniquim', 'ean' => '7898955700417', 'volume_ml' => 500, 'teor_alcoolico' => 5.0, 'descricao' => 'Hefeweizen tradicional com banana e cravo.'],
            ['nome' => 'Lohn Bier Pilsen 355ml', 'marca' => 'Lohn Bier', 'fabricante' => 'Lohn Bier', 'ean' => '7898955700500', 'volume_ml' => 355, 'teor_alcoolico' => 4.6, 'descricao' => 'Pilsen artesanal catarinense com influência alemã.'],
            ['nome' => 'Lohn Bier IPA 473ml', 'marca' => 'Lohn Bier', 'fabricante' => 'Lohn Bier', 'ean' => '7898955700517', 'volume_ml' => 473, 'teor_alcoolico' => 6.5, 'descricao' => 'India Pale Ale catarinense com lupulagem intensa.'],
            ['nome' => 'Cervejaria Blumenau Pilsen 600ml', 'marca' => 'Blumenau', 'fabricante' => 'Cervejaria Blumenau', 'ean' => '7898955700600', 'volume_ml' => 600, 'teor_alcoolico' => 4.5, 'descricao' => 'Pilsen artesanal com tradição germânica.'],
            ['nome' => 'Blumenau Dunkel 600ml', 'marca' => 'Blumenau', 'fabricante' => 'Cervejaria Blumenau', 'ean' => '7898955700617', 'volume_ml' => 600, 'teor_alcoolico' => 5.2, 'descricao' => 'Dunkel com notas de caramelo e toffee.'],
            ['nome' => 'Way Beer American Lager 350ml', 'marca' => 'Way Beer', 'fabricante' => 'Way Beer', 'ean' => '7898955700700', 'volume_ml' => 350, 'teor_alcoolico' => 4.5, 'descricao' => 'American Lager limpa e refrescante.'],
            ['nome' => 'Way Beer Amburana Lager 600ml', 'marca' => 'Way Beer', 'fabricante' => 'Way Beer', 'ean' => '7898955700717', 'volume_ml' => 600, 'teor_alcoolico' => 5.0, 'descricao' => 'Lager envelhecida em madeira amburana.'],
            ['nome' => 'Três Lobos APA 473ml', 'marca' => 'Três Lobos', 'fabricante' => 'Cervejaria Três Lobos', 'ean' => '7898955700800', 'volume_ml' => 473, 'teor_alcoolico' => 5.5, 'descricao' => 'American Pale Ale com lúpulos cítricos.'],
            ['nome' => 'Bamberg Rauchbier 600ml', 'marca' => 'Bamberg', 'fabricante' => 'Cervejaria Bamberg', 'ean' => '7898955700900', 'volume_ml' => 600, 'teor_alcoolico' => 5.2, 'descricao' => 'Rauchbier defumada estilo Bamberg.'],
            ['nome' => 'Bamberg Pilsen 600ml', 'marca' => 'Bamberg', 'fabricante' => 'Cervejaria Bamberg', 'ean' => '7898955700917', 'volume_ml' => 600, 'teor_alcoolico' => 4.5, 'descricao' => 'Pilsen tcheca com caráter maltado e final seco.'],

            // Importadas populares
            ['nome' => 'Corona Extra 330ml', 'marca' => 'Corona', 'fabricante' => 'Grupo Modelo', 'ean' => '7501064191305', 'volume_ml' => 330, 'teor_alcoolico' => 4.5, 'descricao' => 'Lager mexicana refrescante, ideal com limão.'],
            ['nome' => 'Guinness Draught 440ml', 'marca' => 'Guinness', 'fabricante' => 'Diageo', 'ean' => '5000213000519', 'volume_ml' => 440, 'teor_alcoolico' => 4.2, 'descricao' => 'Stout irlandesa cremosa e encorpada.'],
            ['nome' => 'Hoegaarden Wit 330ml', 'marca' => 'Hoegaarden', 'fabricante' => 'Ambev', 'ean' => '7891149105007', 'volume_ml' => 330, 'teor_alcoolico' => 4.9, 'descricao' => 'Witbier belga com laranja e coentro.'],
            ['nome' => 'Leffe Blonde 330ml', 'marca' => 'Leffe', 'fabricante' => 'Ambev', 'ean' => '7891149106502', 'volume_ml' => 330, 'teor_alcoolico' => 6.6, 'descricao' => 'Blonde Ale abacial com notas frutadas.'],
            ['nome' => 'Erdinger Weissbier 500ml', 'marca' => 'Erdinger', 'fabricante' => 'Erdinger Weißbräu', 'ean' => '4002103248248', 'volume_ml' => 500, 'teor_alcoolico' => 5.3, 'descricao' => 'Weissbier bávara tradicional com banana e cravo.'],
            ['nome' => 'Paulaner Hefe-Weißbier 500ml', 'marca' => 'Paulaner', 'fabricante' => 'Paulaner', 'ean' => '4066600610717', 'volume_ml' => 500, 'teor_alcoolico' => 5.5, 'descricao' => 'Hefeweizen muniquense com caráter frutado.'],
            ['nome' => 'Chimay Red 330ml', 'marca' => 'Chimay', 'fabricante' => 'Abbaye de Scourmont', 'ean' => '5410268000011', 'volume_ml' => 330, 'teor_alcoolico' => 7.0, 'descricao' => 'Dubbel trapista com frutas secas e caramelo.'],
            ['nome' => 'Duvel 330ml', 'marca' => 'Duvel', 'fabricante' => 'Duvel Moortgat', 'ean' => '5414270000051', 'volume_ml' => 330, 'teor_alcoolico' => 8.5, 'descricao' => 'Belgian Strong Golden Ale efervescente e complexa.'],
            ['nome' => 'Delirium Tremens 330ml', 'marca' => 'Delirium', 'fabricante' => 'Brouwerij Huyghe', 'ean' => '5410702000076', 'volume_ml' => 330, 'teor_alcoolico' => 8.5, 'descricao' => 'Belgian Strong Ale premiada mundialmente.'],
            ['nome' => 'La Chouffe Blonde 330ml', 'marca' => 'La Chouffe', 'fabricante' => 'Brasserie d\'Achouffe', 'ean' => '5411053100010', 'volume_ml' => 330, 'teor_alcoolico' => 8.0, 'descricao' => 'Belgian Blonde com especiarias e coentro.'],
        ];
    }
}
