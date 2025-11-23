<?php
/**
 * Front Controller Forwarder
 * 
 * Este archivo permite que el sitio cargue correctamente en hostings compartidos
 * donde la configuración de carpetas públicas puede ser restrictiva.
 * Simplemente carga el index real que está en la carpeta public.
 */

require_once __DIR__ . '/public/index.php';
