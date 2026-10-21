{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/politica-de-privacidad.blade.php
  QUÉ ES  : Página estática del tratamiento de datos personales (Ley 1581 de 2012).
  RUTA    : GET /politica-de-privacidad (politica)
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - Ninguno.
  BASE DE DATOS: Ninguna.
  QUÉ HAY QUE HACER (en orden):
    1. Redactar el texto legal con quien corresponda (no es asesoría de este equipo).
    2. Verificar que el texto coincida con lo que realmente hace el sistema (qué datos se guardan
       y para qué).
  CÓMO HACERLO / NOTAS:
    - Puede quedarse como Route::view.
  CÓMO SE COMPRUEBA:
    - El enlace del registro y del footer la abren.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Política de privacidad · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/politica-de-privacidad.css') }}">
@endpush
@section('content')
  <main class="min-h-screen pt-28 pb-16 px-margin bg-surface">
    <article class="card max-w-3xl mx-auto p-space-xl">
      <h1 class="font-headline-xl text-headline-xl text-on-surface mb-space-md">Política de privacidad</h1>
{{-- BORRADOR N5: requiere validación del equipo y revisión jurídica antes de publicar. --}}

<div class="mb-space-lg rounded-lg border border-amber-300 bg-amber-50 p-space-md">
    <p class="font-body-md text-body-md text-on-surface">
        <strong>Aviso de revisión:</strong> este documento es un borrador.
        Antes de publicar, el equipo debe completar los datos del responsable
        del tratamiento, confirmar las prácticas reales del sistema y obtener
        revisión jurídica.
    </p>
</div>

<div class="space-y-space-lg font-body-md text-body-md text-on-surface-variant leading-relaxed">
    <p>
        <strong>Última actualización:</strong> [COMPLETAR FECHA].
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        1. Responsable del tratamiento
    </h2>

    <p>
        Cuentas Claras es una herramienta de organización financiera personal
        y familiar. El responsable del tratamiento de los datos personales es
        [NOMBRE O RAZÓN SOCIAL], identificado con [NIT O IDENTIFICACIÓN, SI
        CORRESPONDE], con domicilio en [CIUDAD, COLOMBIA].
    </p>

    <p>
        Para consultas sobre el tratamiento de datos personales, puedes
        comunicarte a través de [CORREO DE CONTACTO] o [OTRO CANAL OFICIAL].
        Estos datos deben completarse antes de publicar esta política.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        2. Datos personales que podemos tratar
    </h2>

    <p>
        De acuerdo con las funciones que utilices y la información que
        proporciones, el sistema puede tratar datos como:
    </p>

   <ul class="list-disc pl-6 font-body-md text-body-md text-on-surface-variant leading-relaxed" style="list-style-type: disc; padding-left: 1.5rem;">
        <li>Datos de identificación, como nombre y número de cédula, cuando sean solicitados.</li>
        <li>Datos de contacto, como correo electrónico.</li>
        <li>Información financiera ingresada en la plataforma, como ingresos, gastos, presupuestos, obligaciones y metas de ahorro.</li>
        <li>Datos asociados a tu cuenta y a su funcionamiento, cuando sean necesarios para prestar el servicio.</li>
    </ul>

    <p>
        El equipo debe verificar esta lista frente a los formularios, la base
        de datos y los registros técnicos del sistema. No debe interpretarse
        como una afirmación de que todos estos datos se recogen en todos los
        casos.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        3. Finalidades del tratamiento
    </h2>

    <p>Los datos podrán utilizarse, según las funciones habilitadas, para:</p>

    <ul class="list-disc pl-6 font-body-md text-body-md text-on-surface-variant leading-relaxed" style="list-style-type: disc; padding-left: 1.5rem;">
        <li>Crear, administrar y proteger tu cuenta.</li>
        <li>Permitir el registro y la consulta de ingresos, gastos y presupuestos.</li>
        <li>Gestionar metas de ahorro personales o compartidas.</li>
        <li>Generar recordatorios relacionados con vencimientos, cuando esta función esté disponible.</li>
        <li>Ofrecer herramientas de simulación y organización financiera.</li>
        <li>Atender solicitudes, consultas y reclamaciones.</li>
        <li>Cumplir obligaciones legales y gestionar la seguridad del servicio.</li>
    </ul>

    <p>
        Los usos adicionales deberán informarse y contar con la autorización
        o la base jurídica que corresponda, según la normativa aplicable.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        4. Autorización y tratamiento
    </h2>

    <p>
        Cuando la normativa lo exija, solicitaremos la autorización previa,
        expresa e informada para el tratamiento de tus datos personales.
        Podrás consultar las finalidades del tratamiento y ejercer los
        derechos reconocidos por la legislación colombiana.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        5. Compartición y protección de la información
    </h2>

    <p>
        Cuentas Claras no debe utilizar tus datos para finalidades incompatibles
        con las informadas en esta política. El equipo debe verificar qué
        proveedores tecnológicos, encargados del tratamiento u otros
        destinatarios pueden acceder a información personal y documentar
        las condiciones aplicables.
    </p>

    <p>
        Se adoptarán medidas técnicas, humanas y administrativas razonables
        para proteger la información contra pérdida, acceso no autorizado,
        alteración o uso indebido. No se debe afirmar que existe un tipo
        específico de cifrado ni garantizar seguridad absoluta sin evidencia
        técnica que lo respalde.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        6. Derechos de los titulares
    </h2>

    <p>
        Conforme a la Ley 1581 de 2012 y las normas que la reglamentan,
        puedes ejercer, entre otros, los siguientes derechos:
    </p>

   <ul class="list-disc pl-6 font-body-md text-body-md text-on-surface-variant leading-relaxed" style="list-style-type: disc; padding-left: 1.5rem;">
        <li>Conocer los datos personales que se están tratando.</li>
        <li>Solicitar la actualización o rectificación de información incompleta, inexacta o desactualizada.</li>
        <li>Solicitar prueba de la autorización otorgada, cuando corresponda.</li>
        <li>Consultar el uso que se ha dado a tus datos.</li>
        <li>Presentar consultas o reclamaciones relacionadas con el tratamiento.</li>
        <li>Solicitar la supresión de datos o revocar la autorización cuando proceda legalmente, teniendo en cuenta las excepciones y obligaciones aplicables.</li>
        <li>Presentar quejas ante la Superintendencia de Industria y Comercio, una vez agotado el trámite de consulta o reclamación que corresponda.</li>
    </ul>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        7. Cómo presentar una consulta o reclamación
    </h2>

    <p>
        Envía tu solicitud a [CORREO O CANAL OFICIAL], indicando tu nombre,
        un medio de contacto, el derecho que deseas ejercer y la información
        necesaria para identificar tu solicitud. Si se requiere acreditar
        identidad o representación, se te indicará cómo hacerlo de forma
        adecuada.
    </p>

    <p>
        Las consultas y reclamaciones serán atendidas conforme a los
        procedimientos y plazos previstos en la legislación colombiana.
        El responsable debe definir y publicar el procedimiento aplicable
        antes de poner esta política en producción.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        8. Eliminación de la cuenta y de los datos
    </h2>

    <p>
        Puedes consultar las opciones de eliminación de cuenta disponibles
        en la plataforma o solicitar información mediante el canal oficial
        de contacto. La eliminación de una cuenta no implica necesariamente
        la supresión inmediata de todos los registros: pueden existir
        obligaciones legales, motivos legítimos de conservación u otros
        requisitos que deban evaluarse en cada caso.
    </p>

    <p>
        Antes de publicar, el equipo debe comprobar qué datos elimina el
        sistema al cerrar una cuenta, qué registros conserva y durante cuánto
        tiempo, y describir el procedimiento real sin ofrecer garantías que
        la implementación no cumpla.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        9. Conservación de los datos
    </h2>

    <p>
        Los datos se conservarán durante el tiempo necesario para cumplir
        las finalidades informadas y las obligaciones legales aplicables.
        El responsable debe definir los criterios o periodos de conservación
        de acuerdo con los tipos de datos y las necesidades reales del servicio.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        10. Cambios a esta política
    </h2>

    <p>
        Esta política podrá actualizarse cuando cambien el servicio, las
        prácticas de tratamiento o los requisitos legales. La versión vigente
        estará disponible en esta página. Cuando sea necesario, se informarán
        los cambios por los medios apropiados.
    </p>

</div>
    </article>
  </main>
@endsection
