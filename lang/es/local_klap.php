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
 * Strings for component 'local_klap', language 'es'
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Klap';

$string['controlpanel'] = 'Panel de Control';
$string['fullharvester'] = 'Recolección completa de datos';
$string['activate'] = 'Activar';
$string['activatedescription'] = 'Activa el trazado de datos de Moodle al servidor Klap';
$string['endpoint'] = 'Servidor Klap';
$string['endpointdescription'] = 'Selecciona el servidor de Klap al que deseas conectarte';
$string['defaultserver'] = 'Servidor por defecto';
$string['secureserver'] = 'Servidor seguro';
$string['localserver'] = 'Servidor local';
$string['authtype'] = 'Tipo de autenticación';
$string['authtypedescription'] = 'Selecciona el tipo de autenticación con la plataforma Klap';
$string['basic'] = 'Básica';
$string['oauth'] = 'oAUTH';
$string['username'] = 'Usuario';
$string['usernamedescription'] = 'Para autenticación básica, introduce el usuario a utilizar con la plataforma Klap';
$string['password'] = 'Contraseña';
$string['passworddescription'] = 'Para autenticación básica, introduce la contraseña a utilizar con la plataforma Klap';
$string['save_log'] = 'Almacenar registro';
$string['savelogdescription'] = 'Almacena registro de todas las ejecuciones de la tarea de recolección que se realicen en el sistema de ficheros de Moodle';
$string['savelog_ok_statement'] = 'Almacena en el registro registro Declaraciones de Estado exitosas';
$string['savelog_ok_statement_description'] = 'Si está activo almacena en el registro las declaraciones des estado exitosas y fallidas de lo contrario solo almacena en el registro las declaraciones fallidas';
$string['check_statement'] = 'Comprobar Sentencias';
$string['checkstatementdescription'] = 'Comprueba la validez de las sentencias previo envio al servidor Klap';
$string['collector_status'] = 'Estado de los Recolectores';
$string['collector_name'] = 'Nombre del Colector';
$string['active'] = 'Activo';
$string['last_registry'] = 'Último Registro (Ejecutado/Total)';
$string['last_exectime'] = 'Última marca de tiempo ejecutada';
$string['execution_log'] = 'Registro de Ejecuciones';
$string['dashboard'] = 'Cuadro de Mando';
$string['studentdashboard'] = 'Panel de Alumno';
$string['teacherdashboard'] = 'Panel de Profesor';
$string['institutiondashboard'] = 'Panel de Institución';
$string['nostudentrole'] = 'No tienes los permisos adecuados para visualizar el Panel de Estudiante';
$string['noteacherrole'] = 'No tienes los permisos adecuados para visualizar el Panel de Profesor';
$string['noinstitutionrole'] = 'No tienes los permisos adecuados para visualizar el Panel de Institución';
$string['configure_access'] = 'Configurar acceso a Panel Klap';
$string['register'] = 'Registrar nuevo usuario en Klap';
$string['noaccess'] = 'Acceso denegado por el usuario';
$string['activate_student_dashboard'] = 'Activar panel de alumno';
$string['activate_teacher_dashboard'] = 'Activar panel de profesor';
$string['activate_institution_dashboard'] = 'Activar panel de institucion';
$string['activate_student_dashboard_description'] = 'Activar el acceso al panel del alumno de Klap en los cursos';
$string['activate_teacher_dashboard_description'] = 'Activar el acceso al panel del profesor de Klap en los cursos';
$string['activate_institution_dashboard_description'] = 'Activar el acceso al panel de insitucion de Klap en el sitio';
$string['student_dashboard_noactive'] = 'El panel de alumno está inactivo. Consulta con el administrador de la plataforma';
$string['teacher_dashboard_noactive'] = 'El panel de estudiante está inactivo. Consulte con el administrador de la plataforma';
$string['institution_dashboard_noactive'] = 'El panel de institución está inactivo. Consulte con el administrador de la plataforma';