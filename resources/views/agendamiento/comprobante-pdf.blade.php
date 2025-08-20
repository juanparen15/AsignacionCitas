<!-- resources/views/agendamiento/comprobante-pdf.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Cita - {{ $cita->numero_cita }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        
        .numero-cita {
            background: #f3f4f6;
            border: 2px solid #2563eb;
            padding: 15px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        
        .numero-cita h2 {
            margin: 0;
            color: #2563eb;
            font-size: 20px;
        }
        
        .numero-cita .numero {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 5px;
        }
        
        .seccion {
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }
        
        .seccion h3 {
            margin: 0 0 15px 0;
            color: #374151;
            font-size: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #4b5563;
            padding: 8px 20px 8px 0;
            width: 40%;
        }
        
        .info-value {
            display: table-cell;
            color: #1f2937;
            padding: 8px 0;
        }
        
        .estado {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .estado-programada {
            background: #fef3c7;
            color: #92400e;
        }
        
        .estado-confirmada {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .instrucciones {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin-top: 30px;
        }
        
        .instrucciones h3 {
            margin: 0 0 10px 0;
            color: #0c4a6e;
            font-size: 16px;
        }
        
        .instrucciones ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .instrucciones li {
            margin-bottom: 5px;
            color: #0c4a6e;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .qr-info {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
            border-radius: 4px;
        }
        
        .fecha-destacada {
            background: #ecfdf5;
            border: 2px solid #10b981;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        .fecha-destacada .fecha {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
        }
        
        .fecha-destacada .hora {
            font-size: 24px;
            font-weight: bold;
            color: #047857;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COMPROBANTE DE CITA</h1>
        <p>Sistema de Agendamiento de Citas</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="numero-cita">
        <h2>Número de Cita</h2>
        <div class="numero">{{ $cita->numero_cita }}</div>
    </div>

    <div class="seccion">
        <h3>Información del Ciudadano</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre Completo:</div>
                <div class="info-value">{{ $cita->nombres }} {{ $cita->apellidos }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Documento:</div>
                <div class="info-value">{{ $cita::TIPOS_DOCUMENTO[$cita->tipo_documento] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Número de Documento:</div>
                <div class="info-value">{{ $cita->numero_documento }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Correo Electrónico:</div>
                <div class="info-value">{{ $cita->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Teléfono:</div>
                <div class="info-value">{{ $cita->telefono }}</div>
            </div>
            @if($cita->direccion)
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value">{{ $cita->direccion }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="seccion">
        <h3>Información del Trámite</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Secretaría:</div>
                <div class="info-value">{{ $cita->tramite->area->secretaria->nombre }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Área/Dependencia:</div>
                <div class="info-value">{{ $cita->tramite->area->nombre }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Trámite:</div>
                <div class="info-value">{{ $cita->tramite->nombre }}</div>
            </div>
            @if($cita->tramite->descripcion)
            <div class="info-row">
                <div class="info-label">Descripción:</div>
                <div class="info-value">{{ $cita->tramite->descripcion }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Costo:</div>
                <div class="info-value">{{ $cita->tramite->costo_formateado }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Duración Estimada:</div>
                <div class="info-value">{{ $cita->tramite->duracion_minutos }} minutos</div>
            </div>
        </div>
    </div>

    <div class="fecha-destacada">
        <div style="font-size: 14px; color: #065f46; margin-bottom: 5px;">FECHA Y HORA DE LA CITA</div>
        <div class="fecha">{{ $cita->fecha_cita->format('l, d \d\e F \d\e Y') }}</div>
        <div class="hora">{{ $cita->hora_cita->format('H:i') }}</div>
    </div>

    <div class="seccion">
        <h3>Estado de la Cita</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Estado Actual:</div>
                <div class="info-value">
                    <span class="estado estado-{{ $cita->estado }}">
                        {{ $cita->estado_label }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Creación:</div>
                <div class="info-value">{{ $cita->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($cita->observaciones)
            <div class="info-row">
                <div class="info-label">Observaciones:</div>
                <div class="info-value">{{ $cita->observaciones }}</div>
            </div>
            @endif
        </div>
    </div>

    @if($cita->tramite->requisitos)
    <div class="seccion">
        <h3>Requisitos del Trámite</h3>
        <div style="white-space: pre-line;">{{ $cita->tramite->requisitos }}</div>
    </div>
    @endif

    <div class="instrucciones">
        <h3>Instrucciones Importantes</h3>
        <ul>
            <li><strong>Llegue 15 minutos antes</strong> de la hora programada</li>
            <li>Traiga todos los <strong>documentos requeridos</strong> en original y copia</li>
            <li>Presente este comprobante y su documento de identidad</li>
            <li>Si no puede asistir, comuníquese con anticipación para reprogramar</li>
            <li>La cita se considerará <strong>no asistida</strong> después de 15 minutos de retraso</li>
            <li>Guarde este número de cita para futuras consultas: <strong>{{ $cita->numero_cita }}</strong></li>
        </ul>
    </div>

    <div class="qr-info">
        <p><strong>Para consultas sobre su cita:</strong></p>
        <p>Número de Cita: {{ $cita->numero_cita }}</p>
        <p>Documento: {{ $cita->numero_documento }}</p>
    </div>

    <div class="footer">
        <p>Este comprobante es válido únicamente para la fecha y hora especificadas.</p>
        <p>Generado automáticamente por el Sistema de Agendamiento de Citas</p>
        <p>© {{ date('Y') }} - Todos los derechos reservados</p>
    </div>
</body>
</html>