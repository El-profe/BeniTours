<?php
require_once __DIR__ . '/../app/bootstrap_env.php';
require_once __DIR__ . '/../app/Core/Autoloader.php';
\App\Core\Autoloader::register();

$db = \App\Core\Database::getConnection();

// 1. Insertar o actualizar las 3 nuevas categorías
$categoriasNuevas = [
    [
        'nombre' => 'Hospitales y Salud',
        'slug' => 'hospitales-salud',
        'descripcion' => 'Hospitales, postas de salud y centros de atención médica de urgencias en Trinidad.',
        'icono' => 'fa-solid fa-hospital',
        'tipo_defecto' => 'PUBLICO'
    ],
    [
        'nombre' => 'Policía y Seguridad',
        'slug' => 'policia-seguridad',
        'descripcion' => 'Comandos policiales, módulos EPI y unidades de auxilio ciudadano y emergencias.',
        'icono' => 'fa-solid fa-shield-halved',
        'tipo_defecto' => 'PUBLICO'
    ],
    [
        'nombre' => 'Bancos y Cajeros (ATM)',
        'slug' => 'bancos-cajeros',
        'descripcion' => 'Cajeros automáticos 24 horas y entidades financieras de la red bancaria en Trinidad.',
        'icono' => 'fa-solid fa-money-bill-wave',
        'tipo_defecto' => 'PUBLICO'
    ]
];

foreach ($categoriasNuevas as $cat) {
    $stmtCheck = $db->prepare("SELECT id_categoria FROM categorias WHERE slug = ?");
    $stmtCheck->execute([$cat['slug']]);
    $catExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($catExistente) {
        $stmtUpd = $db->prepare("UPDATE categorias SET nombre = ?, descripcion = ?, icono = ?, tipo_defecto = ?, activo = 1 WHERE id_categoria = ?");
        $stmtUpd->execute([$cat['nombre'], $cat['descripcion'], $cat['icono'], $cat['tipo_defecto'], $catExistente['id_categoria']]);
        echo "Categoría actualizada: {$cat['nombre']} (ID {$catExistente['id_categoria']})\n";
    } else {
        $stmtIns = $db->prepare("INSERT INTO categorias (nombre, slug, descripcion, icono, tipo_defecto, activo) VALUES (?, ?, ?, ?, ?, 1)");
        $stmtIns->execute([$cat['nombre'], $cat['slug'], $cat['descripcion'], $cat['icono'], $cat['tipo_defecto']]);
        echo "Categoría creada: {$cat['nombre']} (ID {$db->lastInsertId()})\n";
    }
}

// 2. Obtener IDs de las categorías para asociar los lugares
function getCatId($db, $slug) {
    $stmt = $db->prepare("SELECT id_categoria FROM categorias WHERE slug = ?");
    $stmt->execute([$slug]);
    return (int)$stmt->fetchColumn();
}

$idCatSalud = getCatId($db, 'hospitales-salud');
$idCatPolicia = getCatId($db, 'policia-seguridad');
$idCatCajeros = getCatId($db, 'bancos-cajeros');

// Trinidad Municipio ID: generalmente 1
$idMunTrinidad = 1;
$stmtMun = $db->query("SELECT id_municipio FROM municipios WHERE nombre LIKE '%Trinidad%' LIMIT 1");
if ($mRow = $stmtMun->fetch(PDO::FETCH_ASSOC)) {
    $idMunTrinidad = (int)$mRow['id_municipio'];
}

echo "Municipio Trinidad ID: $idMunTrinidad\n";
echo "IDs Categorías -> Salud: $idCatSalud, Policía: $idCatPolicia, Cajeros: $idCatCajeros\n";

// 3. Lugares iniciales emblemáticos de Trinidad para cada categoría
$lugaresNuevos = [
    // --- HOSPITALES Y SALUD ---
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatSalud,
        'nombre' => 'Hospital Presidente Germán Busch',
        'slug' => 'hospital-presidente-german-busch',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Principal centro hospitalario de tercer nivel de la ciudad de Trinidad y del departamento del Beni, con servicio de emergencias médicas las 24 horas, terapia intensiva, traumatología, cirugía general y farmacia institucional.',
        'direccion' => 'Av. Japón esq. Calle Cipriano Barace',
        'referencia_ubicacion' => 'Zona Central / Villa Busch, a pocas cuadras de la plaza principal',
        'latitud' => -14.83150000,
        'longitud' => -64.89850000,
        'telefono_contacto' => '34621122',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Atención de Emergencias 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatSalud,
        'nombre' => 'Hospital Materno Infantil de Trinidad',
        'slug' => 'hospital-materno-infantil-trinidad',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Centro especializado de referencia departamental en ginecología, obstetricia, pediatría y neonatología. Atención continua a urgencias maternas e infantiles con personal de guardia 24/7.',
        'direccion' => 'Calle Félix Pinto Saucedo',
        'referencia_ubicacion' => 'Zona San Vicente, próximo al Parque Pantanal',
        'latitud' => -14.83980000,
        'longitud' => -64.90820000,
        'telefono_contacto' => '34621414',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Emergencias Pediátricas y Maternidad 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatSalud,
        'nombre' => 'Hospital Obrero N° 8 - Caja Nacional de Salud (CNS)',
        'slug' => 'hospital-obrero-8-cns-trinidad',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Hospital de especialidades de la Caja Nacional de Salud con servicio integral de emergencias, laboratorio clínico, rayos X y farmacia para asegurados y atención de urgencias vitales.',
        'direccion' => 'Av. Bolívar, entre Calles Gil Coimbra y Cochabamba',
        'referencia_ubicacion' => 'Zona Central Trinidad',
        'latitud' => -14.83400000,
        'longitud' => -64.90120000,
        'telefono_contacto' => '34620220',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Emergencias 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],

    // --- POLICÍA Y SEGURIDAD ---
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatPolicia,
        'nombre' => 'Comando Departamental de la Policía del Beni',
        'slug' => 'comando-departamental-policia-beni',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Sede central de la Policía Boliviana en el departamento del Beni. Central telefónica de Radio Patrullas 110, atención a denuncias ciudadanas, auxilio inmediato y seguridad en eventos públicos.',
        'direccion' => 'Calle La Paz esq. Calle Pedro de la Rocha',
        'referencia_ubicacion' => 'A una cuadra de la Plaza Principal Mariscal Ballivián',
        'latitud' => -14.83510000,
        'longitud' => -64.90480000,
        'telefono_contacto' => '110',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Guardia Policial y Radio Patrullas 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatPolicia,
        'nombre' => 'Fuerza Especial de Lucha Contra el Crimen (FELCC)',
        'slug' => 'felcc-trinidad-beni',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Organismo especializado de investigación de delitos y protección a víctimas. Unidad operativa para denuncias, extravío de documentos y auxilio policial continuo.',
        'direccion' => 'Av. del Mar esq. Calle Antonio Vaca Díez',
        'referencia_ubicacion' => 'Zona Pompeya',
        'latitud' => -14.82800000,
        'longitud' => -64.90700000,
        'telefono_contacto' => '34624444',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Atención de Denuncias 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatPolicia,
        'nombre' => 'EPI Pompeya (Estación Policial Integral)',
        'slug' => 'epi-pompeya-trinidad',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Estación Policial Integral del sector norte de Trinidad. Brinda patrullaje preventivo, auxilio rápido a vecinos y visitantes, y recepción de contingencias de tránsito y seguridad.',
        'direccion' => 'Av. Panamericana frente al Mercado Pompeya',
        'referencia_ubicacion' => 'Distrito Pompeya',
        'latitud' => -14.82100000,
        'longitud' => -64.90650000,
        'telefono_contacto' => '110',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Atención y Patrullaje 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],

    // --- BANCOS Y CAJEROS AUTOMÁTICOS (ATMs) ---
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatCajeros,
        'nombre' => 'Cajero Automático Banco Unión · Plaza Principal',
        'slug' => 'cajero-banco-union-plaza-principal',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Cajero automático Red Enlace de Banco Unión disponible las 24 horas. Retiros en bolivianos y dólares, transferencias y cobro de bonos. Ubicación céntrica con iluminación y vigilancia.',
        'direccion' => 'Calle Mariscal Sucre esq. Calle La Paz',
        'referencia_ubicacion' => 'Frente a la Plaza Principal Mariscal Ballivián',
        'latitud' => -14.83620000,
        'longitud' => -64.90420000,
        'telefono_contacto' => '800102872',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Cajero Automático 24 Horas / Red Enlace',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatCajeros,
        'nombre' => 'Cajero Automático BCP · Calle La Paz',
        'slug' => 'cajero-bcp-calle-la-paz',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Cajero automático del Banco de Crédito de Bolivia (BCP). Retiro con tarjetas Visa y Mastercard internacionales, consultas y depósitos rápidos.',
        'direccion' => 'Calle La Paz N° 145, entre Suárez y Bolivar',
        'referencia_ubicacion' => 'A media cuadra de la Plaza Central',
        'latitud' => -14.83550000,
        'longitud' => -64.90350000,
        'telefono_contacto' => '34620000',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Cajero Automático 24 Horas / Red Link-Cirrus',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatCajeros,
        'nombre' => 'Cajero Automático BNB (Banco Nacional de Bolivia)',
        'slug' => 'cajero-bnb-trinidad-centro',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Cajero multifuncional BNB en el centro financiero de Trinidad. Acepta tarjetas nacionales y del extranjero (Red Enlace, Visa, Plus, Mastercard).',
        'direccion' => 'Calle Suárez N° 230',
        'referencia_ubicacion' => 'Casco histórico de Trinidad',
        'latitud' => -14.83480000,
        'longitud' => -64.90300000,
        'telefono_contacto' => '800107272',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Cajero Automático 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ],
    [
        'id_municipio' => $idMunTrinidad,
        'id_categoria' => $idCatCajeros,
        'nombre' => 'Cajero Automático Banco Ganadero · Av. 6 de Agosto',
        'slug' => 'cajero-banco-ganadero-6-agosto',
        'tipo_lugar' => 'PUBLICO',
        'descripcion' => 'Cajero automático Red Enlace Banco Ganadero sobre la principal avenida comercial y gastronómica de Trinidad. Espacio seguro y con acceso a estacionamiento de motocicletas.',
        'direccion' => 'Av. 6 de Agosto esq. Calle Cochabamba',
        'referencia_ubicacion' => 'Frente al sector comercial de la Av. 6 de Agosto',
        'latitud' => -14.83220000,
        'longitud' => -64.90150000,
        'telefono_contacto' => '800103999',
        'whatsapp_contacto' => '',
        'horario_atencion' => 'Cajero Automático 24 Horas',
        'turnos_comida' => '',
        'estado_operativo' => 'ABIERTO_HABITUAL'
    ]
];

foreach ($lugaresNuevos as $lug) {
    $stmtCheckL = $db->prepare("SELECT id_lugar FROM lugares WHERE slug = ?");
    $stmtCheckL->execute([$lug['slug']]);
    $lExist = $stmtCheckL->fetch(PDO::FETCH_ASSOC);

    if ($lExist) {
        $stmtUpdL = $db->prepare("UPDATE lugares SET 
            id_municipio = ?, id_categoria = ?, nombre = ?, tipo_lugar = ?, descripcion = ?, 
            direccion = ?, referencia_ubicacion = ?, latitud = ?, longitud = ?, telefono_contacto = ?, 
            whatsapp_contacto = ?, horario_atencion = ?, estado_operativo = ? 
            WHERE id_lugar = ?");
        $stmtUpdL->execute([
            $lug['id_municipio'], $lug['id_categoria'], $lug['nombre'], $lug['tipo_lugar'], $lug['descripcion'],
            $lug['direccion'], $lug['referencia_ubicacion'], $lug['latitud'], $lug['longitud'], $lug['telefono_contacto'],
            $lug['whatsapp_contacto'], $lug['horario_atencion'], $lug['estado_operativo'], $lExist['id_lugar']
        ]);
        echo "Lugar actualizado: {$lug['nombre']} (ID {$lExist['id_lugar']})\n";
    } else {
        $stmtInsL = $db->prepare("INSERT INTO lugares (
            id_municipio, id_categoria, nombre, slug, tipo_lugar, descripcion, 
            direccion, referencia_ubicacion, latitud, longitud, telefono_contacto, 
            whatsapp_contacto, horario_atencion, estado_operativo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsL->execute([
            $lug['id_municipio'], $lug['id_categoria'], $lug['nombre'], $lug['slug'], $lug['tipo_lugar'], $lug['descripcion'],
            $lug['direccion'], $lug['referencia_ubicacion'], $lug['latitud'], $lug['longitud'], $lug['telefono_contacto'],
            $lug['whatsapp_contacto'], $lug['horario_atencion'], $lug['estado_operativo']
        ]);
        echo "Lugar creado: {$lug['nombre']} (ID {$db->lastInsertId()})\n";
    }
}

echo "Migración completada con éxito.\n";
