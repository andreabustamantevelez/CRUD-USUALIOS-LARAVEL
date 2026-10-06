<?php

namespace App\Policies;

/**
 * Quién puede ver y marcar como leída una notificación: solo su dueño.
 * Las notificaciones las crea ServicioNotificaciones (J6); nadie las crea a mano desde un formulario.
 */
class NotificacionPolicy extends PoliticaDeDueno
{
}
