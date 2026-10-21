{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/terminos-y-condiciones.blade.php
  QUÉ ES  : Página estática de términos y condiciones de uso.
  RUTA    : GET /terminos (terminos)
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - Ninguno.
  BASE DE DATOS: Ninguna.
  QUÉ HAY QUE HACER (en orden):
    1. Redactar el texto legal.
    2. Enlazarla desde el registro si se exige aceptarla.
  CÓMO HACERLO / NOTAS:
    - Puede quedarse como Route::view.
  CÓMO SE COMPRUEBA:
    - El enlace del footer la abre.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Términos y condiciones · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/terminos-y-condiciones.css') }}">
@endpush
@section('content')
  <main class="min-h-screen pt-28 pb-16 px-margin bg-surface">
    <article class="card max-w-3xl mx-auto p-space-xl">
      <h1 class="font-headline-xl text-headline-xl text-on-surface mb-space-md">Términos y condiciones</h1>
     {{-- BORRADOR N5: requiere validación del equipo y revisión jurídica antes de publicar. --}}

<div class="mb-space-lg rounded-lg border border-amber-300 bg-amber-50 p-space-md">
    <p class="font-body-md text-body-md text-on-surface">
        <strong>Aviso de revisión:</strong> este documento es un borrador.
        Antes de publicar, el equipo debe verificar que sus condiciones
        coincidan con las funciones reales de Cuentas Claras y obtener
        revisión jurídica.
    </p>
</div>

<div class="space-y-space-lg font-body-md text-body-md text-on-surface-variant leading-relaxed">

    <p>
        <strong>Última actualización:</strong> [COMPLETAR FECHA].
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        1. Aceptación de los términos
    </h2>

    <p>
        Estos términos regulan el acceso y uso de Cuentas Claras, una
        herramienta de organización financiera personal y familiar.
        Al utilizar la plataforma, aceptas cumplir las condiciones
        aquí descritas y la normativa aplicable. Si no estás de acuerdo,
        debes abstenerte de utilizar las funciones correspondientes.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        2. Descripción del servicio
    </h2>

    <p>
        Cuentas Claras está diseñada para ayudar a organizar información
        financiera, como ingresos, gastos, presupuestos y metas de ahorro,
        de acuerdo con las funciones disponibles en la plataforma.
    </p>

    <p>
        Las herramientas y funcionalidades pueden cambiar, mejorar,
        limitarse o dejar de estar disponibles. El equipo deberá verificar
        que esta descripción coincida con las funciones implementadas.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        3. Registro y seguridad de la cuenta
    </h2>

    <p>
        Cuando el servicio requiera una cuenta, debes proporcionar
        información veraz, mantenerla actualizada y proteger tus
        credenciales de acceso. También debes informar al responsable
        del servicio si detectas un uso no autorizado de tu cuenta.
    </p>

    <p>
        El equipo debe confirmar los procedimientos reales para recuperar
        el acceso, actualizar los datos y cerrar una cuenta antes de
        publicar condiciones específicas sobre estos procesos.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        4. Uso permitido
    </h2>

    <p>Al utilizar Cuentas Claras, te comprometes a:</p>

    <ul class="list-disc pl-6 font-body-md text-body-md text-on-surface-variant leading-relaxed" style="list-style-type: disc; padding-left: 1.5rem;">
        <li>Utilizar la plataforma de forma lícita y de buena fe.</li>
        <li>Ingresar información que estés autorizado a proporcionar.</li>
        <li>No intentar acceder sin autorización a cuentas, datos o sistemas de otras personas.</li>
        <li>No interferir con el funcionamiento, la disponibilidad o la seguridad del servicio.</li>
        <li>No utilizar la plataforma para actividades fraudulentas o ilícitas.</li>
        <li>No copiar, alterar o explotar indebidamente los componentes del servicio, salvo cuando la ley lo permita.</li>
    </ul>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        5. Alcance informativo y responsabilidad financiera
    </h2>

    <p>
        Cuentas Claras es una herramienta de organización y seguimiento
        financiero. Salvo que se indique expresamente lo contrario,
        la información y los cálculos que ofrece no constituyen asesoría
        financiera, tributaria, contable ni jurídica personalizada.
    </p>

    <p>
        Las decisiones financieras son responsabilidad de cada usuario.
        Debes revisar los datos que ingresas y comprobar los resultados
        antes de utilizarlos para tomar decisiones. El equipo debe verificar
        las funciones de cálculo y los límites reales del servicio para
        evitar promesas de exactitud que no pueda garantizar.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        6. Información personal y privacidad
    </h2>

    <p>
        El tratamiento de los datos personales se describe en nuestra
        <a href="{{ route('politica') }}" class="underline">
            Política de privacidad
        </a>,
        que explica las finalidades del tratamiento y los derechos de los
        titulares conforme a la normativa aplicable.
    </p>

    <p>
        Antes de publicar, el equipo debe verificar si el registro requiere
        una aceptación expresa de estos términos y de la política de
        privacidad, y cómo se registra dicha aceptación.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        7. Disponibilidad y cambios del servicio
    </h2>

    <p>
        Se procurará mantener la plataforma disponible y en funcionamiento,
        pero no se garantiza que el acceso sea ininterrumpido o esté libre
        de errores. Podrán realizarse tareas de mantenimiento, actualizaciones
        o cambios necesarios para el funcionamiento del servicio.
    </p>

    <p>
        Los cambios relevantes de estos términos se publicarán en esta
        página, indicando la fecha de actualización. Cuando la normativa
        lo exija, se solicitará la aceptación correspondiente.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        8. Propiedad intelectual
    </h2>

    <p>
        Los elementos de la plataforma, incluidos su diseño, marca,
        textos y componentes propios, estarán sujetos a los derechos
        de propiedad intelectual que correspondan. No se concede una
        autorización para utilizarlos fuera de lo permitido por la ley
        o por el titular de los derechos.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        9. Limitaciones de responsabilidad
    </h2>

    <p>
        En la medida permitida por la legislación aplicable, cada usuario
        es responsable de la información que introduce y de las decisiones
        que toma a partir de ella. Nada en estos términos excluye o limita
        derechos o responsabilidades que legalmente no puedan excluirse
        ni limitarse.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        10. Cierre o suspensión de cuentas
    </h2>

    <p>
        El acceso podrá suspenderse o limitarse cuando existan motivos
        legítimos, como riesgos de seguridad o un uso contrario a estos
        términos, respetando la legislación aplicable.
    </p>

    <p>
        Las opciones para cerrar una cuenta y el tratamiento posterior
        de los datos se regirán por los procedimientos disponibles y por
        la Política de privacidad. El equipo debe confirmar que estas
        opciones existen y funcionan como se describen.
    </p>

    <h2 class="font-headline-md text-headline-md text-on-surface">
        11. Legislación aplicable y contacto
    </h2>

    <p>
        Estos términos se interpretarán conforme a la legislación
        colombiana, sin perjuicio de las normas imperativas que resulten
        aplicables.
    </p>

    <p>
        Para consultas o reclamaciones, puedes comunicarte a través de
        [CORREO O CANAL OFICIAL]. Este dato debe completarse antes de
        publicar el documento.
    </p>

</div>
    </article>
  </main>
@endsection
