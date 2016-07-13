<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'local_smart_klass', language 'es'
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'SmartKlass';

$string['smartklass_manage'] = 'Gestionar SmartKlass';
$string['controlpanel'] = 'Panel de Control';
$string['fullharvester'] = 'Recolección completa de datos';
$string['activate'] = 'Activar';
$string['activatedescription'] = 'Activa el trazado de datos de Moodle al servidor SmartKlass';
$string['save_log'] = 'Almacenar registro';
$string['savelogdescription'] = 'Almacena registro de todas las ejecuciones de la tarea de recolección que se realicen en el sistema de ficheros de Moodle';
$string['savelog_ok_statement'] = 'Almacena en el registro registro Declaraciones de Estado exitosas';
$string['savelog_ok_statement_description'] = 'Si está activo, almacena en el registro las declaraciones de estado exitosas y fallidas; de lo contrario solo almacena en el registro las declaraciones fallidas';
$string['check_statement'] = 'Comprobar Sentencias';
$string['checkstatementdescription'] = 'Comprueba la validez de las sentencias previo envío al servidor SmartKlass';
$string['max_block_cicles'] = 'Número máximo de ciclos de bloqueo';
$string['max_block_ciclesdescription'] = 'Define el número máximo de ciclos de recolección hasta que se libere el bloqueo de recolección';
$string['collector_status'] = 'Estado de los Recolectores';
$string['collector_name'] = 'Nombre del Colector';
$string['active'] = 'Activo';
$string['last_registry'] = 'Último Registro (Recogido/Máximo)';
$string['last_exectime'] = 'Última marca de tiempo recogida';
$string['execution_log'] = 'Registro de Ejecuciones';
$string['dashboard'] = 'Cuadro de Mando';
$string['studentdashboard'] = 'Panel de Alumno';
$string['teacherdashboard'] = 'Panel de Profesor';
$string['institutiondashboard'] = 'Panel de Institución';
$string['nostudentrole'] = 'No tienes los permisos adecuados para visualizar el Panel de Estudiante';
$string['noteacherrole'] = 'No tienes los permisos adecuados para visualizar el Panel de Profesor';
$string['noinstitutionrole'] = 'No tienes los permisos adecuados para visualizar el Panel de Institución';
$string['configure_access'] = 'Configurar acceso a Panel SmartKlass';
$string['register'] = 'Registrar nuevo usuario en SmartKlass';
$string['noaccess'] = 'Acceso denegado por el usuario';
$string['activate_student_dashboard'] = 'Activar panel de alumno';
$string['activate_teacher_dashboard'] = 'Activar panel de profesor';
$string['activate_institution_dashboard'] = 'Activar panel de institucion';
$string['activate_student_dashboard_description'] = 'Activar el acceso al panel del alumno de SmartKlass en los cursos';
$string['activate_teacher_dashboard_description'] = 'Activar el acceso al panel del profesor de SmartKlass en los cursos';
$string['activate_institution_dashboard_description'] = 'Activar el acceso al panel de insitucion de SmartKlass en el sitio';
$string['student_dashboard_noactive'] = 'El panel de alumno está inactivo. Consulta con el administrador de la plataforma';
$string['teacher_dashboard_noactive'] = 'El panel de estudiante está inactivo. Consulte con el administrador de la plataforma';
$string['institution_dashboard_noactive'] = 'El panel de institución está inactivo. Consulte con el administrador de la plataforma';
$string['invalid_dataprovider'] = 'El tipo DataProvider proporcionado es incorrecto.';
$string['harvester_service_unavailable'] = 'Servicio de recolección de datos inactivo';
$string['harvester_service_instance_running'] = 'Instancia del servicio de recolección de datos en curso. Solamente puede existir una instancia activa';
$string['user_no_complete_course'] = 'El usuario {$a->user} no ha completado en el curso {$a->course} por lo que no se enviará el registro';
$string['user_no_enrol_course'] = 'El usuario {$a->user} no está matriculado en el curso {$a->course} por lo que no se enviará el registro';
$string['user_no_init_course'] = 'El usuario {$a->user} no ha iniciado el curso {$a->course} por lo que no se enviará el registro';
$string['user_no_completed_activity'] = 'El usuario {$a->user} no ha completado la actividad {$a->activity} por lo que no se enviará el registro';
$string['error_lo_verb_from_log'] = 'No se puede generar un verbo para el modulo {$a->module} y la acción {$a->action}';
$string['no_record_to_update'] = 'No hay nuevos registros a actualizar';
$string['statement_send_ok'] =  'Sentencia enviada correctamente';
$string['ok'] =  'OK';
$string['ko'] =  'KO';
$tring['crontask'] = 'Tareas programadas de SmartKlass';
$string['view'] = 'Ver';
$string['edit'] = 'Editar';
$string['no_role'] = 'No hay rol establecido';
$string['no_oauth_comunication'] = 'No hay comunicación con el servidor de autenticación';
$string['harvest'] = 'Recolectar';
$string['no_dashboard_endpoint'] = 'La URL del dashboard es incorrecta. Intentalo de nuevo más tarde';
$string['no_access_token_aviable'] = 'Las credenciales de acceso no están disponibles. Intentalo de nuevo más tarde';
$string['harvester_service_not_register'] = 'El servicio SmartKlass no esta registrado. Por faver revisa el registro en la aplicación';
$string['lrs_error'] = 'Error intentando conectar con el LRS';

$string['register_institution'] = 'SmartKlass - Registro';
$string['institution'] = 'INSTITUCIÓN';
$string['manager_name'] = 'NOMBRE';
$string['manager_lastname'] = 'APELLIDOS';
$string['manager_email'] = 'EMAIL';
$string['password'] = 'CONTRASEÑA';
$string['confirm_password'] = 'CONFIRMAR CONTRASEÑA';
$string['captcha'] = 'CAPTCHA';
$string['accept'] = 'Acepto';
$string['terms'] = 'Términos y condiciones';
$string['register_submit'] = 'Registrar';
$string['password_no_match'] = 'Las contraseñas no coinciden';
$string['captcha_no_match'] = 'El Captcha no coincide';
$string['password_no_length'] = 'La contraseña debe tener, al menos, 8 caracteres';
$string['email_wrong'] = 'Email incorrecto';

$string['warning_activity_completion'] = 'IMPORTANTE: Por favor, asegúrate que tienes activada el Control de Finalización de Actividad en la configuración de Moodle!!';