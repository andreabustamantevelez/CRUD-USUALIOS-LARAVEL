<?php

/**
 * Configuración legal del sitio.
 * Todos los datos de la empresa y las versiones de los documentos viven aquí
 * para que un solo cambio se refleje en política y términos.
 *
 * En producción, define las variables en .env (no en este archivo).
 */
return [
    'app_name' => env('LEGAL_APP_NAME', env('APP_NAME', 'Cuentas Claras')),

    // Empresa responsable del tratamiento
    'company_name'     => env('LEGAL_COMPANY_NAME', 'Cuentas Claras S.A.S.'),
    'company_nit'      => env('LEGAL_COMPANY_NIT', '900.000.000-0'),
    'company_domicile' => env('LEGAL_COMPANY_DOMICILE', 'Cali, Valle del Cauca, Colombia'),
    'company_address'  => env('LEGAL_COMPANY_ADDRESS', ''),

    // Canales de contacto
    'contact_email'    => env('LEGAL_CONTACT_EMAIL', 'contacto@cuentasclaras.app'),
    'dpo_name'         => env('LEGAL_DPO_NAME', ''),
    'dpo_email'        => env('LEGAL_DPO_EMAIL', 'datos@cuentasclaras.app'),

    // Versiones de los documentos
    'policy_version'   => env('LEGAL_POLICY_VERSION', '1.0.0'),
    'policy_date'      => env('LEGAL_POLICY_DATE', '2026-10-22'),
    'terms_version'    => env('LEGAL_TERMS_VERSION', '1.0.0'),
    'terms_date'       => env('LEGAL_TERMS_DATE', '2026-10-22'),

    // Reglas de negocio
    'min_age'                     => (int) env('LEGAL_MIN_AGE', 18),
    'retention_days_active'       => (int) env('LEGAL_RETENTION_ACTIVE', 365),
    'retention_days_closed'       => (int) env('LEGAL_RETENTION_CLOSED', 1095),
    'retention_days_audit_log'    => (int) env('LEGAL_RETENTION_AUDIT', 1825),
    'incident_notification_hours' => (int) env('LEGAL_INCIDENT_HOURS', 72),
];