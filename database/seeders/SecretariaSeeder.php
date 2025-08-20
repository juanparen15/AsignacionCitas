<?php
// database/seeders/SecretariaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Secretaria;
use App\Models\Area;
use App\Models\Tramite;

class SecretariaSeeder extends Seeder
{
    public function run()
    {
        // Secretaría de Hacienda
        $secretariaHacienda = Secretaria::create([
            'nombre' => 'Secretaría de Hacienda',
            'descripcion' => 'Administración financiera y tributaria',
            'activa' => true,
            'orden' => 2,
        ]);

        $areaTributos = Area::create([
            'secretaria_id' => $secretariaHacienda->id,
            'nombre' => 'Rentas y Tributos',
            'descripcion' => 'Gestión de impuestos municipales',
            'activa' => true,
            'orden' => 1,
        ]);

        Tramite::create([
            'secretaria_id' => $secretariaHacienda->id,
            'area_id' => $areaTributos->id,
            'nombre' => 'Liquidación de Impuesto Predial',
            'descripcion' => 'Solicitud de liquidación del impuesto predial unificado',
            'requisitos' => "- Cédula de ciudadanía\n- Escrituras del predio\n- Último recibo de pago",
            'costo' => 15000,
            'es_gratuito' => false,
            'duracion_minutos' => 20,
            'activo' => true,
            'orden' => 1,
        ]);

        Tramite::create([
            'secretaria_id' => $secretariaHacienda->id,
            'area_id' => $areaTributos->id,
            'nombre' => 'Certificado de Paz y Salvo Tributario',
            'descripcion' => 'Certificación de estar al día con las obligaciones tributarias',
            'requisitos' => "- Cédula de ciudadanía\n- Número de matrícula inmobiliaria o catastral",
            'costo' => 25000,
            'es_gratuito' => false,
            'duracion_minutos' => 15,
            'activo' => true,
            'orden' => 2,
        ]);

        $areaTesoreria = Area::create([
            'secretaria_id' => $secretariaHacienda->id,
            'nombre' => 'Tesorería',
            'descripcion' => 'Pagos y recaudos municipales',
            'activa' => true,
            'orden' => 2,
        ]);

        Tramite::create([
            'secretaria_id' => $secretariaHacienda->id,
            'area_id' => $areaTesoreria->id,
            'nombre' => 'Solicitud de Devolución de Pagos',
            'descripcion' => 'Trámite para devolución de pagos en exceso',
            'requisitos' => "- Cédula de ciudadanía\n- Recibos de pago originales\n- Cuenta bancaria para consignación",
            'costo' => 0,
            'es_gratuito' => true,
            'duracion_minutos' => 30,
            'activo' => true,
            'orden' => 1,
        ]);

        // Secretaría de Planeación
        $secretariaPlaneacion = Secretaria::create([
            'nombre' => 'Secretaría de Planeación',
            'descripcion' => 'Desarrollo urbano y territorial',
            'activa' => true,
            'orden' => 3,
        ]);

        $areaUrbana = Area::create([
            'secretaria_id' => $secretariaPlaneacion->id,
            'nombre' => 'Planeación Urbana',
            'descripcion' => 'Licencias y permisos urbanísticos',
            'activa' => true,
            'orden' => 1,
        ]);

        Tramite::create([
            'secretaria_id' => $secretariaPlaneacion->id,
            'area_id' => $areaUrbana->id,
            'nombre' => 'Licencia de Construcción',
            'descripcion' => 'Permiso para construcción de obras civiles',
            'requisitos' => "- Cédula de ciudadanía\n- Escrituras del predio\n- Planos arquitectónicos\n- Estudio de suelos\n- Paz y salvo tributario",
            'costo' => 150000,
            'es_gratuito' => false,
            'duracion_minutos' => 60,
            'activo' => true,
            'orden' => 1,
        ]);

        Tramite::create([
            'secretaria_id' => $secretariaPlaneacion->id,
            'area_id' => $areaUrbana->id,
            'nombre' => 'Certificado de Estratificación',
            'descripcion' => 'Certificación del estrato socioeconómico del predio',
            'requisitos' => "- Cédula de ciudadanía\n- Dirección exacta del predio",
            'costo' => 12000,
            'es_gratuito' => false,
            'duracion_minutos' => 10,
            'activo' => true,
            'orden' => 2,
        ]);

        // Secretaría de Salud
        $secretariaSalud = Secretaria::create([
            'nombre' => 'Secretaría de Salud',
            'descripcion' => 'Salud pública y saneamiento',
            'activa' => true,
            'orden' => 4,
        ]);

        $areaSaneamiento = Area::create([
            'secretaria_id' => $secretariaSalud->id,
            'nombre' => 'Saneamiento Ambiental',
            'descripcion' => 'Control sanitario y ambiental',
            'activa' => true,
            'orden' => 1,
        ]);

        Tramite::create([
            'secretaria_id' => $secretariaSalud->id,
            'area_id' => $areaSaneamiento->id,
            'nombre' => 'Concepto Sanitario',
            'descripcion' => 'Evaluación de condiciones sanitarias de establecimientos',
            'requisitos' => "- Cédula de ciudadanía\n- RUT del establecimiento\n- Planos del local\n- Certificado de fumigación",
            'costo' => 80000,
            'es_gratuito' => false,
            'duracion_minutos' => 45,
            'activo' => true,
            'orden' => 1,
        ]);

        $areaVigilancia = Area::create([
            'secretaria_id' => $secretariaSalud->id,
            'nombre' => 'Vigilancia en Salud Pública',
            'descripcion' => 'Control y vigilancia epidemiológica',
            'activa' => true,
            'orden' => 2,
        ]);

        Tramite::create([
            'secretaria_id' => $secretariaSalud->id,
            'area_id' => $areaVigilancia->id,
            'nombre' => 'Certificado de Defunción',
            'descripcion' => 'Expedición de certificados médicos de defunción',
            'requisitos' => "- Cédula del solicitante\n- Documento de identidad del fallecido\n- Registro civil de defunción",
            'costo' => 35000,
            'es_gratuito' => false,
            'duracion_minutos' => 20,
            'activo' => true,
            'orden' => 1,
        ]);
    }
}

// database/seeders/DatabaseSeeder.php
// <?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     public function run()
//     {
//         $this->call([
//             SecretariaSeeder::class,
//         ]);
//     }
// }